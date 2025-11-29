<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';
require_once __DIR__ . '/../controladores/dashboard.controlador.php';

header('Content-Type: application/json');

// Obtener acción desde POST o GET
$action = $_POST['action'] ?? $_GET['action'] ?? '';
error_log("dashboard.ajax.php received action: " . $action);

try {
switch ($action) {
        case 'getMetricasClientes':
            $periodo = $_POST['periodo'] ?? 'mensual';
            error_log("dashboard.ajax.php received periodo: " . $periodo);
            $response = ControladorDashboard::ctrObtenerMetricasClientes();
            break;
            
        case 'getReunionesSemana':
            $response = ControladorDashboard::ctrObtenerReunionesSemana();
            break;
            
        case 'getIndicadoresClave':
            $response = ControladorDashboard::ctrObtenerIndicadoresClave();
            break;
            
        case 'getEvolucionMensual':
            $response = ControladorDashboard::ctrObtenerEvolucionMensual();
            break;

        case 'getTotalesDashboard':
            $response = ControladorDashboard::ctrObtenerTotalesDashboard();
            break;
            
        default:
            $response = [
                'status' => 'error',
                'message' => 'Acción no válida para el dashboard'
            ];
            break;
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}