<?php
require_once "../controladores/calendario.controlador.php";
require_once "../modelos/calendario.modelo.php";

class AjaxCalendario {

    public $id;
    public $datos;

    // Crear reunión
    public function ajaxCrearReunion() {
        $respuesta = ControladorCalendario::ctrCrearReunion($this->datos);
        echo json_encode($respuesta);
    }

    // Actualizar reunión
    public function ajaxActualizarReunion() {
        $respuesta = ControladorCalendario::ctrEditarReunion($this->datos);
        echo json_encode($respuesta);
    }

    // Actualizar solo fecha de reunión
    public function ajaxActualizarFechaReunion() {
        $respuesta = ControladorCalendario::ctrActualizarFechaReunion($this->datos);
        echo json_encode($respuesta);
    }

    // Eliminar reunión
    public function ajaxEliminarReunion() {
        $respuesta = ControladorCalendario::ctrEliminarReunion($this->id);
        echo json_encode($respuesta);
    }

    // Mostrar reuniones
    public function ajaxMostrarReuniones() {
        $respuesta = ControladorCalendario::ctrMostrarReuniones();
        echo json_encode($respuesta);
    }
}

// Crear reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "crear") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxCrearReunion();
}

// Actualizar reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxActualizarReunion();
}

// Eliminar reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "eliminar") {
    $ajax = new AjaxCalendario();
    $ajax->id = $_POST["id"];
    $ajax->ajaxEliminarReunion();
}

// Mostrar reuniones
if(isset($_POST["accion"]) && $_POST["accion"] == "mostrar") {
    $ajax = new AjaxCalendario();
    $ajax->ajaxMostrarReuniones();
}

// Actualizar fecha/hora reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar_fecha") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxActualizarFechaReunion();
}
?>
