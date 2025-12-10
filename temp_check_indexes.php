<?php
$pdo = new PDO('mysql:host=127.0.0.1;dbname=u652153415_atlantisdb;charset=utf8','root','');
// Columns
$stmt = $pdo->query("SHOW CREATE TABLE clientes");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo $row['Create Table'];
