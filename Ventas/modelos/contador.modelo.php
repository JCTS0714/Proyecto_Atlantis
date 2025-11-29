<?php

require_once "conexion.php";

class ModeloContador {

  static public function mdlMostrarContador($tabla, $item, $valor) {
    if ($item != null) {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY id DESC");
      $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      } catch (PDOException $e) {
        error_log("mdlMostrarContador ERROR: " . $e->getMessage());
        return [];
      }
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY id DESC");
      try {
        $stmt->execute();
        return $stmt->fetchAll();
      } catch (PDOException $e) {
        error_log("mdlMostrarContador(ALL) ERROR: " . $e->getMessage());
        return [];
      }
    }
    $stmt = null;
  }

  static public function mdlRegistrarContador($tabla, $datos) {
    // Calcular nro automáticamente si no fue provisto
    if (empty($datos['nro'])) {
      try {
        $maxStmt = Conexion::conectar()->prepare("SELECT MAX(CAST(nro AS UNSIGNED)) AS maxn FROM $tabla");
        $maxStmt->execute();
        $row = $maxStmt->fetch(PDO::FETCH_ASSOC);
        $next = ($row && $row['maxn']) ? intval($row['maxn']) + 1 : 1;
        $datos['nro'] = (string)$next;
      } catch (PDOException $e) {
        error_log('mdlRegistrarContador (calc nro) EXCEPTION: ' . $e->getMessage());
        $datos['nro'] = '1';
      }
    }

    $sql = "INSERT INTO $tabla(nro, comercio, nom_contador, titular_tlf, telefono, telefono_actu, link, usuario, contrasena) VALUES (:nro, :comercio, :nom_contador, :titular_tlf, :telefono, :telefono_actu, :link, :usuario, :contrasena)";
    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(':nro', $datos['nro'], PDO::PARAM_STR);
    $stmt->bindParam(':comercio', $datos['comercio'], PDO::PARAM_STR);
    $stmt->bindParam(':nom_contador', $datos['nom_contador'], PDO::PARAM_STR);
    $stmt->bindParam(':titular_tlf', $datos['titular_tlf'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono_actu', $datos['telefono_actu'], PDO::PARAM_STR);
    $stmt->bindParam(':link', $datos['link'], PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $datos['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);

    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlRegistrarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlRegistrarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  static public function mdlEditarContador($tabla, $datos) {
    // No se permite modificar el campo 'nro' en la edición; conservar valor existente
    $sql = "UPDATE $tabla SET comercio = :comercio, nom_contador = :nom_contador, titular_tlf = :titular_tlf, telefono = :telefono, telefono_actu = :telefono_actu, link = :link, usuario = :usuario, contrasena = :contrasena WHERE id = :id";
    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(':comercio', $datos['comercio'], PDO::PARAM_STR);
    $stmt->bindParam(':nom_contador', $datos['nom_contador'], PDO::PARAM_STR);
    $stmt->bindParam(':titular_tlf', $datos['titular_tlf'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono', $datos['telefono'], PDO::PARAM_STR);
    $stmt->bindParam(':telefono_actu', $datos['telefono_actu'], PDO::PARAM_STR);
    $stmt->bindParam(':link', $datos['link'], PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $datos['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':contrasena', $datos['contrasena'], PDO::PARAM_STR);
    $stmt->bindParam(':id', $datos['id'], PDO::PARAM_INT);

    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlEditarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlEditarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

  static public function mdlEliminarContador($tabla, $id) {
    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    try {
      if ($stmt->execute()) {
        return 'ok';
      } else {
        $err = $stmt->errorInfo();
        error_log('mdlEliminarContador ERROR: ' . json_encode($err));
        return 'error';
      }
    } catch (PDOException $e) {
      error_log('mdlEliminarContador EXCEPTION: ' . $e->getMessage());
      return 'error';
    }
  }

}

?>
