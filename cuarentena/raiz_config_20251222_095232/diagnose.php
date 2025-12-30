<?php
header('Content-Type: text/plain; charset=utf-8');
echo "DIAGNOSTIC REPORT\n";
echo "=================\n";

echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "User: " . (get_current_user() ?: 'unknown') . "\n";

echo "\nSERVER VARS (selected)\n";
foreach (['SERVER_NAME','SERVER_ADDR','SERVER_SOFTWARE','DOCUMENT_ROOT','HTTP_HOST','REQUEST_URI','SCRIPT_NAME','SCRIPT_FILENAME'] as $k) {
    echo sprintf("%s: %s\n", $k, $_SERVER[$k] ?? '(missing)');
}

echo "\nCheck access to index.php content (first 200 chars):\n";
$index = __DIR__ . '/index.php';
if (file_exists($index)) {
    echo substr(file_get_contents($index), 0, 200) . "\n";
} else {
    echo "index.php not found\n";
}

echo "\nList of root files (non-recursive):\n";
$files = scandir(__DIR__);
foreach ($files as $f) {
    if ($f === '.' || $f === '..') continue;
    echo $f . (is_dir(__DIR__ . '/' . $f) ? '/' : '') . "\n";
}

echo "\nIf you get 404 when opening /inicio, check that Apache's mod_rewrite is enabled and AllowOverride is set to All in httpd.conf for this path.\n";

?>
