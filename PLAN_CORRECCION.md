# PLAN DE CORRECCIÓN - PROYECTO ATLANTIS

**Fecha:** 11 de Noviembre de 2025  
**Estado:** 🟡 EN PLANIFICACIÓN

---

## 📋 Tabla de Control

| ID | Problema | Severidad | Archivo | Estado | Fecha |
|----|----------|-----------|---------|--------|-------|
| ERR-001 | Credenciales hardcodeadas | 🔴 Crítica | `modelos/conexion.php` | ⏳ Pendiente | - |
| ERR-002 | crypt() inseguro | 🔴 Crítica | `controladores/usuarios.controlador.php` | ⏳ Pendiente | - |
| ERR-003 | SQL Injection potencial | 🔴 Crítica | `modelos/*.php` | ⏳ Pendiente | - |
| ERR-004 | Sin CSRF | 🔴 Crítica | `ajax/*.php` | ⏳ Pendiente | - |
| ERR-005 | Métodos inconsistentes | 🟠 Media | Multiple | ⏳ Pendiente | - |
| ERR-006 | Validación débil | 🟠 Media | `ajax/*.php` | ⏳ Pendiente | - |
| ERR-007 | Sin Rate Limiting | 🟠 Media | `ajax/usuarios.ajax.php` | ⏳ Pendiente | - |
| ERR-008 | Sin Auditoría | 🟡 Baja | `controladores/*.php` | ⏳ Pendiente | - |

---

## 🔧 CORRECCIONES DETALLADAS

### ERR-001: Credenciales Hardcodeadas

#### Ubicación
- Archivo: `modelos/conexion.php`

#### Código Actual
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

#### Código Corregido
```php
<?php
class Conexion{
    
    private static $instance = null;
    
    static public function conectar(){
        if (self::$instance === null) {
            // Cargar configuración desde variables de entorno
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'atlantisbd';
            $user = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            $port = getenv('DB_PORT') ?: '3306';
            
            try {
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                self::$instance = new PDO(
                    $dsn,
                    $user,
                    $password,
                    array(
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false,
                    )
                );
                self::$instance->exec("set names utf8mb4");
            } catch (PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                die('Error de conexión a la base de datos');
            }
        }
        return self::$instance;
    }
}
?>
```

#### Pasos de Implementación
1. Crear archivo `.env` en raíz del proyecto
2. Agregar al `.gitignore`
3. Instalar paquete: `composer require vlucas/phpdotenv`
4. Cargar variables en `index.php`

#### Archivo `.env` Ejemplo
```
DB_HOST=localhost
DB_PORT=3306
DB_NAME=atlantisbd
DB_USER=root
DB_PASSWORD=tu_password_segura
ENVIRONMENT=production
```

---

### ERR-002: Contraseñas con crypt()

#### Ubicación
- Archivo: `controladores/usuarios.controlador.php` (línea ~20)
- Archivo: `modelos/usuarios.modelo.php`

#### Código Actual
```php
$encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
// ... comparación
if ($respuesta["password"] == $encriptar) {
```

