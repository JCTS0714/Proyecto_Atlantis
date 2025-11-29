<?php
require_once "conexion.php";

class ModeloCalendario {

public static function mdlCrearReunion($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (cliente_id, usuario_id, titulo, descripcion, fecha, hora_inicio, hora_fin, ubicacion, observaciones, ultima_notificacion) VALUES (:cliente_id, :usuario_id, :titulo, :descripcion, :fecha, :hora_inicio, :hora_fin, :ubicacion, :observaciones, :ultima_notificacion)");

    $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
    $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
    $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);


    $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
    $stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
    $stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);
    $stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
    $ultima_notificacion = null;
    $stmt->bindParam(":ultima_notificacion", $ultima_notificacion, PDO::PARAM_NULL);
    $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

    if($stmt->execute()){
        return "ok";
    } else {
        return "error";
    }

        $stmt->close();
        $stmt = null;
    }

    // Método para editar una reunión
    public static function mdlEditarReunion($tabla, $datos) {
        // Validar que usuario_id esté definido y no sea vacío
        if (!isset($datos["usuario_id"]) || empty($datos["usuario_id"])) {
            return json_encode(["success" => false, "error" => "No se ha seleccionado ningún usuario."]);
        }
        $usuario_id = $datos["usuario_id"];
        $conexion = Conexion::conectar();
        $stmtUsuario = $conexion->prepare("SELECT id FROM usuarios WHERE id = :usuario_id");
        $stmtUsuario->bindParam(":usuario_id", $usuario_id, PDO::PARAM_INT);
        $stmtUsuario->execute();
        if ($stmtUsuario->rowCount() == 0) {
            return json_encode(["success" => false, "error" => "El usuario seleccionado no existe."]);
        }
        $stmtUsuario->closeCursor();

        $stmt = $conexion->prepare("UPDATE $tabla SET cliente_id = :cliente_id, usuario_id = :usuario_id, titulo = :titulo, descripcion = :descripcion, fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin, ubicacion = :ubicacion, estado = :estado, recordatorio = :recordatorio, observaciones = :observaciones, evento_id = :evento_id, ultima_notificacion = NULL WHERE id = :id");

        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        try {
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
            $stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
            $stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            $stmt->bindParam(":recordatorio", $datos["recordatorio"], PDO::PARAM_INT);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
            $stmt->bindParam(":evento_id", $datos["evento_id"], PDO::PARAM_INT);
            $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

            if($stmt->execute()){
                return json_encode(["success" => true]);
            } else {
                return json_encode(["success" => false, "error" => "No se pudo actualizar la reunión."]);
            }
        } catch (PDOException $e) {
            return json_encode(["success" => false, "error" => $e->getMessage()]);
        }
        $stmt->close();
        $stmt = null;
    }

    // Método para eliminar una reunión
    public static function mdlEliminarReunion($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    // Método para mostrar reuniones
    public static function mdlMostrarReuniones($tabla, $item = null, $valor = null) {
        $conexion = Conexion::conectar();
        try {
            if ($item != null) {
                // Incluir nombre del cliente mediante LEFT JOIN
                $sql = "SELECT r.*, c.nombre AS nombre_cliente FROM $tabla r LEFT JOIN clientes c ON r.cliente_id = c.id WHERE r.$item = :valor";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':valor', $valor, PDO::PARAM_STR);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Excluir reuniones archivadas del calendario y traer nombre de cliente
                $sql = "SELECT r.*, c.nombre AS nombre_cliente FROM $tabla r LEFT JOIN clientes c ON r.cliente_id = c.id WHERE r.archivado = 0";
                $stmt = $conexion->prepare($sql);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log("mdlMostrarReuniones ERROR: " . $e->getMessage());
            return [];
        }

        $stmt->close();
        $stmt = null;
    }

    // Método para actualizar solo fecha de una reunión
    public static function mdlActualizarFechaReunion($tabla, $datos) {
        // Al actualizar fecha, limpiar ultima_notificacion para permitir re-notificación si corresponde
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha = :fecha, ultima_notificacion = NULL WHERE id = :id");

        $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }
    
    // Método para verificar si un evento existe en la tabla eventos
    public static function mdlExisteEvento($eventoId) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM evento WHERE id = :id");
        $stmt->bindParam(":id", $eventoId, PDO::PARAM_INT);
        $stmt->execute();
        $existe = $stmt->fetch();
        return $existe ? true : false;
    }

    // Método para verificar si un cliente existe en la tabla clientes
    public static function mdlExisteCliente($clienteId) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM clientes WHERE id = :id");
        $stmt->bindParam(":id", $clienteId, PDO::PARAM_INT);
        $stmt->execute();
        $existe = $stmt->fetch();
        return $existe ? true : false;
    }
    // Método para obtener reuniones próximas a notificar (3, 2 y 1 día antes) sin filtrar por recordatorio ni estado, y que no hayan sido notificadas hoy
    public static function mdlObtenerReunionesParaNotificar($tabla, $usuario_id) {
        // Ambos perfiles (admin y vendedor) ven las mismas notificaciones
        // Excluir reuniones archivadas para evitar notificar sobre eventos ya procesados
        $sql = "SELECT r.*, c.nombre AS nombre_cliente, CONCAT(r.fecha, ' ', r.hora_inicio) AS fecha_hora
            FROM $tabla r
            LEFT JOIN clientes c ON r.cliente_id = c.id
            WHERE r.archivado = 0";
        $stmt = Conexion::conectar()->prepare($sql);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en mdlObtenerReunionesParaNotificar: " . $e->getMessage());
            return [];
        }
    }

    // Método para actualizar la fecha de última notificación
    public static function mdlActualizarUltimaNotificacion($tabla, $id, $fecha) {
        $sql = "UPDATE $tabla SET ultima_notificacion = :fecha WHERE id = :id";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Método para actualizar la fecha de última notificación en lote
    public static function mdlActualizarUltimaNotificacionLote($tabla, $ids, $fecha) {
        if (empty($ids) || !is_array($ids)) {
            return false;
        }
        // Crear placeholders (?, ?, ?)
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "UPDATE $tabla SET ultima_notificacion = ? WHERE id IN ($placeholders)";
        $stmt = Conexion::conectar()->prepare($sql);

        // Primer parámetro es la fecha
        $params = array_merge([$fecha], $ids);

        try {
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Error mdlActualizarUltimaNotificacionLote: ' . $e->getMessage());
            return false;
        }
    }

    // Método para verificar si ya existe una actividad con el mismo título para el mismo cliente
    public static function mdlExisteActividadDuplicada($clienteId, $titulo) {
        $stmt = Conexion::conectar()->prepare("SELECT id FROM reuniones WHERE cliente_id = :cliente_id AND titulo = :titulo");
        $stmt->bindParam(":cliente_id", $clienteId, PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
        $stmt->execute();
        $existe = $stmt->fetch();
        return $existe ? true : false;
    }

    // Método para obtener reuniones de múltiples clientes
    public static function mdlMostrarReunionesPorClientes($clienteIds) {
        if (empty($clienteIds)) {
            return [];
        }
        $placeholders = str_repeat('?,', count($clienteIds) - 1) . '?';
        // Excluir reuniones archivadas para no mostrarlas en la lista de clientes recientes
        $stmt = Conexion::conectar()->prepare("SELECT * FROM reuniones WHERE cliente_id IN ($placeholders) AND archivado = 0");
        $stmt->execute($clienteIds);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Listar reuniones pasadas que no han sido archivadas
        public static function mdlListarReunionesPasadas($tabla, $limit = 100) {
                // Considera reuniones con fecha pasada o cuyo datetime (fecha + hora_fin) sea menor que NOW().
                // Usa STR_TO_DATE tolerante a formatos con o sin segundos.
                $sql = "SELECT r.*, c.nombre AS nombre_cliente
                                FROM $tabla r
                                LEFT JOIN clientes c ON r.cliente_id = c.id
                                WHERE r.archivado = 0
                                    AND (
                                        COALESCE(
                                            STR_TO_DATE(CONCAT(r.fecha, ' ', r.hora_fin),'%Y-%m-%d %H:%i:%s'),
                                            STR_TO_DATE(CONCAT(r.fecha, ' ', r.hora_fin),'%Y-%m-%d %H:%i')
                                        ) < NOW()
                                        OR r.fecha < CURDATE()
                                    )
                                ORDER BY r.fecha DESC, r.hora_fin DESC
                                LIMIT :limit";
                $stmt = Conexion::conectar()->prepare($sql);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

    // Archivar o marcar como concretado una reunión (marca archivado=1 y opcional estado)
    public static function mdlArchivarReunion($tabla, $id, $estado = null, $usuario_id = null) {
        $sql = "UPDATE $tabla SET archivado = 1";
        if ($estado !== null) $sql .= ", estado = :estado";
        if ($usuario_id !== null) $sql .= ", archivado_por = :archivado_por, archivado_en = NOW()";
        $sql .= " WHERE id = :id";

        $stmt = Conexion::conectar()->prepare($sql);
        if ($estado !== null) $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        if ($usuario_id !== null) $stmt->bindParam(':archivado_por', $usuario_id, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener reuniones archivadas para la interfaz de 'Reuniones archivadas'
    public static function mdlObtenerReunionesArchivadas($tabla, $limit = 500) {
        $sql = "SELECT r.*, c.nombre AS nombre_cliente FROM $tabla r LEFT JOIN clientes c ON r.cliente_id = c.id WHERE r.archivado = 1 ORDER BY r.archivado_en DESC LIMIT :limit";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
