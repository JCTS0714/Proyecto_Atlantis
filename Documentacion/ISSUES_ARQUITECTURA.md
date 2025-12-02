# Issues de Arquitectura - Proyecto Atlantis CRM

Documento creado: 2 de diciembre de 2025

---

## Issue #1: crm.php tiene estructura HTML completa dentro de un módulo

### Descripción
El archivo `vistas/modulos/crm.php` contiene un documento HTML completo (`<!DOCTYPE>`, `<html>`, `<head>`, `<body>`, etc.) pero se incluye dentro de `plantilla.php` como cualquier otro módulo.

### Ubicación
- **Archivo afectado:** `vistas/modulos/crm.php`
- **Líneas problemáticas:**
  - Línea 1: `<!DOCTYPE html>`
  - Línea 2: `<html lang="es">`
  - Línea 3-87: `<head>...</head>`
  - Línea 88: `<body>`
  - Línea 721: `</body>`
  - Línea 722: `</html>`

### Cómo se carga el módulo
En `plantilla.php` línea 217:
```php
include "modulos/".$_GET["ruta"].".php";
```

Cuando la ruta es `crm`, se incluye `crm.php` dentro del HTML ya existente de la plantilla.

### Consecuencias
1. **HTML inválido:** Se genera HTML con DOCTYPE, html, head, body duplicados/anidados
2. **Ruta de script inconsistente:** Usa `<script src="../js/oportunidades.js">` en lugar de `BASE_URL`
3. **Estilos inline:** Tiene ~75 líneas de CSS dentro de `<style>` tags en lugar de archivo externo
4. **Select2 duplicado:** Carga Select2 desde CDN aunque ya está en la plantilla principal

### Código actual (extracto)
```php
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tablero Kanban - CRM</title>
  <!-- ... -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script src="../js/oportunidades.js"></script>
  <style>
    /* ~75 líneas de CSS */
  </style>
</head>
<body>
  <!-- contenido del kanban -->
</body>
</html>
```

### Solución propuesta
Refactorizar `crm.php` para que sea un módulo normal:

1. **Eliminar** las etiquetas de documento HTML (`<!DOCTYPE>`, `<html>`, `<head>`, `<body>`, etc.)
2. **Mover CSS** a un archivo separado `vistas/dist/css/crm-kanban.css`
3. **Cambiar ruta del script** a usar `BASE_URL`:
   ```php
   <script src="<?php echo BASE_URL; ?>/vistas/js/oportunidades.js"></script>
   ```
4. **Eliminar** carga duplicada de Select2 (ya está en plantilla.php)
5. **Agregar** la carga del CSS en la plantilla cuando sea necesario

### Prioridad
**Media** - Funciona actualmente pero genera HTML técnicamente inválido y es inconsistente con el resto de módulos.

### Estado
⏳ Pendiente de corrección

---

## Auditoría de rutas completada - 2 de diciembre 2025

### Rutas verificadas como correctas:
- ✅ Todos los `include` en `vistas/modulos/*.php` usan rutas relativas correctas
- ✅ Todos los `<script src>` en `plantilla.php` usan `BASE_URL`
- ✅ Archivos PHP incluidos existen en disco
- ✅ Archivos JS referenciados existen en disco
- ✅ Archivos AJAX usan `../` o `__DIR__` (ambos funcionan correctamente)

### Correcciones aplicadas previamente:
- ✅ `prospectos.php`: Corregido `include 'modulos/partials/...'` → `include 'partials/...'`
- ✅ `seguimiento.php`: Misma corrección
- ✅ `no-clientes.php`: Misma corrección  
- ✅ `zona-espera.php`: Misma corrección (2 includes)

---

## Historial de cambios

| Fecha | Commit | Descripción |
|-------|--------|-------------|
| 2024-12-02 | 6c70413 | Corregidas rutas include en 4 módulos de contactos |
| 2024-12-02 | 0411d65 | Fix calendario.ajax.php undefined array key |
| 2024-12-02 | 6f3d904 | Mejorados error handlers en index.php |
