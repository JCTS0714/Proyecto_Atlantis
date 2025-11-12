<?php
require_once __DIR__ . "/../modelos/calendario.modelo.php";

date_default_timezone_set('America/Lima');

$datos = [
    'cliente_id' => 20,
    'usuario_id' => 27,
    'titulo' => 'Reunión de prueba',
    'descripcion' => 'Prueba de notificación',
    'fecha' => date('Y-m-d'),
    'hora_inicio' => date('H:i:s', strtotime('+10 minutes')),
    'hora_fin' => date('H:i:s', strtotime('+40 minutes')),
    'ubicacion' => 'Oficina',
    'estado' => 'Pendiente',
    'recordatorio' => 1,
    'observaciones' => 'Ninguna',
    'evento_id' => 1
];

$resultado = ModeloCalendario::mdlCrearReunion('reuniones', $datos);
echo $resultado;
?>
