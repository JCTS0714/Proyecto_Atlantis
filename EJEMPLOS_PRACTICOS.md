# EJEMPLOS PRÁCTICOS DE CORRECCIONES

**Documento de Referencia para Implementación**  
**Fecha:** 11 de Noviembre de 2025

---

## 📚 Tabla de Contenidos

1. Archivos Helper a Crear
2. Ejemplos de Corrección por Error
3. Scripts de Migración
4. Pruebas Recomendadas

---

## 1️⃣ ARCHIVOS HELPER A CREAR

### Archivo 1: `includes/config.php`

```php
<?php
/**
 * Configuración centralizada del proyecto
 */

// Cargar variables de entorno
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

// Incluir dotenv si está disponible
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Configuración de Base de Datos
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'atlantisbd'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', ''));

// Configuración de Aplicación
define('APP_ENV', env('APP_ENV', 'development'));
define('APP_DEBUG', env('APP_DEBUG', 'true') === 'true');
define('APP_URL', env('APP_URL', 'http://localhost'));

// Zona horaria
date_default_timezone_set('America/Lima');

// Configuración de sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime', 30 * 24 * 60 * 60); // 30 días

// Logging
define('LOG_FILE', __DIR__ . '/../logs/app.log');
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}

?>
```

### Archivo 2: `includes/CsrfToken.php`

```php
<?php
/**
 * Clase para manejo de tokens CSRF
 * Protege contra ataques Cross-Site Request Forgery
 */

class CsrfToken {
    
    const TOKEN_NAME = 'csrf_token';
    const TOKEN_LIFETIME = 3600; // 1 hora
    
    /**
     * Inicializar sesión si no está iniciada
     */
    private static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Generar nuevo token CSRF
     * 
     * @return string Token generado
     */
    public static function generateToken() {
        self::startSession();
        
        // Verificar si hay token válido
        $tokenExists = !empty($_SESSION[self::TOKEN_NAME]);
        $tokenExpired = empty($_SESSION[self::TOKEN_NAME . '_time']) || 
                       (time() - $_SESSION[self::TOKEN_NAME . '_time'] > self::TOKEN_LIFETIME);
        
        if (!$tokenExists || $tokenExpired) {
            // Generar nuevo token
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[self::TOKEN_NAME . '_time'] = time();
        }
        
        return $_SESSION[self::TOKEN_NAME];
    }
    
    /**
     * Obtener token actual
     * 
     * @return string|null Token o null si no existe
     */
    public static function getToken() {
        self::startSession();
        return $_SESSION[self::TOKEN_NAME] ?? null;
    }
    
    /**
     * Validar token CSRF
     * 
     * @param string $token Token a validar
     * @return bool True si válido, false si no
     */
    public static function validateToken($token) {
        self::startSession();
        
        if (empty($_SESSION[self::TOKEN_NAME])) {
            error_log("CSRF: No token in session");
            return false;
        }
        
        if (empty($token)) {
            error_log("CSRF: Empty token provided");
            return false;
        }
        
        // Usar hash_equals para evitar timing attacks
        if (!hash_equals($_SESSION[self::TOKEN_NAME], $token)) {
            error_log("CSRF: Token mismatch");
            return false;
        }
        
        return true;
    }
    
    /**
     * Generar input HTML para formularios
     * 
     * @return string HTML del input
     */
    public static function renderInput() {
        $token = self::generateToken();
        $token = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }
    
    /**
     * Middleware para validar CSRF en POST
     * 
     * @return bool True si válido o no es POST
     */
    public static function validateRequest() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return true;
        }
        
        $token = $_POST[self::TOKEN_NAME] ?? null;
        
        if (!self::validateToken($token)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Validación CSRF fallida'
            ]);
            return false;
        }
        
        return true;
    }
}

?>
```

### Archivo 3: `includes/Validador.php`

