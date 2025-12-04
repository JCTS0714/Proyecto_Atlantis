<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';

header('Content-Type: application/json; charset=utf-8');
require_once "../modelos/conexion.php";
require_once "../modelos/clientes.modelo.php";

// DataTables server-side processing parameters
$draw = isset($_GET['draw']) ? intval($_GET['draw']) : (isset($_POST['draw']) ? intval($_POST['draw']) : 0);
$start = isset($_GET['start']) ? intval($_GET['start']) : (isset($_POST['start']) ? intval($_POST['start']) : 0);
$length = isset($_GET['length']) ? intval($_GET['length']) : (isset($_POST['length']) ? intval($_POST['length']) : 10);

// Advanced search filters (may come via GET or POST)
$filters = [];
// Accept both direct names and the advanced-search prefixed names (adv_*) coming from the UI
$filters['nombre'] = isset($_REQUEST['nombre']) ? trim($_REQUEST['nombre']) : (isset($_REQUEST['adv_nombre']) ? trim($_REQUEST['adv_nombre']) : '');
$filters['telefono'] = isset($_REQUEST['telefono']) ? trim($_REQUEST['telefono']) : (isset($_REQUEST['adv_telefono']) ? trim($_REQUEST['adv_telefono']) : '');
$filters['documento'] = isset($_REQUEST['documento']) ? trim($_REQUEST['documento']) : (isset($_REQUEST['adv_documento']) ? trim($_REQUEST['adv_documento']) : '');
$filters['periodo'] = isset($_REQUEST['periodo']) ? trim($_REQUEST['periodo']) : (isset($_REQUEST['adv_periodo']) ? trim($_REQUEST['adv_periodo']) : '');
$filters['fecha_inicio'] = isset($_REQUEST['fecha_inicio']) ? trim($_REQUEST['fecha_inicio']) : (isset($_REQUEST['adv_fecha_inicio']) ? trim($_REQUEST['adv_fecha_inicio']) : '');
$filters['fecha_fin'] = isset($_REQUEST['fecha_fin']) ? trim($_REQUEST['fecha_fin']) : (isset($_REQUEST['adv_fecha_fin']) ? trim($_REQUEST['adv_fecha_fin']) : '');
// Nuevos campos de periodo
$filters['mes_unico'] = isset($_REQUEST['mes_unico']) ? trim($_REQUEST['mes_unico']) : (isset($_REQUEST['adv_mes_unico']) ? trim($_REQUEST['adv_mes_unico']) : '');
$filters['mes_desde'] = isset($_REQUEST['mes_desde']) ? trim($_REQUEST['mes_desde']) : (isset($_REQUEST['adv_mes_desde']) ? trim($_REQUEST['adv_mes_desde']) : '');
$filters['mes_hasta'] = isset($_REQUEST['mes_hasta']) ? trim($_REQUEST['mes_hasta']) : (isset($_REQUEST['adv_mes_hasta']) ? trim($_REQUEST['adv_mes_hasta']) : '');
$filters['fecha_unica'] = isset($_REQUEST['fecha_unica']) ? trim($_REQUEST['fecha_unica']) : (isset($_REQUEST['adv_fecha_unica']) ? trim($_REQUEST['adv_fecha_unica']) : '');
// Nuevo: tipo de fecha (fecha_creacion o fecha_contacto)
$filters['tipo_fecha'] = isset($_REQUEST['tipo_fecha']) ? trim($_REQUEST['tipo_fecha']) : (isset($_REQUEST['adv_tipo_fecha']) ? trim($_REQUEST['adv_tipo_fecha']) : 'fecha_creacion');
// Filtro por servidor
$filters['servidor'] = isset($_REQUEST['servidor']) ? trim($_REQUEST['servidor']) : (isset($_REQUEST['adv_servidor']) ? trim($_REQUEST['adv_servidor']) : '');

// Debug mode flag (client can request with adv_debug=1)
$debugMode = isset($_REQUEST['adv_debug']) && intval($_REQUEST['adv_debug']) === 1;

