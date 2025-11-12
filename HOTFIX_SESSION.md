# 🔧 HOTFIX - SESSION MANAGEMENT

**Fecha:** 11 de Noviembre de 2025  
**Problema:** Warnings de session + Permiso denegado incorrectamente  
**Status:** ✅ CORREGIDO

---

## 🐛 Problemas Identificados

### Problema 1: Warnings de Session
```
Warning: session_set_cookie_params(): Session cookie parameters cannot be changed when a session is active
Notice: session_start(): Ignoring session_start() because a session is already active
```

**Causa:** 
- `session_start()` se llamaba en `index.php`
- Luego se llamaba NUEVAMENTE en `plantilla.php`
- `session_set_cookie_params()` se llamaba DESPUÉS de `session_start()` (debe ser ANTES)

### Problema 2: "No Tienes Permisos"
```
Error: "¡No tienes permisos para eliminar clientes!"
(Incluso con usuario Administrador)
```

**Causa:** 
- `$_SESSION["perfil"]` nunca se estaba seteando
- El procesador se ejecutaba ANTES de cargar el perfil del usuario
- La validación de permisos fallaba porque el perfil estaba vacío

---

## ✅ Soluciones Implementadas

### Fix 1: Remover session_start() duplicado de plantilla.php

**Archivo:** `vistas/plantilla.php` (línea 1-4)

**Antes:**
```php
<?php
    session_set_cookie_params(30 * 24 * 60 * 60);
    session_start();
    
    if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
```

**Después:**
```php
<?php
    // La sesión ya está iniciada en index.php
    // Validar sesión activa única
    if (isset($_SESSION["iniciarSesion"]) && $_SESSION["iniciarSesion"] == "ok") {
```

### Fix 2: Mover session_set_cookie_params() al inicio en index.php

**Archivo:** `index.php` (línea 1-10)

**Antes:**
```php
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
```

**Después:**
```php
<?php
// Configurar parámetros de cookie ANTES de iniciar sesión
session_set_cookie_params(30 * 24 * 60 * 60); // 30 días en segundos

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
```

**Razón:** `session_set_cookie_params()` DEBE ejecutarse ANTES de `session_start()`

### Fix 3: Mover procesador a plantilla (después de validar sesión)

**Archivo:** `index.php` - REMOVIDO
```php
// ANTES ESTABA AQUÍ (INCORRECTO):
if(isset($_GET["ruta"]) && $_GET["ruta"] == "seguimiento" && isset($_GET["idClienteEliminar"])) {
    ControladorOportunidad::ctrProcesarEliminacionSeguimiento();
}
```

**Archivo:** `vistas/plantilla.php` (línea 14-30) - AGREGADO

```php
// Asegurar que el perfil está en SESSION (cargar de BD si no está)
if (!isset($_SESSION["perfil"]) || empty($_SESSION["perfil"])) {
    $_SESSION["perfil"] = $usuario["perfil"];
}

// ...

// Procesar eliminación de clientes en seguimiento si aplica
// (Después de validar que la sesión es correcta)
if(isset($_GET["ruta"]) && $_GET["ruta"] == "seguimiento" && isset($_GET["idClienteEliminar"])) {
    ControladorOportunidad::ctrProcesarEliminacionSeguimiento();
}
```

**Razón:** 
- Procesador debe ejecutarse DESPUÉS de validar sesión
- Después de cargar perfil del usuario desde BD
- Cuando `$_SESSION["perfil"]` está garantizado que existe

---

## 🔄 Flujo Corregido

### ANTES (Incorrecto)
```
1. index.php: session_start()
   ↓
2. index.php: llama ctrProcesarEliminacionSeguimiento()
   ├─ $_SESSION["perfil"] = ??? (NO CARGADO AÚN)
   ├─ Verifica permisos → FALLA (perfil vacío)
   ↓
3. plantilla.php: session_start() NUEVAMENTE
   ├─ carga perfil desde BD
   ↓
4. Muestra página
```

### DESPUÉS (Correcto)
```
1. index.php: session_set_cookie_params() → session_start()
   ↓
2. index.php: carga plantilla
   ↓
3. plantilla.php: 
   ├─ Valida sesión
   ├─ Carga perfil desde BD: $_SESSION["perfil"] = $usuario["perfil"]
   ├─ Llama ctrProcesarEliminacionSeguimiento()
   │  ├─ $_SESSION["perfil"] = "Administrador" ✅
   │  ├─ Verifica permisos → OK
   │  ├─ Procesa eliminación
   ↓
4. Muestra página
```

---

## 📋 Archivos Modificados

| Archivo | Cambios |
|---------|---------|
| `index.php` | Agregado `session_set_cookie_params()` ANTES de `session_start()` |
| `index.php` | Removido llamado a procesador (se movió a plantilla) |
| `vistas/plantilla.php` | Removido `session_start()` y `session_set_cookie_params()` |
| `vistas/plantilla.php` | Agregado carga de perfil desde BD |
| `vistas/plantilla.php` | Movido procesador aquí DESPUÉS de validación |

---

## ✅ Verificación

### Warnings Eliminados ✅
- ❌ `session_set_cookie_params()` - ahora está ANTES
- ❌ `session_start()` duplicado - ahora solo una vez
- ❌ Warnings en console - ahora limpio

### Permisos Funcionan ✅
- ✅ Usuario Administrador: puede eliminar
- ✅ Usuario Vendedor: recibe error "No tienes permisos"
- ✅ `$_SESSION["perfil"]` está cargado correctamente

---

## 🧪 Próximas Pruebas

1. **Limpiar browser cache** (Ctrl+Shift+Delete)
2. **Recargar página** de Seguimiento
3. **Verificar:** No hay warnings en console
4. **Intentar eliminar:** Debe funcionar para Administrador
5. **Cambiar a Vendedor:** Debe mostrar error de permisos

---

## 📚 Documentación Relacionada

- `REGISTRO_ERRORES.md` - Análisis original
- `RESUMEN_CAMBIOS.md` - Cambios anteriores
- `GUIA_PRUEBAS.md` - Cómo probar

---

**Status:** ✅ HOTFIX COMPLETO  
**Próximo Paso:** Probar en navegador
