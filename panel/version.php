<?php
// Simple version/introspection endpoint for deployment troubleshooting.
header('Content-Type: application/json; charset=utf-8');

$root = realpath(__DIR__ . '/..');
$indexPath = $root . DIRECTORY_SEPARATOR . 'index.php';

$indexExists = is_file($indexPath);
$indexMtime = $indexExists ? filemtime($indexPath) : null;
$indexSize = $indexExists ? filesize($indexPath) : null;

$needle1 = "multitenant/bootstrap.php";
$needle2 = "APP_MODE') && APP_MODE === 'panel";

$indexHasBootstrap = false;
$indexHasPanelSwitch = false;

if ($indexExists) {
    $contents = @file_get_contents($indexPath);
    if (is_string($contents)) {
        $indexHasBootstrap = (strpos($contents, $needle1) !== false);
        $indexHasPanelSwitch = (strpos($contents, $needle2) !== false);
    }
}

echo json_encode([
    'ok' => true,
    'host' => $_SERVER['HTTP_HOST'] ?? null,
    'root' => $root,
    'index' => [
        'exists' => $indexExists,
        'mtime' => $indexMtime ? date('c', $indexMtime) : null,
        'size' => $indexSize,
        'has_multitenant_bootstrap' => $indexHasBootstrap,
        'has_panel_switch' => $indexHasPanelSwitch,
    ],
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
