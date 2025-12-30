<?php
// Tenant health check (safe JSON) - helps debug blank pages per subdomain
header('Content-Type: application/json; charset=utf-8');

$host = $_SERVER['HTTP_HOST'] ?? '';

try {
    require_once __DIR__ . '/modelos/conexion.php';
    require_once __DIR__ . '/multitenant/bootstrap.php';

    $pdo = Conexion::conectar();
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();

    echo json_encode([
        'ok' => true,
        'host' => $host,
        'app_mode' => defined('APP_MODE') ? APP_MODE : null,
        'tenant_id' => defined('TENANT_ID') ? TENANT_ID : null,
        'tenant_subdomain' => defined('TENANT_SUBDOMAIN') ? TENANT_SUBDOMAIN : null,
        'db_current' => $dbName,
        'env_seen' => [
            'DB_HOST' => isset($_ENV['DB_HOST']) ? '[set]' : null,
            'DB_NAME' => isset($_ENV['DB_NAME']) ? '[set]' : null,
            'DB_USER' => isset($_ENV['DB_USER']) ? '[set]' : null,
            'DB_PASS' => isset($_ENV['DB_PASS']) ? '[set]' : null,
            'PANEL_HOST' => isset($_ENV['PANEL_HOST']) ? $_ENV['PANEL_HOST'] : null,
            'MAIN_HOST' => isset($_ENV['MAIN_HOST']) ? $_ENV['MAIN_HOST'] : null,
        ],
        'php' => PHP_VERSION,
        'time' => date('c'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'host' => $host,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'php' => PHP_VERSION,
        'time' => date('c'),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
