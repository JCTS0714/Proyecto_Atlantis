<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

$searchTerm = $_GET['q'] ?? null;
$clientes = ControladorCliente::ctrMostrarClientesParaOportunidad($searchTerm);

header('Content-Type: application/json');
echo json_encode($clientes);
?>