// Log incoming filters only when debug mode requested
if ($debugMode) {
  error_log('datatable-clientes incoming filters: ' . json_encode($filters));
}

$table = 'clientes';

// Build base SQL using similar logic as ModeloCliente::mdlMostrarClientesFiltrados but with pagination
$where = [];
$params = [];

// Nombre busca en nombre (Contacto) y empresa (Comercio)
if ($filters['nombre'] !== '') { 
  $where[] = "(c.nombre LIKE :nombre OR c.empresa LIKE :nombre2)"; 
  $params[':nombre'] = '%' . $filters['nombre'] . '%'; 
  $params[':nombre2'] = '%' . $filters['nombre'] . '%'; 
}
if ($filters['documento'] !== '') { $where[] = "c.documento LIKE :documento"; $params[':documento'] = '%' . $filters['documento'] . '%'; }
if ($filters['telefono'] !== '') { $where[] = "c.telefono LIKE :telefono"; $params[':telefono'] = '%' . $filters['telefono'] . '%'; }
if ($filters['servidor'] !== '') { $where[] = "c.servidor = :servidor"; $params[':servidor'] = $filters['servidor']; }

// Determinar el campo de fecha a usar (fecha_creacion o fecha_contacto)
$campoFecha = ($filters['tipo_fecha'] === 'fecha_contacto') ? 'c.fecha_contacto' : 'c.fecha_creacion';

// Date filtering based on periodo or explicit fechas
// Nuevos filtros de periodo
if ($filters['periodo'] === 'por_mes' && $filters['mes_unico'] !== '') {
  // Formato mes_unico: YYYY-MM
  $where[] = "DATE_FORMAT($campoFecha, '%Y-%m') = :mes_unico";
  $params[':mes_unico'] = $filters['mes_unico'];
} elseif ($filters['periodo'] === 'entre_meses' && $filters['mes_desde'] !== '' && $filters['mes_hasta'] !== '') {
  // Formato mes_desde/mes_hasta: YYYY-MM
  $where[] = "DATE_FORMAT($campoFecha, '%Y-%m') BETWEEN :mes_desde AND :mes_hasta";
  $params[':mes_desde'] = $filters['mes_desde'];
  $params[':mes_hasta'] = $filters['mes_hasta'];
} elseif ($filters['periodo'] === 'por_fecha' && $filters['fecha_unica'] !== '') {
  // Formato fecha_unica: YYYY-MM-DD
  $where[] = "DATE($campoFecha) = :fecha_unica";
  $params[':fecha_unica'] = $filters['fecha_unica'];
} elseif ($filters['periodo'] === 'entre_fechas' && $filters['fecha_inicio'] !== '' && $filters['fecha_fin'] !== '') {
  // Formato fecha_inicio/fecha_fin: YYYY-MM-DD
  $where[] = "DATE($campoFecha) BETWEEN :fi AND :ff";
  $params[':fi'] = $filters['fecha_inicio'];
  $params[':ff'] = $filters['fecha_fin'];
} elseif ($filters['periodo'] === 'today') {
  // Mantener compatibilidad con valores antiguos
  $where[] = "DATE($campoFecha) = CURDATE()";
} elseif ($filters['periodo'] === 'yesterday') {
  $where[] = "DATE($campoFecha) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
} elseif ($filters['periodo'] === 'this_week') {
  $where[] = "YEARWEEK($campoFecha, 1) = YEARWEEK(CURDATE(), 1)";
} elseif ($filters['periodo'] === 'last_week') {
  $where[] = "YEARWEEK($campoFecha, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)";
} elseif ($filters['periodo'] === 'this_month') {
  $where[] = "YEAR($campoFecha) = YEAR(CURDATE()) AND MONTH($campoFecha) = MONTH(CURDATE())";
} elseif ($filters['periodo'] === 'last_month') {
  $where[] = "YEAR($campoFecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH($campoFecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
} elseif ($filters['periodo'] === 'this_year') {
  $where[] = "YEAR($campoFecha) = YEAR(CURDATE())";
} elseif ($filters['periodo'] === 'custom' && $filters['fecha_inicio'] !== '' && $filters['fecha_fin'] !== '') {
  $where[] = "DATE($campoFecha) BETWEEN :fi AND :ff";
  $params[':fi'] = $filters['fecha_inicio'];
  $params[':ff'] = $filters['fecha_fin'];
} elseif ($filters['fecha_inicio'] !== '' && $filters['fecha_fin'] !== '') {
  $where[] = "DATE($campoFecha) BETWEEN :fi AND :ff";
  $params[':fi'] = $filters['fecha_inicio'];
  $params[':ff'] = $filters['fecha_fin'];
}