```php
<?php
/**
 * Clase para validación de entradas
 * Centraliza todas las validaciones de datos
 */

class Validador {
    
    /**
     * Validar email
     * 
     * @param string $email Email a validar
     * @return bool True si válido
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validar teléfono (9 dígitos - Perú)
     * 
     * @param string $telefono Teléfono a validar
     * @return bool True si válido
     */
    public static function telefono($telefono) {
        return preg_match('/^[0-9]{9}$/', $telefono) === 1;
    }
    
    /**
     * Validar DNI (8 dígitos)
     * 
     * @param string $dni DNI a validar
     * @return bool True si válido
     */
    public static function dni($dni) {
        return preg_match('/^[0-9]{8}$/', $dni) === 1;
    }
    
    /**
     * Validar RUC (11 dígitos)
     * 
     * @param string $ruc RUC a validar
     * @return bool True si válido
     */
    public static function ruc($ruc) {
        return preg_match('/^[0-9]{11}$/', $ruc) === 1;
    }
    
    /**
     * Validar fecha (formato YYYY-MM-DD)
     * 
     * @param string $fecha Fecha a validar
     * @return bool True si válido
     */
    public static function fecha($fecha) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return false;
        }
        
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
    
    /**
     * Validar nombre o razón social
     * 
     * @param string $nombre Nombre a validar
     * @param int $minLength Longitud mínima
     * @param int $maxLength Longitud máxima
     * @return bool True si válido
     */
    public static function nombre($nombre, $minLength = 2, $maxLength = 100) {
        $length = strlen($nombre);
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }
        
        // Permite letras, números, acentos, espacios, puntos y guiones
        return preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s\.\-]+$/u', $nombre) === 1;
    }
    
    /**
     * Validar dirección
     * 
     * @param string $direccion Dirección a validar
     * @return bool True si válido
     */
    public static function direccion($direccion) {
        if (strlen($direccion) < 5 || strlen($direccion) > 200) {
            return false;
        }
        
        return preg_match('/^[\p{L}\p{N}\s\.,#\-]+$/u', $direccion) === 1;
    }
    
    /**
     * Validar nombre de usuario
     * 
     * @param string $usuario Usuario a validar
     * @return bool True si válido
     */
    public static function usuario($usuario) {
        return preg_match('/^[a-zA-Z0-9._-]{3,20}$/', $usuario) === 1;
    }
    
    /**
     * Validar contraseña (requisitos mínimos)
     * 
     * @param string $password Contraseña a validar
     * @return array ['valid' => bool, 'errors' => array]
     */
    public static function password($password) {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = "Mínimo 8 caracteres";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Debe contener mayúsculas";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Debe contener minúsculas";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Debe contener números";
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Validar valor numérico
     * 
     * @param mixed $valor Valor a validar
     * @param int|null $min Valor mínimo
     * @param int|null $max Valor máximo
     * @return bool True si válido
     */
    public static function numero($valor, $min = null, $max = null) {
        if (!is_numeric($valor)) {
            return false;
        }
        
        $valor = (int)$valor;
        
        if ($min !== null && $valor < $min) {
            return false;
        }
        
        if ($max !== null && $valor > $max) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitizar entrada de tipo texto
     * 
     * @param string $valor Valor a sanitizar
     * @return string Valor sanitizado
     */
    public static function sanitizarTexto($valor) {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitizar email
     * 
     * @param string $email Email a sanitizar
     * @return string Email sanitizado
     */
    public static function sanitizarEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitizar entrada según tipo
     * 
     * @param string $valor Valor a sanitizar
     * @param string $tipo Tipo de sanitización
     * @return mixed Valor sanitizado
     */
    public static function sanitizar($valor, $tipo = 'string') {
        $valor = trim($valor);
        
        switch ($tipo) {
            case 'email':
                return filter_var($valor, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($valor, FILTER_SANITIZE_URL);
            case 'number':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
            case 'float':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            case 'string':
            default:
                return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        }
    }
}

?>
```

---

## 2️⃣ EJEMPLOS DE CORRECCIÓN POR ERROR

### Corrección ERR-001: Conexión con Variables de Entorno

**Archivo:** `modelos/conexion.php`

**ANTES (Vulnerable):**
```php
<?php
class Conexion{
    static public function conectar(){
        $link = new PDO("mysql:host=localhost;dbname=atlantisbd;charset=utf8mb4",
                         "root",
                         "");
        $link->exec("set names utf8mb4");
        return $link;
    }
}
```

