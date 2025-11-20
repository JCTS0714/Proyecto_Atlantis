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
            $link = new PDO($dsn, $user, $pass, $options);
            return $link;
        } catch (PDOException $e) {
            // Lanzar la excepción para que el flujo superior la maneje.
            throw $e;
        }
    }
}