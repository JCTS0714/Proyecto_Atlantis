# Instrucciones para Sincronizar Cambios a Producción (infinityfree)

## ✅ Cambios Completados en `plantilla.php`

Todos los CSS y JavaScript ahora usan **rutas absolutas** (comienzan con `/`) para que funcionen correctamente con el `.htaccess` que reescribe las URLs.

### Cambios Realizados:
1. **CSS links** (líneas 49-88): Todos ahora tienen `/vistas/` al inicio
   - Bootstrap, Font Awesome, Ionicons, fullCalendar, jQuery UI, Select2, DataTables, AdminLTE

2. **JavaScript includes** (líneas 119-150): Todos ahora tienen `/vistas/` al inicio
   - jQuery, jQuery UI, moment.js, fullCalendar, Select2, Bootstrap, SlimScroll, FastClick, AdminLTE, DataTables

3. **Scripts custom** (líneas 225-239): Todos ahora con ruta absoluta `/vistas/js/`
   - plantilla.js, usuarios.js, categorias.js, productos.js, clientes.js, incidencias.js, proveedor.js, ventas.js, oportunidades.js, prospectos.js, calendario.js, evento.js, dashboard.js, notificaciones.js, alarma.js

## 🚀 Pasos para Subir a Producción

### Opción 1: Usando FTP (recomendado)
1. Conecta via FTP a `atlantiscrm.infinityfreeapp.com` (o tu hosting)
2. Navega a `/htdocs/Ventas/vistas/`
3. **Reemplaza** `plantilla.php` con la versión actualizada

### Opción 2: Usando administrador de archivos infinityfree
1. Inicia sesión en tu panel de infinityfree
2. Abre el administrador de archivos
3. Navega a `/htdocs/Ventas/vistas/`
4. Sube/reemplaza `plantilla.php`

## ✅ Verificación Post-Deploy

Después de subir el archivo, prueba lo siguiente:

### 1. Verifica que CSS carga:
- Accede a: `https://atlantiscrm.infinityfreeapp.com/`
- Deberías ver:
  - Fondo azul AdminLTE
  - Botones con estilos Bootstrap
  - Iconos de Font Awesome
  - NO debe verse como "diseño plano"

### 2. Verifica funcionalidad de botones:
- Navega a: `https://atlantiscrm.infinityfreeapp.com/seguimiento`
- Haz clic en botón "Editar" de cualquier cliente
- Debe aparecer modal con datos del cliente
- Haz clic en botón "Eliminar" 
- Debe pedir confirmación y luego eliminar

### 3. Verifica todas las rutas:
- `https://atlantiscrm.infinityfreeapp.com/clientes` ✓
- `https://atlantiscrm.infinityfreeapp.com/usuarios` ✓
- `https://atlantiscrm.infinityfreeapp.com/productos` ✓
- `https://atlantiscrm.infinityfreeapp.com/ventas` ✓
- Todas deben mostrar con estilos correctos

## 📋 Resumen de Archivos Actualizados

| Archivo | Cambio | Estado |
|---------|--------|--------|
| `Ventas/vistas/plantilla.php` | Rutas relativas → absolutas | ✅ Completado |
| `Ventas/vistas/seguimiento.php` | Modal + scripts agregados | ✅ Completado (anterior) |
| `Ventas/vistas/no-clientes.php` | Modal + scripts agregados | ✅ Completado (anterior) |
| `Ventas/vistas/zona-espera.php` | Modal + scripts agregados | ✅ Completado (anterior) |
| `Ventas/ajax/clientes.ajax.php` | Endpoint delete agregado | ✅ Completado (anterior) |
| `Ventas/js/clientes.js` | Handler delete agregado | ✅ Completado (anterior) |
| `.htaccess` (en /htdocs/) | RewriteCond agregado | ✅ Completado (anterior) |

## 🔍 Si Aún Hay Problemas

**Si los CSS/JS no cargan después de subir:**

1. Abre DevTools del navegador (F12)
2. Mira la pestaña "Network"
3. Busca requests con estado 404
4. Verifica la URL que se está pidiendo
5. Confirma que esa ruta existe en el servidor

**Ejemplo de URL correcta:**
- `https://atlantiscrm.infinityfreeapp.com/vistas/bower_components/bootstrap/dist/css/bootstrap.min.css`

**Url incorrecta (no funcionará):**
- `https://atlantiscrm.infinityfreeapp.com/vistas/vistas/bower_components/...` (vistas duplicado)

## 📞 Soporte

Si necesitas hacer ajustes adicionales después del deploy, todos los archivos JavaScript están documentados:
- `clientes.js` - Gestión de clientes
- `usuarios.js` - Gestión de usuarios
- `dashboard.js` - Panel de control

