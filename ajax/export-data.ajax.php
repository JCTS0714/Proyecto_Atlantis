<?php
/**
 * =====================================================
 * ENDPOINT DE EXPORTACIÓN DE DATOS COMPLETOS
 * =====================================================
 * 
 * Versión: 1.0.0
 * Fecha: 2025-12-10
 * 
 * Proporciona todos los datos de una tabla para exportación
 * (sin paginación) respetando filtros aplicados.
 * 
 * Usado por export-tables.js para tablas con server-side processing
 */

// Configuración de zona horaria y manejo de errores
require_once __DIR__ . '/_timezone.php';
require_once __DIR__ . '/_error_handler.php';

// Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Validar que el usuario esté autenticado
if (!isset($_SESSION['iniciarSesion']) || $_SESSION['iniciarSesion'] !== 'ok') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'error' => 'No autorizado. Debe iniciar sesión.',
        'data' => []
    ]);
    exit;
}

// Cargar modelos necesarios
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/clientes.modelo.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Obtener parámetros de la solicitud
    $tabla = $_POST['tabla'] ?? '';
    $filters = $_POST['filters'] ?? [];
    $search = $_POST['search'] ?? '';

    // Log para debugging
    error_log('[export-data] Tabla: ' . $tabla);
    error_log('[export-data] Filtros: ' . json_encode($filters));
    error_log('[export-data] Búsqueda: ' . $search);

    // Validar tabla solicitada
    $tablasPermitidas = [
        'tablaClientes',
        'tablaSeguimiento', 
        'tablaNoClientes',
        'tablaZonaEspera',
        'example2',
        'tablaProspectos',
        'tablaContadores',
        'tablaIncidencias',
        'tablaUsuarios'
    ];

    if (!in_array($tabla, $tablasPermitidas)) {
        throw new Exception('Tabla no válida o no permitida');
    }

    // Obtener datos según la tabla solicitada
    $data = [];
    
    switch ($tabla) {
        case 'tablaClientes':
            $data = exportarClientes($filters, $search);
            break;
            
        case 'tablaSeguimiento':
            $data = exportarSeguimiento($filters, $search);
            break;
            
        case 'tablaNoClientes':
            $data = exportarNoClientes($filters, $search);
            break;
            
        case 'tablaZonaEspera':
            $data = exportarZonaEspera($filters, $search);
            break;
            
        case 'example2':
        case 'tablaProspectos':
            $data = exportarProspectos($filters, $search);
            break;
            
        case 'tablaContadores':
            $data = exportarContadores($filters, $search);
            break;
            
        case 'tablaIncidencias':
            $data = exportarIncidencias($filters, $search);
            break;
            
        case 'tablaUsuarios':
            $data = exportarUsuarios($filters, $search);
            break;
            
        default:
            throw new Exception('Función de exportación no implementada para esta tabla');
    }

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'data' => $data,
        'recordsTotal' => count($data)
    ]);

} catch (Exception $e) {
    error_log('[export-data] ERROR: ' . $e->getMessage());
    echo json_encode([
        'error' => $e->getMessage(),
        'data' => []
    ]);
}

        // Ensure multi-tenant bootstrap runs before models/connections
        require_once __DIR__ . '/../multitenant/bootstrap.php';

/**
 * =====================================================
 * FUNCIONES DE EXPORTACIÓN POR TABLA
 * =====================================================
 */

/**
 * Exportar datos de Clientes (Oportunidades)
 */
