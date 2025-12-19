<?php
header('Content-Type: text/plain; charset=utf-8');
echo "panel ping OK\n";
echo "php=" . PHP_VERSION . "\n";
echo "host=" . ($_SERVER['HTTP_HOST'] ?? '') . "\n";
