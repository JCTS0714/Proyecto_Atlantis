<?php
require_once __DIR__ . '/../../modelos/conexion.php';
$pdo = Conexion::conectar();
// Columns
$stmt = $pdo->query("SHOW CREATE TABLE clientes");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['Create Table'];
