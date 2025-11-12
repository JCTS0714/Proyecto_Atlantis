<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "../controladores/ControladorOportunidad.php";
require_once "../modelos/ModeloCRM.php";

$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'getOportunidades':
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
        echo json_encode(ControladorOportunidad::ctrMostrarClientes());
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}