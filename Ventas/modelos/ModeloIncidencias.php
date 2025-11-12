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
        $stmt = Conexion::conectar()->prepare("INSERT INTO incidencias (correlativo, nombre_incidencia, cliente_id, usuario_id, fecha_solicitud, prioridad, observaciones, columna_backlog, fecha_creacion) VALUES (:correlativo, :nombre_incidencia, :cliente_id, :usuario_id, :fecha_solicitud, :prioridad, :observaciones, 'En proceso', NOW())");

        $stmt->bindParam(":correlativo", $datos["correlativo"], PDO::PARAM_STR);
        $stmt->bindParam(":nombre_incidencia", $datos["nombre_incidencia"], PDO::PARAM_STR);
        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_solicitud", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }

    static public function mdlMostrarIncidencias($item = null, $valor = null) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT i.*, i.columna_backlog, c.nombre as nombre_cliente, u.nombre as nombre_usuario FROM incidencias i LEFT JOIN clientes c ON i.cliente_id = c.id LEFT JOIN usuarios u ON i.usuario_id = u.id WHERE i.$item = :$item ORDER BY i.fecha_creacion DESC");
            $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT i.*, i.columna_backlog, c.nombre as nombre_cliente, u.nombre as nombre_usuario FROM incidencias i LEFT JOIN clientes c ON i.cliente_id = c.id LEFT JOIN usuarios u ON i.usuario_id = u.id ORDER BY i.fecha_creacion DESC");
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

    static public function mdlEditarIncidencia($datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE incidencias SET nombre_incidencia = :nombre_incidencia, cliente_id = :cliente_id, usuario_id = :usuario_id, fecha_solicitud = :fecha_solicitud, prioridad = :prioridad, observaciones = :observaciones WHERE id = :id");

        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
        $stmt->bindParam(":nombre_incidencia", $datos["nombre_incidencia"], PDO::PARAM_STR);
        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_solicitud", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":prioridad", $datos["prioridad"], PDO::PARAM_STR);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

        if ($stmt->execute()) {
            return "ok";
        } else {
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
        $stmt = Conexion::conectar()->prepare("UPDATE incidencias SET columna_backlog = :columna WHERE id = :id");
        $stmt->bindParam(":columna", $columna, PDO::PARAM_STR);
        $stmt->bindParam(":id", $idIncidencia, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "ok";
        } else {
            return "error";
        }
    }
}
?>
