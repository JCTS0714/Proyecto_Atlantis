<?php
require_once "../controladores/productos.controlador.php";
require_once "../modelos/productos.modelo.php";

class AjaxProductos{
    /**==============
     * EDITAR PRODUCTO
     * ==============*/

    public $idProducto;

    public function ajaxEditarProducto(){
        $item = "id";
        $valor = $this->idProducto;

        $respuesta = ControladorProductos::ctrMostrarProductos($item,$valor);

        echo json_encode($respuesta);
    }
    /**==============
     * ACTIVAR PRODUCTO
     * ==============*/
    public $activarProducto;
    public $activarId;
    public function ajaxActivarProducto(){
        $tabla = "productos";

        $item1 = "estado";
        $valor1 = $this->activarProducto;

        $item2 = "id";
        $valor2 = $this->activarId;

        $respuesta = ModeloProductos::mdlActualizarProducto($tabla, $item1, $valor1, $item2, $valor2);

        echo $respuesta;
    }
}
//EDITAR PRODUCTO = CREAR UN OBJETO PARA VERIFICAR LOS QUE NOS VENGA DEL ID PRODUCTO
if(isset($_POST["idProducto"])){
    $editar = new AjaxProductos();
    $editar -> idProducto = $_POST["idProducto"];
    $editar -> ajaxEditarProducto();
}
//objeto para activa producto
/**==============
     * ACTIVAR PRODUCTO OBJETO PARA ACTIVAR
     * ==============*/
if(isset($_POST["activarProducto"])){
        $activarProducto = new AjaxProductos();
        $activarProducto -> activarProducto = $_POST["activarProducto"];
        $activarProducto -> activarId = $_POST["activarId"];
        $activarProducto -> ajaxActivarProducto();
}