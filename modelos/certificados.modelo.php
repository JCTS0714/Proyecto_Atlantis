<?php
require_once "conexion.php";

class ModeloCertificados{

  static public function mdlMostrarCertificados($tabla, $item = null, $valor = null){
    if($item != null){
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY fecha_creacion DESC");
      $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
      try{ $stmt->execute(); } catch(PDOException $e){ error_log('mdlMostrarCertificados SELECT ERROR: '.$e->getMessage()); return []; }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
      $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha_creacion DESC");
      try{ $stmt->execute(); } catch(PDOException $e){ error_log('mdlMostrarCertificados(ALL) SELECT ERROR: '.$e->getMessage()); return []; }
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
  }

  static public function mdlCrearCertificado($tabla, $datos){
    $sql = "INSERT INTO $tabla (nombre, ruc, usuario, clave, fecha_creacion, fecha_vencimiento, estado, observacion, tipo, creado_por, creado_en) VALUES (:nombre,:ruc,:usuario,:clave,:fecha_creacion,:fecha_vencimiento,:estado,:observacion,:tipo,:creado_por,NOW())";
    $stmt = Conexion::conectar()->prepare($sql);
    $stmt->bindParam(':nombre', $datos['nombre'], PDO::PARAM_STR);
    $stmt->bindParam(':ruc', $datos['ruc'], PDO::PARAM_STR);
    $stmt->bindParam(':usuario', $datos['usuario'], PDO::PARAM_STR);
    $stmt->bindParam(':clave', $datos['clave'], PDO::PARAM_STR);
    $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
    $stmt->bindParam(':fecha_vencimiento', $datos['fecha_vencimiento'], PDO::PARAM_STR);
    $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
    $stmt->bindParam(':observacion', $datos['observacion'], PDO::PARAM_STR);
    $stmt->bindParam(':tipo', $datos['tipo'], PDO::PARAM_STR);
    $stmt->bindParam(':creado_por', $datos['creado_por'], PDO::PARAM_INT);
    try{ if($stmt->execute()) return 'ok'; else { error_log('mdlCrearCertificado ERROR: '.json_encode($stmt->errorInfo())); return 'error'; } } catch(PDOException $e){ error_log('mdlCrearCertificado EXCEPTION: '.$e->getMessage()); return 'error'; }
  }

  static public function mdlEditarCertificado($tabla, $datos){
    $sets = [];
    $params = [];
    foreach(['nombre','ruc','usuario','clave','fecha_creacion','fecha_vencimiento','estado','observacion','tipo'] as $f){ if(isset($datos[$f])){ $sets[] = "$f = :$f"; $params[":$f"] = $datos[$f]; } }
    if(empty($sets)) return 'error';
    $params[':id'] = $datos['id'];
    $sql = "UPDATE $tabla SET ".implode(',', $sets)." WHERE id = :id";
    $stmt = Conexion::conectar()->prepare($sql);
    try{ if($stmt->execute($params)) return 'ok'; else { error_log('mdlEditarCertificado ERROR: '.json_encode($stmt->errorInfo())); return 'error'; } } catch(PDOException $e){ error_log('mdlEditarCertificado EXCEPTION: '.$e->getMessage()); return 'error'; }
  }

  static public function mdlEliminarCertificado($tabla, $id){
    $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    try{ if($stmt->execute()) return 'ok'; else { error_log('mdlEliminarCertificado ERROR: '.json_encode($stmt->errorInfo())); return 'error'; } } catch(PDOException $e){ error_log('mdlEliminarCertificado EXCEPTION: '.$e->getMessage()); return 'error'; }
  }

}
