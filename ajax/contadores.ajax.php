<?php
// Bootstrap y helpers comunes
require_once __DIR__ . '/_error_handler.php';
require_once __DIR__ . '/_timezone.php';
require_once __DIR__ . '/_json.php';

require_once "../modelos/contador.modelo.php";
require_once "../modelos/conexion.php";

// Auth mínima
if (!isset($_SESSION['id'])) {
  ajax_json_respond(ajax_json_normalize(ajax_json_error('AUTH_REQUIRED', 'Usuario no autenticado.'), false, 401), 401);
}

$q = $_GET['q'] ?? '';

// ========================================
// ENDPOINT: Buscar clientes por empresa (para Select2)
// ========================================
if (isset($_GET['action']) && $_GET['action'] === 'buscarEmpresas') {
  $termino = isset($_GET['term']) ? trim($_GET['term']) : '';
  $clientes = ModeloContador::mdlBuscarClientesPorEmpresa($termino);

  // Formatear para Select2
  $results = [];
  foreach ($clientes as $c) {
    $results[] = [
      'id' => $c['id'],
      'text' => $c['empresa'] . ($c['nombre'] ? ' (' . $c['nombre'] . ')' : '')
    ];
  }

  ajax_json_respond(ajax_json_normalize(['results' => $results], true, 200), 200);
}

// ========================================
// ENDPOINT: Obtener clientes asignados a un contador
// ========================================
if (isset($_GET['action']) && $_GET['action'] === 'getClientesContador') {
  $contadorId = isset($_GET['contador_id']) ? intval($_GET['contador_id']) : 0;
  $clientes = ModeloContador::mdlObtenerClientesContador($contadorId);

  // Formatear para Select2 (preselección)
  $results = [];
  foreach ($clientes as $c) {
    $results[] = [
      'id' => $c['id'],
      'text' => $c['empresa'] . ($c['nombre'] ? ' (' . $c['nombre'] . ')' : '')
    ];
  }

  ajax_json_respond(ajax_json_normalize($results, true, 200), 200);
}

// ========================================
// ENDPOINT: Asignar clientes a contador
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'asignarClientes') {
  $contadorId = isset($_POST['contador_id']) ? intval($_POST['contador_id']) : 0;
  $clienteIds = isset($_POST['cliente_ids']) ? $_POST['cliente_ids'] : [];

  // Si viene como string separado por comas, convertir a array
  if (is_string($clienteIds)) {
    $clienteIds = array_filter(explode(',', $clienteIds));
  }

  if ($contadorId <= 0) {
    ajax_json_respond(ajax_json_normalize(['status' => 'error', 'message' => 'ID de contador inválido'], false, 400), 400);
  }

  $resultado = ModeloContador::mdlAsignarClientes($contadorId, $clienteIds);

  if ($resultado === 'ok') {
    ajax_json_respond(ajax_json_normalize(['status' => 'ok'], true, 200), 200);
  }

  ajax_json_respond(ajax_json_normalize(['status' => 'error', 'message' => 'No se pudieron asignar clientes.'], false, 500), 500);
}

// Obtener por id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idContador']) && !isset($_POST['action'])) {
  $id = intval($_POST['idContador']);
  $datos = ModeloContador::mdlMostrarContador('contador', 'id', $id);

  // También obtener los clientes asignados
  $clientesAsignados = ModeloContador::mdlObtenerClientesContador($id);

  if ($datos && count($datos) > 0) {
    $response = $datos[0];
    $response['clientes_asignados'] = $clientesAsignados;
    ajax_json_respond(ajax_json_normalize($response, true, 200), 200);
  }

  ajax_json_respond(ajax_json_normalize(new stdClass(), false, 404), 404);
}

// Búsqueda simple para select/autocomplete (opcional)
if ($q !== '') {
  $clientes = ModeloContador::mdlMostrarContador('contador', null, null);
  ajax_json_respond(ajax_json_normalize($clientes, true, 200), 200);
}

// Endpoint para obtener el próximo nro (MAX(nro)+1)
if (isset($_GET['next_nro'])) {
  try {
    $db = Conexion::conectar();
    $stmt = $db->prepare("SELECT COALESCE(MAX(CAST(nro AS UNSIGNED)),0) AS maxn FROM contador");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $next = ($row && $row['maxn']) ? intval($row['maxn']) + 1 : 1;
    ajax_json_respond(ajax_json_normalize(['next_nro' => $next], true, 200), 200);
  } catch (Exception $e) {
    ajax_json_respond(ajax_json_normalize(['next_nro' => 1], false, 500), 500);
  }
}
