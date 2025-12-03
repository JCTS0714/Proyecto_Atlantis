<?php
// Entry point for the application.
// This index assumes the application files (ajax, vistas, modelos, controladores, config, etc.)
// are located in the same directory as this file (deployment root).
// Ensure this file is the single entrypoint used in production; do not change
// working directory at runtime (avoid `chdir()` wrappers that break predictable paths).
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

// --- Runtime diagnostics & robust error logging (safe for production) ---
// Ensure a writable logs directory exists so we can capture fatal errors.
$logDir = __DIR__ . '/logs';
if (!is_dir($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/php_error_production.log';

// Register handlers to capture and persist errors to file (does not expose details to users)
if (!defined('APP_ERROR_HANDLER_INSTALLED')) {
    define('APP_ERROR_HANDLER_INSTALLED', true);

    set_exception_handler(function($e) use ($logFile) {
        error_log("Uncaught Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", 3, $logFile);
        // Solo mostrar error si no se ha enviado contenido HTML completo
        if (!headers_sent() && ob_get_level() === 0) {
            http_response_code(500);
            echo "Internal server error";
        }
        // No usar exit si la página ya terminó de renderizar
    });

    set_error_handler(function($severity, $message, $file, $line) use ($logFile) {
        // Ignorar errores de tipo Notice, Deprecated y Strict (no son críticos)
        $ignoredSeverities = [E_NOTICE, E_STRICT, E_DEPRECATED, E_USER_NOTICE, E_USER_DEPRECATED];
        if (in_array($severity, $ignoredSeverities)) {
            // Solo loguear sin lanzar excepción
            $msg = "PHP Notice/Deprecated: {$message} in {$file} on line {$line}";
            error_log($msg . "\n", 3, $logFile);
            return true; // Continuar ejecución normal
        }
        
        $msg = "PHP Error: {$message} in {$file} on line {$line}";
        error_log($msg . "\n", 3, $logFile);
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    register_shutdown_function(function() use ($logFile) {
        $err = error_get_last();
        // Solo loguear errores fatales reales (no notices ni warnings)
        if ($err && in_array($err['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            error_log("Shutdown Fatal: " . print_r($err, true) . "\n", 3, $logFile);
        }
    });
}


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

/**
 * PROCESAR LOGIN ANTES DE LA PLANTILLA
 * Esto permite usar header() para redirección ya que no se ha enviado HTML
 */
if (isset($_POST["ingUsuario"]) && isset($_POST["ingPassword"])) {
    $login = new ControladorUsuarios();
    $login->ctrIngresoUsuario();
    // Si el login fue exitoso, el controlador hace exit después de redirect
    // Si llegamos aquí, hubo error y continuamos mostrando el formulario
}

/**CREAMOS EL OBJETO $PLANTILLA QUE HACE INSTANCIA DE LA CLASE ControladoPlantilla Y ACCEDEMOS A SU MÉTODO */

$plantilla = new ControladorPlantilla();
$plantilla->ctrPlantilla();