#### Código Corregido (Login)
```php
<?php
class ControladorUsuarios {

    public function ctrIngresoUsuario() {
        if (isset($_POST["ingUsuario"]) && isset($_POST["ingPassword"])) {
            
            // Validación básica
            if (empty($_POST["ingUsuario"]) || empty($_POST["ingPassword"])) {
                echo '<br><div class="alert alert-danger">Usuario y contraseña son requeridos</div>';
                return;
            }
            
            // Limitar caracteres permitidos en usuario (más permisivo)
            if (!preg_match('/^[a-zA-Z0-9._-]{3,20}$/', $_POST["ingUsuario"])) {
                echo '<br><div class="alert alert-danger">Usuario inválido</div>';
                error_log("Login attempt with invalid username: " . $_POST["ingUsuario"]);
                return;
            }
            
            // Obtener usuario de BD
            $tabla = "usuarios";
            $item = "usuario";
            $valor = $_POST["ingUsuario"];
            
            $respuesta = ModeloUsuarios::mdlMostrarUsuarios($tabla, $item, $valor);
            
            if (!$respuesta || !is_array($respuesta)) {
                echo '<br><div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
                error_log("Login: User not found - " . $_POST["ingUsuario"]);
                return;
            }
            
            // ✅ Usar password_verify en lugar de crypt
            if (!password_verify($_POST["ingPassword"], $respuesta["password"])) {
                echo '<br><div class="alert alert-danger">Usuario o contraseña incorrectos</div>';
                error_log("Login: Invalid password for user - " . $_POST["ingUsuario"]);
                return;
            }
            
            // Verificar estado del usuario
            if ($respuesta["estado"] != 1) {
                echo '<br><div class="alert alert-danger">El usuario está inactivo</div>';
                error_log("Login: Inactive user - " . $_POST["ingUsuario"]);
                return;
            }
            
            // Generar tokens seguros
            $this->crearSesionSegura($respuesta);
            
            echo '<script>window.location = "inicio";</script>';
        }
    }
    
    private function crearSesionSegura($usuario) {
        // Configurar parámetros de sesión ANTES de iniciar
        session_set_cookie_params([
            'lifetime' => 30 * 24 * 60 * 60,  // 30 días
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => isset($_SERVER['HTTPS']),  // Solo HTTPS
            'httponly' => true,  // No accesible por JavaScript
            'samesite' => 'Strict'  // Protección CSRF
        ]);
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Generar tokens
        $sesion_token = bin2hex(random_bytes(32));
        date_default_timezone_set('America/Lima');
        $fechaExpira = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Guardar en sesión
        $_SESSION["iniciarSesion"] = "ok";
        $_SESSION["id"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["usuario"] = $usuario["usuario"];
        $_SESSION["foto"] = $usuario["foto"];
        $_SESSION["perfil"] = $usuario["perfil"];
        $_SESSION["sesion_token"] = $sesion_token;
        $_SESSION["session_ip"] = $_SERVER['REMOTE_ADDR'];
        $_SESSION["user_agent"] = $_SERVER['HTTP_USER_AGENT'];
        
        // Actualizar en BD
        $tabla = "usuarios";
        $fechaActual = date('Y-m-d H:i:s');
        
        ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "ultimo_login", $fechaActual, "id", $usuario["id"]);
        ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_token", $sesion_token, "id", $usuario["id"]);
        ModeloUsuarios::mdlActualizarCampoUsuario($tabla, "sesion_expira", $fechaExpira, "id", $usuario["id"]);
    }
}
?>
```

#### Código Corregido (Registro de Usuario)
```php
// En el controlador donde se crea un usuario:

// ✅ Al registrar un usuario, hashear la contraseña
$password_hash = password_hash($_POST["password"], PASSWORD_BCRYPT, ['cost' => 12]);

$datos = array(
    "nombre" => $_POST["nombre"],
    "usuario" => $_POST["usuario"],
    "password" => $password_hash,  // ← Guardar hash, no la contraseña
    "perfil" => $_POST["perfil"],
    "foto" => $_POST["foto"]
);

$respuesta = ModeloUsuarios::mdlRegistrarUsuario($tabla, $datos);
```

#### Código en BD (Migración)
```sql
-- Actualizar contraseña existente (UNA SOLA VEZ)
-- Nota: Esto solo funciona si la contraseña "admin" es la actual
UPDATE usuarios 
SET password = '$2y$12$ZuHHyXTvbXlHvC6Xq7d1Y.zqC0Xq3R8KqL9V8ZqL9V8ZqL9V8ZqL9'
WHERE usuario = 'admin';

-- De ahora en adelante, todas las nuevas contraseñas se guardan hasheadas
```

---

### ERR-003: SQL Injection en Modelos

#### Ubicación
- `modelos/clientes.modelo.php`
- `modelos/usuarios.modelo.php`
- `modelos/ModeloCRM.php`

#### Código Actual (VULNERABLE)
```php
static public function MdlMostrarCliente($tabla, $item, $valor){
    if($item != null) {
        // ❌ $item se concatena directamente
        $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
        $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
```

