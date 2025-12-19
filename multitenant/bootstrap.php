<?php
/**
 * Multi-tenant bootstrap (semi-automático):
 * - Dominio principal usa la BD master (actual).
 * - Subdominios usan su propia BD, mapeada en tablas master: tenants + tenant_db.
 * - Panel admin vive en un host dedicado (ej: admin.grupoatlantiscrm.eu) y usa la BD master.
 *
 * Este archivo debe incluirse MUY temprano en index.php (antes de modelos/controladores).
 */

require_once __DIR__ . '/../modelos/conexion.php';

function mt_env($key, $default = '') {
    $val = getenv($key);
    if ($val !== false && $val !== '') return $val;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
    return $default;
}

function mt_normalize_host($hostRaw) {
    $hostRaw = strtolower(trim((string)$hostRaw));
    if ($hostRaw === '') return '';
    // remover puerto si viene (host:port)
    $parts = explode(':', $hostRaw);
    return $parts[0];
}

function mt_parent_domain_from_panel_host($panelHost) {
    $panelHost = mt_normalize_host($panelHost);
    if ($panelHost === '') return '';
    $labels = explode('.', $panelHost);
    if (count($labels) < 3) return ''; // necesita algo.tipo.tld
    array_shift($labels); // quita primer label (admin)
    return implode('.', $labels);
}

function mt_subdomain_for_host($host, $mainHost) {
    $host = mt_normalize_host($host);
    $mainHost = mt_normalize_host($mainHost);
    if ($host === '' || $mainHost === '') return null;
    if ($host === $mainHost) return null;
    $suffix = '.' . $mainHost;
    if (substr($host, -strlen($suffix)) !== $suffix) return null;
    $sub = substr($host, 0, -strlen($suffix));
    if ($sub === '' || strpos($sub, '.') !== false) return null; // solo 1 nivel de subdominio
    return $sub;
}

$host = mt_normalize_host($_SERVER['HTTP_HOST'] ?? '');

// Configuración por entorno (preferir variables de entorno)
$panelHost = mt_normalize_host(mt_env('PANEL_HOST', ''));
if ($panelHost === '') {
    // fallback: el usuario indicó este host
    $panelHost = 'admin.grupoatlantiscrm.eu';
}

$mainHost = mt_normalize_host(mt_env('MAIN_HOST', ''));
if ($mainHost === '') {
    $mainHost = mt_parent_domain_from_panel_host($panelHost);
}

// Normalizar alias www del dominio principal
$mainHostWww = ($mainHost !== '') ? ('www.' . $mainHost) : '';

// Modo PANEL
if ($host !== '' && $panelHost !== '' && $host === $panelHost) {
    if (!defined('APP_MODE')) define('APP_MODE', 'panel');
    // Panel siempre usa BD master (sin override)
    Conexion::setConfigOverride(null);
    return;
}

// Modo PRINCIPAL
if ($host !== '' && ($host === $mainHost || $host === $mainHostWww || $mainHost === '')) {
    if (!defined('APP_MODE')) define('APP_MODE', 'tenant');
    if (!defined('TENANT_SUBDOMAIN')) define('TENANT_SUBDOMAIN', null);
    if (!defined('TENANT_ID')) define('TENANT_ID', null);
    // Dominio principal: BD master actual (sin override)
    Conexion::setConfigOverride(null);
    return;
}

// Modo SUBDOMINIO (TENANT)
$subdomain = mt_subdomain_for_host($host, $mainHost);
if ($subdomain === null) {
    // Host no reconocido: no exponer detalles
    http_response_code(404);
    echo 'Not found';
    exit;
}

// Buscar credenciales del tenant en la BD master
try {
    $masterPdo = Conexion::conectar();
    $sql = "SELECT t.id, t.status, d.db_host, d.db_name, d.db_user, d.db_pass, d.db_charset\n            FROM tenants t\n            JOIN tenant_db d ON d.tenant_id = t.id\n            WHERE t.subdomain = :subdomain\n            LIMIT 1";
    $stmt = $masterPdo->prepare($sql);
    $stmt->bindValue(':subdomain', $subdomain, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo 'Not found';
        exit;
    }

    if ($row['status'] !== 'active') {
        http_response_code(403);
        echo 'Account inactive';
        exit;
    }

    // Aplicar override de conexión para el resto del request
    Conexion::setConfigOverride([
        'host' => $row['db_host'],
        'name' => $row['db_name'],
        'user' => $row['db_user'],
        'pass' => $row['db_pass'],
        'charset' => $row['db_charset'] ?: 'utf8mb4',
    ]);

    if (!defined('APP_MODE')) define('APP_MODE', 'tenant');
    if (!defined('TENANT_SUBDOMAIN')) define('TENANT_SUBDOMAIN', $subdomain);
    if (!defined('TENANT_ID')) define('TENANT_ID', (int)$row['id']);

} catch (Exception $e) {
    error_log('Multi-tenant bootstrap error: ' . $e->getMessage());
    http_response_code(500);
    echo 'Internal server error';
    exit;
}
