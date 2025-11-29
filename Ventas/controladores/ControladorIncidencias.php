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

                if ($respuesta === "ok" || (is_array($respuesta) && isset($respuesta['status']) && $respuesta['status'] === 'ok')) {
                    $insertedId = is_array($respuesta) && isset($respuesta['id']) ? $respuesta['id'] : null;
                    return array("status" => "success", "message" => "Incidencia registrada correctamente.", "id" => $insertedId);
                } else {
                // Modelo puede devolver detalles de error
                if (is_array($respuesta)) {
                    if (isset($respuesta['exception'])) {
                        $msg = 'DB Exception: ' . $respuesta['exception'];
                    } elseif (isset($respuesta['db_error'])) {
                        $msg = 'DB Error: ' . json_encode($respuesta['db_error']);
                    } else {
                        $msg = 'Error desconocido en la base de datos.';
                    }
                    return array("status" => "error", "message" => $msg);
                }

                return array("status" => "error", "message" => "Error al registrar la incidencia.");
            }
        }
    }

    static public function ctrMostrarIncidencias($item = null, $valor = null) {
        return ModeloIncidencias::mdlMostrarIncidencias($item, $valor);
    }

    static public function ctrEditarIncidencia($datos = null) {
        // Soporta llamada directa (desde AJAX que pasa $datos) o lectura desde POST tradicional
        if ($datos === null) {
            // flujo antiguo (leer desde $_POST)
            if (!isset($_POST["editarNombreIncidencia"])) {
                return array("success" => false, "message" => "No hay datos para actualizar.");
            }

            if (empty($_POST["editarNombreIncidencia"]) || empty($_POST["editarIdClienteSeleccionado"]) || empty($_POST["editarFecha"]) || empty($_POST["editarPrioridad"])) {
                return array("success" => false, "message" => "Todos los campos obligatorios deben ser completados.");
            }

            if (!isset($_SESSION["id"])) {
                return array("success" => false, "message" => "Usuario no autenticado.");
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
        } else {
            // $datos fue pasado por el AJAX (ajax/backlog.ajax.php)
            // Validar campos mínimos
            if (empty($datos["nombre_incidencia"]) || empty($datos["cliente_id"]) || empty($datos["fecha"]) || empty($datos["prioridad"])) {
                return array("success" => false, "message" => "Todos los campos obligatorios deben ser completados.");
            }
            // Asegurar usuario_id
            if (!isset($datos["usuario_id"]) || empty($datos["usuario_id"])) {
                if (isset($_SESSION["id"])) {
                    $datos["usuario_id"] = $_SESSION["id"];
                } else {
                    return array("success" => false, "message" => "Usuario no autenticado.");
                }
            }
        }

        $respuesta = ModeloIncidencias::mdlEditarIncidencia($datos);

        if ($respuesta == "ok") {
            return array("success" => true, "message" => "Incidencia actualizada correctamente.");
        } else {
            return array("success" => false, "message" => "Error al actualizar la incidencia.");
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