$sqlWhere = '';
if (!empty($where)) { $sqlWhere = ' WHERE ' . implode(' AND ', $where); }


$rows = [];
// Debug mode if client requested it
$debugMode = isset($_REQUEST['adv_debug']) && intval($_REQUEST['adv_debug']) === 1;
$debugInfo = [];
try {
  $pdo = Conexion::conectar();

  // Get total records
  $totalStmt = $pdo->prepare("SELECT COUNT(*) as total FROM $table");
  $totalStmt->execute();
  $totalTotal = $totalStmt->fetch(PDO::FETCH_ASSOC);
  $recordsTotal = isset($totalTotal['total']) ? intval($totalTotal['total']) : 0;

  // Get filtered count
  $countSql = "SELECT COUNT(DISTINCT c.id) as total FROM $table c" . $sqlWhere;
  $countStmt = $pdo->prepare($countSql);
  foreach ($params as $k => $v) { $countStmt->bindValue($k, $v); }
  $countStmt->execute();
  $countRow = $countStmt->fetch(PDO::FETCH_ASSOC);
  $recordsFiltered = isset($countRow['total']) ? intval($countRow['total']) : 0;

  // Fetch paginated data
  $dataSql = "SELECT DISTINCT c.* FROM $table c" . $sqlWhere . " ORDER BY c.fecha_creacion DESC LIMIT :start, :length";
  $dataStmt = $pdo->prepare($dataSql);
  foreach ($params as $k => $v) { $dataStmt->bindValue($k, $v); }
  $dataStmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
  $dataStmt->bindValue(':length', (int)$length, PDO::PARAM_INT);

  $dataStmt->execute();
  $rows = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

  if ($debugMode) {
    $debugInfo['filters'] = $filters;
    $debugInfo['sqlWhere'] = $sqlWhere;
    $debugInfo['countSql'] = $countSql;
    $debugInfo['dataSql'] = $dataSql;
    $debugInfo['params'] = $params;
  }

} catch (PDOException $e) {
  error_log('datatable-clientes ERROR: ' . $e->getMessage());
  // In case of error, return a valid DataTables structure with zero rows
  $response = [
    'draw' => $draw,
    'recordsTotal' => isset($recordsTotal) ? intval($recordsTotal) : 0,
    'recordsFiltered' => isset($recordsFiltered) ? intval($recordsFiltered) : 0,
    'data' => []
  ];
  if ($debugMode) $response['debug'] = $debugInfo;
  echo json_encode($response);
  exit;
}


$response = [
  'draw' => $draw,
  'recordsTotal' => $recordsTotal,
  'recordsFiltered' => $recordsFiltered,
  'data' => []
];

// Attach debug info only when requested by client
if ($debugMode) {
  $response['debug'] = array_merge($debugInfo, [
    'filters' => $filters,
    'sqlWhere' => $sqlWhere,
    'params' => $params,
    'countSql' => isset($countSql) ? $countSql : null,
    'dataSql' => isset($dataSql) ? $dataSql : null,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'rowsFetched' => is_array($rows) ? count($rows) : 0
  ]);
}

// Format data rows to match table columns order used in clientes.php
// Orden: N°, Comercio, Contacto, Celular, Ciudad, Precio, RUC, Rubro, Año, Mes, Link, Usuario, Contraseña, Cambiar Estado, Acciones
$mesesNombre = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

