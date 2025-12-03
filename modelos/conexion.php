<?php
class Conexion{

    static private function loadEnvFile($path){
        if (!file_exists($path)) return;
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) continue;
            if (strpos($line, '=') === false) continue;
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if ($name === '') continue;
            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
            }
        }
    }

    static public function conectar(){
        // Intentar cargar un archivo .env en la carpeta Ventas o en la raíz del proyecto
        $envLocal = dirname(__FILE__) . '/../.env';
        $envRoot = dirname(__FILE__, 2) . '/.env';
        if (file_exists($envLocal)) {
            self::loadEnvFile($envLocal);
        } else if (file_exists($envRoot)) {
            self::loadEnvFile($envRoot);
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'u652153415_atlantisdb';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $charset = getenv('DB_CHARSET') ?: 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset}"
        ];

        try {
            $link = new PDO($dsn, $user, $pass, $options);
            // Forzar zona horaria de sesión a America/Lima (offset -05:00) para que NOW() y funciones de tiempo usen Lima
            try {
                $link->exec("SET time_zone = '-05:00'");
            } catch (Exception $e) {
                // No detener la conexión si la sesión no puede cambiar la zona; registrar para diagnóstico
                error_log('No se pudo establecer time_zone en la conexión MySQL: ' . $e->getMessage());
            }
            return $link;
        } catch (PDOException $e) {
            // Log detallado en archivo `Ventas/logs` para diagnóstico en producción
            $logDir = __DIR__ . '/../logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0755, true);
            }
            $logFile = $logDir . '/conexion_errors.log';
            error_log("PDOException connecting to DB: " . $e->getMessage() . " | DSN: {$dsn} \n", 3, $logFile);
            // Re-lanzar para comportamiento actual (capturado más arriba por el controlador)
            throw $e;
        }
    }
}