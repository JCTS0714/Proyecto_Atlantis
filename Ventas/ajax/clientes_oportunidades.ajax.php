<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $cliente = ControladorCliente::ctrMostrarClientePorId($id);
    header('Content-Type: application/json');
    echo json_encode($cliente);
    exit;
}

$searchTerm = $_GET['q'] ?? null;
$clientes = ControladorCliente::ctrMostrarClientesParaOportunidad($searchTerm);

header('Content-Type: application/json');
echo json_encode($clientes);
?>
