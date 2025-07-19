<?php
class ControladorCliente {

    /** MÉTODO PARA REGISTRAR UN NUEVO CLIENTE */
    static public function ctrCrearCliente() {
        if (isset($_POST["nuevoNombre"])) {
            if (
                preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]+$/', $_POST["nuevoNombre"]) &&
                in_array($_POST["nuevoTipo"], ["persona", "empresa"]) &&
                preg_match('/^[0-9]+$/', $_POST["nuevoDocumento"]) &&
                filter_var($_POST["nuevoCorreo"], FILTER_VALIDATE_EMAIL) &&
                preg_match('/^[0-9]+$/', $_POST["nuevoTelefono"]) &&
                preg_match('/^[\p{L}\p{N}\s\.,#\-]+$/u', $_POST["nuevoDireccion"]) &&
                in_array($_POST["nuevaClasificacion"], ["nuevo", "recurrente", "vip"]) &&
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["nuevaFechaCreacion"])
            ) {
                $tabla = "clientes";
                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "tipo" => $_POST["nuevoTipo"],
                    "documento" => $_POST["nuevoDocumento"],
                    "telefono" => $_POST["nuevoTelefono"],
                    "correo" => $_POST["nuevoCorreo"],
                    "direccion" => $_POST["nuevoDireccion"],
                    "clasificacion" => $_POST["nuevaClasificacion"],
                    "fecha_creacion" => $_POST["nuevaFechaCreacion"]
                );

                $respuesta = ModeloCliente::mdlRegistrarCliente($tabla, $datos);

                if ($respuesta == "ok") {
                    echo '<script>
                        swal.fire({
                            icon: "success",
                            title: "¡El cliente ha sido registrado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.value) {
                                window.location = "clientes";
                            }
                        });
                    </script>';
                } else {
                    echo '<script>
                        swal.fire({
                            icon: "error",
                            title: "¡Error al registrar el Cliente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.value) {
                                window.location = "clientes";
                            }
                        });
                    </script>';
                }
            } else {
                echo '<script>
                    swal.fire({
                        icon: "error",
                        title: "¡Los campos tienen errores o están vacíos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true
                    }).then((result) => {
                        if (result.value) {
                            window.location = "clientes";
                        }
                    });
                </script>';
            }
        }
    }

    /**============================
     * MÉTODO PARA MOSTRAR CLIENTE
     * ============================ */
    static public function ctrMostrarCliente($item, $valor) {
        $tabla = "clientes";
        $respuesta = ModeloCliente::MdlMostrarCliente($tabla, $item, $valor);
        return $respuesta;
    }

    /**============================
     * MÉTODO PARA EDITAR CLIENTE
     * ============================ */
    static public function ctrEditarCliente(){
        if(isset($_POST["editarNombre"])){
            $tabla = "clientes";
            $datos = array(
                "id" => $_POST["idCliente"],
                "nombre" => $_POST["editarNombre"],
                "tipo" => $_POST["editarTipo"],
                "documento" => $_POST["editarDocumento"],
                "telefono" => $_POST["editarTelefono"],
                "correo" => $_POST["editarCorreo"],
                "direccion" => $_POST["editarDireccion"],
                "clasificacion" => $_POST["editarClasificacion"],
                "fecha_creacion" => $_POST["editarFechaCreacion"]
            );
            $respuesta = ModeloCliente::mdlEditarCliente($tabla, $datos);

            if($respuesta == "ok"){
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡El cliente ha sido editado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "clientes";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al editar el cliente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }

    //**METODO PARA ELIMINAR CLIENTE */
    static public function ctrEliminarCliente(){
        if(isset($_GET["idClienteEliminar"])){
            $tabla = "clientes";
            $datos = $_GET["idClienteEliminar"];
            $respuesta = ModeloCliente::mdlEliminarCliente($tabla, $datos);
            if($respuesta == "ok"){
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡El cliente ha sido eliminado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "clientes";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al eliminar el cliente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }
}
