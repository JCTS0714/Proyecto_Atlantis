<?php

require_once "conexion.php";

class ModeloCliente{

  /**============================
   * MÉTODO PARA MOSTRAR CLIENTES
   * ============================
   */
  static public function MdlMostrarCliente($tabla,$item,$valor){

    if($item != null)
    {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");

      $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);

      $stmt->execute();

      return $stmt->fetch(); /**Nos retorna solo una fila */
    }
    else
    {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");

      $stmt->execute();

      return $stmt->fetchAll(); /**Nos retorna toda las filas de la tabla */
    }
    
    $stmt -> close();

    $stmt=null;

  }

  /* MÉTODO PARA REGISTRAR CLIENTE */
  static public function mdlRegistrarCliente($tabla,$datos){
    $stmt = Conexion::conectar()->prepare("INSERT INTO clientes(nombre, tipo, documento, telefono, correo, direccion, fecha_creacion) VALUES(:nombre, :tipo, :documento, :telefono, :correo, :direccion, :fecha_creacion)");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_creacion", $datos["fecha_creacion"], PDO::PARAM_STR);

    if($stmt->execute()){
      return "ok";
    }
    else{
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA EDITAR CLIENTE */
  static public function mdlEditarCliente($tabla,$datos){
    $stmt = Conexion::conectar()->prepare("UPDATE clientes SET nombre = :nombre, tipo = :tipo, documento = :documento, telefono = :telefono, correo = :correo, direccion = :direccion, fecha_creacion = :fecha_creacion WHERE id = :id");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":direccion", $datos["direccion"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_creacion", $datos["fecha_creacion"], PDO::PARAM_STR);
    $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

    if($stmt->execute()){
      return "ok";
    }
    else{
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA ELIMINAR CLIENTE */
  static public function mdlEliminarCliente($tabla, $id){
    $stmt = Conexion::conectar()->prepare("DELETE FROM clientes WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }
    $stmt->closeCursor();
    $stmt = null;
  }
}
