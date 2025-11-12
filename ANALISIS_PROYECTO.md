# ANÁLISIS DEL PROYECTO ATLANTIS CRM
**Fecha de Análisis:** 11 de Noviembre de 2025

## 📋 Resumen Ejecutivo
El proyecto Atlantis es un CRM (Customer Relationship Management) construido con PHP, MySQL y tecnologías frontend (Bootstrap, Chart.js, FullCalendar). El análisis revela múltiples problemas de seguridad, errores lógicos y malas prácticas que necesitan corrección inmediata.

**Estado General:** ⚠️ CRÍTICO - Se encontraron vulnerabilidades de seguridad y errores de lógica

---

## 🔴 ERRORES CRÍTICOS (Prioridad: ALTA)

### 1. **Vulnerabilidad SQL Injection en Modelos**
**Ubicación:** `modelos/clientes.modelo.php`, `modelos/usuarios.modelo.php`, `modelos/ModeloCRM.php`

**Problema:**
```php
// ❌ INCORRECTO - Concatenación directa de nombres de tabla
$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
```

**Riesgo:** Aunque se usan parámetros vinculados para valores, los nombres de tabla e items se concatenan directamente sin validación.

**Impacto:** Moderado - requiere acceso a aplicación, pero podría permitir acceso a datos sensibles.

**Solución Recomendada:**
- Crear lista blanca de campos permitidos
- Validar nombres de tablas contra constantes predefinidas
- Usar enum o constantes para nombres de tablas

**Ejemplo de Corrección:**
```php
// ✅ CORRECTO
$camposPermitidos = ['id', 'nombre', 'usuario', 'estado', 'email'];
if (!in_array($item, $camposPermitidos)) {
    throw new Exception("Campo no permitido");
}
$stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item");
```

---

### 2. **Credenciales de Base de Datos Hardcodeadas**
**Ubicación:** `modelos/conexion.php`

**Problema:**
```php
$link = new PDO("mysql:host=localhost;dbname=atlantisbd;charset=utf8mb4",
                 "root",
                 "");  // ❌ Contraseña vacía expuesta
```

**Riesgo:** Crítico - Las credenciales están expuestas en el código fuente.

**Impacto:** Acceso no autorizado a la base de datos.

**Solución Recomendada:**
```php
// ✅ CORRECTO - Usar archivo de configuración externo
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'atlantisbd';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

$link = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
```

---

### 3. **Validación Débil de Contraseñas en Login**
**Ubicación:** `controladores/usuarios.controlador.php` (línea ~20)

**Problema:**
```php
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST["ingPassword"])) {
    $encriptar = crypt($_POST["ingPassword"], '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
```

**Riesgos:**
1. La función `crypt()` es obsoleta y insegura para contraseñas
2. Salt hardcodeado en el código fuente
3. Las contraseñas almacenadas no usan bcrypt o argon2
4. Validación muy restrictiva (solo alfanuméricos)

**Impacto:** Crítico - Contraseñas vulnerables a ataques de fuerza bruta.

**Solución Recomendada:**
```php
// ✅ CORRECTO
if (password_verify($_POST["ingPassword"], $respuesta["password"])) {
    // Login exitoso
}
```

Las contraseñas deben guardarse con:
```php
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
```

---

### 4. **Sin Protección CSRF (Cross-Site Request Forgery)**
**Ubicación:** Todos los formularios en `ajax/*.php` y controladores

**Problema:** No hay validación de tokens CSRF en formularios POST.

**Ejemplo Vulnerable:**
```php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"])) {
    // ❌ Sin validación de token CSRF
    $cliente = ControladorCliente::ctrMostrarCliente($item, $valor);
}
```

**Riesgo:** Crítico - Ataques CSRF permitirían realizar acciones en nombre del usuario.

**Solución:**
```php
// ✅ CORRECTO - Generar token en sesión
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validar token en POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!hash_equals($_POST['csrf_token'], $_SESSION['csrf_token'])) {
        http_response_code(403);
        die('CSRF token validation failed');
    }
}
```

---

### 5. **Gestión de Sesiones Inconsistente**
**Ubicación:** `controladores/usuarios.controlador.php`

**Problemas:**
```php
// ❌ Problemas identificados:

// 1. Validación débil de sesión activa
if (isset($_SESSION["sesion_token"]) && $_SESSION["sesion_token"] === $respuesta["sesion_token"]) {
    // Solo verifica pero no previene multi-login

// 2. La cookie se configura DESPUÉS de iniciar sesión
session_set_cookie_params(30 * 24 * 60 * 60); // Demasiado tarde

// 3. No hay validación de expiración consistente
$current_time = date('Y-m-d H:i:s');
if ($respuesta["sesion_expira"] < $current_time) {
    // Error lógico: debería rechazar, pero genera nuevo token igual
```

