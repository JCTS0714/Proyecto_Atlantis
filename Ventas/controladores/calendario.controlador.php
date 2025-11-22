<?php
class ControladorCalendario {

    // Método para mostrar la vista del calendario
    public function ctrMostrarCalendario() {
        include "Vistas/modulos/calendario.php";
    }

    // Método para crear una nueva reunión
    public static function ctrCrearReunion($datos) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlCrearReunion($tabla, $datos);
        return $respuesta;
    }

    // Método para editar una reunión existente
    public static function ctrEditarReunion($datos) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlEditarReunion($tabla, $datos);
        return $respuesta;
    }

    // Método para actualizar solo fecha y horas de una reunión
    public static function ctrActualizarFechaReunion($datos) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlActualizarFechaReunion($tabla, $datos);
        return $respuesta;
    }

    // Método para eliminar una reunión
    /**
     * Elimina una reunión por su ID.
     * 
     * Verifica que la sesión esté iniciada para acceder a $_SESSION.
     * Solo permite eliminar si el perfil no es "Vendedor".
     * 
     * @param int $id ID de la reunión a eliminar.
     * @return string Resultado de la operación ("ok" o "error").
     */
    public static function ctrEliminarReunion($id) {

        // Verificar que la sesión esté iniciada
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Verificar permisos: solo Administrador puede eliminar reuniones
        if(!isset($_SESSION["perfil"]) || $_SESSION["perfil"] == "Vendedor"){

          echo '<script>

            Swal.fire({

              icon: "error",

              title: "¡No tienes permisos para eliminar reuniones!",

              showConfirmButton: true,

              confirmButtonText: "Cerrar",

              showCloseButton: true

            }).then((result)=>{

              if(result.isConfirmed){

                window.location = "calendario";

              }

            });

          </script>';

          return;

        }

        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlEliminarReunion($tabla, $id);
        return $respuesta;
    }

    // Método para mostrar reuniones
    public static function ctrMostrarReuniones($item = null, $valor = null) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlMostrarReuniones($tabla, $item, $valor);
        return $respuesta;
    }

    // Método para obtener reuniones próximas a notificar
    public static function ctrObtenerReunionesParaNotificar($usuario_id) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlObtenerReunionesParaNotificar($tabla, $usuario_id);
        return $respuesta;
    }

    // Método para actualizar última notificación
    public static function ctrActualizarUltimaNotificacion($id, $fecha) {
        $tabla = "reuniones";
        $respuesta = ModeloCalendario::mdlActualizarUltimaNotificacion($tabla, $id, $fecha);
        return $respuesta;
    }

    // Método para actualizar última notificación en lote
    public static function ctrActualizarUltimaNotificacionLote($ids, $fecha) {
      $tabla = "reuniones";
      $respuesta = ModeloCalendario::mdlActualizarUltimaNotificacionLote($tabla, $ids, $fecha);
      return $respuesta;
    }

    // Listar reuniones pasadas (no archivadas)
    public static function ctrListarReunionesPasadas($limit = 100) {
      $tabla = "reuniones";
      $respuesta = ModeloCalendario::mdlListarReunionesPasadas($tabla, $limit);
      return $respuesta;
    }

    // Archivar / marcar concretado una reunión
    public static function ctrArchivarReunion($id, $estado = null, $usuario_id = null) {
      $tabla = "reuniones";
      $respuesta = ModeloCalendario::mdlArchivarReunion($tabla, $id, $estado, $usuario_id);
      return $respuesta;
    }

    // Obtener reuniones archivadas
    public static function ctrObtenerReunionesArchivadas($limit = 500) {
      $tabla = "reuniones";
      $respuesta = ModeloCalendario::mdlObtenerReunionesArchivadas($tabla, $limit);
      return $respuesta;
    }
}
?>
             