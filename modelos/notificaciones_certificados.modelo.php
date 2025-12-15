<?php
require_once "conexion.php";

class ModeloNotificacionesCertificados {

  // Obtiene conteo de envíos hoy para un certificado
  static public function mdlContarEnviosHoy($certificadoId){
    $stmt = Conexion::conectar()->prepare("SELECT COUNT(*) as total FROM notificaciones_certificados WHERE certificado_id = :cid AND DATE(fecha_envio) = CURDATE()");
    $stmt->bindParam(':cid', $certificadoId, PDO::PARAM_INT);
    try{ $stmt->execute(); $r = $stmt->fetch(PDO::FETCH_ASSOC); return intval($r['total'] ?? 0); } catch(PDOException $e){ error_log('mdlContarEnviosHoy ERROR: '.$e->getMessage()); return 0; }
  }

  static public function mdlRegistrarEnvio($certificadoId, $tipo, $usuarioId = null){
    $stmt = Conexion::conectar()->prepare("INSERT INTO notificaciones_certificados (certificado_id, tipo, fecha_envio, usuario_id) VALUES (:cid, :tipo, NOW(), :uid)");
    $stmt->bindParam(':cid', $certificadoId, PDO::PARAM_INT);
    $stmt->bindParam(':tipo', $tipo, PDO::PARAM_INT);
    $stmt->bindParam(':uid', $usuarioId, PDO::PARAM_INT);
    try{ if($stmt->execute()) return 'ok'; else return 'error'; } catch(PDOException $e){ error_log('mdlRegistrarEnvio ERROR: '.$e->getMessage()); return 'error'; }
  }

}
