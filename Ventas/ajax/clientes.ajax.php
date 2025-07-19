<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

class AjaxClientes {

    /**=============================
     * EDITAR CLIENTE
     * ============================*/
    public $idCliente;

    public function ajaxEditarCliente() {
        $item = "id";
        $valor = $this->idCliente;

        $respuesta = ControladorCliente::ctrMostrarCliente($item, $valor);

        echo json_encode($respuesta);
    }

    /**=============================
     * ACTIVAR CLIENTE
     * ============================*/
    public $activarCliente;
    public $activarId;

    public function ajaxActivarCliente() {
        $tabla = "cliente";

        $item1 = "estado";
        $valor1 = $this->activarCliente;

        $item2 = "id";
        $valor2 = $this->activarId;

        $respuesta = ModeloClientes::mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2);

        echo $respuesta;
    }
}
if (isset($_POST["idCliente"])) {
    $cliente = new AjaxClientes();
    $cliente->idCliente = $_POST["idCliente"];
    $cliente->ajaxEditarCliente();
}
if (isset($_POST["activarCliente"])) {
    $activarCliente = new AjaxClientes();
    $activarCliente->activarCliente = $_POST["activarCliente"];
    $activarCliente->activarId = $_POST["activarId"];
    $activarCliente->ajaxActivarCliente();
}