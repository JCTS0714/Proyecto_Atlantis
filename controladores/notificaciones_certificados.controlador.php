<?php
// Intentar incluir el modelo de notificaciones; si no existe, crear un stub
$modelPath = __DIR__ . '/../modelos/notificaciones_certificados.modelo.php';
if (file_exists($modelPath)) {
  require_once $modelPath;
} else {
  error_log("notificaciones_certificados.modelo.php missing - creating fallback stub: $modelPath");
  if (!class_exists('ModeloNotificacionesCertificados')) {
    class ModeloNotificacionesCertificados {
      public static function mdlContarEnviosHoy($certificadoId){
        return 0; // no envíos registrados
      }
      public static function mdlRegistrarEnvio($certificadoId, $tipo, $usuarioId = null){
        return false; // no-op
      }
    }
  }
}

class ControladorNotificacionesCertificados {

  static public function ctrContarEnviosHoy($certificadoId){
    return ModeloNotificacionesCertificados::mdlContarEnviosHoy($certificadoId);
  }

  static public function ctrRegistrarEnvio($certificadoId, $tipo, $usuarioId = null){
    return ModeloNotificacionesCertificados::mdlRegistrarEnvio($certificadoId, $tipo, $usuarioId);
  }

}
