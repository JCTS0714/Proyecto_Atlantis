<?php
require_once __DIR__ . '/config/paths.php';
header('Content-Type: text/plain; charset=utf-8');
echo "BASE_URL=" . (defined('BASE_URL') ? BASE_URL : '(no definido)') . "\n";
echo "BASE_PATH=" . (defined('BASE_PATH') ? BASE_PATH : '(no definido)') . "\n";
echo "REQUEST_URI=" . ($_SERVER['REQUEST_URI'] ?? '') . "\n";
echo "SCRIPT_NAME=" . ($_SERVER['SCRIPT_NAME'] ?? '') . "\n";
echo "SCRIPT_FILENAME=" . ($_SERVER['SCRIPT_FILENAME'] ?? '') . "\n";
echo "HTTP_HOST=" . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
echo "HTTPS=" . (!empty($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'off') . "\n";

// Check if .htaccess exists and print first lines
$ht = __DIR__ . '/.htaccess';
echo "\.htaccess exists: " . (file_exists($ht) ? 'yes' : 'no') . "\n";
if (file_exists($ht)) {
    echo "--- .htaccess (first 2000 chars) ---\n";
    $content = file_get_contents($ht);
    echo substr($content, 0, 2000) . "\n";
}

// Suggest next step
echo "\nOpen this file from your browser exactly at the path you use for the app, e.g.:\n";
echo "http://localhost/Proyecto_Atlantis/Ventas/test_baseurl.php\n";

?>