**Impacto:** Seguridad débil en sesiones - posibilidad de sesiones hijacked.

---

## 🟠 ERRORES DE LÓGICA (Prioridad: MEDIA)

### 1. **Inconsistencia en Nombres de Métodos**
**Ubicación:** Multiple archivos

**Problemas:**
```php
// ❌ Inconsistencia - Algunos métodos usan 'Mdl' y otros 'mdl'
ModeloCliente::MdlMostrarCliente()      // Mayúscula M
ModeloCliente::mdlRegistrarCliente()   // Minúscula m
ModeloUsuarios::MdlMostrarUsuarios()   // Mayúscula M
ModeloUsuarios::mdlBorrarUsuario()     // Minúscula m
```

**Impacto:** Difícil de mantener y propenso a errores.

**Solución:**
```php
// ✅ CORRECTO - Usar convención consistente (camelCase)
ModeloCliente::mdlMostrarCliente()
ModeloCliente::mdlRegistrarCliente()
```

---

### 2. **Retorno Inconsistente de Datos**
**Ubicación:** `modelos/clientes.modelo.php` línea ~40

**Problema:**
```php
public function MdlMostrarCliente($tabla, $item, $valor) {
    if ($item != null) {
        // ... código ...
        return $stmt->fetchAll(); // Retorna array
    }
    // ...
    $stmt->close();      // ❌ Nunca ejecuta después del return
    $stmt = null;        // ❌ Código inalcanzable
}
```

**Impacto:** Código muerto, gestión de recursos incompleta.

---

### 3. **Validación de Estado Inconsistente**
**Ubicación:** `ajax/clientes.ajax.php` línea ~75

**Problema:**
```php
// ❌ El método 'ctrMostrarClientesFiltrados' se llama pero NO EXISTE
$clientes = ControladorCliente::ctrMostrarClientesFiltrados($filtros);
```

**Impacto:** Error fatal si se ejecuta este endpoint.

---

### 4. **Manejo de Errores Deficiente**
**Ubicación:** Todos los archivos AJAX

**Problema:**
```php
// ❌ Sin manejo de excepciones
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["activarId"]) && isset($_POST["activarEstado"])) {
    $activarEstado = new AjaxClientes();
    $activarEstado->activarId = $_POST["activarId"];
    $activarEstado->activarEstado = $_POST["activarEstado"];
    $activarEstado->ajaxActivarEstado();  // Podría lanzar excepción sin control
    exit;
}
```

**Impacto:** Errores no controlados expondrían información técnica.

---

### 5. **Lógica de Mapeo de Estados Confusa**
**Ubicación:** `controladores/ControladorOportunidad.php` línea ~140

**Problema:**
```php
// ❌ Mapeo no claro entre estados KANBAN y estados de CLIENTE
if ($_POST["nuevoEstado"] == KANBAN_PERDIDO) {
    $nuevoEstadoCliente = ESTADO_NO_CLIENTE;
} else {
    $nuevoEstadoCliente = self::obtenerEstadoDesdeKanban($_POST["nuevoEstado"]);
}
```

Sin ver las constantes, es difícil entender la relación entre estados.

---

## 🟡 PROBLEMAS DE SEGURIDAD (Prioridad: MEDIA)

### 1. **Sin Validación de Entrada Consistente**
**Ubicación:** `ajax/clientes.ajax.php`, `ajax/oportunidades.ajax.php`

**Problema:**
```php
// ⚠️ Algunos campos se validan, otros no
$q = $_GET['q'] ?? '';  // Sin sanitización
$action = $_GET['action'] ?? $_POST['action'] ?? '';  // Sin whitelist
```

**Solución:**
```php
// ✅ CORRECTO
$action = $_GET['action'] ?? $_POST['action'] ?? '';
$allowedActions = ['getOportunidades', 'crearOportunidad', 'cambiarEstado'];
if (!in_array($action, $allowedActions)) {
    http_response_code(400);
    die('Acción no válida');
}
```

---

### 2. **Rutas Seguras No Implementadas**
**Ubicación:** Todos los archivos PHP

**Problema:**
```php
// ❌ Las rutas están expuestas directamente
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";
```

**Riesgo:** Si se expone la estructura de directorios, es fácil acceder a archivos.

**Solución:**
- Mover archivos fuera de web root
- Usar rutas absolutas definidas en constantes

---

### 3. **Sin Rate Limiting**
**Ubicación:** Todos los endpoints AJAX

**Problema:** No hay protección contra ataques de fuerza bruta en login o endpoints.

**Impacto:** Vulnerable a ataques DoS y fuerza bruta.

---

### 4. **Sin Logging de Auditoría**
**Ubicación:** Críticas (login, cambios de datos)

**Problema:**
```php
// ❌ Sin registrar quién hizo qué cambio
$respuesta = ModeloCliente::mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2);
echo $respuesta;  // Solo devuelve "ok" o "error"
```

