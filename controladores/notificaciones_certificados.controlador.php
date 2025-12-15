<?php
require_once __DIR__ . '/../modelos/notificaciones_certificados.modelo.php';

class ControladorNotificacionesCertificados {

  static public function ctrContarEnviosHoy($certificadoId){
    return ModeloNotificacionesCertificados::mdlContarEnviosHoy($certificadoId);
  }

  static public function ctrRegistrarEnvio($certificadoId, $tipo, $usuarioId = null){
    return ModeloNotificacionesCertificados::mdlRegistrarEnvio($certificadoId, $tipo, $usuarioId);
  }

}