foreach ($rows as $k => $r) {
  $actions = '<div class="btn-group">';
  $actions .= '<button class="btn btn-warning btnEditarCliente" idCliente="'.htmlspecialchars($r['id']).'" data-toggle="modal" data-target="#modalActualizarClientes"><i class="fa fa-pencil"></i></button>';
  $actions .= '<button class="btn btn-info btnRegistrarIncidencia" idCliente="'.htmlspecialchars($r['id']).'" nombreCliente="'.htmlspecialchars($r['nombre']).'"><i class="fa fa-exclamation-triangle"></i> Incidencia</button>';
  if (isset($_SESSION['perfil']) && $_SESSION['perfil'] !== 'Vendedor') {
    $actions .= '<button class="btn btn-danger btnEliminarCliente" idCliente="'.htmlspecialchars($r['id']).'"><i class="fa fa-trash"></i></button>';
  }
  $actions .= '</div>';

  $selectEstado = '<select class="form-control input-sm select-estado-cliente" data-id="'.htmlspecialchars($r['id']).'">'
    .'<option value="0"'.($r['estado'] == 0 ? ' selected' : '').'>Prospecto</option>'
    .'<option value="1"'.($r['estado'] == 1 ? ' selected' : '').'>Seguimiento</option>'
    .'<option value="2"'.($r['estado'] == 2 ? ' selected' : '').'>Cliente</option>'
    .'<option value="3"'.($r['estado'] == 3 ? ' selected' : '').'>No Cliente</option>'
    .'<option value="4"'.($r['estado'] == 4 ? ' selected' : '').'>En Espera</option>'
    .'</select>';

  // Preparar el link con protocolo si no lo tiene
  $linkUrl = $r['post_link'] ?? '';
  $linkHtml = '-';
  if (!empty($linkUrl)) {
    if (!preg_match('/^https?:\/\//i', $linkUrl)) {
      $linkUrl = 'https://' . $linkUrl;
    }
    $linkHtml = '<a href="'.htmlspecialchars($linkUrl).'" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-external-link"></i></a>';
  }

  // Obtener nombre del mes
  $mesNum = isset($r['post_mes']) ? intval($r['post_mes']) : 0;
  $mesNombre = isset($mesesNombre[$mesNum]) ? $mesesNombre[$mesNum] : '';

  // Formatear fechas
  $fechaCreacion = !empty($r['fecha_creacion']) ? date('d/m/Y', strtotime($r['fecha_creacion'])) : '-';
  $fechaContacto = !empty($r['fecha_contacto']) ? date('d/m/Y', strtotime($r['fecha_contacto'])) : '-';

  $response['data'][] = [
    $start + $k + 1,                                    // N° (col 0)
    htmlspecialchars($r['empresa'] ?? ''),              // Comercio (col 1)
    htmlspecialchars($r['nombre'] ?? ''),               // Contacto (col 2)
    htmlspecialchars($r['telefono'] ?? ''),             // Celular (col 3)
    htmlspecialchars($r['ciudad'] ?? ''),               // Ciudad (col 4)
    htmlspecialchars($r['post_precio'] ?? '-'),         // Precio (col 5)
    htmlspecialchars($r['documento'] ?? ''),            // RUC (col 6)
    htmlspecialchars($r['post_rubro'] ?? '-'),          // Rubro (col 7)
    htmlspecialchars($r['post_ano'] ?? '-'),            // Año (col 8)
    $mesNombre,                                         // Mes (col 9)
    $linkHtml,                                          // Link (col 10)
    htmlspecialchars($r['post_usuario'] ?? '-'),        // Usuario (col 11)
    htmlspecialchars($r['post_contrasena'] ?? '-'),     // Contraseña (col 12)
    htmlspecialchars($r['servidor'] ?? '-'),            // Servidor (col 13)
    $fechaCreacion,                                     // F. Creación (col 14) - oculta
    $fechaContacto,                                     // F. Contacto (col 15) - oculta
    $selectEstado,                                      // Cambiar Estado (col 16)
    $actions                                            // Acciones (col 17)
  ];
}

echo json_encode($response);
