<?php
// If this file is placed at the webroot (e.g. public_html) and a `Ventas`
// subfolder exists, forward execution into that folder so the app loads
// correctly without changing all the internal require paths.
$__base_dir_name = strtolower(basename(__DIR__));
if ($__base_dir_name !== 'ventas' && (is_dir(__DIR__ . '/Ventas') || is_dir(__DIR__ . '/ventas')) ) {
    $targetDir = is_dir(__DIR__ . '/Ventas') ? __DIR__ . '/Ventas' : __DIR__ . '/ventas';
    chdir($targetDir);
    require $targetDir . '/index.php';
    exit;
}
// Configurar parámetros de cookie ANTES de iniciar sesión
$cookieLifetime = 30 * 24 * 60 * 60; // 30 días
$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
$httponly = true;

// Use SameSite support when available (PHP 7.3+)
if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => $cookieLifetime,
        'path' => '/',
        'domain' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '',
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax'
    ]);
} else {
    // Fallback for older PHP: set basic params (SameSite may not be supported)
    session_set_cookie_params($cookieLifetime, '/', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '', $secure, $httponly);
}

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Asegurar zona horaria consistente para toda la aplicación
// Si prefieres controlar esto desde php.ini, quita esta línea.
date_default_timezone_set('America/Lima');

/**REQUERIMOS CONFIGURACIÓN */
require_once "config/estados.php";
// Rutas base (detecta entorno y arma BASE_URL)
require_once "config/paths.php";

/**REQUERIMOS DE CONTROLADOR */
require_once "controladores/plantilla.controlador.php";
require_once "controladores/clientes.controlador.php";
require_once "controladores/usuarios.controlador.php";
require_once "controladores/ventas.controlador.php";
require_once "controladores/ControladorOportunidad.php";
require_once "controladores/prospectos.controlador.php";
require_once "controladores/evento.controlador.php";
require_once "controladores/calendario.controlador.php";
require_once "controladores/contador.controlador.php";


/**REQUERIMOS DE MODELOS */
require_once "modelos/clientes.modelo.php";
require_once "modelos/usuarios.modelo.php";
require_once "modelos/ventas.modelo.php";
require_once "modelos/ModeloCRM.php";
require_once "modelos/evento.modelo.php";
require_once "modelos/calendario.modelo.php";
require_once "modelos/contador.modelo.php";



/**CREAMOS EL OBJETO $PLANTILLA QUE HACE INSTANCIA DE LA CLASE ControladoPlantilla Y ACCEDEMOS A SU MÉTODO */

$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();