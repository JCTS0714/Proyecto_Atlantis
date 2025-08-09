<?php
class ControladorEvento {

    // Crear evento
    public static function ctrCrearEvento($datos) {
        $tabla = "evento";
        $respuesta = ModeloEvento::mdlCrearEvento($tabla, $datos);
        return $respuesta;
    }

    // Editar evento
    public static function ctrEditarEvento($datos) {
        $tabla = "evento";
        $respuesta = ModeloEvento::mdlEditarEvento($tabla, $datos);
        return $respuesta;
    }

    // Eliminar evento
    public static function ctrEliminarEvento($id) {
        $tabla = "evento";
        $respuesta = ModeloEvento::mdlEliminarEvento($tabla, $id);
        return $respuesta;
    }

    // Mostrar eventos
    public static function ctrMostrarEventos() {
        $tabla = "evento";
        $respuesta = ModeloEvento::mdlMostrarEventos($tabla);
        return $respuesta;
    }

    // Mostrar eventos por reunión
    public static function ctrMostrarEventosPorReunion($reunion_id) {
        $tabla = "evento";
        $respuesta = ModeloEvento::mdlMostrarEventosPorReunion($tabla, $reunion_id);
        return $respuesta;
    }
}
?>
