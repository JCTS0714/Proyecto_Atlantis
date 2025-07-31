<?php
class ControladorOportunidad {

    static public function ctrMostrarOportunidades($item = null, $valor = null) {
        $tabla = "oportunidades";
        return ModeloCRM::mdlMostrarOportunidades($tabla, $item, $valor);
    }

    static public function ctrCrearOportunidad() {
        if (isset($_POST["nuevoTitulo"])) {
            $tabla = "oportunidades";

            $datos = array(
                "cliente_id" => $_POST["idCliente"],
                "usuario_id" => $_POST["idUsuario"],
                "titulo" => $_POST["nuevoTitulo"],
                "descripcion" => $_POST["nuevaDescripcion"],
                "valor_estimado" => $_POST["nuevoValorEstimado"],
                "probabilidad" => $_POST["nuevaProbabilidad"],
                "estado" => $_POST["nuevoEstado"],
                "fecha_cierre_estimada" => $_POST["nuevaFechaCierre"]
            );

            $respuesta = ModeloCRM::mdlRegistrarOportunidad($tabla, $datos);

            header('Content-Type: application/json');
            if ($respuesta == "ok") {
                echo json_encode(array(
                    "status" => "success",
                    "message" => "¡La oportunidad ha sido registrada correctamente!"
                ));
            } else {
                echo json_encode(array(
                    "status" => "error",
                    "message" => "¡Error al registrar la oportunidad!"
                ));
            }
        }
    }

static public function ctrActualizarEstadoOportunidad() {
        header('Content-Type: application/json');
        if (isset($_POST["idOportunidad"]) && isset($_POST["nuevoEstado"])) {
            $tabla = "oportunidades";
            $respuesta = ModeloCRM::mdlActualizarEstado($tabla, $_POST["idOportunidad"], $_POST["nuevoEstado"]);

            if ($respuesta == "ok") {
                echo json_encode(['status' => 'success', 'message' => 'Estado actualizado']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el estado']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Parámetros incompletos']);
        }
    }

    static public function ctrEliminarOportunidad() {
        header('Content-Type: application/json');
        if (isset($_POST["id"])) {
            $tabla = "oportunidades";
            $respuesta = ModeloCRM::mdlEliminarOportunidad($tabla, $_POST["id"]);

            if ($respuesta == "ok") {
                echo json_encode([
                    "status" => "success",
                    "message" => "¡Oportunidad eliminada correctamente!"
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "¡Error al eliminar la oportunidad!"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Parámetro id no recibido"
            ]);
        }
    }

    static public function ctrMostrarClientes($item = null, $valor = null) {
        $tabla = "clientes";
        return ModeloCRM::mdlMostrarClientes($tabla, $item, $valor);
    }
}
?>
