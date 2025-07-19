<?php

require_once "conexion.php";

class ModeloProductos {

  // Mostrar productos
  static public function mdlMostrarProductos($tabla, $item, $valor) {
      if ($item != null) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");
        $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(); // Retorna una sola fila
      } else {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
        $stmt->execute();
        return $stmt->fetchAll();
      }

      $stmt->close();
      $stmt = null;
    }

    

  // Insertar producto
  static public function mdlIngresarProducto($tabla, $datos) {
    try {
      $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, codigo, descripcion, imagen, stock, precio_compra, precio_venta, ventas, fecha, id_categoria) 
        VALUES(:nombre, :codigo, :descripcion, :imagen, :stock, :precio_compra, :precio_venta, :ventas, :fecha, :id_categoria)");

      $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
      $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
      $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
      $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
      $stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_INT);
      $stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
      $stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
      $stmt->bindParam(":ventas", $datos["ventas"], PDO::PARAM_INT);
      $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
      $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);

      if ($stmt->execute()) {
        $stmt = null;
        return "ok";
      } else {
        $error = $stmt->errorInfo();
        $stmt = null;
        return "error: " . $error[2];
      }

    } catch (PDOException $e) {
      $stmt = null;
      return "Error: " . $e->getMessage();
    }
  }
  //**METODO PARA EDITAR PRODUCTOS */
  static public function mdlEditarProducto($tabla, $datos) {
      $stmt = Conexion::conectar()->prepare
      ("UPDATE $tabla SET nombre = :nombre, codigo = :codigo, descripcion = :descripcion, imagen = :imagen, stock = :stock, precio_compra = :precio_compra, precio_venta = :precio_venta, ventas = :ventas, fecha = :fecha, id_categoria = :id_categoria WHERE id = :id");

      $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);
      $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
      $stmt->bindParam(":codigo", $datos["codigo"], PDO::PARAM_STR);
      $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
      $stmt->bindParam(":imagen", $datos["imagen"], PDO::PARAM_STR);
      $stmt->bindParam(":stock", $datos["stock"], PDO::PARAM_INT);
      $stmt->bindParam(":precio_compra", $datos["precio_compra"], PDO::PARAM_STR);
      $stmt->bindParam(":precio_venta", $datos["precio_venta"], PDO::PARAM_STR);
      $stmt->bindParam(":ventas", $datos["ventas"], PDO::PARAM_INT);
      $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
      $stmt->bindParam(":id_categoria", $datos["id_categoria"], PDO::PARAM_INT);

      if ($stmt->execute()) {
        return "ok";
      } else {
        return "error";
      }
   }
  //**METODO PARA BORRAR PRODUCTOS */ 
  static public function mdlBorrarProducto($tabla, $datos) {
      $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
      $stmt->bindParam(":id", $datos, PDO::PARAM_INT);

      if ($stmt->execute()) {
        return "ok";
      } else {
        return "error"; 
      }
        $stmt -> close();
        $stmt = null;
    }
  }


