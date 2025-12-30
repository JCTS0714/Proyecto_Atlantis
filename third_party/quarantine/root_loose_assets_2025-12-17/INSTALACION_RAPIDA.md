# 🚀 GUÍA DE INSTALACIÓN RÁPIDA - Exportación Excel

## ⚡ Instalación en 5 Minutos

### Paso 1: Verificar Archivos Creados ✅

Todos los archivos ya están creados. Verificar que existan:

```
✅ vistas/js/export-tables.js
✅ vistas/css/export-buttons.css
✅ ajax/export-data.ajax.php
✅ Documentacion/EXPORTACION_EXCEL.md
```

### Paso 2: Verificar Modificaciones ✅

Los siguientes archivos YA ESTÁN modificados:

```
✅ vistas/plantilla.php (librerías agregadas)
✅ vistas/js/plantilla.js (inicialización agregada)
✅ vistas/modulos/clientes.php (contenedor agregado)
✅ vistas/modulos/seguimiento.php (contenedor agregado)
✅ vistas/modulos/no-clientes.php (contenedor agregado)
✅ vistas/modulos/zona-espera.php (contenedor agregado)
✅ vistas/modulos/prospectos.php (contenedor agregado)
✅ vistas/modulos/contadores.php (contenedor agregado)
✅ vistas/modulos/incidencias.php (contenedor agregado)
✅ vistas/modulos/usuarios.php (contenedor agregado)
```

### Paso 3: NO Requiere Instalación Manual

**Las librerías se cargan desde CDN** (Internet requerido):
- ✅ JSZip
- ✅ DataTables Buttons
- ✅ Buttons HTML5
- ✅ Buttons Bootstrap

Ya están configuradas en `vistas/plantilla.php`

### Paso 4: Primera Prueba

1. **Abrir navegador**
2. **Ir a:** `http://localhost/Proyecto_Atlantis/Ventas/contadores`
3. **Buscar:** Botones en esquina superior derecha
4. **Hacer clic:** Botón verde "Excel"

**Resultado Esperado:**
- Se descarga archivo `Contadores_YYYYMMDD.xlsx`

---

## 🔍 Test Rápido de Funcionalidad

### Test 1: Exportar sin filtros

```
Tabla: Contadores
Acción: Clic en botón "Excel"
Resultado Esperado: Descarga archivo con todos los registros
✅ / ❌
```

### Test 2: Exportar con búsqueda

```
Tabla: Prospectos  
Acción: 
  1. Buscar "Lima" en caja de búsqueda
  2. Clic en botón "Excel"
Resultado Esperado: Archivo solo con registros que contienen "Lima"
✅ / ❌
```

### Test 3: Copiar al portapapeles

```
Tabla: Cualquiera
Acción: Clic en botón "Copiar"
Resultado Esperado: 
  1. Mensaje "Datos copiados al portapapeles"
  2. Pegar en Excel → datos aparecen
✅ / ❌
```

### Test 4: Server-Side (Clientes)

```
Tabla: Clientes
Acción:
  1. Ver que hay más de 10 registros en total
  2. Clic en botón "Excel"
Resultado Esperado: Archivo con TODOS los registros (no solo 10)
✅ / ❌
```

---

## 🐛 Solución Rápida de Problemas

### Problema: No aparecen botones

**Solución:**
1. Presionar `F12` (abrir consola)
2. Buscar errores en rojo
3. Si dice "404" → Verificar que archivos existan
4. Si dice "ExportTables is not defined" → Verificar plantilla.php tiene export-tables.js

**Fix Rápido:**
```javascript
// Ejecutar en consola:
console.log('ExportTables:', typeof ExportTables);
// Debe decir: "object"
```

### Problema: Solo exporta 10 registros (Server-Side)

**Verificar:**
1. Ir a `vistas/js/export-tables.js`
2. Buscar la tabla en `tableConfigs`
3. Verificar que tenga `serverSide: true`

**Ejemplo correcto:**
```javascript
'tablaClientes': {
  filename: 'Clientes_Oportunidades',
  title: 'Listado de Clientes',
  serverSide: true  // ← DEBE SER true
}
```

### Problema: Error 500 al exportar

**Verificar:**
1. Iniciar sesión en el sistema (no funciona sin sesión)
2. Revisar logs: `C:\xampp\apache\logs\error.log`

**Fix Común:**
```php
// En ajax/export-data.ajax.php
// Verificar que la tabla esté en $tablasPermitidas
$tablasPermitidas = [
  'tablaClientes',
  'tablaSeguimiento',
  // ... etc
];
```

### Problema: Caracteres raros (Ãº en vez de ú)

**Verificar:**
- Archivo se abre en Excel (no Notepad)
- Si persiste, en `export-tables.js` buscar:
```javascript
charset: 'utf-8',
bom: true  // ← DEBE existir
```

---

## 📊 Checklist de Verificación

Marcar cuando esté listo:

### Archivos
- [ ] `export-tables.js` existe
- [ ] `export-buttons.css` existe  
- [ ] `export-data.ajax.php` existe

### Funcionalidad
- [ ] Botones aparecen en tablas
- [ ] Excel se descarga correctamente
- [ ] CSV se descarga correctamente
- [ ] Copiar funciona
- [ ] Imprimir abre ventana

### Integraciones
- [ ] Filtros avanzados funcionan
- [ ] Columnas ocultas funcionan
- [ ] Server-side funciona (exporta todo)
- [ ] Nombres de archivo tienen fecha

### Responsive
- [ ] Botones se ven bien en desktop
- [ ] Botones se ven bien en móvil

---

## 🎯 Comandos de Debug

### Ver versión de DataTables
```javascript
// En consola del navegador (F12)
$.fn.dataTable.version
```

### Ver tablas inicializadas
```javascript
// En consola del navegador
$.fn.DataTable.tables()
```

### Forzar exportación manual
```javascript
// En consola del navegador
ExportTables.init('tablaClientes');
```

### Habilitar logs detallados
```javascript
// En consola del navegador
ExportTables.enableDebug();
```

---

## 📞 Soporte

### Si algo no funciona:

1. **Consola del navegador (F12)**
   - Buscar errores en rojo
   - Copiar mensaje de error

2. **Logs PHP**
   ```powershell
   Get-Content C:\xampp\apache\logs\error.log -Tail 20
   ```

3. **Verificar conexión**
   - Exportación usa CDN → Requiere internet
   - Sin internet → Descargar librerías localmente

---

## ✅ Implementación Completada

**TODO ESTÁ LISTO.**  
Solo falta probar que funcione correctamente.

### Próximo paso:
```
1. Abrir: http://localhost/Proyecto_Atlantis/Ventas/contadores
2. Ver botones de exportación
3. Clic en "Excel"
4. Verificar descarga
```

**¿Funcionó? ✅**  
**¿No funcionó? ❌ → Revisar sección de problemas arriba**

---

**Última actualización:** 2025-12-10  
**Versión:** 1.0.0  
**Estado:** ✅ LISTO PARA USAR
