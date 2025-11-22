<?php
require_once "modelos/conexion.php";

function consultarReunionesUsuario($usuario_id) {
    $conexion = Conexion::conectar();
    $sql = "SELECT id, cliente_id, usuario_id, titulo, fecha, hora_inicio, hora_fin, ultima_notificacion, recordatorio 
            FROM reuniones 
            WHERE usuario_id = :usuario_id
            ORDER BY fecha DESC, hora_inicio DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $reuniones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $reuniones;
}

// Cambia este valor por el ID de usuario que quieres consultar
$usuario_id = 27;

$reuniones = consultarReunionesUsuario($usuario_id);

header('Content-Type: application/json');
echo json_encode([
    'usuario_id' => $usuario_id,
    'total_reuniones' => count($reuniones),
    'reuniones' => $reuniones
], JSON_PRETTY_PRINT);
?>
