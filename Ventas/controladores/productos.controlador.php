<?php

class ControladorProductos {

  // Crear producto
  static public function ctrCrearProducto() {
    if (isset($_POST["nuevoNombre"])) {

      // Validación de campos
      if (
        preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevoNombre"]) &&
        preg_match('/^[a-zA-Z0-9]+$/', $_POST["nuevoCodigo"]) &&
        preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["nuevaDescripcion"]) &&
        is_numeric($_POST["nuevoStock"]) &&
        is_numeric($_POST["nuevoPrecioCompra"]) &&
        is_numeric($_POST["nuevoPrecioVenta"]) &&
        is_numeric($_POST["nuevaCantidadVentas"]) &&
        !empty($_POST["nuevaCategoria"])
      ) {

        $ruta = "vistas/img/productos/default/producto.png"; // Imagen por defecto

        // Procesamiento de imagen si hay una subida
        if (isset($_FILES["nuevaFoto"]["tmp_name"]) && !empty($_FILES["nuevaFoto"]["tmp_name"])) {
          
          list($ancho, $alto) = getimagesize($_FILES["nuevaFoto"]["tmp_name"]);
          $nuevoAncho = 500;
          $nuevoAlto = 500;

          // Crear carpeta
          $directorio = "vistas/img/productos/" . $_POST["nuevoCodigo"];
          if (!file_exists($directorio)) {
            mkdir($directorio, 0755, true);
          }

          $aleatorio = mt_rand(100, 999);

          // Redimensionar imagen
          if ($_FILES["nuevaFoto"]["type"] == "image/jpeg") {
            $ruta = $directorio . "/" . $aleatorio . ".jpg";
            $origen = imagecreatefromjpeg($_FILES["nuevaFoto"]["tmp_name"]);
          } elseif ($_FILES["nuevaFoto"]["type"] == "image/png") {
            $ruta = $directorio . "/" . $aleatorio . ".png";
            $origen = imagecreatefrompng($_FILES["nuevaFoto"]["tmp_name"]);
          }

          $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
          imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);

          if ($_FILES["nuevaFoto"]["type"] == "image/jpeg") {
            imagejpeg($destino, $ruta);
          } elseif ($_FILES["nuevaFoto"]["type"] == "image/png") {
            imagepng($destino, $ruta);
          }
        }

        // Datos a insertar
        $datos = array(
          "nombre" => $_POST["nuevoNombre"],
          "codigo" => $_POST["nuevoCodigo"],
          "descripcion" => $_POST["nuevaDescripcion"],
          "imagen" => $ruta,
          "stock" => $_POST["nuevoStock"],
          "precio_compra" => $_POST["nuevoPrecioCompra"],
          "precio_venta" => $_POST["nuevoPrecioVenta"],
          "ventas" => $_POST["nuevaCantidadVentas"],
          "fecha" => date('Y-m-d'),
          "id_categoria" => $_POST["nuevaCategoria"]
        );

        $tabla = "productos";
        $respuesta = ModeloProductos::mdlIngresarProducto($tabla, $datos);

        // Mostrar resultado con SweetAlert
        if ($respuesta == "ok") {
          echo '<script>
            Swal.fire({
              icon: "success",
              title: "¡Producto registrado correctamente!",
              showConfirmButton: true,
              confirmButtonText: "Cerrar"
            }).then((result) => {
              if (result.value) {
                window.location = "productos";
              }
            });
          </script>';
        } else {
          echo '<script>
            Swal.fire({
              icon: "error",
              title: "¡Error al registrar el producto!",
              text: "' . $respuesta . '",
              showConfirmButton: true,
              confirmButtonText: "Cerrar"
            });
          </script>';
        }

      } else {
        echo '<script>
          Swal.fire({
            icon: "error",
            title: "¡Error en los datos!",
            text: "Verifica que todos los campos estén correctamente llenados",
            showConfirmButton: true,
            confirmButtonText: "Cerrar"
          });
        </script>';
      }
    }
  }

  // Mostrar productos
  static public function ctrMostrarProductos($item, $valor) {
    $tabla = "productos";
    $respuesta = ModeloProductos::mdlMostrarProductos($tabla, $item, $valor);
    return $respuesta;
  }

  // Mostrar categorías
  static public function ctrMostrarCategoria($item, $valor) {
    return ModeloProductos::mdlMostrarCategorias("categorias", $item, $valor);
  }
