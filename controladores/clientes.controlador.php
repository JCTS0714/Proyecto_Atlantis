<?php
class ControladorCliente {

    /**============================
     * FUNCIÓN PRIVADA PARA OBTENER RUTA
     * ============================ */
    private static function obtenerRuta($data) {
        $ruta = isset($data['ruta']) ? $data['ruta'] : 'clientes';
        if (!isset($data['ruta'])) {
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referer, 'prospectos.php') !== false) {
                $ruta = 'prospectos';
            }
        }
        return $ruta;
    }

    /** MÉTODO PARA REGISTRAR UN NUEVO CLIENTE */
    static public function ctrCrearCliente() {
        if (isset($_POST["nuevoNombre"])) {
            $tipo = $_POST["nuevoTipo"];
            $documento = $_POST["nuevoDocumento"];
            
            // Tipos permitidos: DNI, RUC, otros (consistente con edición)
            $allowedTipos = ["DNI", "RUC", "otros"];
            
            // Validar documento según tipo; para 'otros' permitimos cualquier valor
            $documentoValido = true;
            if ($tipo === "DNI") {
                $documentoValido = preg_match('/^[0-9]{8}$/', $documento) === 1;
            } elseif ($tipo === "RUC") {
                $documentoValido = preg_match('/^[0-9]{11}$/', $documento) === 1;
            }
            // Para 'otros' no validamos formato específico

            // Ajustar validaciones para permitir espacios y caracteres comunes en nombre y empresa
            if (
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoNombre"]) &&
                in_array($tipo, $allowedTipos) &&
                $documentoValido &&
                isset($_POST["nuevoCorreo"]) &&
                preg_match('/^[0-9]{9}$/', $_POST["nuevoTelefono"]) &&
                (empty($_POST["nuevoCiudad"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoCiudad"])) &&
                (empty($_POST["nuevoMigracion"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoMigracion"])) &&
                (empty($_POST["nuevoReferencia"]) || preg_match('/^[\p{L}\p{N}\s\.,#\-]+$/u', $_POST["nuevoReferencia"])) &&
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["nuevoFechaContacto"]) &&
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoEmpresa"])
            ) {
                $tabla = "clientes";
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
                    $ruta = self::obtenerRuta($_POST);
                    echo '<script>
                        swal.fire({
                            icon: "success",
                            title: "¡El prospecto ha sido registrado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = window.BASE_URL + "/'.$ruta.'";
                            }
                        });
                    </script>';
                } else {
                    $ruta = self::obtenerRuta($_POST);
                    echo '<script>
                        swal.fire({
                            icon: "error",
                            title: "¡Error al registrar el prospecto!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar",
                            showCloseButton: true
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location = window.BASE_URL + "/'.$ruta.'";
                            }
                        });
                    </script>';
                }
            } else {
                $ruta = self::obtenerRuta($_POST);
                echo '<script>
                    swal.fire({
                        icon: "error",
                        title: "¡Los campos tienen errores o están vacíos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
                        showCloseButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = window.BASE_URL + "/'.$ruta.'";
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
     * NUEVO MÉTODO PARA MOSTRAR CLIENTE POR ID
     * ============================ */
    static public function ctrMostrarClientePorId($id) {
        $tabla = "clientes";
        $respuesta = ModeloCliente::mdlMostrarCliente($tabla, "id", $id);
        return $respuesta;
    }

    /**============================
     * MÉTODO PARA EDITAR CLIENTE
     * ============================ */
    static public function ctrEditarCliente(){
        if(isset($_POST["editarNombre"])){
            $tipo = $_POST["editarTipo"];
            $documento = $_POST["editarDocumento"];

            // Aceptar tipos: DNI, RUC, otros
            $allowedTipos = ["DNI", "RUC", "otros"];

            // Validar documento según tipo; para 'otros' permitimos cualquier valor (o vacío)
            $documentoValido = true;
            if ($tipo === "DNI") {
                $documentoValido = preg_match('/^[0-9]{8}$/', $documento) === 1;
            } elseif ($tipo === "RUC") {
                $documentoValido = preg_match('/^[0-9]{11}$/', $documento) === 1;
            }

            // Validaciones similares a ctrCrearCliente, pero permitiendo 'otros' tipo
            if (
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarNombre"]) &&
                in_array($tipo, $allowedTipos) &&
                $documentoValido &&
                isset($_POST["editarCorreo"]) &&
                preg_match('/^[0-9]{9}$/', $_POST["editarTelefono"]) &&
                (empty($_POST["editarCiudad"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarCiudad"])) &&
                (empty($_POST["editarMigracion"]) || preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarMigracion"])) &&
                (empty($_POST["editarReferencia"]) || preg_match('/^[\p{L}\p{N}\s\.,#\-]+$/u', $_POST["editarReferencia"])) &&
                preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST["editarFechaContacto"]) &&
                preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarEmpresa"]) &&
                (empty($_POST["editarMotivo"]) || is_string($_POST["editarMotivo"]))
            ) {
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
                    "motivo" => $_POST["editarMotivo"],
                    "fecha_contacto" => $_POST["editarFechaContacto"],
                    "empresa" => $_POST["editarEmpresa"],
                    "fecha_creacion" => $_POST["editarFechaCreacion"] ?? null
                );

                $respuesta = ModeloCliente::mdlEditarCliente($tabla, $datos);

                if($respuesta == "ok"){
                    $ruta = self::obtenerRuta($_POST);
                    echo '<script>
                        Swal.fire({
                            icon: "success",
                            title: "¡El cliente ha sido editado correctamente!",
                            showConfirmButton: true,
                            confirmButtonText: "Cerrar"
                        }).then((result) => {
                            if(result.isConfirmed){
                                window.location = window.BASE_URL + "/'.$ruta.'";
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
            } else {
                $ruta = self::obtenerRuta($_POST);
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡Los campos tienen errores o están vacíos!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location = window.BASE_URL + "/'.$ruta.'";
                        }
                    });
                </script>';
            }
        }
    }
    /**============================
     * MÉTODO PARA MOSTRAR CLIENTES PARA OPORTUNIDAD
     * ============================
     */
    static public function ctrMostrarClientesParaOportunidad($searchTerm = null) {
        $tabla = "clientes";
        $respuesta = ModeloCliente::mdlMostrarClientesParaOportunidad($searchTerm);
        return $respuesta;
    }

    //**METODO PARA ELIMINAR CLIENTE */
    static public function ctrEliminarCliente(){
        if(isset($_GET["idClienteEliminar"])){

            // Verificar permisos: solo Administrador puede eliminar clientes
            if($_SESSION["perfil"] != "Administrador"){

              echo '<script>

                Swal.fire({

                  icon: "error",

                  title: "¡No tienes permisos para eliminar clientes!",

                  showConfirmButton: true,

                  confirmButtonText: "Cerrar",

                  showCloseButton: true

                }).then((result)=>{

                  if(result.isConfirmed){

                    window.location = "clientes";

                  }

                });

              </script>';

              return;

            }

            $tabla = "clientes";
            $datos = $_GET["idClienteEliminar"];
            $ruta = isset($_GET['ruta']) ? $_GET['ruta'] : 'clientes';

            // Verificar si el cliente tiene oportunidades asociadas antes de eliminar
            $tieneOportunidades = ModeloCliente::mdlVerificarOportunidades($datos);
            if ($tieneOportunidades) {
                echo '<script>
                    Swal.fire({
                        icon: "error",
                        title: "¡No se puede eliminar el cliente porque tiene oportunidades asociadas! Primero elimine las oportunidades.",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = window.BASE_URL + "/'.$ruta.'";
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
                        title: "¡El cliente ha sido eliminado correctamente!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar"
                    }).then((result) => {
                        if(result.isConfirmed){
                            window.location = window.BASE_URL + "/'.$ruta.'";
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

    /**============================
     * MÉTODO PARA MOSTRAR CLIENTES FILTRADOS
     * ============================ */
    static public function ctrMostrarClientesFiltrados($filtros = []) {
        $tabla = "clientes";
        $respuesta = ModeloCliente::mdlMostrarClientesFiltrados($tabla, $filtros);
        return $respuesta;
    }
}
