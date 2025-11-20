<?php

require_once "conexion.php";

class ModeloUsuarios {

  /** ============================
   * MÉTODO PARA MOSTRAR USUARIOS
   * ============================ */
  static public function mdlMostrarUsuarios($tabla, $item, $valor) {
    if ($item != null) {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
      $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
      $stmt->execute();
      $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $resultado; // Devuelve una sola fila
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
      $stmt->execute();
      $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $stmt->closeCursor();
      return $resultado; // Devuelve todas las filas
    }
  }

  /** =============================
   * MÉTODO PARA REGISTRAR USUARIOS
   * ============================= */
  static public function mdlRegistrarUsuario($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla(nombre, usuario, password, perfil, foto) VALUES(:nombre, :usuario, :password, :perfil, :foto)");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
    $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
    $stmt->bindParam(":perfil", $datos["perfil"], PDO::PARAM_STR);
    $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);

    if ($stmt->execute()) {
      error_log("mdlRegistrarUsuario: execute OK for usuario=" . $datos["usuario"] . " | hash_prefix=" . substr($datos["password"],0,10) . " | hash_len=" . strlen($datos["password"]));
      return "ok";
    } else {
      $err = $stmt->errorInfo();
      error_log("mdlRegistrarUsuario: execute ERROR for usuario=" . $datos["usuario"] . " | SQLSTATE=" . $err[0] . " | code=" . $err[1] . " | message=" . $err[2]);
      return "error";
    }
  }

  /** ============================
   * MÉTODO PARA EDITAR USUARIOS
   * ============================ */
  static public function mdlEditarUsuario($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, password = :password, perfil = :perfil, foto = :foto, estado = :estado WHERE usuario = :usuario");

    $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
    $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
    $stmt->bindParam(":perfil", $datos["perfil"], PDO::PARAM_STR);
    $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
    $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
    $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error";
    }
  }

  /** ============================
   * MÉTODO PARA VERIFICAR SI UN USUARIO EXISTE
   * ============================ */
  static public function mdlExisteUsuario($usuarioId) {
    $stmt = Conexion::conectar()->prepare("SELECT id FROM usuarios WHERE id = :id");
    $stmt->bindParam(":id", $usuarioId, PDO::PARAM_INT);
    $stmt->execute();
    $existe = $stmt->fetch();
    return $existe ? true : false;
  }

  /** ============================
   * BORRAR USUARIO
   * ============================ */
  static public function mdlBorrarUsuario($tabla, $datos) {
    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
    $stmt->bindParam(":id", $datos, PDO::PARAM_INT);

    if ($stmt->execute()) {
      return "ok";
    } else {
      return "error"; 
    }
  }

    /** ============================
     * MÉTODO PARA ACTUALIZAR USUARIO
     * ============================ */
    static public function mdlActualizarUsuario($tabla, $datos) {
      $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET nombre = :nombre, usuario = :usuario, password = :password, perfil = :perfil, foto = :foto WHERE id = :id");

      $stmt->bindParam(":nombre", $datos["nombre"], PDO::PARAM_STR);
      $stmt->bindParam(":usuario", $datos["usuario"], PDO::PARAM_STR);
      $stmt->bindParam(":password", $datos["password"], PDO::PARAM_STR);
      $stmt->bindParam(":perfil", $datos["perfil"], PDO::PARAM_STR);
      $stmt->bindParam(":foto", $datos["foto"], PDO::PARAM_STR);
      $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

      if ($stmt->execute()) {
        return "ok";
      } else {
        return "error";
      }
    }

      /** ============================
       * MÉTODO PARA ACTUALIZAR UN CAMPO DE USUARIO
       * ============================ */
      static public function mdlActualizarCampoUsuario($tabla, $campo, $valor, $item, $id) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET $campo = :valor WHERE $item = :id");

        // Use appropriate parameter type based on the field
        if ($campo === "estado") {
          $stmt->bindParam(":valor", $valor, PDO::PARAM_INT);
        } else {
          $stmt->bindParam(":valor", $valor, PDO::PARAM_STR);
        }

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
          return "ok";
        } else {
          return "error";
        }
      }

      /** ============================
       * MÉTODO PARA ACTUALIZAR TOKEN DE RECUERDO
       * ============================ */
      static public function mdlActualizarRememberToken($id, $remember_token, $remember_expires) {
        $stmt = Conexion::conectar()->prepare("UPDATE usuarios SET remember_token = :remember_token, remember_expires = :remember_expires WHERE id = :id");

        $stmt->bindParam(":remember_token", $remember_token, PDO::PARAM_STR);
        $stmt->bindParam(":remember_expires", $remember_expires, PDO::PARAM_STR);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
          return "ok";
        } else {
          return "error";
        }
      }

      /** ============================
       * MÉTODO PARA MOSTRAR USUARIO POR TOKEN DE RECUERDO
       * ============================ */
      static public function mdlMostrarUsuarioPorRememberToken($remember_token) {
        $stmt = Conexion::conectar()->prepare("SELECT * FROM usuarios WHERE remember_token = :remember_token AND remember_expires > NOW()");

        $stmt->bindParam(":remember_token", $remember_token, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetch();
        $stmt->closeCursor();
        return $resultado;
      }
}
?>