/*=============================================
EDITAR PRODUCTO
=============================================*/
static public function ctrEditarProducto() {

    if (isset($_POST["editarNombre"])) {

        // Validaciones básicas (ajusta según tus necesidades)
        if (
            preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u', $_POST["editarNombre"]) &&
            preg_match('/^[a-zA-Z0-9]+$/', $_POST["editarCodigo"]) &&
            is_numeric($_POST["editarStock"]) &&
            is_numeric($_POST["editarPrecioCompra"]) &&
            is_numeric($_POST["editarPrecioVenta"]) &&
            is_numeric($_POST["editarCantidadVentas"])
        ) {

            $ruta = $_POST["fotoActual"];

            // Procesar imagen si se sube una nueva
            if (isset($_FILES["editarFoto"]["tmp_name"]) && !empty($_FILES["editarFoto"]["tmp_name"])) {

                list($ancho, $alto) = getimagesize($_FILES["editarFoto"]["tmp_name"]);
                $nuevoAncho = 500;
                $nuevoAlto = 500;

                $directorio = "vistas/img/productos/" . $_POST["editarCodigo"];

                // Si ya existe una imagen, eliminarla
                if (!empty($_POST["fotoActual"]) && file_exists($_POST["fotoActual"])) {
                 unlink($_POST["fotoActual"]);
                } else { 
                        mkdir($directorio, 0755); 
                }

                 $aleatorio = mt_rand(100,999);

                // Guardar imagen según el tipo
                if ($_FILES["editarFoto"]["type"] == "image/jpeg") {
                    /*=============================================
						        GUARDAMOS LA IMAGEN EN EL DIRECTORIO
						        =============================================*/

						        $ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".png";
                    $origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);	
                    $origen = imagecreatefromjpeg($_FILES["editarFoto"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                    imagejpeg($destino, $ruta);
                }
                if ($_FILES["editarFoto"]["type"] == "image/png") {
                    $ruta = "vistas/img/productos/".$_POST["editarCodigo"]."/".$aleatorio.".png";
                    $origen = imagecreatefrompng($_FILES["editarFoto"]["tmp_name"]);
                    $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
                    imagecopyresized($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
                    imagepng($destino, $ruta);
                }
            }

            $tabla = "productos";

            $datos = array(
                "id" => $_POST["idProducto"],
                "nombre" => $_POST["editarNombre"],
                "codigo" => $_POST["editarCodigo"],
                "descripcion" => $_POST["editarDescripcionProducto"],
                "imagen" => $ruta,
                "stock" => $_POST["editarStock"],
                "precio_compra" => $_POST["editarPrecioCompra"],
                "precio_venta" => $_POST["editarPrecioVenta"],
                "ventas" => $_POST["editarCantidadVentas"],
                "id_categoria" => $_POST["editarCategoria"]
            );

            $respuesta = ModeloProductos::mdlEditarProducto($tabla, $datos);

            if ($respuesta == "ok") {
                echo '<script>
                Swal.fire({
                  icon: "success",
                  title: "¡El producto ha sido editado correctamente!",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar",
                  showCloseButton: true
                }).then((result) => {
                  if(result.value){
                    window.location = "productos";
                  }
                });
                </script>';
            }

        } else {
            echo '<script>
                Swal.fire({
                  icon: "error",
                  title: "¡Error en los datos!",
                  text: "Verifica que todos los campos estén correctamente llenados",
                  showConfirmButton: true,
                  confirmButtonText: "Cerrar"
                });
            </script>';
        }
    }
  }
  /**METODO PARA BORRAR PRODUCTOS */
   static public function ctrEliminarProducto() {
    if (isset($_GET["idProducto"])) {

        // Verificar permisos: solo Administrador puede borrar productos
        if($_SESSION["perfil"] == "Vendedor"){

          echo '<script>

            Swal.fire({

              icon: "error",

              title: "¡No tienes permisos para eliminar productos!",

              showConfirmButton: true,

              confirmButtonText: "Cerrar",

              showCloseButton: true

            }).then((result)=>{

              if(result.isConfirmed){

                window.location = "productos";

              }

            });

          </script>';

          return;

        }

        $tabla = "productos";
        $datos = $_GET["idProducto"];

        // Eliminar carpeta del producto y todas sus imágenes si existe
        if (!empty($_GET["fotoProducto"])) {
            $carpeta = dirname($_GET["fotoProducto"]);
            if (file_exists($carpeta)) {
                self::eliminarCarpetaRecursiva($carpeta);
            }
        }

        $respuesta = ModeloProductos::mdlBorrarProducto($tabla, $datos);

        if ($respuesta == "ok") {
            echo '<script>
              Swal.fire({
                icon: "success",
                title: "¡El producto ha sido borrado correctamente!",
                showConfirmButton: true,
                confirmButtonText: "Cerrar"
              }).then((result) => {
                if(result.value){
                  window.location = "productos";
                }
              });
            </script>';
        }
    }
  }

  // ESTO SE AGREGGO para eliminar una carpeta y su contenido recursivamente, GRACIAS A ALEJANDRO EXPOSITOR
  static private function eliminarCarpetaRecursiva($dir) {
    if (!file_exists($dir)) return;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) {
      if ($item == '.' || $item == '..') continue;
      $path = $dir . DIRECTORY_SEPARATOR . $item;
      if (is_dir($path)) {
        self::eliminarCarpetaRecursiva($path);
      } else {
        unlink($path);
      }
    }
    rmdir($dir);
  }
}

