<?php
class ControladorProspectos {

    /** MÉTODO PARA MOSTRAR PROSPECTOS */
    static public function ctrMostrarProspectos($item, $valor) {
        $tabla = "clientes";
        $itemEstado = "estado";
        $valorEstado = 0; // Prospectos
        $respuesta = ModeloCliente::MdlMostrarCliente($tabla, $item, $valor);
        // Filtrar solo prospectos
        $prospectos = array_filter($respuesta, function($cliente) use ($itemEstado, $valorEstado) {
            return isset($cliente[$itemEstado]) && $cliente[$itemEstado] == $valorEstado;
        });
        return $prospectos;
    }

    /** MÉTODO PARA REGISTRAR UN NUEVO PROSPECTO */
    static public function ctrCrearProspecto() {
        if (isset($_POST["nuevoNombre"])) {
            $tipo = $_POST["nuevoTipo"];
            $documento = $_POST["nuevoDocumento"];
            $documentoValido = false;

            if ($tipo === "DNI" && preg_match('/^[0-9]{8}$/', $documento)) {
                $documentoValido = true;
            } elseif ($tipo === "RUC" && preg_match('/^[0-9]{11}$/', $documento)) {
                $documentoValido = true;
            } elseif ($tipo === "otros" && preg_match('/^[0-9]{8}$/', $documento)) {
                $documentoValido = true;
            }

            $telefonoLimpio = preg_replace('/\s+/', '', $_POST["nuevoTelefono"]);

            if (
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoNombre"]) &&
                in_array($tipo, ["DNI", "RUC", "otros"]) &&
                $documentoValido &&
                isset($_POST["nuevoCorreo"]) &&
                preg_match('/^\d{1,11}$/', $telefonoLimpio) &&
                (empty($_POST["nuevoCiudad"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoCiudad"])) &&
                (empty($_POST["nuevoMigracion"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoMigracion"])) &&
                (empty($_POST["nuevoReferencia"]) || preg_match('/^[\p{L}\p{N}\s\.,#\-]+$/u', $_POST["nuevoReferencia"])) &&
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["nuevoFechaContacto"]) &&
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoEmpresa"])
            ) {
                $tabla = "clientes";

                // (Se omitió la validación por documento: solo se validará por teléfono)

                // Verificar teléfono existente (normalizando)
                $telefonoParaBuscar = preg_replace('/\s+/', '', $_POST["nuevoTelefono"]);
                if (!empty($telefonoParaBuscar)) {
                        $existeTelefono = ModeloCliente::mdlBuscarPorTelefono($telefonoParaBuscar);
                        if (!empty($existeTelefono)) {
                            $ruta = isset($_POST['ruta']) ? $_POST['ruta'] : 'prospectos';
                            echo '<script>
                                swal.fire({
                                    icon: "error",
                                    title: "Ya existe un cliente con ese numero de telefono",
                                    showConfirmButton: true,
                                    confirmButtonText: "Cerrar"
                                }).then((result) => {
                                    if (result.value) {
                                        window.location = "'.$ruta.'";
                                    }
                                });
                            </script>';
                            return;
                        }
                }

                $datos = array(
                    "nombre" => $_POST["nuevoNombre"],
                    "tipo" => $_POST["nuevoTipo"],
                    "documento" => $_POST["nuevoDocumento"],
                    "telefono" => $_POST["nuevoTelefono"],
                    "correo" => $_POST["nuevoCorreo"],
                    "ciudad" => $_POST["nuevoCiudad"],
                    "migracion" => $_POST["nuevoMigracion"],
                    "referencia" => $_POST["nuevoReferencia"],
                    "fecha_contacto" => $_POST["nuevoFechaContacto"],
                    "empresa" => $_POST["nuevoEmpresa"],
                    "estado" => 0
                );

                $respuesta = ModeloCliente::mdlRegistrarCliente($tabla, $datos);

                if ($respuesta == "ok") {
                    $ruta = isset($_POST['ruta']) ? $_POST['ruta'] : 'prospectos';
                    echo '<script>
                        swal.fire({
                            icon: "success",
                            title: "¡El prospecto ha sido registrado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.value) {
                                window.location = "'.$ruta.'";
                            }
                        });
                    </script>';
                } else {
                    $ruta = isset($_POST['ruta']) ? $_POST['ruta'] : 'prospectos';
                    echo '<script>
                        swal.fire({
                            icon: "error",
                            title: "¡Error al registrar el prospecto!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.value) {
                                window.location = "'.$ruta.'";
                            }
                        });
                    </script>';
                }
            } else {
                $ruta = isset($_POST['ruta']) ? $_POST['ruta'] : 'prospectos';
                echo '<script>
                    swal.fire({
                        icon: "error",
                        title: "¡Los campos tienen errores o están vacíos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true
                    }).then((result) => {
                        if (result.value) {
                            window.location = "'.$ruta.'";
                        }
                    });
                </script>';
            }
        }
    }

    /** MÉTODO PARA EDITAR PROSPECTO */
    static public function ctrEditarProspecto(){
        if(isset($_POST["editarNombre"])){
            $tabla = "clientes";
            $datos = array(
                "id" => $_POST["idCliente"],
                "nombre" => $_POST["editarNombre"],
                "tipo" => $_POST["editarTipo"],
                "documento" => $_POST["editarDocumento"],
                "telefono" => $_POST["editarTelefono"],
                "correo" => $_POST["editarCorreo"],
                "ciudad" => $_POST["editarCiudad"],
                "migracion" => $_POST["editarMigracion"],
                "referencia" => $_POST["editarReferencia"],
                "fecha_contacto" => $_POST["editarFechaContacto"],
                "empresa" => $_POST["editarEmpresa"],
                "fecha_creacion" => $_POST["editarFechaCreacion"],
                "estado" => 0
            );
            $telefonoEditarLimpio = preg_replace('/\s+/', '', $_POST["editarTelefono"]);
            if (!preg_match('/^\d{1,11}$/', $telefonoEditarLimpio)) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡El teléfono debe contener solo dígitos y un máximo de 11!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
                return;
            }
            $respuesta = ModeloCliente::mdlEditarCliente($tabla, $datos);

            if($respuesta == "ok"){
                $ruta = isset($_POST['ruta']) ? $_POST['ruta'] : 'prospectos';
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡El prospecto ha sido editado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "'.$ruta.'";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al editar el prospecto!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }

    /** MÉTODO PARA ELIMINAR PROSPECTO */
    static public function ctrEliminarProspecto(){
        if(isset($_GET["idClienteEliminar"])){

            // Verificar permisos: solo Administrador puede eliminar prospectos
            if($_SESSION["perfil"] == "Vendedor"){
                $ruta = isset($_GET['ruta']) ? $_GET['ruta'] : 'prospectos';
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡No tienes permisos para eliminar prospectos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "'.$ruta.'";
                        }
                    });
                </script>';
                return;
            }

            $tabla = "clientes";
            $datos = $_GET["idClienteEliminar"];

            // Forzar redirección a prospectos
            $ruta = isset($_GET['ruta']) ? $_GET['ruta'] : 'prospectos';

            // Verificar si el prospecto tiene oportunidades asociadas antes de eliminar
            $tieneOportunidades = ModeloCliente::mdlVerificarOportunidades($datos);
            if ($tieneOportunidades) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡No se puede eliminar el prospecto porque tiene oportunidades asociadas! Primero elimine las oportunidades.",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "'.$ruta.'";
                        }
                    });
                </script>';
                return;
            }

            $respuesta = ModeloCliente::mdlEliminarCliente($tabla, $datos);
            if($respuesta == "ok"){
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "¡El prospecto ha sido eliminado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = "'.$ruta.'";
                        }
                    });
                </script>';
            } else {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Error al eliminar el prospecto!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    });
                </script>';
            }
        }
    }
}