#### Código Corregido
```php
<?php
require_once "conexion.php";

class ModeloCliente {
    
    // Campos permitidos para búsqueda
    const CAMPOS_PERMITIDOS = ['id', 'nombre', 'email', 'telefono', 'estado', 'usuario_id'];
    
    /**
     * MÉTODO PARA MOSTRAR CLIENTES
     * @param string $tabla Tabla a consultar
     * @param string|null $item Campo para WHERE
     * @param mixed $valor Valor a buscar
     * @return array Resultados
     */
    static public function mdlMostrarCliente($tabla, $item = null, $valor = null) {
        
        // Validar tabla
        if ($tabla !== 'clientes' && $tabla !== 'prospectos') {
            throw new Exception("Tabla no permitida: $tabla");
        }
        
        try {
            if ($item !== null) {
                // ✅ Validar que el campo esté en lista blanca
                if (!in_array($item, self::CAMPOS_PERMITIDOS)) {
                    throw new Exception("Campo no permitido: $item");
                }
                
                // Para búsqueda por nombre, usar LIKE
                if ($item === 'nombre') {
                    $stmt = Conexion::conectar()->prepare(
                        "SELECT * FROM $tabla WHERE $item LIKE :$item"
                    );
                    $valor = "%$valor%";
                } else {
                    $stmt = Conexion::conectar()->prepare(
                        "SELECT * FROM $tabla WHERE $item = :$item"
                    );
                }
                
                $stmt->bindParam(":" . $item, $valor, PDO::PARAM_STR);
            } else {
                $stmt = Conexion::conectar()->prepare(
                    "SELECT * FROM $tabla"
                );
            }
            
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;
            
            return $resultado;
            
        } catch (PDOException $e) {
            error_log("Database error in mdlMostrarCliente: " . $e->getMessage());
            throw new Exception("Error en la consulta de clientes");
        }
    }
    
    static public function mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2) {
        
        // Validar campos
        if (!in_array($item1, self::CAMPOS_PERMITIDOS) || !in_array($item2, self::CAMPOS_PERMITIDOS)) {
            throw new Exception("Campo no permitido en actualización");
        }
        
        try {
            $sql = "UPDATE $tabla SET $item1 = :val1 WHERE $item2 = :val2";
            $stmt = Conexion::conectar()->prepare($sql);
            $stmt->bindParam(":val1", $valor1, PDO::PARAM_STR);
            $stmt->bindParam(":val2", $valor2, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                return "ok";
            } else {
                return "error";
            }
        } catch (PDOException $e) {
            error_log("Database error in mdlActualizarCliente: " . $e->getMessage());
            return "error";
        }
    }
}
?>
```

---

### ERR-004: Sin Protección CSRF

#### Ubicación
- `ajax/clientes.ajax.php`
- `ajax/usuarios.ajax.php`
- `ajax/oportunidades.ajax.php`

#### Crear Clase Helper
```php
<?php
// Archivo: includes/CsrfToken.php

class CsrfToken {
    
    const TOKEN_NAME = 'csrf_token';
    const TOKEN_LIFETIME = 3600; // 1 hora
    
    /**
     * Generar o regenerar token CSRF
     */
    static public function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerar token cada cierto tiempo o si no existe
        if (
            empty($_SESSION[self::TOKEN_NAME]) ||
            empty($_SESSION[self::TOKEN_NAME . '_time']) ||
            (time() - $_SESSION[self::TOKEN_NAME . '_time'] > self::TOKEN_LIFETIME)
        ) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[self::TOKEN_NAME . '_time'] = time();
        }
        
        return $_SESSION[self::TOKEN_NAME];
    }
    
    /**
     * Obtener token actual
     */
    static public function getToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION[self::TOKEN_NAME] ?? null;
    }
    
    /**
     * Validar token CSRF
     */
    static public function validateToken($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION[self::TOKEN_NAME])) {
            return false;
        }
        
        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }
    
    /**
     * Generar input HTML para formularios
     */
    static public function renderInput() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
?>
```

#### Uso en AJAX
```php
<?php
// Archivo: ajax/clientes.ajax.php

require_once "../includes/CsrfToken.php";
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

// ✅ Validar CSRF para POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_POST["csrf_token"]) || !CsrfToken::validateToken($_POST["csrf_token"])) {
        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'CSRF token validation failed']);
        exit;
    }
}

// Resto del código...
?>
```

#### Uso en Formularios
```html
<!-- En formularios HTML -->
<form method="POST" action="">
    <?php echo CsrfToken::renderInput(); ?>
    <!-- resto del formulario -->
</form>

<!-- En AJAX -->
<script>
fetch('/ajax/clientes.ajax.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: new URLSearchParams({
        csrf_token: '<?php echo CsrfToken::getToken(); ?>',
        action: 'guardar',
        // más parámetros...
    })
})
</script>
```

