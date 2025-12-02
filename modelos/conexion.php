<?php
/**
 * Clase de conexión a base de datos con patrón Singleton
 * Evita múltiples conexiones innecesarias por request
 */
class Conexion{

    // Instancia única de la conexión (Singleton)
    static private $conexion = null;
    static private $envLoaded = false;

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
        // Si ya existe una conexión activa, reutilizarla (Singleton)
        if (self::$conexion !== null) {
            return self::$conexion;
        }

        // Cargar variables de entorno solo una vez
        if (!self::$envLoaded) {
            $envLocal = dirname(__FILE__) . '/../.env';
            $envRoot = dirname(__FILE__, 2) . '/.env';
            if (file_exists($envLocal)) {
                self::loadEnvFile($envLocal);
            } else if (file_exists($envRoot)) {
                self::loadEnvFile($envRoot);
            }
            self::$envLoaded = true;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $name = getenv('DB_NAME') ?: 'atlantisbd';
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
            self::$conexion = new PDO($dsn, $user, $pass, $options);
            // Forzar zona horaria de sesión a America/Lima (offset -05:00) para que NOW() y funciones de tiempo usen Lima
            try {
                self::$conexion->exec("SET time_zone = '-05:00'");
            } catch (Exception $e) {
                // No detener la conexión si la sesión no puede cambiar la zona; registrar para diagnóstico
                error_log('No se pudo establecer time_zone en la conexión MySQL: ' . $e->getMessage());
            }
            return self::$conexion;
        } catch (PDOException $e) {
            // Log detallado en archivo `logs` para diagnóstico en producción
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

    /**
     * Cerrar la conexión y resetear el singleton
     * Útil para tests o cuando se necesita reconectar
     */
    static public function cerrar(){
        self::$conexion = null;
    }
}