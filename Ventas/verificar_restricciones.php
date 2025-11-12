<?php
require_once 'modelos/conexion.php';

$conn = Conexion::conectar();
$clienteId = 71;

echo "=== ANÁLISIS DE DEPENDENCIAS PARA CLIENTE $clienteId ===\n\n";

// Verificar en cada tabla relacionada
$tablas = [
    'actividades' => 'cliente_id',
    'incidencias' => 'cliente_id', 
    'oportunidades' => 'cliente_id',
    'reuniones' => 'cliente_id'
];

$tieneRestriccion = false;

foreach($tablas as $tabla => $columna) {
    $sql = "SELECT COUNT(*) as count FROM $tabla WHERE $columna = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'];
    
    echo "Tabla: $tabla\n";
    echo "  Registros con cliente_id = $clienteId: $count\n";
    
    if($count > 0) {
        $tieneRestriccion = true;
        echo "  ⚠️ RESTRICCIÓN: No se puede eliminar el cliente\n";
        
        // Mostrar los registros
        $sql2 = "SELECT * FROM $tabla WHERE $columna = :id";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(':id', $clienteId, PDO::PARAM_INT);
        $stmt2->execute();
        while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            echo "    - Registro ID: " . $row['id'] . "\n";
        }
    } else {
        echo "  ✅ OK: No hay dependencias\n";
    }
    echo "\n";
}

if(!$tieneRestriccion) {
    echo "\n=== RESULTADO: El cliente $clienteId PUEDE ser eliminado ===\n";
} else {
    echo "\n=== RESULTADO: El cliente $clienteId TIENE RESTRICCIONES ===\n";
}

// Ahora probar la eliminación real
echo "\n=== INTENTANDO ELIMINAR CLIENTE $clienteId ===\n\n";

try {
    $sql = "DELETE FROM clientes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $clienteId, PDO::PARAM_INT);
    
    if($stmt->execute()) {
        echo "✅ DELETE ejecutado exitosamente\n";
        echo "Filas afectadas: " . $stmt->rowCount() . "\n";
    }
} catch(PDOException $e) {
    echo "❌ ERROR en DELETE:\n";
    echo "Código: " . $e->getCode() . "\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    
    // Revertir si hay transacción
    if($conn->inTransaction()) {
        $conn->rollBack();
    }
}
?>
