<?php
require_once 'modelos/conexion.php';

$conn = Conexion::conectar();

echo "=== TABLAS EN LA BASE DE DATOS ===\n\n";

// Obtener tablas
$sql = "SHOW TABLES FROM atlantisbd";
$result = $conn->query($sql);
$tables = [];
while($row = $result->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
    echo "• " . $row[0] . "\n";
}

echo "\n=== ESTRUCTURA DE TABLAS RELACIONADAS A CLIENTES Y OPORTUNIDADES ===\n\n";

// Analizar estructura de tabla 'clientes'
echo "--- TABLA: clientes ---\n";
$sql = "DESCRIBE clientes";
$result = $conn->query($sql);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("  %-20s %-15s %-10s %-10s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Key'] ?? '', 
        $row['Extra'] ?? ''
    );
}

// Analizar estructura de tabla 'oportunidades'
echo "\n--- TABLA: oportunidades ---\n";
$sql = "DESCRIBE oportunidades";
$result = $conn->query($sql);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo sprintf("  %-20s %-15s %-10s %-10s\n", 
        $row['Field'], 
        $row['Type'], 
        $row['Key'] ?? '', 
        $row['Extra'] ?? ''
    );
}

echo "\n=== RESTRICCIONES DE CLAVE FORÁNEA ===\n\n";

// Obtener información de restricciones de clave foránea
$sql = "SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'atlantisbd' 
AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME";

$result = $conn->query($sql);
$hasFK = false;
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $hasFK = true;
    echo "Tabla: " . $row['TABLE_NAME'] . "\n";
    echo "  Columna: " . $row['COLUMN_NAME'] . "\n";
    echo "  Referencia: " . $row['REFERENCED_TABLE_NAME'] . "(" . $row['REFERENCED_COLUMN_NAME'] . ")\n";
    echo "  Constraint: " . $row['CONSTRAINT_NAME'] . "\n\n";
}

if(!$hasFK) {
    echo "No hay restricciones de clave foránea definidas en la base de datos.\n";
}

echo "\n=== DATOS EN TABLA CLIENTES (estado = 1 / seguimiento) ===\n\n";
$sql = "SELECT id, nombre, estado FROM clientes WHERE estado = 1 LIMIT 5";
$result = $conn->query($sql);
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Nombre: " . $row['nombre'] . " | Estado: " . $row['estado'] . "\n";
}

echo "\n=== OPORTUNIDADES PARA CLIENTE 71 ===\n\n";
$sql = "SELECT id, titulo, cliente_id FROM oportunidades WHERE cliente_id = 71";
$result = $conn->query($sql);
$count = 0;
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . " | Titulo: " . $row['titulo'] . " | Cliente: " . $row['cliente_id'] . "\n";
    $count++;
}
echo "Total: " . $count . " oportunidades\n";
?>
