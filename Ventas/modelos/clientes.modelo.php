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
      if ($item == "nombre") {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item LIKE :$item");
        $valor = "%".$valor."%";
      } else {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
      }

      $stmt->bindParam(":".$item,$valor,PDO::PARAM_STR);

      $stmt->execute();

      return $stmt->fetchAll(); /**Nos retorna todas las filas que coinciden */
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

  /** ============================
   * MÉTODO PARA ACTUALIZAR CLIENTE
   * ============================
   */
  static public function mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2) {

    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $item1 = :$item1 WHERE $item2 = :$item2");
    $stmt->bindParam(":".$item1, $valor1, PDO::PARAM_STR);
    $stmt->bindParam(":".$item2, $valor2, PDO::PARAM_STR);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }
  }

  /* Nuevo método para obtener clientes para oportunidades */
  static public function mdlMostrarClientesParaOportunidad($searchTerm = null) {
    if ($searchTerm) {
      $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes WHERE nombre LIKE :nombre ORDER BY nombre ASC");
      $likeTerm = "%".$searchTerm."%";
      $stmt->bindParam(":nombre", $likeTerm, PDO::PARAM_STR);
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
    }
    $stmt->execute();
    return $stmt->fetchAll();
    $stmt->closeCursor();
    $stmt = null;
  }

  /* MÉTODO PARA REGISTRAR CLIENTE */
  static public function mdlRegistrarCliente($tabla,$datos){
    $stmt = Conexion::conectar()->prepare("INSERT INTO clientes(nombre, tipo, documento, telefono, correo, ciudad, migracion, referencia, fecha_contacto, empresa, estado) VALUES(:nombre, :tipo, :documento, :telefono, :correo, :ciudad, :migracion, :referencia, :fecha_contacto, :empresa, :estado)");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
    $stmt->bindParam(":migracion", $datos["migracion"], PDO::PARAM_STR);
    $stmt->bindParam(":referencia", $datos["referencia"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_contacto", $datos["fecha_contacto"], PDO::PARAM_STR);
    $stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);

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
    $stmt = Conexion::conectar()->prepare("UPDATE clientes SET nombre = :nombre, tipo = :tipo, documento = :documento, telefono = :telefono, correo = :correo, ciudad = :ciudad, migracion = :migracion, referencia = :referencia, fecha_contacto = :fecha_contacto, empresa = :empresa, fecha_creacion = :fecha_creacion WHERE id = :id");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":tipo", $datos["tipo"], PDO::PARAM_STR);
    $stmt->bindParam(":documento", $datos["documento"], PDO::PARAM_STR);
    $stmt->bindParam(":telefono", $datos["telefono"], PDO::PARAM_STR);
    $stmt->bindParam(":correo", $datos["correo"], PDO::PARAM_STR);
    $stmt->bindParam(":ciudad", $datos["ciudad"], PDO::PARAM_STR);
    $stmt->bindParam(":migracion", $datos["migracion"], PDO::PARAM_STR);
    $stmt->bindParam(":referencia", $datos["referencia"], PDO::PARAM_STR);
    $stmt->bindParam(":fecha_contacto", $datos["fecha_contacto"], PDO::PARAM_STR);
    $stmt->bindParam(":empresa", $datos["empresa"], PDO::PARAM_STR);
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

  /* MÉTODO PARA VERIFICAR SI UN CLIENTE TIENE OPORTUNIDADES */
  static public function mdlVerificarOportunidades($idCliente) {
    $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM oportunidades WHERE cliente_id = :cliente_id");
    $stmt->bindParam(":cliente_id", $idCliente, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total'] > 0;
  }
}
