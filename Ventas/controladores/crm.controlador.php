<?php

require_once "conexion.php";

class ModeloCRM {

  /* Mostrar tarjetas del CRM */
  static public function mdlMostrarTarjetas($tabla, $estado = null) {
    if ($estado != null) {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE estado = :estado ORDER BY fecha DESC");
      $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->fetchAll();
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha DESC");
      $stmt->execute();
      return $stmt->fetchAll();
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* Registrar nueva tarjeta */
  static public function mdlRegistrarTarjeta($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(id_cliente, titulo, estado, fecha) VALUES(:id_cliente, :titulo, :estado, NOW())");

    $stmt->bindParam(":id_cliente", $datos["id_cliente"], PDO::PARAM_INT);
    $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);

    if($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }

    $stmt->closeCursor();
    $stmt = null;
  }

  /* Actualizar estado de tarjeta */
  static public function mdlActualizarEstado($tabla, $id, $estado) {
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");

    $stmt->bindParam(":estado", $estado, PDO::PARAM_STR);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }

    $stmt->closeCursor();
    $stmt = null;
  }

  /* Eliminar tarjeta */
  static public function mdlEliminarTarjeta($tabla, $id) {
    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }

    $stmt->closeCursor();
    $stmt = null;
  }
}
