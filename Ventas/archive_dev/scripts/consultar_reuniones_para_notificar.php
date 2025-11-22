<?php
require_once __DIR__ . '/../modelos/calendario.modelo.php';

$tabla = "reuniones";

// Consultar reuniones que deben notificarse: fecha y hora_fin no pasadas
try {
    $conexion = Conexion::conectar();
    $stmt = $conexion->prepare("
        SELECT r.*, e.color 
        FROM $tabla r 
        LEFT JOIN evento e ON r.evento_id = e.id
        WHERE CONCAT(r.fecha, ' ', r.hora_fin) >= NOW()
        ORDER BY r.fecha ASC, r.hora_inicio ASC
    ");
    $stmt->execute();
    $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "total" => count($reuniones),
        "reuniones" => $reuniones
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
