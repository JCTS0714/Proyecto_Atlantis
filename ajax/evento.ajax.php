<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';

require_once "../controladores/evento.controlador.php";
require_once "../modelos/evento.modelo.php";

class AjaxEvento {

    public $id;
    public $datos;

    // Crear evento - solo con título y color
    public function ajaxCrearEvento() {
        $respuesta = ControladorEvento::ctrCrearEvento($this->datos);
        echo json_encode($respuesta);
    }

    // Actualizar evento - solo título y color
    public function ajaxActualizarEvento() {
        $respuesta = ControladorEvento::ctrEditarEvento($this->datos);
        echo json_encode($respuesta);
    }

    // Eliminar evento
    public function ajaxEliminarEvento() {
        $respuesta = ControladorEvento::ctrEliminarEvento($this->id);
        echo json_encode($respuesta);
    }

    // Mostrar eventos - solo título y color
    public function ajaxMostrarEventos() {
        $respuesta = ControladorEvento::ctrMostrarEventos();
        echo json_encode($respuesta);
    }
}

// Crear evento
if(isset($_POST["accion"]) && $_POST["accion"] == "crear_evento") {
    $ajax = new AjaxEvento();
    $ajax->datos = $_POST;
    $ajax->ajaxCrearEvento();
}

// Actualizar evento
if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar_evento") {
    $ajax = new AjaxEvento();
    $ajax->datos = $_POST;
    $ajax->ajaxActualizarEvento();
}

// Eliminar evento
if(isset($_POST["accion"]) && $_POST["accion"] == "eliminar_evento") {
    $ajax = new AjaxEvento();
    $ajax->id = $_POST["id"];
    $ajax->ajaxEliminarEvento();
}

// Mostrar eventos
if(isset($_POST["accion"]) && $_POST["accion"] == "mostrar_eventos") {
    $ajax = new AjaxEvento();
    $ajax->ajaxMostrarEventos();
}