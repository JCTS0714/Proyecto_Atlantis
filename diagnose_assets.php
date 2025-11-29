<?php
// Diagnostic script to check BASE_URL and common asset files
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/config/paths.php';

echo "BASE_URL=" . (defined('BASE_URL') ? BASE_URL : '(no definido)') . "\n";
echo "BASE_PATH=" . (defined('BASE_PATH') ? BASE_PATH : '(no definido)') . "\n";
echo "REQUEST_URI=" . ($_SERVER['REQUEST_URI'] ?? '') . "\n\n";

$assets = [
    '/vistas/dist/css/AdminLTE.css',
    '/vistas/bower_components/bootstrap/dist/css/bootstrap.min.css',
    '/vistas/bower_components/font-awesome/css/font-awesome.min.css',
    '/vistas/bower_components/jquery/dist/jquery.min.js',
    '/vistas/dist/js/adminlte.min.js'
];

foreach ($assets as $asset) {
    $fsPath = __DIR__ . $asset;
    echo "Asset: " . $asset . "\n";
    echo "  Filesystem path: " . $fsPath . "\n";
    if (file_exists($fsPath)) {
        echo "  Exists: yes\n";
        echo "  Readable: " . (is_readable($fsPath) ? 'yes' : 'no') . "\n";
        echo "  Size: " . filesize($fsPath) . " bytes\n";
        $perms = substr(sprintf('%o', fileperms($fsPath)), -4);
        echo "  Perms: " . $perms . "\n";
    } else {
        echo "  Exists: NO\n";
    }
    echo "\n";
}

// Suggest checking these URLs from browser
echo "\nCheck these URLs from your browser (Network tab) to find 404s:\n";
foreach ($assets as $asset) {
    echo "  " . (defined('BASE_URL') ? rtrim(BASE_URL, '/') : '') . $asset . "\n";
}

echo "\nIf files exist on disk but browser reports 404, ensure .htaccess rules allow serving existing files, and that Apache has 'AllowOverride All' and 'mod_rewrite' enabled.\n";
?>