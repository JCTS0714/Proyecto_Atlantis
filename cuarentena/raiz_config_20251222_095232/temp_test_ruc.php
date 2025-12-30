<?php
require_once __DIR__ . '/modelos/clientes.modelo.php';

$ruc = '12345678901';
$empresa1 = 'Comercio Uno';
$empresa2 = 'Comercio Dos';

$datos1 = [
    'empresa' => $empresa1,
    'nombre' => 'Contacto Uno',
    'telefono' => '999999999',
    'ciudad' => 'Ciudad',
    'tipo' => 'RUC',
    'documento' => $ruc,
    'post_precio' => 100.0,
    'post_rubro' => 'Rubro',
    'post_ano' => 2025,
    'post_mes' => 12,
    'post_link' => '',
    'post_usuario' => '',
    'post_contrasena' => '',
    'servidor' => '',
    'estado' => 2
];

$datos2 = $datos1;
$datos2['empresa'] = $empresa2;

echo "Inserting first record...\n";
$res1 = ModeloCliente::mdlRegistrarClientePostventa($datos1);
var_dump($res1);

echo "Inserting second record (same RUC different empresa)...\n";
$res2 = ModeloCliente::mdlRegistrarClientePostventa($datos2);
var_dump($res2);

// Cleanup: (not doing deletion automatically to avoid removing real data)

