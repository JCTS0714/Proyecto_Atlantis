<?php
require_once '../controladores/ControladorOportunidad.php';
require_once '../modelos/ModeloCRM.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'getOportunidades':
        header('Content-Type: application/json');
        echo json_encode(ControladorOportunidad::ctrMostrarOportunidades());
        break;
    case 'crearOportunidad':
        ControladorOportunidad::ctrCrearOportunidad();
        break;
    case 'cambiarEstado':
        ControladorOportunidad::ctrActualizarEstadoOportunidad();
        break;
    case 'eliminarOportunidad':
        ControladorOportunidad::ctrEliminarOportunidad();
        break;
    case 'getClientes':
        header('Content-Type: application/json');
        echo json_encode(ControladorOportunidad::ctrMostrarClientes());
        break;
}
?>