---

## 🟢 ERRORES DE SINTAXIS ENCONTRADOS

### 1. **Typo en Clase**
**Ubicación:** `index.php` línea 35

**Problema:**
```php
// ❌ El nombre de la clase está mal escrito
$plantilla = new ControladorPlantilla();  // Probable: ControladorPlantilla
```

Verificar que la clase en `controladores/plantilla.controlador.php` sea `ControladorPlantilla`.

---

### 2. **Archivo Incluido Faltante**
**Ubicación:** `ajax/clientes.ajax.php` línea 75

**Problema:**
```php
// ❌ Se llama método que no existe
$clientes = ControladorCliente::ctrMostrarClientesFiltrados($filtros);
```

Debería ser `ctrMostrarCliente()` o crear el método.

---

## 📊 Matriz de Riesgos

| Problema | Severidad | Impacto | Esfuerzo | Estado |
|----------|-----------|--------|---------|--------|
| SQL Injection | Crítica | Datos expostos | Medio | ❌ NO RESUELTO |
| Credenciales Hardcodeadas | Crítica | Acceso no autorizado | Bajo | ❌ NO RESUELTO |
| Contraseñas débiles | Crítica | Hijacking de sesión | Medio | ❌ NO RESUELTO |
| Sin CSRF | Crítica | Acciones no autorizadas | Bajo | ❌ NO RESUELTO |
| Validación inconsistente | Alta | Datos inválidos | Medio | ⚠️ PARCIAL |
| Métodos inconsistentes | Media | Mantenimiento difícil | Alto | ❌ NO RESUELTO |
| Sin Rate Limiting | Alta | Ataques DoS | Medio | ❌ NO RESUELTO |
| Sin Auditoría | Media | Falta de trazabilidad | Medio | ❌ NO RESUELTO |

---

## ✅ RECOMENDACIONES POR PRIORIDAD

### INMEDIATO (Semana 1-2)
1. [ ] Mover credenciales de BD a variables de entorno `.env`
2. [ ] Cambiar `crypt()` por `password_hash()` y `password_verify()`
3. [ ] Implementar validación de CSRF tokens
4. [ ] Crear lista blanca de campos/tablas para queries

### CORTO PLAZO (Semana 3-4)
1. [ ] Implementar logging de auditoría
2. [ ] Estandarizar nombres de métodos (camelCase)
3. [ ] Remover código inalcanzable
4. [ ] Implementar validación consistente de entrada

### MEDIANO PLAZO (Mes 2-3)
1. [ ] Implementar rate limiting
2. [ ] Agregar pruebas unitarias
3. [ ] Refactorizar controladores para usar servicios
4. [ ] Implementar manejo de excepciones global

### LARGO PLAZO (Mes 4+)
1. [ ] Migrar a framework modern (Laravel, Symfony)
2. [ ] Implementar API REST con autenticación JWT
3. [ ] Agregar testing automatizado
4. [ ] Implementar CI/CD pipeline

---

## 📁 Archivos a Revisar Prioritariamente

1. `modelos/conexion.php` - Credenciales
2. `controladores/usuarios.controlador.php` - Login
3. `modelos/usuarios.modelo.php` - Hashing de contraseñas
4. `modelos/clientes.modelo.php` - SQL Injection
5. `ajax/*.php` - CSRF y validación

---

## 🔍 Archivos Analizados

### Modelos
- ✅ `modelos/conexion.php`
- ✅ `modelos/usuarios.modelo.php`
- ✅ `modelos/clientes.modelo.php`
- ✅ `modelos/ModeloCRM.php`
- ✅ `modelos/evento.modelo.php`
- ✅ `modelos/proveedor.modelo.php`

### Controladores
- ✅ `controladores/plantilla.controlador.php`
- ✅ `controladores/usuarios.controlador.php`
- ✅ `controladores/clientes.controlador.php`
- ✅ `controladores/dashboard.controlador.php`
- ✅ `controladores/ControladorOportunidad.php`

### AJAX
- ✅ `ajax/usuarios.ajax.php`
- ✅ `ajax/clientes.ajax.php`
- ✅ `ajax/oportunidades.ajax.php`

### Configuración
- ✅ `verificar_estructura_bd.php`
- ✅ `index.php`

---

## 📝 Próximos Pasos

1. **Crear archivo de configuración `.env`** para credenciales
2. **Implementar clase base** para validación y sanitización
3. **Crear función helper** para protección CSRF
4. **Refactorizar modelos** para evitar SQL Injection
5. **Establecer estándares** de codificación

---

## Notas Importantes

- Este análisis se basa en revisión estática de código
- Se recomienda pruebas dinámicas con herramientas SAST/DAST
- Considerar audit de seguridad externo antes de producción
- Documentar todos los cambios realizados

**Generado:** 11/11/2025
**Versión del Análisis:** 1.0
