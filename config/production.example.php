<?php
/**
 * Production environment example configuration
 * Copy this file to `config/production.php` and fill values.
 * Keep `production.php` out of source control.
 */
return [
    // Environment
    'APP_ENV' => 'production',

    // Error reporting
    // In production: disable display_errors and log errors to file
    'display_errors' => false,
    'error_reporting' => E_ALL & ~E_DEPRECATED & ~E_STRICT,
    'log_errors' => true,
    'error_log' => __DIR__ . '/../logs/php_errors.log',

    // Force reload behavior (avoid forced reloads in production)
    'FORCE_RELOAD_ON_UPDATE' => false,

    // Base host override (optional). Leave null to detect dynamically.
    'BASE_HOST' => null,

    // Force HTTP (not recommended) - set to true only for special cases
    'FORCE_HTTP' => false,

    // Database: keep empty here. Set in a secure `production.php` not committed.
    'DB' => [
        'host' => '',
        'name' => '',
        'user' => '',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],

    // Other production flags
    'SESSION_SECURE_ONLY' => true, // require secure cookies when using HTTPS
];
