<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';
require_once "../modelos/contador.modelo.php";
require_once "../modelos/conexion.php";

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
  
  header('Content-Type: application/json');
  echo json_encode(['results' => $results]);
  exit;
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
  
  header('Content-Type: application/json');
  echo json_encode($results);
  exit;
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
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'ID de contador inválido']);
    exit;
  }
  
  $resultado = ModeloContador::mdlAsignarClientes($contadorId, $clienteIds);
  
  header('Content-Type: application/json');
  echo json_encode(['status' => $resultado]);
  exit;
}

// Obtener por id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idContador']) && !isset($_POST['action'])) {
  $id = intval($_POST['idContador']);
  $datos = ModeloContador::mdlMostrarContador('contador', 'id', $id);
  
  // También obtener los clientes asignados
  $clientesAsignados = ModeloContador::mdlObtenerClientesContador($id);
  
  header('Content-Type: application/json');
  if ($datos && count($datos) > 0) {
    $response = $datos[0];
    $response['clientes_asignados'] = $clientesAsignados;
    echo json_encode($response);
  } else {
    echo json_encode(new stdClass());
  }
  exit;
}

// Búsqueda simple para select/autocomplete (opcional)
if ($q !== '') {
  $clientes = ModeloContador::mdlMostrarContador('contador', null, null);
  header('Content-Type: application/json');
  echo json_encode($clientes);
  exit;
}

// Endpoint para obtener el próximo nro (MAX(nro)+1)
if (isset($_GET['next_nro'])) {
  try {
    $db = Conexion::conectar();
    $stmt = $db->prepare("SELECT COALESCE(MAX(CAST(nro AS UNSIGNED)),0) AS maxn FROM contador");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $next = ($row && $row['maxn']) ? intval($row['maxn']) + 1 : 1;
    header('Content-Type: application/json');
    echo json_encode(['next_nro' => $next]);
    exit;
  } catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['next_nro' => 1]);
    exit;
  }
}
