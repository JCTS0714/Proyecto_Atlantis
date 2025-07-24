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
        if (isset($_GET["idOportunidadEliminar"])) {
            $tabla = "oportunidades";
            $respuesta = ModeloCRM::mdlEliminarOportunidad($tabla, $_GET["idOportunidadEliminar"]);

            if ($respuesta == "ok") {
                echo '
                <script>
                    Swal.fire({
                        icon: "success",
                        title: "¡Oportunidad eliminada correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = "crm";
                        }
                    });
                </script>';
            } else {
                echo '
                <script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al eliminar la oportunidad!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }

    static public function ctrMostrarClientes($item = null, $valor = null) {
        $tabla = "clientes";
        return ModeloCRM::mdlMostrarClientes($tabla, $item, $valor);
    }
}
?>