**DESPUÉS (Seguro):**
```php
<?php
require_once __DIR__ . '/../includes/config.php';

class Conexion {
    
    private static $instance = null;
    private static $lastError = null;
    
    /**
     * Obtener instancia de conexión (Singleton)
     * 
     * @return PDO Conexión a base de datos
     * @throws PDOException Si hay error de conexión
     */
    static public function conectar() {
        if (self::$instance === null) {
            self::$instance = self::crearConexion();
        }
        
        return self::$instance;
    }
    
    /**
     * Crear nueva conexión a la base de datos
     * 
     * @return PDO Conexión a base de datos
     * @throws PDOException Si hay error de conexión
     */
    private static function crearConexion() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . 
                   ";dbname=" . DB_NAME . ";charset=utf8mb4";
            
            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASSWORD,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 10,
                )
            );
            
            $pdo->exec("SET NAMES utf8mb4");
            $pdo->exec("SET CHARACTER SET utf8mb4");
            
            return $pdo;
            
        } catch (PDOException $e) {
            self::$lastError = $e->getMessage();
            error_log("Database Connection Error: " . $e->getMessage());
            
            if (APP_DEBUG) {
                throw $e;
            } else {
                throw new PDOException("Error de conexión a la base de datos");
            }
        }
    }
    
    /**
     * Obtener último error
     * 
     * @return string|null Mensaje de error
     */
    static public function getLastError() {
        return self::$lastError;
    }
    
    /**
     * Desconectar
     */
    static public function desconectar() {
        self::$instance = null;
    }
}

?>
```

---

### Corrección ERR-002: Login con password_hash

**Archivo:** `controladores/usuarios.controlador.php`

**ANTES (Inseguro):**
```php
$encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
if ($respuesta["password"] == $encriptar) {
    // Login exitoso
}
```

**DESPUÉS (Seguro):**
```php
// En login - verificar contraseña
if (!password_verify($_POST["ingPassword"], $respuesta["password"])) {
    echo '<div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
    error_log("Failed login attempt for user: " . $_POST["ingUsuario"]);
    return;
}

// En registro - hashear contraseña
$password_hash = password_hash($_POST["password"], PASSWORD_BCRYPT, ['cost' => 12]);

$datos = array(
    "nombre" => $sanitized_nombre,
    "usuario" => $sanitized_usuario,
    "password" => $password_hash,  // ← Guardar hash
    "perfil" => $sanitized_perfil,
    "foto" => ""
);

$respuesta = ModeloUsuarios::mdlRegistrarUsuario($tabla, $datos);
```

---

### Corrección ERR-003: SQL Injection

**Archivo:** `modelos/clientes.modelo.php`

**ANTES (Vulnerable):**
```php
static public function MdlMostrarCliente($tabla, $item, $valor) {
    if ($item != null) {
        // ❌ $item se concatena directamente
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
```

**DESPUÉS (Seguro):**
```php
<?php
require_once "conexion.php";

class ModeloCliente {
    
    // Campos permitidos para búsqueda - LISTA BLANCA
    const CAMPOS_PERMITIDOS = [
        'id',
        'nombre',
        'email',
        'telefono',
        'estado',
        'usuario_id',
        'documento',
        'empresa',
        'ciudad'
    ];
    
    /**
     * Mostrar cliente(s)
     * 
     * @param string $tabla Tabla (validada)
     * @param string|null $item Campo (validado contra lista blanca)
     * @param mixed $valor Valor (preparado con bindParam)
     * @return array Resultados
     */
    static public function mdlMostrarCliente($tabla, $item = null, $valor = null) {
        
        // Validar tabla
        if (!in_array($tabla, ['clientes', 'prospectos'])) {
            error_log("Invalid table requested: $tabla");
            throw new Exception("Tabla no permitida");
        }
        
        try {
            if ($item !== null) {
                // ✅ Validar campo contra lista blanca
                if (!in_array($item, self::CAMPOS_PERMITIDOS)) {
                    error_log("Invalid field requested: $item");
                    throw new Exception("Campo no permitido");
                }
                
                // Para búsqueda por nombre, usar LIKE
                if ($item === 'nombre') {
                    $query = "SELECT * FROM $tabla WHERE $item LIKE :$item";
                    $valor = "%$valor%";
                } else {
                    $query = "SELECT * FROM $tabla WHERE $item = :$item";
                }
                
                $stmt = Conexion::conectar()->prepare($query);
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
                
            } else {
                // Si no hay filtro, traer todos
                $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla");
            }
            
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Database error in mdlMostrarCliente: " . $e->getMessage());
            return [];
        }
    }
}

?>
```

---

### Corrección ERR-004: CSRF

**Archivo:** `ajax/clientes.ajax.php`

