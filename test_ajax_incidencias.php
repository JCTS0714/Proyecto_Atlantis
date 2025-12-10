<?php
// Simular llamada AJAX
$_GET['action'] = 'mostrarIncidencias';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Capturar la salida
ob_start();
include 'ajax/incidencias.ajax.php';
$output = ob_get_clean();

// Mostrar resultado
header('Content-Type: application/json');
echo $output;
