# 🧪 CHECKLIST DE VERIFICACIÓN POST-DEPLOY

## Antes de Subir a Producción ✅

### Verificaciones Locales Completadas
- [x] Botón editar en seguimiento.php funciona
- [x] Botón eliminar en seguimiento.php funciona
- [x] Botón editar en no-clientes.php funciona
- [x] Botón editar en zona-espera.php funciona
- [x] Modal se llena con datos del cliente
- [x] Guardado redirige a la página correcta
- [x] Eliminación pide confirmación
- [x] `.htaccess` configurado correctamente
- [x] Todas las rutas en `plantilla.php` son absolutas
- [x] CSS links comienzan con `/vistas/`
- [x] JS links comienzan con `/vistas/`

---

## Después de Subir `plantilla.php` a Producción

### ✅ Paso 1: Verificar que la página carga
**URL:** `https://atlantiscrm.infinityfreeapp.com/`

- [ ] Página aparece sin errores
- [ ] Se ve la página de LOGIN
- [ ] **IMPORTANTE:** ¿Se ve con ESTILOS? (fondo azul AdminLTE)
  - ✅ Si se ve con estilos = CSS cargó correctamente
  - ❌ Si se ve plano = CSS no cargó (problema aún sin resolver)

---

### ✅ Paso 2: Iniciar sesión
**Credenciales:** (usa las tuyas)

- [ ] Login funciona
- [ ] Se abre dashboard
- [ ] Dashboard tiene estilos correctos

---

### ✅ Paso 3: Navegar a seguimiento
**URL:** `https://atlantiscrm.infinityfreeapp.com/seguimiento`

- [ ] Página carga sin error
- [ ] Se ve tabla de clientes
- [ ] Tabla tiene estilos (colores, bordes, etc.)
- [ ] Columnas visible están correcto
- [ ] Botones "Editar" son visibles
- [ ] Botones "Eliminar" son visibles (solo si NO eres Vendedor)

---

### ✅ Paso 4: Probar botón EDITAR
**Acción:** Click en botón "Editar" de cualquier cliente

- [ ] Modal se abre
- [ ] Modal tiene título "Actualizar Cliente"
- [ ] Campo "Nombre" tiene contenido
- [ ] Campo "Tipo de Cliente" tiene contenido
- [ ] Campo "Documento" tiene contenido
- [ ] Todos los campos están llenos con datos reales
- [ ] Modal tiene botones "Guardar" y "Cancelar"

**Si el modal NO abre:**
- [ ] Abre DevTools (F12)
- [ ] Mira Console para errores JavaScript
- [ ] Mira Network para requests fallidos
- [ ] Busca 404 errors

---

### ✅ Paso 5: Editar datos y guardar
**Acción:** Cambiar un campo (ej: nombre) y hacer click "Guardar"

- [ ] Modal se cierra
- [ ] Permaneces en la página `/seguimiento`
- [ ] Los datos cambios se ven reflejados en la tabla
- [ ] No hay redirección a otra página

---

### ✅ Paso 6: Probar botón ELIMINAR
**Acción:** Click en botón "Eliminar" de un cliente que NO tengas planes de usar

- [ ] Aparece confirmación SweetAlert
- [ ] Dice algo como "¿Estás seguro de que deseas eliminar?"
- [ ] Tienes opciones "Cancelar" y "Confirmar"

**Si haces click "Cancelar":**
- [ ] Modal se cierra
- [ ] Nada se elimina
- [ ] Permaneces en la misma página

**Si haces click "Confirmar":**
- [ ] Modal se cierra
- [ ] Página se recarga
- [ ] El cliente eliminado YA NO aparece en la tabla
- [ ] Otros clientes siguen ahí

---

### ✅ Paso 7: Verificar CSS de todas las páginas
**Accede a estas URLs y verifica que todas tengan ESTILOS:**

