<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Devolver cliente completo para autocompletar empresa/telefono
    $cliente = ControladorCliente::ctrMostrarClientePorId($id);
    header('Content-Type: application/json');
    echo json_encode($cliente);
    exit;
}

$searchTerm = $_GET['q'] ?? null;
$estadoFilter = isset($_GET['estado']) ? $_GET['estado'] : null;

// Si se solicita filtrar por estado (ej: prospectos = 0), usar el modelo de filtros
if ($searchTerm) {
    if ($estadoFilter !== null && $estadoFilter !== '') {
        // Usar el método genérico de filtros para obtener id/nombre
        $result = ModeloCliente::mdlMostrarClientesFiltrados('clientes', ['nombre' => $searchTerm, 'estado' => $estadoFilter]);
    } else {
        // Buscar por nombre sin filtro de estado
        $result = ControladorCliente::ctrMostrarClientesParaOportunidad($searchTerm);
    }
} else {
    if ($estadoFilter !== null && $estadoFilter !== '') {
        $result = ModeloCliente::mdlMostrarClientesFiltrados('clientes', ['estado' => $estadoFilter]);
    } else {
        $result = ControladorCliente::ctrMostrarClientesParaOportunidad();
    }
}

// Normalizar la salida para select2: array de objetos {id, nombre}
$out = [];
foreach ($result as $r) {
    $out[] = [
        'id' => $r['id'],
        'nombre' => $r['nombre'],
        'empresa' => $r['empresa'] ?? '',
        'telefono' => $r['telefono'] ?? ''
    ];
}

header('Content-Type: application/json');
echo json_encode($out);
?>
