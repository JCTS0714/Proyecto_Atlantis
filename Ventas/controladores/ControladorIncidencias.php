<?php
require_once __DIR__ . "/../modelos/ModeloIncidencias.php";

class ControladorIncidencias {

    static public function ctrGenerarCorrelativo() {
        $correlativo = ModeloIncidencias::mdlGenerarCorrelativo();
        return array("success" => true, "correlativo" => $correlativo);
    }

    static public function ctrBuscarClientes($term) {
        $clientes = ModeloIncidencias::mdlBuscarClientes($term);
        $result = array();
        foreach ($clientes as $cliente) {
            $result[] = array(
                "label" => $cliente["nombre"],
                "value" => $cliente["id"]
            );
        }
        return $result;
    }

    static public function ctrCrearIncidencia() {
        if (isset($_POST["nuevoNombreIncidencia"])) {
            // Validar campos requeridos
            if (empty($_POST["nuevoNombreIncidencia"]) || empty($_POST["idClienteSeleccionado"]) || empty($_POST["nuevaFecha"]) || empty($_POST["nuevaPrioridad"])) {
                return array("status" => "error", "message" => "Todos los campos obligatorios deben ser completados.");
            }

            // Obtener el ID del usuario actual (asumiendo que está en la sesión)
            if (!isset($_SESSION["id"])) {
                return array("status" => "error", "message" => "Usuario no autenticado.");
            }

            // Generar correlativo si no está presente
            $correlativo = isset($_POST["nuevoCorrelativo"]) && !empty($_POST["nuevoCorrelativo"]) ? $_POST["nuevoCorrelativo"] : self::ctrGenerarCorrelativo()["correlativo"];

            $datos = array(
                "correlativo" => $correlativo,
                "nombre_incidencia" => $_POST["nuevoNombreIncidencia"],
                "cliente_id" => $_POST["idClienteSeleccionado"],
                "usuario_id" => $_SESSION["id"],
                "fecha" => $_POST["nuevaFecha"],
                "prioridad" => $_POST["nuevaPrioridad"],
                "observaciones" => isset($_POST["nuevaObservaciones"]) ? $_POST["nuevaObservaciones"] : ""
            );

            $respuesta = ModeloIncidencias::mdlCrearIncidencia($datos);

            if ($respuesta == "ok") {
                return array("status" => "success", "message" => "Incidencia registrada correctamente.");
            } else {
                return array("status" => "error", "message" => "Error al registrar la incidencia.");
            }
        }
    }

    static public function ctrMostrarIncidencias($item = null, $valor = null) {
        return ModeloIncidencias::mdlMostrarIncidencias($item, $valor);
    }

    static public function ctrEditarIncidencia() {
        if (isset($_POST["editarNombreIncidencia"])) {
            // Validar campos requeridos
            if (empty($_POST["editarNombreIncidencia"]) || empty($_POST["editarIdClienteSeleccionado"]) || empty($_POST["editarFecha"]) || empty($_POST["editarPrioridad"])) {
                return array("status" => "error", "message" => "Todos los campos obligatorios deben ser completados.");
            }

            // Obtener el ID del usuario actual (asumiendo que está en la sesión)
            if (!isset($_SESSION["id"])) {
                return array("status" => "error", "message" => "Usuario no autenticado.");
            }

            $datos = array(
                "id" => $_POST["idIncidencia"],
                "nombre_incidencia" => $_POST["editarNombreIncidencia"],
                "cliente_id" => $_POST["editarIdClienteSeleccionado"],
                "usuario_id" => $_SESSION["id"],
                "fecha" => $_POST["editarFecha"],
                "prioridad" => $_POST["editarPrioridad"],
                "observaciones" => isset($_POST["editarObservaciones"]) ? $_POST["editarObservaciones"] : ""
            );

            $respuesta = ModeloIncidencias::mdlEditarIncidencia($datos);

            if ($respuesta == "ok") {
                return array("status" => "success", "message" => "Incidencia actualizada correctamente.");
            } else {
                return array("status" => "error", "message" => "Error al actualizar la incidencia.");
            }
        }
    }

    static public function ctrEliminarIncidencia() {
        if (isset($_POST["idIncidenciaEliminar"])) {
            $respuesta = ModeloIncidencias::mdlEliminarIncidencia($_POST["idIncidenciaEliminar"]);

            if ($respuesta == "ok") {
                return array("status" => "success", "message" => "Incidencia eliminada correctamente.");
            } else {
                return array("status" => "error", "message" => "Error al eliminar la incidencia.");
            }
        }
    }

    static public function ctrMostrarIncidencia($idIncidencia) {
        return ModeloIncidencias::mdlMostrarIncidencias("id", $idIncidencia);
    }

    static public function ctrActualizarColumnaBacklog($idIncidencia, $columna) {
        $respuesta = ModeloIncidencias::mdlActualizarColumnaBacklog($idIncidencia, $columna);

        if ($respuesta == "ok") {
            return array("success" => true, "message" => "Columna actualizada correctamente.");
        } else {
            return array("success" => false, "message" => "Error al actualizar la columna.");
        }
    }

    static public function ctrBorrarIncidencia($idIncidencia) {
        $respuesta = ModeloIncidencias::mdlEliminarIncidencia($idIncidencia);

        if ($respuesta == "ok") {
            return array("success" => true, "message" => "Incidencia eliminada correctamente.");
        } else {
            return array("success" => false, "message" => "Error al eliminar la incidencia.");
        }
    }
}
?>
