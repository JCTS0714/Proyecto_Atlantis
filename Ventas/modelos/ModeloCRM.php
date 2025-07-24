<?php
require_once "conexion.php";

class ModeloCRM {
    static public function mdlMostrarOportunidades($tabla, $item = null, $valor = null) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        $stmt = null;
        return $resultado;
    }

    static public function mdlRegistrarOportunidad($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (cliente_id, usuario_id, titulo, descripcion, valor_estimado, probabilidad, estado, fecha_cierre_estimada) VALUES (:cliente_id, :usuario_id, :titulo, :descripcion, :valor_estimado, :probabilidad, :estado, :fecha_cierre_estimada)");

        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":valor_estimado", $datos["valor_estimado"]);
        $stmt->bindParam(":probabilidad", $datos["probabilidad"], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha_cierre_estimada", $datos["fecha_cierre_estimada"]);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlActualizarEstado($tabla, $id, $estado) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");

        $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlEliminarOportunidad($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlMostrarClientes($tabla, $item = null, $valor = null) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        $stmt = null;
        return $resultado;
    }
}
?>
