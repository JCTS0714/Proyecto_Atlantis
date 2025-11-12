<?php



require_once "../controladores/categorias.controlador.php";

require_once "../modelos/categorias.modelo.php";



class AjaxCategorias {
    /**==============
     * EDITAR CATEGORIA
     * ==============*/

 

 public $idCategoria;



 public function ajaxEditarCategoria(){

  $item = "id";

  $valor = $this->idCategoria;

  $respuesta = ControladorCategoria::ctrMostrarCategoria($item, $valor);

  echo json_encode($respuesta);

 }
 /**==============
     * ACTIVAR CATEGORIA
     * ==============*/
    public $activarCategoria;
    public $activarId;
    public function ajaxActivarCategoria(){
        $tabla = "categoria";

        $item1 = "estado";
        $valor1 = $this->activarCategoria;

        $item2 = "id";
        $valor2 = $this->activarId;

        $respuesta = ModeloCategorias::mdlActualizarCategoria($tabla, $item1, $valor1, $item2, $valor2);

        echo $respuesta;
    }

}



if(isset($_POST["idCategoria"])){

 $categoria = new AjaxCategorias();

 $categoria->idCategoria = $_POST["idCategoria"];

 $categoria->ajaxEditarCategoria();

}
//objeto para activar categoria
/**==============
     * ACTIVAR CATEGORIA OBJETO PARA ACTIVAR
     * ==============*/
if(isset($_POST["activarCategoria"])){
        $activarCategoria = new AjaxCategorias();
        $activarCategoria -> activarCategoria = $_POST["activarCategoria"];
        $activarCategoria -> activarId = $_POST["activarId"];
        $activarCategoria -> ajaxActivarCategoria();
}



