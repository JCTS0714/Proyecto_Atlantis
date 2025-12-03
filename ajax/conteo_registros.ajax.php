<?php
/**
 * AJAX para contar registros de clientes por periodo de tiempo
 * Soporta: Hoy, Esta semana, Este mes, Mes pasado, Este año, Personalizado
 */
require_once __DIR__ . '/_timezone.php';
require_once "../modelos/conexion.php";

header('Content-Type: application/json');

// Obtener parámetros
$estado = isset($_GET['estado']) ? intval($_GET['estado']) : null;
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'todo';
$fechaInicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fechaFin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Construir condiciones SQL
$whereConditions = [];
$params = [];

// Filtrar por estado si se especifica
if ($estado !== null) {
    $whereConditions[] = "estado = :estado";
    $params[':estado'] = $estado;
}

// Construir filtro de fecha según el periodo
$campoFecha = 'fecha_creacion'; // Campo de fecha a usar para filtrar

switch ($periodo) {
    case 'hoy':
        $whereConditions[] = "DATE($campoFecha) = CURDATE()";
        break;
    
    case 'ayer':
        $whereConditions[] = "DATE($campoFecha) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        break;
    
    case 'esta_semana':
        $whereConditions[] = "YEARWEEK($campoFecha, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    
    case 'semana_pasada':
        $whereConditions[] = "YEARWEEK($campoFecha, 1) = YEARWEEK(DATE_SUB(CURDATE(), INTERVAL 1 WEEK), 1)";
        break;
    
    case 'este_mes':
        $whereConditions[] = "MONTH($campoFecha) = MONTH(CURDATE()) AND YEAR($campoFecha) = YEAR(CURDATE())";
        break;
    
    case 'mes_pasado':
        $whereConditions[] = "MONTH($campoFecha) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR($campoFecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
        break;
    
    case 'este_ano':
        $whereConditions[] = "YEAR($campoFecha) = YEAR(CURDATE())";
        break;
    
    case 'ano_pasado':
        $whereConditions[] = "YEAR($campoFecha) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 YEAR))";
        break;
    
    case 'personalizado':
        if ($fechaInicio && $fechaFin) {
            $whereConditions[] = "DATE($campoFecha) BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $fechaInicio;
            $params[':fecha_fin'] = $fechaFin;
        }
        break;
    
    case 'todo':
    default:
        // Sin filtro de fecha, contar todos
        break;
}

// Construir query
$sql = "SELECT COUNT(*) as total FROM clientes";
if (!empty($whereConditions)) {
    $sql .= " WHERE " . implode(" AND ", $whereConditions);
}

try {
    $stmt = Conexion::conectar()->prepare($sql);
    
    foreach ($params as $key => $value) {
        if ($key === ':estado') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
    }
    
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Obtener información adicional del periodo
    $infoPeriodo = obtenerInfoPeriodo($periodo, $fechaInicio, $fechaFin);
    
    echo json_encode([
        'status' => 'success',
        'total' => intval($resultado['total']),
        'periodo' => $periodo,
        'descripcion' => $infoPeriodo['descripcion'],
        'rango' => $infoPeriodo['rango']
    ]);
    
} catch (PDOException $e) {
    error_log("Error en conteo_registros.ajax.php: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al obtener el conteo',
        'total' => 0
    ]);
}

/**
 * Obtener información descriptiva del periodo seleccionado
 */
function obtenerInfoPeriodo($periodo, $fechaInicio = null, $fechaFin = null) {
    $descripcion = '';
    $rango = '';
    
    // Array de nombres de meses en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    switch ($periodo) {
        case 'hoy':
            $descripcion = 'Registros de hoy';
            $rango = date('d/m/Y');
            break;
        
        case 'ayer':
            $descripcion = 'Registros de ayer';
            $rango = date('d/m/Y', strtotime('-1 day'));
            break;
        
        case 'esta_semana':
            $descripcion = 'Registros de esta semana';
            $inicio = date('d/m/Y', strtotime('monday this week'));
            $fin = date('d/m/Y', strtotime('sunday this week'));
            $rango = "$inicio - $fin";
            break;
        
        case 'semana_pasada':
            $descripcion = 'Registros de la semana pasada';
            $inicio = date('d/m/Y', strtotime('monday last week'));
            $fin = date('d/m/Y', strtotime('sunday last week'));
            $rango = "$inicio - $fin";
            break;
        
        case 'este_mes':
            $descripcion = 'Registros de este mes';
            $mesNum = intval(date('n'));
            $rango = $meses[$mesNum] . ' ' . date('Y');
            break;
        
        case 'mes_pasado':
            $descripcion = 'Registros del mes pasado';
            $fechaMesPasado = strtotime('first day of last month');
            $mesNum = intval(date('n', $fechaMesPasado));
            $rango = $meses[$mesNum] . ' ' . date('Y', $fechaMesPasado);
            break;
        
        case 'este_ano':
            $descripcion = 'Registros de este año';
            $rango = date('Y');
            break;
        
        case 'ano_pasado':
            $descripcion = 'Registros del año pasado';
            $rango = date('Y', strtotime('-1 year'));
            break;
        
        case 'personalizado':
            $descripcion = 'Periodo personalizado';
            if ($fechaInicio && $fechaFin) {
                $rango = date('d/m/Y', strtotime($fechaInicio)) . ' - ' . date('d/m/Y', strtotime($fechaFin));
            }
            break;
        
        case 'todo':
        default:
            $descripcion = 'Todos los registros';
            $rango = 'Sin filtro de fecha';
            break;
    }
    
    return [
        'descripcion' => $descripcion,
        'rango' => $rango
    ];
}
