<?php
/**
 * Configuración de rutas base para entornos locales/producción.
 * Define BASE_URL que apunta al esquema + host + carpeta del script.
 */
// Determinar si estamos en un entorno local según variables de entorno
$appEnv = getenv('APP_ENV') ?: null;
$forceHttp = getenv('FORCE_HTTP');

// Forzar protocolo HTTP en desarrollo/local si se solicita
if ($appEnv === 'local' || $forceHttp == '1') {
	$protocol = 'http';
	$host = getenv('BASE_HOST') ?: (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
} else {
	// Detectar protocolo (producción)
	$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
	$protocol = $isHttps ? 'https' : 'http';
	// Host (incluye puerto si aplica)
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
}

// Path base (carpeta donde está index.php), útil para entornos locales
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');

// Construir BASE_URL sin slash final (ej: http://localhost/Proyecto_Atlantis/Ventas)
define('BASE_URL', $protocol . '://' . $host . $scriptDir);

// Opcional: ruta absoluta del filesystem a la raíz del proyecto
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/'));

?>
