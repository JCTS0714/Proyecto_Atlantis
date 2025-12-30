<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

try {
    include __DIR__ . '/Ventas/index.php';
} catch (Throwable $e) {
    echo "EXC: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
