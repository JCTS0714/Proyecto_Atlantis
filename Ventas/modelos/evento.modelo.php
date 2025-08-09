<?php
require_once "conexion.php";

class ModeloEvento {

    // Crear evento - solo con título y color
    public static function mdlCrearEvento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (titulo, color) VALUES (:titulo, :color)");

        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":color", $datos["color"], PDO::PARAM_STR);

        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    // Editar evento - solo título y color
    public static function mdlEditarEvento($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET titulo = :titulo, color = :color WHERE id = :id");

        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":color", $datos["color"], PDO::PARAM_STR);
        $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

        if($stmt->execute()){
            return "ok";
        } else {
            return "error";
        }

        $stmt->close();
        $stmt = null;
    }

    // Eliminar evento
    public static function mdlEliminarEvento($tabla, $id) {
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

    // Mostrar eventos - solo título y color
    public static function mdlMostrarEventos($tabla) {
        $stmt = Conexion::conectar()->prepare("SELECT id, titulo, color FROM $tabla");
        $stmt->execute();
        return $stmt->fetchAll();

        $stmt->close();
        $stmt = null;
    }
}
?>
