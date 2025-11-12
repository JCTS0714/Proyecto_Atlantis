<?php



class ControladorCategoria{



 /**MÉTODO PARA REGISTRAR UN NUEVO USUARIO */

 static public function ctrCrearCategoria(){

  if(isset($_POST["nuevaCategoria"])){

   if(preg_match('/^[\p{L}\p{N}\p{P}\p{M}\s]+$/u',$_POST["nuevaCategoria"])){

    $tabla = "categoria";

    $datos = array("categoria" => $_POST["nuevaCategoria"]);

    $respuesta = ModeloCategoria::mdlRegistrarCategoria($tabla,$datos);

    if($respuesta == "ok"){

     echo '<script>



      swal.fire({

       icon: "success",

       title: "¡La Categoria ha sido registrado correctamente!",

       showConfirmButton: true,

       confirmButtonText:"Cerrar",

       showCloseButton:true



      }).then((result=>{



       if(result.value){

        window.location = "categorias";

       }

      

      }));

     

     </script>';

    }

    else

    {

     echo '<script>



      swal.fire({

       icon: "error",

       title: "¡El campo usuario no puede estar vacio o con caracteres especiales!",

       showConfirmButton: true,

       confirmButtonText:"Cerrar",

       showCloseButton:true

      }).then((result=>{



       if(result.value){

        window.location = "categorias";

       }

      

      }));

      

     </script>';

    }

   }

  }

 }



   /**============================



  * MÉTODO PARA MOSTRAR CATEGORIA



  * ============================



  */





  static public function ctrMostrarCategoria($item,$valor){

  $tabla = "categoria";



  $respuesta = ModeloCategoria::MdlMostrarCategoria($tabla,$item,$valor);



  return $respuesta;

  }

  static public function ctrEliminarCategoria(){

 if (isset($_GET["idCategoriaEliminar"])) {

  // Verificar permisos: solo Administrador puede eliminar categorías
  if($_SESSION["perfil"] == "Vendedor"){

   echo '<script>

    Swal.fire({

     icon: "error",

     title: "¡No tienes permisos para eliminar categorías!",

     showConfirmButton: true,

     confirmButtonText: "Cerrar",

     showCloseButton: true

    }).then((result)=>{

     if(result.value){

      window.location = "categorias";

     }

    });

   </script>';

   return;

  }

  $tabla = "categoria";

  $datos = $_GET["idCategoriaEliminar"];



  $respuesta = ModeloCategoria::mdlEliminarCategoria($tabla, $datos);



  if ($respuesta == "ok") {

   echo '<script>

    Swal.fire({

     icon: "success",

     title: "¡La categoría ha sido eliminada correctamente!",

     showConfirmButton: true,

     confirmButtonText: "Cerrar"

    }).then((result) => {

     if (result.isConfirmed) {

      window.location = "categorias";

     }

    });

   </script>';

  } else {

   echo '<script>

    Swal.fire({

     icon: "error",

     title: "¡No se pudo eliminar la categoría!",

     showConfirmButton: true,

     confirmButtonText: "Cerrar"

    }).then((result) => {

     if (result.isConfirmed) {

      window.location = "categorias";

     }

    });

   </script>';

  }

 }

}
static public function ctrEditarCategoria(){
    if(isset($_POST["editarCategoria"])){
        $tabla = "categoria";
        $datos = array(
            "categoria" => $_POST["editarCategoria"],
            "id" => $_POST["idCategoria"]
        );
        $respuesta = ModeloCategoria::mdlEditarCategoria($tabla, $datos);

        if($respuesta == "ok"){
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "¡La categoría ha sido editada correctamente!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                }).then((result) => {
                    if(result.isConfirmed){
                        window.location = "categorias";
                    }
                });
            </script>';
        } else {
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "¡Error al editar la categoría!",
                    showConfirmButton: true,
                    confirmButtonText: "Cerrar"
                });
            </script>';
        }
      }
    }
}

