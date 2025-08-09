<?php
require_once "conexion.php";

class ModeloCalendario {

    // Método para crear una nueva reunión
    public static function mdlCrearReunion($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (cliente_id, usuario_id, titulo, descripcion, fecha, hora_inicio, hora_fin, ubicacion, estado, recordatorio, observaciones, evento_id) VALUES (:cliente_id, :usuario_id, :titulo, :descripcion, :fecha, :hora_inicio, :hora_fin, :ubicacion, :estado, :recordatorio, :observaciones, :evento_id)");

        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
        $stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
        $stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);
        $stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
        $stmt->bindParam(":recordatorio", $datos["recordatorio"], PDO::PARAM_INT);
        $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);
        $stmt->bindParam(":evento_id", $datos["evento_id"], PDO::PARAM_INT);

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
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET cliente_id = :cliente_id, usuario_id = :usuario_id, titulo = :titulo, descripcion = :descripcion, fecha = :fecha, hora_inicio = :hora_inicio, hora_fin = :hora_fin, ubicacion = :ubicacion, estado = :estado, recordatorio = :recordatorio, observaciones = :observaciones, evento_id = :evento_id WHERE id = :id");

        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
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
            return "ok";
        } else {
            return "error";
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
        if($item != null){
            $stmt = Conexion::conectar()->prepare("SELECT r.*, e.color FROM $tabla r LEFT JOIN eventos e ON r.evento_id = e.id WHERE r.$item = :$item");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT r.*, e.color FROM $tabla r LEFT JOIN eventos e ON r.evento_id = e.id");
            $stmt->execute();
            return $stmt->fetchAll();
        }

        $stmt->close();
        $stmt = null;
    }

    // Método para actualizar solo fecha de una reunión
    public static function mdlActualizarFechaReunion($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET fecha = :fecha WHERE id = :id");

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
}
?>
