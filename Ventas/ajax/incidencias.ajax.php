<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../modelos/conexion.php";
require_once "../modelos/ModeloIncidencias.php";
require_once "../controladores/ControladorIncidencias.php";

class AjaxIncidencias {

    public function ajaxGenerarCorrelativo() {
        $respuesta = ControladorIncidencias::ctrGenerarCorrelativo();
        echo json_encode($respuesta);
    }

    public function ajaxBuscarClientes() {
        $term = isset($_GET['term']) ? $_GET['term'] : '';
        $respuesta = ControladorIncidencias::ctrBuscarClientes($term);
        echo json_encode($respuesta);
    }

    public function ajaxCrearIncidencia() {
        if (isset($_POST["nuevoNombreIncidencia"])) {
            $respuesta = ControladorIncidencias::ctrCrearIncidencia();
            echo json_encode($respuesta);
        }
    }

    public function ajaxMostrarIncidencias() {
        $respuesta = ControladorIncidencias::ctrMostrarIncidencias();
        echo json_encode($respuesta);
    }

    public function ajaxEditarIncidencia() {
        if (isset($_POST["editarNombreIncidencia"])) {
            $respuesta = ControladorIncidencias::ctrEditarIncidencia();
            echo json_encode($respuesta);
        }
    }

    public function ajaxEliminarIncidencia() {
        if (isset($_POST["idIncidenciaEliminar"])) {
            $respuesta = ControladorIncidencias::ctrEliminarIncidencia();
            echo json_encode($respuesta);
        }
    }

    public function ajaxObtenerIncidencia() {
        if (isset($_GET["id"])) {
            $respuesta = ControladorIncidencias::ctrMostrarIncidencias("id", $_GET["id"]);
            echo json_encode($respuesta);
        }
    }
}

// Ejecutar acciones según el parámetro 'action'
if (isset($_GET['action'])) {
    $ajax = new AjaxIncidencias();

    switch ($_GET['action']) {
        case 'generarCorrelativo':
            $ajax->ajaxGenerarCorrelativo();
            break;
        case 'buscarClientes':
            $ajax->ajaxBuscarClientes();
            break;
        case 'mostrarIncidencias':
            $ajax->ajaxMostrarIncidencias();
            break;
        case 'obtenerIncidencia':
            $ajax->ajaxObtenerIncidencia();
            break;
        default:
            echo json_encode(array("success" => false, "message" => "Acción no válida"));
            break;
    }
} elseif (isset($_POST["nuevoNombreIncidencia"])) {
    $ajax = new AjaxIncidencias();
    $ajax->ajaxCrearIncidencia();
} elseif (isset($_POST["editarNombreIncidencia"])) {
    $ajax = new AjaxIncidencias();
    $ajax->ajaxEditarIncidencia();
    exit; // Evitar redirección
} elseif (isset($_POST["idIncidenciaEliminar"])) {
    $ajax = new AjaxIncidencias();
    $ajax->ajaxEliminarIncidencia();
}