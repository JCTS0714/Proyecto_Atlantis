<?php
// Temporary diagnostic page - remove after debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

echo "<h1>STATUS: Diagnóstico rápido</h1>\n";
echo "<p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>\n";

echo "<h2>PHP</h2>\n";
echo "<p>PHP Version: " . phpversion() . "</p>\n";

echo "<h2>Entorno servidor</h2>\n";
echo "<pre>REQUEST_URI: " . htmlspecialchars(
    (isset(
        
        
        
        
        $_SERVER['REQUEST_URI']
    ) ? $_SERVER['REQUEST_URI'] : ''), ENT_QUOTES) . "\n";
print_r(array_intersect_key($_SERVER, array_flip(['SERVER_NAME','SERVER_ADDR','SERVER_SOFTWARE','DOCUMENT_ROOT','HTTP_HOST','HTTP_USER_AGENT','REMOTE_ADDR'])));
echo "</pre>\n";

echo "<h2>Archivos clave</h2>\n";
$files = [
    __DIR__ . '/index.php',
    __DIR__ . '/.htaccess',
    __DIR__ . '/vistas/plantilla.php'
];
foreach ($files as $f) {
    echo '<p>' . htmlspecialchars($f) . ': ' . (file_exists($f) ? '<strong style="color:green">OK</strong>' : '<strong style="color:red">NO ENCONTRADO</strong>') . '</p>';
}

echo "<h2>phpinfo()</h2>\n";
// Show a reduced phpinfo to avoid huge output in some hosts
if (function_exists('phpinfo')) {
    phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
} else {
    echo "<p>phpinfo() no disponible.</p>\n";
}

echo "<hr><p>Nota: Este archivo es temporal. Elimina `Ventas/status.php` cuando termines.</p>\n";
?>