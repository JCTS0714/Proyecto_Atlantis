<?php
require_once "../modelos/contador.modelo.php";

$q = $_GET['q'] ?? '';

// Obtener por id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idContador'])) {
  $id = intval($_POST['idContador']);
  $datos = ModeloContador::mdlMostrarContador('contador', 'id', $id);
  header('Content-Type: application/json');
  if ($datos && count($datos) > 0) echo json_encode($datos[0]); else echo json_encode(new stdClass());
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
    $stmt = $db->prepare("SELECT COALESCE(MAX(nro),0) AS maxn FROM contador");
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
