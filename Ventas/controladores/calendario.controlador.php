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
    public static function ctrEliminarReunion($id) {
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
}
?>