---

### ERR-005: Normalizar Nombres de Métodos

#### Cambios Necesarios

```php
// ANTES (Inconsistente)
ModeloCliente::MdlMostrarCliente()
ModeloCliente::mdlRegistrarCliente()

// DESPUÉS (Consistente - camelCase)
ModeloCliente::mdlMostrarCliente()
ModeloCliente::mdlRegistrarCliente()
```

#### Archivos a actualizar:
- `modelos/clientes.modelo.php` - Renombrar `MdlMostrarCliente` → `mdlMostrarCliente`
- `modelos/usuarios.modelo.php` - Renombrar `MdlMostrarUsuarios` → `mdlMostrarUsuarios`

---

### ERR-006: Validación Consistente de Entrada

#### Crear Clase Validador
```php
<?php
// Archivo: includes/Validador.php

class Validador {
    
    /**
     * Validar email
     */
    static public function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validar teléfono (9 dígitos Perú)
     */
    static public function telefono($telefono) {
        return preg_match('/^[0-9]{9}$/', $telefono);
    }
    
    /**
     * Validar documento (DNI 8 dígitos)
     */
    static public function dni($dni) {
        return preg_match('/^[0-9]{8}$/', $dni);
    }
    
    /**
     * Validar RUC (11 dígitos)
     */
    static public function ruc($ruc) {
        return preg_match('/^[0-9]{11}$/', $ruc);
    }
    
    /**
     * Validar fecha (YYYY-MM-DD)
     */
    static public function fecha($fecha) {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return false;
        }
        $d = DateTime::createFromFormat('Y-m-d', $fecha);
        return $d && $d->format('Y-m-d') === $fecha;
    }
    
    /**
     * Validar nombre (letras, números, espacios, algunos caracteres especiales)
     */
    static public function nombre($nombre) {
        return preg_match('/^[a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s\.\-]{3,100}$/u', $nombre);
    }
    
    /**
     * Validar dirección
     */
    static public function direccion($direccion) {
        return preg_match('/^[\p{L}\p{N}\s\.,#\-]{5,200}$/u', $direccion);
    }
    
    /**
     * Sanitizar entrada
     */
    static public function sanitizar($valor, $tipo = 'string') {
        switch ($tipo) {
            case 'email':
                return filter_var($valor, FILTER_SANITIZE_EMAIL);
            case 'url':
                return filter_var($valor, FILTER_SANITIZE_URL);
            case 'number':
                return filter_var($valor, FILTER_SANITIZE_NUMBER_INT);
            case 'string':
            default:
                return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
        }
    }
}
?>
```

---

## 📊 Cronograma de Implementación

```
Semana 1-2: CRÍTICO
├─ ERR-001: Mover credenciales a .env
├─ ERR-002: Cambiar crypt a password_hash
├─ ERR-003: Implementar validación de campos en SQL
└─ ERR-004: Agregar protección CSRF

Semana 3-4: IMPORTANTE
├─ ERR-005: Normalizar nombres de métodos
├─ ERR-006: Crear clase Validador y aplicar
└─ Pruebas de regresión

Semana 5-6: COMPLEMENTARIO
├─ ERR-007: Implementar Rate Limiting
├─ ERR-008: Agregar auditoría
└─ Documentación
```

---

## ✅ Checklist de Implementación

- [ ] Crear archivo `.env`
- [ ] Instalar phpenv
- [ ] Actualizar `conexion.php`
- [ ] Crear clase `CsrfToken.php`
- [ ] Crear clase `Validador.php`
- [ ] Actualizar `usuarios.controlador.php`
- [ ] Actualizar `modelos/usuarios.modelo.php`
- [ ] Actualizar `modelos/clientes.modelo.php`
- [ ] Actualizar todos los archivos `ajax/*.php`
- [ ] Pruebas manuales
- [ ] Pruebas de seguridad
- [ ] Deploy a producción

---

## 📞 Notas de Desarrollo

- Todas las correcciones deben ser probadas en ambiente de desarrollo
- Realizar backup de BD antes de ejecutar migraciones
- Comunicar cambios al equipo
- Documentar cambios en controlador de versiones (git)

