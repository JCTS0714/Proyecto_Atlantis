<?php
// Diagnostic endpoint for the admin panel (temporary).
// Returns JSON without exposing passwords.

header('Content-Type: application/json; charset=utf-8');

$host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$panelHost = strtolower((string)(getenv('PANEL_HOST') ?: 'admin.grupoatlantiscrm.eu'));
$mainHost = strtolower((string)(getenv('MAIN_HOST') ?: ''));

$exists = function(string $relPath): bool {
    return file_exists(__DIR__ . '/..' . $relPath);
};

$mode = ($host === $panelHost) ? 'panel' : 'other';

$out = [
    'ok' => true,
    'host' => $host,
    'env' => [
        'PANEL_HOST' => $panelHost,
        'MAIN_HOST' => $mainHost,
        'DB_HOST' => getenv('DB_HOST') ?: null,
        'DB_NAME' => getenv('DB_NAME') ?: null,
        'DB_USER' => getenv('DB_USER') ?: null,
        'DB_CHARSET' => getenv('DB_CHARSET') ?: null,
    ],
    'mode_guess' => $mode,
    'php' => [
        'version' => PHP_VERSION,
        'pdo_mysql_loaded' => extension_loaded('pdo_mysql'),
    ],
    'files' => [
        '/index.php' => $exists('/index.php'),
        '/multitenant/bootstrap.php' => $exists('/multitenant/bootstrap.php'),
        '/panel/index.php' => $exists('/panel/index.php'),
        '/modelos/conexion.php' => $exists('/modelos/conexion.php'),
    ],
];

try {
    require_once __DIR__ . '/../modelos/conexion.php';
    $pdo = Conexion::conectar();
    $pdo->query('SELECT 1');
    $out['db'] = [
        'connect_ok' => true,
    ];

    // Check master tables exist
    $counts = [];
    foreach (['tenants','tenant_db','admin_users'] as $tbl) {
        try {
            $counts[$tbl] = (int)$pdo->query("SELECT COUNT(*) AS c FROM {$tbl}")->fetch(PDO::FETCH_ASSOC)['c'];
        } catch (Throwable $e) {
            $counts[$tbl] = 'ERR: ' . $e->getMessage();
        }
    }
    $out['db']['counts'] = $counts;

} catch (Throwable $e) {
    http_response_code(500);
    $out['ok'] = false;
    $out['db'] = [
        'connect_ok' => false,
        'error' => $e->getMessage(),
    ];
}

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
