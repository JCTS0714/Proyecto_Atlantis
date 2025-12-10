<?php
session_start();
$_SESSION['id'] = 1;

require_once 'modelos/conexion.php';
require_once 'modelos/ModeloIncidencias.php';

header('Content-Type: application/json');

try {
    $result = ModeloIncidencias::mdlMostrarIncidencias();
    echo json_encode([
        'success' => true,
        'count' => count($result),
        'data' => $result
    ], JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ], JSON_PRETTY_PRINT);
}
