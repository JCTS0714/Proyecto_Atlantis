# 🧪 GUÍA DE PRUEBAS - ERRORES RESUELTOS

**Fecha:** 11/11/2025  
**Objetivo:** Validar que ERROR #001 y ERROR #002 están completamente resueltos

---

## TEST #1: ParserError al Eliminar Oportunidad (ERROR #001)

### 📋 Pre-requisitos
- [ ] Sistema Atlantis CRM accesible en `http://localhost/Proyecto_atlantis/`
- [ ] Usuario con sesión iniciada y perfil "Administrador"
- [ ] Navegador con Consola de Desarrollador abierta (F12)
- [ ] Exista al menos 1 oportunidad en Kanban

### 🎬 Procedimiento

#### Paso 1: Navegación
1. Abrir: `http://localhost/Proyecto_atlantis/Ventas/index.php?ruta=oportunidades`
2. Hacer clic en el tab "Kanban" 
3. Verificar que carga correctamente

#### Paso 2: Preparación de Prueba
1. En la Consola (F12), ir a la pestaña "Network"
2. Filtrar por: "oportunidades.ajax.php"
3. Limpiar resultados previos
4. Mantener Consola visible

#### Paso 3: Ejecución
1. En Kanban, ubicar una oportunidad para eliminar
2. Hacer clic en el botón eliminar (ícono ❌ o trash)
3. En SweetAlert, confirmar "Sí, eliminar"

#### Paso 4: Validación

**En SweetAlert:**
- [ ] ✅ Aparece mensaje "¡Éxito!" (NO "parsererror")
- [ ] ✅ Mensaje confirmatorio aparece correctamente
- [ ] ✅ Botón "Cerrar" funciona

**En Consola → Network:**
- [ ] ✅ Request a `oportunidades.ajax.php` status `200`
- [ ] ✅ Response tipo "json" (NO "text")
- [ ] ✅ Response comienza con `{` (válido JSON)
- [ ] ✅ NO hay advertencias rojas

**En la BD:**
- [ ] ✅ Oportunidad se eliminó correctamente
- [ ] ✅ Kanban se actualiza sin la oportunidad

**En Consola → Console:**
- [ ] ✅ No hay errores rojos
- [ ] ✅ No hay warnings

### ✅ Resultado Esperado
```
SweetAlert: "¡Éxito!" con descripción
Network: Status 200, JSON válido
BD: Oportunidad eliminada
Console: Sin errores
```

### ❌ Si falla...
**Problema:** Sigue mostrando "parsererror"

**Diagnóstico:**
1. Verificar en Network → Response de `oportunidades.ajax.php`
2. Si ves HTML/warning antes del JSON → `session_start()` no aplicado
3. Si ves caracteres extraños → Espacios tras `?>` no eliminados

---

## TEST #2: Eliminación en Lista de Seguimiento (ERROR #002)

### 📋 Pre-requisitos
- [ ] Usuario Administrador iniciado en sesión
- [ ] Exista al menos 1 cliente en estado "Seguimiento"
- [ ] Ese cliente NO tenga oportunidades asociadas
- [ ] Navegador con Consola abierta (F12)

### 🎬 Procedimiento

#### Paso 1: Navegación
1. Abrir: `http://localhost/Proyecto_atlantis/Ventas/index.php?ruta=seguimiento`
2. Verificar que lista de seguimiento carga
3. Identificar un cliente para eliminar (sin oportunidades)

#### Paso 2: Preparación
1. Abrir Consola (F12) → pestaña "Network"
2. Filtrar por requests GET a `index.php`
3. Mantener visible

#### Paso 3: Ejecución
1. En la lista de Seguimiento, buscar cliente sin oportunidades
2. Hacer clic en botón eliminar (ícono 🗑️ o ❌)
3. Si aparece SweetAlert de confirmación, hacer clic en "Sí"

#### Paso 4: Validación

**En SweetAlert:**
- [ ] ✅ Aparece confirmación (NO se queda cargando)
- [ ] ✅ Muestra mensaje exitoso "¡Éxito!" o error descriptivo
- [ ] ✅ Botón "Cerrar" es clickeable

**En la URL:**
- [ ] ✅ URL cambia a: `index.php?ruta=seguimiento&idClienteEliminar=XX`
- [ ] ✅ Luego vuelve a: `index.php?ruta=seguimiento` (sin parámetro)