- [ ] `/` o `/inicio` - Debe verse dashboard con estilos
- [ ] `/clientes` - Tabla con estilos
- [ ] `/usuarios` - Tabla con estilos
- [ ] `/productos` - Tabla con estilos
- [ ] `/ventas` - Tabla con estilos
- [ ] `/no-clientes` - Tabla con estilos
- [ ] `/zona-espera` - Tabla con estilos
- [ ] `/calendario` - Calendario con estilos

**Si alguna página se ve PLANA (sin estilos):**
- [ ] Abre DevTools (F12)
- [ ] Mira pestaña "Network"
- [ ] Busca items con color rojo (404 errors)
- [ ] Verifica qué archivos no cargan
- [ ] Ejemplo de 404 esperado: NO debe haber `/vistas/...` 404s

---

### ✅ Paso 8: Verificar JavaScript Funciona
**Acciones para probar JavaScript:**

1. [ ] Abrir Select2 (si hay dropdowns)
   - Selecciona un elemento
   - Debe funcionar el autocompletar
   
2. [ ] DataTables
   - [ ] Filtro de tabla funciona
   - [ ] Paginación funciona (si hay múltiples páginas)
   - [ ] Ordenamiento de columnas funciona

3. [ ] Calendario (si accedes a `/calendario`)
   - [ ] Se ve el calendario con estilos
   - [ ] Puedes navegar entre meses

---

### ⚠️ PROBLEMAS COMUNES Y SOLUCIONES

#### Problema: "Diseño plano" (sin estilos)
**Causa más probable:** CSS no cargó desde `/vistas/`

**Verificación:**
1. F12 → Network
2. Busca archivos `.css`
3. Si alguno dice "404", el archivo no existe en esa ruta

**Solución:**
1. Verifica que `/htdocs/Ventas/vistas/` existe
2. Verifica que `/htdocs/Ventas/vistas/bower_components/` existe
3. Verifica que `/htdocs/Ventas/vistas/dist/` existe
4. Si falta algo, necesitas subir toda la carpeta `Ventas/vistas/`

#### Problema: Modal no abre al hacer click "Editar"
**Causa más probable:** JavaScript no cargó

**Verificación:**
1. F12 → Network
2. Busca `clientes.js`
3. Si dice "404", no está en `/vistas/js/`

**Solución:**
1. Sube `clientes.js` a `/htdocs/Ventas/vistas/js/clientes.js`
2. Recarga página
3. Intenta nuevamente

#### Problema: AJAX call falla (modal no se llena)
**Causa más probable:** `clientes.ajax.php` no accesible

**Verificación:**
1. F12 → Network
2. Busca request a `clientes.ajax.php`
3. Si dice "404", el archivo no está en la ruta correcta

**Solución:**
1. Verifica que `clientes.ajax.php` esté en `/htdocs/Ventas/ajax/clientes.ajax.php`
2. Recarga página
3. Intenta nuevamente

---

## 🎉 Si TODO Funciona

Felicidades! El deploy fue exitoso. 

**Significa que:**
- ✅ CSS carga desde rutas absolutas
- ✅ JavaScript funciona correctamente
- ✅ AJAX comunica correctamente entre frontend y backend
- ✅ Botones editar y eliminar funcionan
- ✅ Base de datos está accesible
- ✅ URLs reescribidas con `.htaccess` funcionan

---

## 📋 Resumen Rápido

| Verificación | Deberías Ver | No Debería Pasar |
|---|---|---|
| `/` | Login con estilos | Página plana, errores |
| `/clientes` | Tabla con estilos | 404, diseño plano |
| Click "Editar" | Modal con datos | Modal no abre, modal vacío |
| Click "Eliminar" | Confirmación | Nada pasa |
| F12 → Network | Archivos con 200 | Archivos CSS/JS con 404 |

---

## 📞 Si Necesitas Ayuda

Si algo no funciona después de este checklist:
1. Anota exactamente qué no funciona
2. Saca screenshot del error
3. Abre F12 y copia los errores de Console
4. Abre Network y verifica 404s
5. Comparte esta información para diagnosicar

