<?php
require_once "modelos/conexion.php";

try {
    $pdo = Conexion::conectar();
    $sql = file_get_contents('scripts/crear_tabla_incidencias.sql');
    $pdo->exec($sql);
    echo "Tabla 'incidencias' creada exitosamente.\n";
} catch (PDOException $e) {
    echo "Error al crear la tabla: " . $e->getMessage() . "\n";
}
?>
