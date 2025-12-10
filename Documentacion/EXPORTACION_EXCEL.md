# Documentación: Exportación de Tablas a Excel/CSV

## Versión 1.0.0 | Fecha: 2025-12-10

---

## 📋 Índice

1. [Introducción](#introducción)
2. [Características](#características)
3. [Tablas Soportadas](#tablas-soportadas)
4. [Cómo Usar](#cómo-usar)
5. [Formatos de Exportación](#formatos-de-exportación)
6. [Personalización](#personalización)
7. [Agregar Exportación a Nuevas Tablas](#agregar-exportación-a-nuevas-tablas)
8. [Troubleshooting](#troubleshooting)
9. [Preguntas Frecuentes](#preguntas-frecuentes)

---

## 🎯 Introducción

El sistema de exportación permite a los usuarios descargar datos de las tablas del CRM en múltiples formatos (Excel, CSV, Copiar, Imprimir). 

**Implementación completamente transparente:**
- ✅ No interfiere con funcionalidades existentes
- ✅ Respeta columnas ocultas por el usuario
- ✅ Aplica filtros de búsqueda avanzada
- ✅ Compatible con server-side processing
- ✅ Responsive y accesible

---

## ⭐ Características

### Formatos Disponibles

| Formato | Icono | Descripción |
|---------|-------|-------------|
| **Excel** | 📗 | Archivo `.xlsx` compatible con Microsoft Excel |
| **CSV** | 📄 | Archivo `.csv` compatible con Google Sheets y otros |
| **Copiar** | 📋 | Copia los datos al portapapeles |
| **Imprimir** | 🖨️ | Abre ventana de impresión con datos formateados |

### Funcionalidades Especiales

- **Respeto de Filtros:** Exporta solo los datos visibles según filtros aplicados
- **Columnas Personalizadas:** Solo exporta columnas visibles
- **Nombres Inteligentes:** Los archivos incluyen fecha y estado de filtros
- **Codificación UTF-8:** Soporta caracteres especiales (tildes, ñ, etc.)
- **Progreso Visual:** Indicador de carga en exportaciones grandes

---

## 📊 Tablas Soportadas

| Tabla | ID HTML | Server-Side | Ubicación |
|-------|---------|-------------|-----------|
| **Clientes** | `tablaClientes` | ✅ | vistas/modulos/clientes.php |
| **Seguimiento** | `tablaSeguimiento` | ✅ | vistas/modulos/seguimiento.php |
| **No Clientes** | `tablaNoClientes` | ✅ | vistas/modulos/no-clientes.php |
| **Zona de Espera** | `tablaZonaEspera` | ✅ | vistas/modulos/zona-espera.php |
| **Prospectos** | `example2` | ✅ | vistas/modulos/prospectos.php |
| **Contadores** | `tablaContadores` | ❌ | vistas/modulos/contadores.php |
| **Incidencias** | `tablaIncidencias` | ❌ | vistas/modulos/incidencias.php |
| **Usuarios** | `example2` | ❌ | vistas/modulos/usuarios.php |

> **Nota:** Las tablas con server-side processing utilizan `ajax/export-data.ajax.php` para obtener datos completos.

---

## 🚀 Cómo Usar

### Exportación Básica

1. **Navegar** a cualquier módulo con tabla (ej: Clientes)
2. **Ubicar** los botones de exportación en la parte superior derecha
3. **Hacer clic** en el formato deseado:
   - 🟢 **Excel** - Descargar archivo Excel
   - 🔵 **CSV** - Descargar archivo CSV
   - ⚫ **Copiar** - Copiar al portapapeles
   - 🔵 **Imprimir** - Abrir vista de impresión

### Exportación con Filtros

1. **Aplicar filtros** usando la búsqueda avanzada
2. **Verificar** que los datos en tabla estén filtrados
3. **Exportar** - Solo se exportarán los datos filtrados

**Ejemplo de nombre de archivo:**
```
Clientes_Oportunidades_20251210_Filtrado.xlsx
```

### Exportación con Columnas Ocultas

1. **Usar** el botón "Mostrar/Ocultar Columnas"
2. **Desmarcar** las columnas que no deseas exportar
3. **Exportar** - Solo las columnas visibles se incluirán

---

## 📁 Formatos de Exportación

### Excel (.xlsx)

**Ventajas:**
- Formato más completo
- Conserva tipos de datos
- Compatible con fórmulas
- Mejor para análisis

**Uso Recomendado:**
- Análisis de datos extensos
- Reportes ejecutivos
- Compartir con equipos

### CSV (.csv)

**Ventajas:**
- Archivo liviano
- Compatible universal
- Fácil importación a otras herramientas
- Soporta UTF-8 con BOM

**Uso Recomendado:**
- Importar a Google Sheets
- Procesar con scripts
- Compartir con sistemas externos

### Copiar

**Ventajas:**
- Instantáneo
- No genera archivos
- Pega directo en Excel/Sheets

**Uso Recomendado:**
- Datos temporales
- Copias rápidas
- Incluir en emails

### Imprimir

**Ventajas:**
- Vista optimizada
- Incluye encabezados
- Sin columnas de acción

**Uso Recomendado:**
- Reportes físicos
- PDFs (usar "Guardar como PDF")
- Presentaciones

---

## 🎨 Personalización

### Configurar Exportación de Nueva Tabla

#### Paso 1: Registrar Configuración

Editar `vistas/js/export-tables.js`:

```javascript
var tableConfigs = {
  'miNuevaTabla': {
    filename: 'Mi_Nueva_Tabla',
    title: 'Listado de Mi Nueva Tabla',
    serverSide: false, // true si usa AJAX server-side
    messageTop: function() {
      return 'Generado: ' + new Date().toLocaleDateString();
    }
  }
};
```

#### Paso 2: Agregar Contenedor en PHP

Editar el módulo correspondiente:

```php
<div class="box-header with-border">
  <button class="btn btn-primary">Agregar</button>
  
  <!-- Contenedor de exportación -->
  <div class="export-buttons-container pull-right" id="export-miNuevaTabla"></div>
</div>
```

#### Paso 3: Inicializar DataTable con Exportación

En el archivo JS del módulo o `plantilla.js`:

```javascript
$('#miNuevaTabla').DataTable({
  // ... opciones normales ...
});

// Inicializar exportación
setTimeout(function() {
  if (window.ExportTables) {
    ExportTables.init('miNuevaTabla');
  }
}, 500);
```

### Personalizar Columnas Exportadas

#### Opción 1: Usando `exportOptions`

```javascript
ExportTables.init('tablaClientes', {
  exportOptions: {
    columns: [1, 2, 3, 4] // Solo exportar columnas 1-4
  }
});
```

#### Opción 2: Marcar columnas como no exportables

En el HTML de la tabla:

```html
<th class="no-export">Acciones</th>
```

### Personalizar Nombre de Archivo

Editar configuración en `export-tables.js`:

```javascript
filename: function() {
  var user = getCurrentUser(); // tu función
  var date = new Date().toISOString().split('T')[0];
  return 'Reporte_' + user + '_' + date;
}
```

---

## ➕ Agregar Exportación a Nuevas Tablas

### Checklist Completo

- [ ] **1. Configurar en export-tables.js**
  ```javascript
  'miTabla': {
    filename: 'Exportacion',
    title: 'Mi Tabla',
    serverSide: false
  }
  ```

- [ ] **2. Agregar contenedor en módulo PHP**
  ```html
  <div class="export-buttons-container pull-right" id="export-miTabla"></div>
  ```

- [ ] **3. Inicializar exportación después de DataTable**
  ```javascript
  ExportTables.init('miTabla');
  ```

- [ ] **4. Si usa server-side, agregar función en export-data.ajax.php**
  ```php
  case 'miTabla':
    $data = exportarMiTabla($filters, $search);
    break;
  
  function exportarMiTabla($filters, $search) {
    // Implementar query y retornar datos
  }
  ```

- [ ] **5. Probar**
  - Sin filtros
  - Con filtros
  - Con columnas ocultas
  - En móvil

---

## 🔧 Troubleshooting

### Problema: Botones no aparecen

**Posibles causas:**

1. **Librerías no cargadas**
   - Verificar consola del navegador
   - Comprobar que `export-tables.js` se carga
   - Verificar que JSZip y Buttons estén cargados

2. **Contenedor faltante**
   ```bash
   # Buscar en el módulo PHP
   grep "export-miTabla" vistas/modulos/mi-modulo.php
   ```

3. **DataTable no inicializado**
   ```javascript
   // En consola del navegador
   $.fn.DataTable.isDataTable('#miTabla')
   // Debe retornar true
   ```

**Solución:**
```javascript
// Habilitar modo debug
ExportTables.enableDebug();
// Revisar consola para mensajes detallados
```

### Problema: Exporta solo página actual (server-side)

**Causa:** Falta configurar `serverSide: true` en configuración.

**Solución:**

1. Editar `export-tables.js`:
   ```javascript
   'miTabla': {
     serverSide: true // IMPORTANTE
   }
   ```

2. Implementar función en `export-data.ajax.php`

### Problema: Caracteres especiales aparecen mal

**Causa:** Codificación incorrecta.

**Solución:**

En `export-data.ajax.php`:
```php
header('Content-Type: application/json; charset=utf-8');
```

En export de CSV, asegurar BOM:
```javascript
charset: 'utf-8',
bom: true
```

### Problema: Exportación muy lenta

**Causas:**
- Tabla con > 10,000 registros
- Queries sin índices
- Server-side mal configurado

**Soluciones:**

1. **Optimizar query:**
   ```php
   // Agregar índices a campos filtrados
   ALTER TABLE clientes ADD INDEX idx_estado (estado);
   ```

2. **Limitar registros:**
   ```javascript
   messageTop: function() {
     return 'Solo se exportan primeros 10,000 registros';
   }
   ```

3. **Implementar paginación en export:**
   ```php
   $sql .= " LIMIT 10000";
   ```

### Problema: Botones desalineados en móvil

**Solución:**

Editar `export-buttons.css`:
```css
@media (max-width: 768px) {
  .export-buttons-container {
    display: block;
    width: 100%;
    text-align: center;
    margin-top: 10px;
  }
}
```

---

## ❓ Preguntas Frecuentes

### ¿Puedo exportar datos sin estar autenticado?

**No.** El endpoint `export-data.ajax.php` valida la sesión:
```php
if (!isset($_SESSION['iniciarSesion'])) {
  echo json_encode(['error' => 'No autorizado']);
  exit;
}
```

### ¿Hay límite de registros exportables?

**No hay límite técnico**, pero se recomienda:
- < 1,000 registros: Instantáneo
- 1,000 - 10,000: 2-5 segundos
- > 10,000: Considerar paginación o filtros

### ¿Los filtros se aplican automáticamente?

**Sí**, para tablas server-side. Para tablas client-side, DataTables maneja automáticamente la exportación de datos filtrados.

### ¿Puedo personalizar el estilo del Excel?

**Sí**, editando la función `customize` en `export-tables.js`:

```javascript
customize: function(xlsx) {
  var sheet = xlsx.xl.worksheets['sheet1.xml'];
  
  // Negritas en encabezados
  $('row:first c', sheet).attr('s', '2');
  
  // Color de fondo
  // ... más personalizaciones XML
}
```

### ¿Funciona offline?

**Sí**, si las librerías están locales. Actualmente usa CDN para JSZip y Buttons. Para offline:

1. Descargar librerías
2. Colocar en `vistas/bower_components/`
3. Actualizar rutas en `plantilla.php`

### ¿Puedo agregar botón de PDF?

**Sí**, agregando pdfMake:

1. Incluir librería en `plantilla.php`:
   ```html
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
   ```

2. Agregar botón en `export-tables.js`:
   ```javascript
   {
     extend: 'pdfHtml5',
     text: '<i class="fa fa-file-pdf-o"></i> PDF',
     className: 'btn btn-danger btn-sm export-btn'
   }
   ```

---

## 📞 Soporte

Para problemas o consultas:

1. **Revisar esta documentación**
2. **Verificar consola del navegador** (F12)
3. **Habilitar modo debug:**
   ```javascript
   ExportTables.enableDebug();
   ```
4. **Consultar logs del servidor** en `ajax/export-data.ajax.php`

---

## 📝 Registro de Cambios

### Versión 1.0.0 (2025-12-10)

- ✨ Implementación inicial
- ✅ Soporte para 8 tablas principales
- ✅ Formatos: Excel, CSV, Copy, Print
- ✅ Integración con filtros avanzados
- ✅ Compatibilidad con column-toggle
- ✅ Responsive design
- ✅ Server-side processing support

---

## 🔜 Futuras Mejoras

- [ ] Exportación programada/automática
- [ ] Plantillas de exportación personalizadas
- [ ] Exportación con gráficos embebidos
- [ ] API REST para exportación externa
- [ ] Compresión ZIP para múltiples tablas

---

**Desarrollado por:** Sistema Atlantis CRM  
**Última actualización:** 2025-12-10  
**Versión:** 1.0.0
