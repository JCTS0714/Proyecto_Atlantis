Preparación para desplegar en Hostinger

Pasos recomendados antes de subir el proyecto:

1) Ubicación en Hostinger
- Si subes los archivos a `public_html` pon los archivos directamente allí.
- Si usas un subdirectorio, recuerda ajustar `RewriteBase` en `.htaccess` (ej: `RewriteBase /mi_subcarpeta/`).

2) Configuración de producción
- Crea `config/production.php` con las claves necesarias (no comitees éste archivo):

  <?php
  return [
    'APP_ENV' => 'production',
    // fuerza protocolo http si lo necesitas: 'FORCE_HTTP' => false,
    'display_errors' => false,
    'error_reporting' => E_ALL & ~E_NOTICE,
    'FORCE_RELOAD_ON_UPDATE' => false,
  ];

- Opcional: define `BASE_HOST` si necesitas forzar un host concreto.

3) Base de datos
- Ejecuta el archivo SQL `scripts/add_motivo_to_clientes.sql` en tu base de datos antes de usar la columna `motivo`.
  Ejemplo (PowerShell local con cliente mysql):

  mysql -uDB_USER -pDB_PASS DB_NAME < "c:\xampp\htdocs\Proyecto_Atlantis\Ventas\scripts\add_motivo_to_clientes.sql"

- Asegúrate de subir/actualizar `modelos/conexion.php` o configurar variables en `.env` según tu setup. (NOTA: no modificar `modelos/conexion.php` sin probar en local primero).

4) Permisos y seguridad
- Asegúrate de que las carpetas de subida (si existen) no permitan ejecución de scripts.
- Verifica que `.htaccess` está activo (mod_rewrite habilitado) y ajusta `RewriteBase` si es necesario.

5) HTTPS
- Hostinger permite forzar HTTPS desde su panel. Si lo activas, valida que `index.php` maneje cookies seguras (ya implementado).

6) Caché del navegador
- Después de desplegar, limpia caché (Ctrl+F5) para recibir las nuevas JS/CSS.

7) Verificaciones post-despliegue
- Comprueba rutas: `BASE_URL` debería apuntar correctamente. Si ves enlaces rotos, revisa `config/production.php` y `RewriteBase`.
- Prueba: login, abrir modal editar cliente, zona de espera (botón Info), y tablas con búsqueda avanzada.

Si quieres, puedo:
- Generar una `config/production.php` de ejemplo listo para editar con tus credenciales (no lo subiré al repo por seguridad).
- Ajustar `RewriteBase` automáticamente si me indicas la ruta pública en tu hosting.
