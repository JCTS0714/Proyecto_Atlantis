<?php
/**
 * Configuración de rutas base para entornos locales/producción.
 * Define BASE_URL que apunta al esquema + host + carpeta del script.
 */
// Determinar si estamos en un entorno local según variables de entorno
// Prefer explicit config file if present (not committed to repo). This allows
// deploying teams to set production parameters in `config/production.php`.
$productionConfig = [];
// Load production config if present (not committed)
if (file_exists(__DIR__ . '/production.php')) {
	$productionConfig = include __DIR__ . '/production.php';
}
// Load local config for testing if present (safe to commit)
$localConfig = [];
if (file_exists(__DIR__ . '/local.php')) {
	$localConfig = include __DIR__ . '/local.php';
}
// Merge: local overrides production for local testing
$config = array_merge($productionConfig ?? [], $localConfig ?? []);

$appEnv = $config['APP_ENV'] ?? getenv('APP_ENV') ?: null;
$forceHttp = $config['FORCE_HTTP'] ?? getenv('FORCE_HTTP');

// Forzar protocolo HTTP en desarrollo/local si se solicita
if ($appEnv === 'local' || $forceHttp == '1') {
	$protocol = 'http';
	// Priorizar configuración explícita en config/local.php (clave BASE_HOST)
	$host = $config['BASE_HOST'] ?? getenv('BASE_HOST') ?: (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
} else {
	// Detectar protocolo (producción)
	$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
	$protocol = $isHttps ? 'https' : 'http';
	// Host (incluye puerto si aplica)
	$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
}

// Path base (carpeta donde está index.php), útil para entornos locales
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
// Normalize scriptDir returned by some environments ('.' or '/')
if ($scriptDir === '.' || $scriptDir === '\\' || $scriptDir === '/') {
	$scriptDir = '';
}

// Normalize host: trim whitespace and remove accidental trailing dots
$host = is_string($host) ? trim($host) : $host;
if (is_string($host)) {
	$host = rtrim($host, '.');
}

// Construir BASE_URL sin slash final (ej: http://localhost/Proyecto_Atlantis/Ventas)
$base = $protocol . '://' . $host;
if ($scriptDir !== '') {
	// scriptDir already contains leading slash from dirname(); append directly
	$base .= $scriptDir;
}
// Ensure no trailing slash
define('BASE_URL', rtrim($base, '/'));

// Opcional: ruta absoluta del filesystem a la raíz del proyecto
define('BASE_PATH', rtrim(str_replace('\\', '/', dirname(__FILE__)), '/'));

// Disable display errors by default in production if configured
if (isset($productionConfig['display_errors'])) {
	ini_set('display_errors', $productionConfig['display_errors'] ? '1' : '0');
	error_reporting($productionConfig['error_reporting'] ?? E_ALL);
}

// Provide FORCE_RELOAD_ON_UPDATE global flag for frontend scripts
if (!defined('FORCE_RELOAD_ON_UPDATE')) {
	$forceReload = $productionConfig['FORCE_RELOAD_ON_UPDATE'] ?? (getenv('FORCE_RELOAD_ON_UPDATE') ?: false);
	define('FORCE_RELOAD_ON_UPDATE', $forceReload);
}

?>
