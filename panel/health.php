<?php
// Minimal health check for the admin panel.
// Safe output (no credentials). Intended for temporary diagnostics.

require_once __DIR__ . '/../multitenant/bootstrap.php';
require_once __DIR__ . '/../modelos/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$host = $_SERVER['HTTP_HOST'] ?? '';

if (!defined('APP_MODE') || APP_MODE !== 'panel') {
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'not_panel_host', 'host' => $host], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $pdo = Conexion::conectar();
    $stmt = $pdo->query('SELECT 1 AS ok');
    $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

    echo json_encode([
        'ok' => true,
        'app_mode' => APP_MODE,
        'host' => $host,
        'db_ok' => (bool)($row && (int)$row['ok'] === 1),
        'db_name' => getenv('DB_NAME') ?: null,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'app_mode' => APP_MODE,
        'host' => $host,
        'db_ok' => false,
        'error' => $e->getMessage(),
    ], JSON_UNESCAPED_UNICODE);
}
