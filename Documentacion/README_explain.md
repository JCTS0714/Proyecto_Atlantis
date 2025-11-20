# Ejecutar EXPLAIN para las queries candidatas

Este README explica cómo usar `tools/run_explain.php` para obtener `EXPLAIN` de las consultas listadas en `tools/queries_to_explain.sql`.

Requisitos:
- PHP CLI disponible (XAMPP lo incluye). Ejecutar desde la raíz del proyecto.
- Archivo `Ventas/modelos/conexion.php` con credenciales o un `.env` en la raíz o en `Ventas/` con las variables:
  - DB_HOST
  - DB_NAME
  - DB_USER
  - DB_PASS

Pasos:
1. Abre un terminal (PowerShell) y sitúate en la raíz del proyecto:

```powershell
cd c:\xampp\htdocs\Proyecto_Atlantis
php tools/run_explain.php
```

2. El script ejecutará `EXPLAIN` para cada query en `tools/queries_to_explain.sql` y guardará los resultados JSON en `reports/explain_results_YYYYMMDD_HHMMSS.json`.

3. Sube el archivo JSON resultante aquí (o copia su contenido) para que lo analice y te dé recomendaciones precisas (índices, reescrituras, costo estimado).

Notas:
- El script reemplaza algunos placeholders comunes (`:desde`, `:hasta`, `:inicioMes`, `:finMes`, `:inicioSemana`, `:finSemana`, `:nombre`) por valores de ejemplo para que EXPLAIN pueda ejecutarse.
- Si alguna query falla por parámetros específicos, edita `tools/queries_to_explain.sql` y ajusta la query con valores concretos para tu BD antes de ejecutar.
- Para análisis más profundo, habilita el slow query log en MySQL y ejecuta pt-query-digest sobre el log (no cubierto por este script).