**ANTES (Sin protección):**
```php
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"])) {
    $cliente = ControladorCliente::ctrMostrarCliente($item, $valor);
    echo json_encode($cliente);
}
?>
```

**DESPUÉS (Con CSRF):**
```php
<?php
require_once "../includes/CsrfToken.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

// ✅ Validar CSRF para todos los POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!CsrfToken::validateRequest()) {
        exit; // La validación ya envió la respuesta de error
    }
}

// Resto del código
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"])) {
    try {
        $cliente = ControladorCliente::ctrMostrarCliente("id", $_POST["idCliente"]);
        
        header('Content-Type: application/json');
        echo json_encode($cliente);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Error en la consulta',
            'message' => APP_DEBUG ? $e->getMessage() : ''
        ]);
    }
}

?>
```

---

## 3️⃣ SCRIPTS DE MIGRACIÓN

### Script 1: Crear tabla de auditoría

```sql
-- Crear tabla de auditoría para registrar cambios
CREATE TABLE IF NOT EXISTS auditoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    accion VARCHAR(50) NOT NULL,
    tabla VARCHAR(50) NOT NULL,
    registro_id INT,
    valores_anteriores JSON,
    valores_nuevos JSON,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_tabla (tabla),
    INDEX idx_fecha_hora (fecha_hora)
);
```

### Script 2: Migrar contraseñas

```sql
-- ⚠️ EJECUTAR SOLO UNA VEZ - Crea contraseña de ejemplo
-- Para contraseña "admin123": $2y$12$...
UPDATE usuarios 
SET password = '$2y$12$ZuHHyXTvbXlHvC6Xq7d1Y.zqC0Xq3R8KqL9V8ZqL9V8ZqL9V8ZqL9'
WHERE usuario = 'admin' AND password != '$2y$12$ZuHHyXTvbXlHvC6Xq7d1Y.zqC0Xq3R8KqL9V8ZqL9V8ZqL9V8ZqL9';
```

---

## 4️⃣ PRUEBAS RECOMENDADAS

### Prueba 1: Validar CSRF

```bash
# ❌ Debe fallar (sin token CSRF)
curl -X POST http://localhost/Proyecto_atlantis/Ventas/ajax/clientes.ajax.php \
  -d "idCliente=1"

# ✅ Debe funcionar (con token CSRF válido)
curl -X POST http://localhost/Proyecto_atlantis/Ventas/ajax/clientes.ajax.php \
  -d "csrf_token=TOKEN_VÁLIDO&idCliente=1"
```

### Prueba 2: Validar Contraseña

```php
<?php
// Verificar que password_hash y password_verify funcionan

// Generar hash
$password = "TestPassword123";
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verificar - debe devolver true
if (password_verify($password, $hash)) {
    echo "✅ password_verify funciona correctamente";
} else {
    echo "❌ Error en password_verify";
}

// Verificar que password incorrecta falla
if (!password_verify("WrongPassword", $hash)) {
    echo "✅ Contraseña incorrecta rechazada";
} else {
    echo "❌ Error en validación de contraseña incorrecta";
}
?>
```

### Prueba 3: Validar SQL Injection

```php
<?php
require_once "modelos/conexion.php";
require_once "modelos/clientes.modelo.php";

// Intento de SQL Injection
$malicious = "' OR '1'='1";

try {
    $result = ModeloCliente::mdlMostrarCliente("clientes", "nombre", $malicious);
    // Debe retornar solo resultados con nombre = "' OR '1'='1"
    echo "✅ SQL Injection bloqueado";
} catch (Exception $e) {
    echo "✅ Campo rechazado: " . $e->getMessage();
}
?>
```

---

## 📋 Checklist de Implementación

### Antes de Implementar
- [ ] Backup de BD
- [ ] Backup de código
- [ ] Crear rama en Git
- [ ] Notificar al equipo

### Implementación
- [ ] Crear archivo `.env`
- [ ] Copiar archivos `includes/*.php`
- [ ] Actualizar `index.php`
- [ ] Actualizar cada modelo
- [ ] Actualizar cada controlador
- [ ] Actualizar cada AJAX

### Después de Implementar
- [ ] Pruebas locales
- [ ] Pruebas de regresión
- [ ] Verificar logs
- [ ] Documentar cambios
- [ ] Commit en Git

---

**Documento generado:** 11/11/2025  
**Versión:** 1.0