**En la Lista:**
- [ ] ✅ Cliente desaparece de la tabla
- [ ] ✅ Página se recarga correctamente
- [ ] ✅ Otros clientes siguen visibles

**En la BD:**
- [ ] ✅ Cliente ya no existe en tabla `clientes`

**En Consola:**
- [ ] ✅ NO hay errores rojos
- [ ] ✅ Requests se completaron correctamente

### ✅ Resultado Esperado
```
URL: Redirect from ?idClienteEliminar=XX a ?ruta=seguimiento
SweetAlert: "¡Éxito!" con confirmación
BD: Cliente eliminado
Lista: Se actualiza sin el cliente
```

### ⚠️ Casos de Prueba Adicionales

**Caso A: Cliente CON oportunidades**
1. Intentar eliminar cliente que SÍ tiene oportunidades
2. **Esperado:** SweetAlert error "No se puede eliminar: cliente tiene oportunidades"

**Caso B: Cliente CON actividades**
1. Crear actividad para cliente
2. Intentar eliminar
3. **Esperado:** SweetAlert error "No se puede eliminar: cliente tiene actividades"

**Caso C: Perfil No-Admin**
1. Cambiar usuario a perfil "Vendedor"
2. Intentar eliminar cliente
3. **Esperado:** SweetAlert error "No tienes permisos"

### ❌ Si falla...
**Problema:** Página se queda cargando sin mostrar SweetAlert

**Diagnóstico:**
1. Verificar en Browser DevTools → Network
2. Buscar request a `index.php?ruta=seguimiento&idClienteEliminar=XX`
3. Si status es 302 (redirect) → código sí intenta redirigir
4. Si status es 200 pero recarga vacía → verificar plantilla

**Problema:** SweetAlert de error inesperado

**Diagnóstico:**
1. Verificar en BD si cliente tiene registros en:
   - `actividades` (cliente_id = XX)
   - `incidencias` (cliente_id = XX)
   - `reuniones` (cliente_id = XX)
   - `oportunidades` (cliente_id = XX)

---

## 📊 Matriz de Validación

### ERROR #001 Checklist
| Item | ✅ | ❌ | Nota |
|------|----|----|------|
| SweetAlert sin error "parsererror" | | | |
| JSON válido en Network | | | |
| Oportunidad se elimina en BD | | | |
| Consola sin errores rojos | | | |
| Kanban se actualiza | | | |

### ERROR #002 Checklist
| Item | ✅ | ❌ | Nota |
|------|----|----|------|
| SweetAlert aparece (no se queda cargando) | | | |
| URL redirige correctamente | | | |
| Cliente se elimina de lista | | | |
| Cliente se elimina de BD | | | |
| Prueba con cliente sin oportunidades | | | |
| Error correcto con cliente con oportunidades | | | |
| Error correcto sin permisos | | | |

---

## 📝 Registro de Prueba

**Tester:** ________________  
**Fecha:** ________________  
**Sistema Operativo:** ________________  
**Navegador:** ________________  
**Versión PHP:** ________________

### Resultados TEST #1 (ParserError)
**Status:** ☐ Pasó ☐ Falló ☐ Parcial  
**Observaciones:** ________________

### Resultados TEST #2 (Seguimiento)
**Status:** ☐ Pasó ☐ Falló ☐ Parcial  
**Observaciones:** ________________

### Casos Adicionales
**Caso A (Cliente con oportunidades):** ☐ OK ☐ Falló  
**Caso B (Cliente con actividades):** ☐ OK ☐ Falló  
**Caso C (Perfil No-Admin):** ☐ OK ☐ Falló  

---

## 🐛 Reporte de Problemas

Si alguna prueba falla, por favor reporte:

1. **Descripción del error**
2. **Pasos para reproducir**
3. **Resultado esperado vs actual**
4. **Screenshot/Video**
5. **Consola de errores (copiar completo)**
6. **Network tab screenshot**

---

## 📞 Soporte

**Documentación relacionada:**
- `REGISTRO_ERRORES.md` - Análisis completo de causes
- `RESUMEN_CAMBIOS.md` - Detalle técnico de implementación

**Scripts de análisis (pueden ser eliminados):**
- `Ventas/analizar_bd.php` - Análisis BD
- `Ventas/verificar_restricciones.php` - Verificación restricciones
