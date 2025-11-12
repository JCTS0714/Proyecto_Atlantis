<?php

require_once "../controladores/ControladorIncidencias.php";
require_once "../modelos/incidencias.modelo.php";

class AjaxBacklog {

    // Mostrar todas las incidencias para el backlog
    public function ajaxMostrarIncidencias() {
        $respuesta = ControladorIncidencias::ctrMostrarIncidencias();
        echo json_encode(array("success" => true, "incidencias" => $respuesta));
    }

    // Obtener detalle de una incidencia específica
    public function ajaxObtenerDetalleIncidencia() {
        if (isset($_GET['id'])) {
            $idIncidencia = $_GET['id'];
            $respuesta = ControladorIncidencias::ctrMostrarIncidencia($idIncidencia);
            if (!empty($respuesta)) {
                echo json_encode(array("success" => true, "incidencia" => $respuesta[0]));
            } else {
                echo json_encode(array("success" => false, "message" => "Incidencia no encontrada"));
            }
        }
    }

    // Actualizar la columna del backlog de una incidencia
    public function ajaxActualizarColumna() {
        if (isset($_POST['idIncidencia']) && isset($_POST['columna'])) {
            $idIncidencia = $_POST['idIncidencia'];
            $columna = $_POST['columna'];

            $respuesta = ControladorIncidencias::ctrActualizarColumnaBacklog($idIncidencia, $columna);
            echo json_encode($respuesta);
        }
    }

    // Actualizar incidencia completa
    public function ajaxActualizarIncidencia() {
        if (isset($_POST['id'])) {
            $datos = array(
                "id" => $_POST['id'],
                "nombre_incidencia" => $_POST['nombre_incidencia'],
                "cliente_id" => $_POST['cliente_id'],
                "fecha" => $_POST['fecha'],
                "prioridad" => $_POST['prioridad'],
                "observaciones" => $_POST['observaciones']
            );

            $respuesta = ControladorIncidencias::ctrEditarIncidencia($datos);
            echo json_encode($respuesta);
        }
    }

    // Eliminar incidencia
    public function ajaxEliminarIncidencia() {
        if (isset($_POST['idIncidencia'])) {
            $idIncidencia = $_POST['idIncidencia'];
            $respuesta = ControladorIncidencias::ctrBorrarIncidencia($idIncidencia);
            echo json_encode($respuesta);
        }
    }

    // Crear nueva incidencia desde backlog
    public function ajaxCrearIncidencia() {
        if (isset($_POST["nuevoNombreIncidencia"])) {
            $respuesta = ControladorIncidencias::ctrCrearIncidencia();
            echo json_encode($respuesta);
        }
    }
}

// Manejar las acciones AJAX
if (isset($_GET['action'])) {
    $backlog = new AjaxBacklog();

    switch ($_GET['action']) {
        case 'mostrarIncidencias':
            $backlog->ajaxMostrarIncidencias();
            break;
        case 'obtenerDetalleIncidencia':
            $backlog->ajaxObtenerDetalleIncidencia();
            break;
        case 'actualizarColumna':
            $backlog->ajaxActualizarColumna();
            break;
        case 'actualizarIncidencia':
            $backlog->ajaxActualizarIncidencia();
            break;
        case 'eliminarIncidencia':
            $backlog->ajaxEliminarIncidencia();
            break;
        case 'crearIncidencia':
            $backlog->ajaxCrearIncidencia();
            break;
        default:
            echo json_encode(array("success" => false, "message" => "Acción no válida"));
            break;
    }
} else {
    echo json_encode(array("success" => false, "message" => "No se especificó una acción"));
}