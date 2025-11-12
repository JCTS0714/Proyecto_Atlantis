<?php

class ControladorProveedores{

  /*=============================================
  REGISTRO DE PROVEEDOR
  =============================================*/
  static public function ctrCrearProveedor(){

    if(isset($_POST["nuevoRazonsocial"])){

      if(preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoRazonsocial"])){

        $tabla = "proveedor";

        $datos = array(
          "razon_social"   => $_POST["nuevoRazonsocial"],
          "ruc"            => $_POST["nuevoRuc"],
          "direccion"      => $_POST["nuevoDireccion"],
          "telefono"       => $_POST["nuevoTelefono"],
          "tipo_proveedor" => $_POST["nuevoTipo_proveedor"],
          "id_usuarios"    => $_SESSION["id"] 
        );

        $respuesta = ModeloProveedores::mdlIngresarProveedor($tabla, $datos);

        if($respuesta == "ok"){

          echo '<script>
            swal.fire({
              icon: "success",
              title: "¡El proveedor ha sido registrado correctamente!",
              showConfirmButton: true,
              confirmButtonText: "Cerrar",
              showCloseButton: true
            }).then((result) => {
              if(result.value){
                window.location = "proveedor"; // corregido si tu archivo se llama proveedor.php
              }
            });
          </script>';

        }

      } else {

        echo '<script>
          swal.fire({
            icon: "error",
            title: "¡El campo Razón Social no puede contener caracteres especiales!",
            showConfirmButton: true,
            confirmButtonText: "Cerrar",
            showCloseButton: true
          }).then((result) => {
            if(result.value){
              window.location = "proveedor";
            }
          });
        </script>';

      }

    }

  }

  /*=============================================
  MOSTRAR PROVEEDORES
  =============================================*/
  static public function ctrMostrarProveedores($item, $valor){

    $tabla = "proveedor";

    $respuesta = ModeloProveedores::mdlMostrarProveedores($tabla, $item, $valor);

    return $respuesta;
  }
  static public function ctrEditarProveedor() {
    if (isset($_POST["editarIdProveedor"])) {
        $tabla = "proveedor";
        $datos = array(
            "id" => $_POST["editarIdProveedor"],
            "razon_social" => $_POST["editarRazonsocial"],
            "ruc" => $_POST["editarRuc"],
            "direccion" => $_POST["editarDireccion"],
            "telefono" => $_POST["editarTelefono"],
            "tipo_proveedor" => $_POST["editarTipoProveedor"]
        );
        $respuesta = ModeloProveedores::mdlEditarProveedor($tabla, $datos);
        if ($respuesta == "ok") {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "¡El proveedor ha sido editado correctamente!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then((result) => {
                    if(result.isConfirmed){
                        window.location = "proveedor";
                    }
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "¡Error al editar el proveedor!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                });
            </script>';
        }
    }
}

static public function ctrEliminarProveedor() {
    if (isset($_GET["idProveedorEliminar"])) {

        // Verificar permisos: solo Administrador puede eliminar proveedores
        if($_SESSION["perfil"] == "Vendedor"){

          echo '<script>

            Swal.fire({

              icon: "error",

              title: "¡No tienes permisos para eliminar proveedores!",

              showConfirmButton: true,

              confirmButtonText: "Cerrar",

              showCloseButton: true

            }).then((result)=>{

              if(result.isConfirmed){

                window.location = "proveedor";

              }

            });

          </script>';

          return;

        }

        $tabla = "proveedor";
        $datos = $_GET["idProveedorEliminar"];
        $respuesta = ModeloProveedores::mdlEliminarProveedor($tabla, $datos);
        if ($respuesta == "ok") {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "¡El proveedor ha sido eliminado correctamente!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then((result) => {
                    if(result.isConfirmed){
                        window.location = "proveedor";
                    }
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "¡Error al eliminar el proveedor!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                });
            </script>';
        }
    }
  }
}