function exportarClientes($filters, $search) {
    $pdo = Conexion::conectar();
    
    // Query base
    $sql = "SELECT 
                c.id,
                c.comercio,
                c.contacto,
                c.celular,
                c.ciudad,
                c.precio,
                c.ruc,
                c.rubro,
                c.anio_facturacion,
                c.mes_facturacion,
                c.link_sistema,
                c.usuario_sistema,
                c.servidor,
                c.fecha_creacion,
                c.fecha_ultimo_contacto,
                c.estado,
                u.nombre as nombre_usuario
            FROM clientes c
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.estado = 'Cliente'";
    
    $params = [];
    
    // Aplicar filtros de búsqueda avanzada
    if (!empty($filters['nombre'])) {
        $sql .= " AND (c.comercio LIKE :nombre OR c.contacto LIKE :nombre2)";
        $params[':nombre'] = '%' . $filters['nombre'] . '%';
        $params[':nombre2'] = '%' . $filters['nombre'] . '%';
    }
    
    if (!empty($filters['documento'])) {
        $sql .= " AND c.ruc LIKE :documento";
        $params[':documento'] = '%' . $filters['documento'] . '%';
    }
    
    if (!empty($filters['telefono'])) {
        $sql .= " AND c.celular LIKE :telefono";
        $params[':telefono'] = '%' . $filters['telefono'] . '%';
    }
    
    if (!empty($filters['fecha_inicio']) && !empty($filters['fecha_fin'])) {
        $sql .= " AND DATE(c.fecha_creacion) BETWEEN :fecha_inicio AND :fecha_fin";
        $params[':fecha_inicio'] = $filters['fecha_inicio'];
        $params[':fecha_fin'] = $filters['fecha_fin'];
    }
    
    // Búsqueda global
    if (!empty($search)) {
        $sql .= " AND (c.comercio LIKE :search OR c.contacto LIKE :search2 OR c.celular LIKE :search3 OR c.ruc LIKE :search4)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
        $params[':search3'] = '%' . $search . '%';
        $params[':search4'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatear datos para exportación
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['comercio'] ?? '',
            $row['contacto'] ?? '',
            $row['celular'] ?? '',
            $row['ciudad'] ?? '',
            $row['precio'] ?? '',
            $row['ruc'] ?? '',
            $row['rubro'] ?? '',
            $row['anio_facturacion'] ?? '',
            $row['mes_facturacion'] ?? '',
            $row['link_sistema'] ?? '',
            $row['usuario_sistema'] ?? '',
            $row['servidor'] ?? '',
            $row['fecha_creacion'] ?? '',
            $row['fecha_ultimo_contacto'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Seguimiento
 */
function exportarSeguimiento($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                c.id,
                c.comercio,
                c.contacto,
                c.celular,
                c.observacion,
                c.ciudad,
                c.estado,
                c.fecha_creacion,
                u.nombre as nombre_usuario
            FROM clientes c
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.estado = 'Seguimiento'";
    
    $params = [];
    
    // Aplicar filtros similares a Clientes
    if (!empty($filters['nombre'])) {
        $sql .= " AND (c.comercio LIKE :nombre OR c.contacto LIKE :nombre2)";
        $params[':nombre'] = '%' . $filters['nombre'] . '%';
        $params[':nombre2'] = '%' . $filters['nombre'] . '%';
    }
    
    if (!empty($search)) {
        $sql .= " AND (c.comercio LIKE :search OR c.contacto LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['comercio'] ?? '',
            $row['contacto'] ?? '',
            $row['celular'] ?? '',
            $row['observacion'] ?? '',
            $row['ciudad'] ?? '',
            $row['fecha_creacion'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de No Clientes
 */
function exportarNoClientes($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                c.id,
                c.comercio,
                c.contacto,
                c.celular,
                c.observacion,
                c.motivo_no_cliente,
                c.ciudad,
                c.fecha_creacion,
                u.nombre as nombre_usuario
            FROM clientes c
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.estado = 'No Cliente'";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (c.comercio LIKE :search OR c.contacto LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['comercio'] ?? '',
            $row['contacto'] ?? '',
            $row['celular'] ?? '',
            $row['observacion'] ?? '',
            $row['motivo_no_cliente'] ?? '',
            $row['ciudad'] ?? '',
            $row['fecha_creacion'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Zona de Espera
 */
function exportarZonaEspera($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                c.id,
                c.comercio,
                c.contacto,
                c.celular,
                c.observacion,
                c.ciudad,
                c.fecha_creacion,
                u.nombre as nombre_usuario
            FROM clientes c
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.estado = 'Zona de Espera'";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (c.comercio LIKE :search OR c.contacto LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['comercio'] ?? '',
            $row['contacto'] ?? '',
            $row['celular'] ?? '',
            $row['observacion'] ?? '',
            $row['ciudad'] ?? '',
            $row['fecha_creacion'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Prospectos
 */
function exportarProspectos($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                c.id,
                c.comercio,
                c.contacto,
                c.celular,
                c.observacion,
                c.ciudad,
                c.fecha_creacion,
                u.nombre as nombre_usuario
            FROM clientes c
            LEFT JOIN usuarios u ON c.id_usuario = u.id
            WHERE c.estado = 'Prospecto'";
    
    $params = [];
    
    if (!empty($search)) {
        $sql .= " AND (c.comercio LIKE :search OR c.contacto LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    $sql .= " ORDER BY c.fecha_creacion DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['comercio'] ?? '',
            $row['contacto'] ?? '',
            $row['celular'] ?? '',
            $row['observacion'] ?? '',
            $row['ciudad'] ?? '',
            $row['fecha_creacion'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Contadores
 */
function exportarContadores($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                c.id,
                c.nro,
                c.comercio,
                c.nombre_contador,
                c.telefono,
                u.nombre as nombre_usuario
            FROM contadores c
            LEFT JOIN usuarios u ON c.id_usuario = u.id";
    
    $params = [];
    $where = [];
    
    if (!empty($search)) {
        $where[] = "(c.comercio LIKE :search OR c.nombre_contador LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY c.nro DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['nro'] ?? '',
            $row['comercio'] ?? '',
            $row['nombre_contador'] ?? '',
            $row['telefono'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Incidencias
 */
function exportarIncidencias($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                i.id,
                i.correlativo,
                i.nombre_incidencia,
                i.descripcion,
                i.estado,
                i.prioridad,
                i.fecha_reporte,
                u.nombre as nombre_usuario
            FROM incidencias i
            LEFT JOIN usuarios u ON i.id_usuario = u.id";
    
    $params = [];
    $where = [];
    
    if (!empty($search)) {
        $where[] = "(i.nombre_incidencia LIKE :search OR i.descripcion LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY i.fecha_reporte DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['correlativo'] ?? '',
            $row['nombre_incidencia'] ?? '',
            $row['descripcion'] ?? '',
            $row['estado'] ?? '',
            $row['prioridad'] ?? '',
            $row['fecha_reporte'] ?? '',
            $row['nombre_usuario'] ?? ''
        ];
    }
    
    return $data;
}

/**
 * Exportar datos de Usuarios
 */
function exportarUsuarios($filters, $search) {
    $pdo = Conexion::conectar();
    
    $sql = "SELECT 
                id,
                nombre,
                usuario,
                perfil,
                foto,
                estado,
                ultimo_login,
                fecha
            FROM usuarios";
    
    $params = [];
    $where = [];
    
    if (!empty($search)) {
        $where[] = "(nombre LIKE :search OR usuario LIKE :search2)";
        $params[':search'] = '%' . $search . '%';
        $params[':search2'] = '%' . $search . '%';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    
    $sql .= " ORDER BY fecha DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $contador = 1;
    foreach ($resultados as $row) {
        $data[] = [
            $contador++,
            $row['nombre'] ?? '',
            $row['usuario'] ?? '',
            $row['perfil'] ?? '',
            $row['estado'] ?? '',
            $row['ultimo_login'] ?? '',
            $row['fecha'] ?? ''
        ];
    }
    
    return $data;
}
