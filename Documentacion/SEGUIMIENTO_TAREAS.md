# Seguimiento de tareas (Atlantis CRM)

Regla: iremos marcando en orden lo que terminamos.
Fecha inicio: 2025-12-17

## 1) Orden / higiene del repo (docroot)
- [x] Cuarentena incremental de assets sueltos del root (sin romper app)
- [x] Cuarentena de meta/build files de CKEditor que estaban en el root
- [x] Root limpio manteniendo entrypoints (index.php/.htaccess) y carpetas de app

## 2) Backend: migraciones fuera del runtime
- [x] Detectar `ALTER TABLE` en runtime (incidencias.columna_backlog)
- [x] Crear migración versionada: `scripts/migrations/2025-12-17_add_incidencias_columna_backlog.sql`
- [x] Eliminar `ALTER TABLE` del runtime en `modelos/ModeloIncidencias.php`
- [x] Buscar otros casos similares en el repo (ALTER/CREATE/DROP en PHP)

## 3) Seguridad inmediata (producción)
- [x] Bloquear acceso web a scripts de diagnóstico/pruebas/debug desde `.htaccess` (403)

## 4) Backend: API/JSON uniforme (próximo)
- [x] Definir contrato estándar de respuesta JSON: `{ ok: true, data }` / `{ ok: false, error: { code, message } }`
- [x] Crear helper reutilizable para respuestas JSON (y headers)
- [x] Migrar 1-2 endpoints representativos (incidencias + oportunidades)
- [x] Ajustar JS cliente para compatibilidad con wrapper (incidencias/backlog)
- [x] Ajustar JS cliente para compatibilidad con wrapper (oportunidades/clientes)
- [x] Migrar `ajax/clientes.ajax.php` al wrapper + compat en `vistas/js/clientes.js`
- [x] Ajustar consumidores (calendario/oportunidades/vistas) para desempaquetar `.data`
- [x] Migrar `ajax/clientes_guardar.ajax.php` al wrapper + ID correcto
- [x] Hardening `ajax/clientes_actividades.ajax.php` (auth + error handler, salida HTML intacta)
- [x] Hardening `ajax/clientes_oportunidades.ajax.php` (auth + error handler, salida Select2 intacta)
- [x] Migrar `ajax/calendario.ajax.php` al wrapper + compat (mantener `eventos`/`success` y reducir debug por defecto)

## 5) Backend: validación / permisos / CSRF
- [ ] Centralizar validación server-side (tipos, longitudes, enums)
- [ ] Control de permisos por acción (no solo autenticación)
- [ ] Añadir CSRF a formularios/endpoints que modifican datos (incremental)

## 6) Backend: router/API (refactor gradual)
- [ ] Crear front controller para API (p.ej. `/api/...`) manteniendo compatibilidad con `ajax/*.php`
- [ ] Separar rutas: páginas vs API (fases)

## 7) Backend: autoload + convención de nombres
- [ ] Definir convención única de nombres (controladores/modelos)
- [ ] Introducir autoload (ideal PSR-4) y reducir `require_once`

## 8) Backend: separar lógica de negocio
- [ ] Introducir capa de servicios/use-cases (ej. `IncidenciaService`)
- [ ] Mantener controladores como capa HTTP fina
