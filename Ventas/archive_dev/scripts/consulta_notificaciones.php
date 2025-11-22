<?php
require_once __DIR__ . '/../modelos/calendario.modelo.php';

$tabla = "reuniones";
$usuario_id = 27; // Cambia este valor por el id de usuario que quieras probar

$hoy = date('Y-m-d');

try {
    $conexion = Conexion::conectar();
    $sql = "SELECT r.*, c.nombre AS nombre_cliente, CONCAT(r.fecha, ' ', r.hora_inicio) AS fecha_hora 
            FROM $tabla r 
            LEFT JOIN clientes c ON r.cliente_id = c.id 
            WHERE r.usuario_id = :usuario_id 
            AND r.fecha BETWEEN :hoy AND DATE_ADD(:hoy, INTERVAL 3 DAY)
            AND (r.ultima_notificacion IS NULL OR r.ultima_notificacion <> :hoy)
            AND CONCAT(r.fecha, ' ', r.hora_fin) >= NOW()";

    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->bindParam(':hoy', $hoy, PDO::PARAM_STR);
    $stmt->execute();

    $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Reuniones que cumplen requisitos para notificación:\n";
    foreach ($reuniones as $r) {
        echo "ID: {$r['id']}, Título: {$r['titulo']}, Fecha: {$r['fecha']}, Hora inicio: {$r['hora_inicio']}, Hora fin: {$r['hora_fin']}, Última notificación: {$r['ultima_notificacion']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
