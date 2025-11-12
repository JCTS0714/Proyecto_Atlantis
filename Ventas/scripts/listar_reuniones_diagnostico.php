<?php
require_once __DIR__ . '/../modelos/calendario.modelo.php';

$tabla = "reuniones";

try {
    $conexion = Conexion::conectar();
    $sql = "SELECT r.id, r.titulo, r.fecha, r.hora_inicio, r.hora_fin, r.ultima_notificacion, r.recordatorio, c.nombre AS nombre_cliente
            FROM $tabla r
            LEFT JOIN clientes c ON r.cliente_id = c.id
            ORDER BY r.fecha ASC, r.hora_inicio ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Listado completo de reuniones para diagnóstico:\n";
    foreach ($reuniones as $r) {
        echo "ID: {$r['id']}, Título: {$r['titulo']}, Fecha: {$r['fecha']}, Hora inicio: {$r['hora_inicio']}, Hora fin: {$r['hora_fin']}, Última notificación: {$r['ultima_notificacion']}, Recordatorio: {$r['recordatorio']}, Cliente: {$r['nombre_cliente']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
