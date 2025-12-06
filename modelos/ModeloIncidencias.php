<?php
require_once "conexion.php";

class ModeloIncidencias {

    static public function mdlGenerarCorrelativo() {
        $stmt = Conexion::conectar()->prepare("SELECT MAX(CAST(correlativo AS UNSIGNED)) as max_correlativo FROM incidencias");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxCorrelativo = $result['max_correlativo'] ? $result['max_correlativo'] : 0;
        $correlativo = str_pad($maxCorrelativo + 1, 6, '0', STR_PAD_LEFT);
        return $correlativo;
    }

    static public function mdlBuscarClientes($term) {
        $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes WHERE estado = 2 AND nombre LIKE :term ORDER BY nombre ASC LIMIT 10");
        $likeTerm = "%" . $term . "%";
        $stmt->bindParam(":term", $likeTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    static public function mdlCrearIncidencia($datos) {
        try {
            // Suponer que la tabla tiene la columna `fecha` y usarla siempre
            $db = Conexion::conectar();
            $has_columna_backlog = false;
            try { $col3 = $db->query("SHOW COLUMNS FROM incidencias LIKE 'columna_backlog'")->fetch(); $has_columna_backlog = !empty($col3); } catch (Exception $e) { $has_columna_backlog = false; }

            if ($has_columna_backlog) {
                $sql = "INSERT INTO incidencias (correlativo, nombre_incidencia, cliente_id, usuario_id, fecha, prioridad, observaciones, columna_backlog, fecha_creacion) VALUES (:correlativo, :nombre_incidencia, :cliente_id, :usuario_id, :fecha_val, :prioridad, :observaciones, 'En proceso', NOW())";
            } else {
                $sql = "INSERT INTO incidencias (correlativo, nombre_incidencia, cliente_id, usuario_id, fecha, prioridad, observaciones, fecha_creacion) VALUES (:correlativo, :nombre_incidencia, :cliente_id, :usuario_id, :fecha_val, :prioridad, :observaciones, NOW())";
            }

            $stmt = $db->prepare($sql);

            $stmt->bindParam(":correlativo", $datos["correlativo"], PDO::PARAM_STR);
            $stmt->bindParam(":nombre_incidencia", $datos["nombre_incidencia"], PDO::PARAM_STR);
            $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
            $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
            $stmt->bindParam(":fecha_val", $datos["fecha"], PDO::PARAM_STR);
            $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

            if ($stmt->execute()) {
                // Obtener id insertado
                $lastId = $db->lastInsertId();
                return array('status' => 'ok', 'id' => $lastId);
            } else {
                $err = $stmt->errorInfo();
                error_log('mdlCrearIncidencia ERROR: ' . json_encode($err));
                return array('status' => 'error', 'db_error' => $err);
            }
        } catch (PDOException $e) {
            error_log('mdlCrearIncidencia EXCEPTION: ' . $e->getMessage());
            return array('status' => 'error', 'exception' => $e->getMessage());
        }
    }

    static public function mdlMostrarIncidencias($item = null, $valor = null) {
        $db = Conexion::conectar();
        // Detectar solo si existe la columna 'columna_backlog' y mapear desde prioridad si no existe
        $has_columna_backlog = false;
        try { $has_columna_backlog = (bool)$db->query("SHOW COLUMNS FROM incidencias LIKE 'columna_backlog'")->fetch(); } catch (Exception $e) { $has_columna_backlog = false; }

        if ($has_columna_backlog) {
            $columnaSelect = 'i.columna_backlog AS columna_backlog';
        } else {
            $columnaSelect = "CASE
                WHEN LOWER(i.prioridad) = 'alta' THEN 'En proceso'
                WHEN LOWER(i.prioridad) = 'media' THEN 'Validado'
                WHEN LOWER(i.prioridad) = 'baja' THEN 'Terminado'
                ELSE 'En proceso' END AS columna_backlog";
        }

        if ($item != null) {
            $sql = "SELECT i.*, i.fecha as fecha_solicitud, $columnaSelect, c.nombre as nombre_cliente, c.empresa as empresa_cliente, u.nombre as nombre_usuario FROM incidencias i LEFT JOIN clientes c ON i.cliente_id = c.id LEFT JOIN usuarios u ON i.usuario_id = u.id WHERE i.$item = :$item ORDER BY i.fecha_creacion DESC";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $sql = "SELECT i.*, i.fecha as fecha_solicitud, $columnaSelect, c.nombre as nombre_cliente, c.empresa as empresa_cliente, u.nombre as nombre_usuario FROM incidencias i LEFT JOIN clientes c ON i.cliente_id = c.id LEFT JOIN usuarios u ON i.usuario_id = u.id ORDER BY i.fecha_creacion DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    static public function mdlEditarIncidencia($datos) {
        $db = Conexion::conectar();
        // Usar siempre la columna 'fecha' para actualizar la incidencia
        $sql = "UPDATE incidencias SET nombre_incidencia = :nombre_incidencia, cliente_id = :cliente_id, usuario_id = :usuario_id, fecha = :fecha, prioridad = :prioridad, observaciones = :observaciones WHERE id = :id";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre_incidencia", $datos["nombre_incidencia"], PDO::PARAM_STR);
        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            $err = $stmt->errorInfo();
            error_log('mdlEditarIncidencia ERROR: ' . json_encode($err));
            return "error";
        }
    }

    static public function mdlEliminarIncidencia($id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM incidencias WHERE id = :id");
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    static public function mdlActualizarColumnaBacklog($idIncidencia, $columna) {
        $db = Conexion::conectar();
        // Si la columna 'columna_backlog' no existe, actualizar prioridad en base a la columna objetivo
        $has_columna_backlog = false;
        try { $has_columna_backlog = (bool)$db->query("SHOW COLUMNS FROM incidencias LIKE 'columna_backlog'")->fetch(); } catch (Exception $e) { $has_columna_backlog = false; }

        if ($has_columna_backlog) {
            $stmt = $db->prepare("UPDATE incidencias SET columna_backlog = :columna WHERE id = :id");
            $stmt->bindParam(":columna", $columna, PDO::PARAM_STR);
            $stmt->bindParam(":id", $idIncidencia, PDO::PARAM_INT);
            if ($stmt->execute()) {
                return "ok";
            } else {
                $err = $stmt->errorInfo();
                error_log('mdlActualizarColumnaBacklog ERROR: ' . json_encode($err));
                return "error";
            }
        } else {
            // La columna 'columna_backlog' no existe en esta DB.
            // Crear la columna y luego actualizarla para persistir el movimiento del Kanban.
            try {
                $alter = $db->prepare("ALTER TABLE incidencias ADD COLUMN columna_backlog VARCHAR(50) DEFAULT 'En proceso'");
                $alter->execute();
            } catch (Exception $e) {
                // Si falló la creación (p.ej. permisos), loguear y devolver error
                error_log('mdlActualizarColumnaBacklog ERROR creating columna_backlog: ' . $e->getMessage());
                return "error";
            }

            // Ahora actualizar la fila específica
            try {
                $stmt = $db->prepare("UPDATE incidencias SET columna_backlog = :columna WHERE id = :id");
                $stmt->bindParam(":columna", $columna, PDO::PARAM_STR);
                $stmt->bindParam(":id", $idIncidencia, PDO::PARAM_INT);
                if ($stmt->execute()) {
                    return "ok";
                } else {
                    $err = $stmt->errorInfo();
                    error_log('mdlActualizarColumnaBacklog UPDATE ERROR: ' . json_encode($err));
                    return "error";
                }
            } catch (Exception $e) {
                error_log('mdlActualizarColumnaBacklog EXCEPTION updating fila: ' . $e->getMessage());
                return "error";
            }
        }
    }
}
?>
