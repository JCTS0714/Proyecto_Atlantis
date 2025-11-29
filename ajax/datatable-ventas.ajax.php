<?php
// Productos removed — return empty dataset to keep datatable working without producto endpoints
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['data' => []]);

