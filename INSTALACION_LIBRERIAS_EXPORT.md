# Instalación de Librerías para Exportación a Excel

## Librerías Necesarias

### Opción 1: Usar CDN (Más Rápido - RECOMENDADO)
No requiere descarga, solo modificar plantilla.php con los enlaces CDN.

### Opción 2: Instalación Local

#### 1. DataTables Buttons
Descargar desde: https://datatables.net/download/

**Archivos necesarios:**
```
vistas/bower_components/datatables.net-buttons/js/
  - dataTables.buttons.min.js
  - buttons.html5.min.js
  - buttons.print.min.js
  
vistas/bower_components/datatables.net-buttons-bs/js/
  - buttons.bootstrap.min.js
  
vistas/bower_components/datatables.net-buttons-bs/css/
  - buttons.bootstrap.min.css
```

#### 2. JSZip (para Excel)
Descargar desde: https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js

**Archivo:**
```
vistas/bower_components/jszip/
  - jszip.min.js
```

#### 3. pdfMake (opcional, para PDF)
Descargar desde: https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/

**Archivos:**
```
vistas/bower_components/pdfmake/
  - pdfmake.min.js
  - vfs_fonts.js
```

## Estructura de Carpetas Resultante

```
vistas/
├── bower_components/
│   ├── datatables.net-buttons/
│   │   └── js/
│   │       ├── dataTables.buttons.min.js
│   │       ├── buttons.html5.min.js
│   │       └── buttons.print.min.js
│   ├── datatables.net-buttons-bs/
│   │   ├── css/
│   │   │   └── buttons.bootstrap.min.css
│   │   └── js/
│   │       └── buttons.bootstrap.min.js
│   ├── jszip/
│   │   └── jszip.min.js
│   └── pdfmake/ (opcional)
│       ├── pdfmake.min.js
│       └── vfs_fonts.js
```

## Verificación

Después de crear las carpetas y colocar los archivos, verificar que las rutas sean accesibles:
- http://localhost/vistas/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js
- http://localhost/vistas/bower_components/jszip/jszip.min.js

## Nota

Para este proyecto, **recomendamos usar CDN** para evitar problemas de compatibilidad de versiones.
Los enlaces CDN se agregarán directamente en plantilla.php.
