# Multi-tenant (semi-automático) — Guía de arranque

Objetivo: un solo código, múltiples subdominios, **una base de datos por cliente**.
En hosting compartido (Premium Web Hosting) la creación de la BD suele ser manual en hPanel, pero el sistema puede inicializarla automáticamente (tablas + datos base).

## Conceptos (1 línea)
- **Dominio principal** (`tudominio.com`): se mantiene como el tenant principal (BD actual).
- **Tenants clientes** (`cliente1.tudominio.com`): cada uno usa su propia BD.
- **Panel** (`panel.tudominio.com`): administra tenants (crear/activar/suspender) y corre sobre la BD master.

## Paso 1 — Crear tablas master (una sola vez)
Ejecuta el script en tu BD actual de producción (la “master”). **Para empezar NO necesitas crear una BD nueva**: la master puede ser la misma BD que hoy usa el sistema principal.
- scripts/migrations/2025-12-19_multitenant_master_tables.sql

En Hostinger puedes hacerlo con phpMyAdmin:
1) Entra a phpMyAdmin
2) Selecciona tu BD actual (ej: u652153415_atlantisdb)
3) Importa y ejecuta el archivo SQL

## Paso 2 — Preparar el bootstrap de un tenant
Necesitarás 2 archivos SQL que representen el estado mínimo para dejar un tenant “listo para usar”:
- `schema.sql`: estructura completa (CREATE TABLE ...)
- `seed.sql`: datos mínimos (roles, usuario admin del tenant, parámetros base, series/establecimientos por defecto, etc.)

Recomendación práctica:
- Exporta desde tu BD actual solo la estructura (sin datos) para construir `schema.sql`.
- Para `seed.sql`, incluye solo los datos esenciales que debe tener cada cliente desde cero.

## Paso 3 — Crear un tenant (por cada cliente)
1) Crear el subdominio en hPanel (ej: `cliente1.tudominio.com`) apuntando al mismo directorio de tu app.
2) Crear una BD nueva en hPanel + su usuario + asignar privilegios “All” sobre esa BD.
3) Registrar el tenant en las tablas master:
   - Insertar en `tenants` (ruc + subdomain + status = provisioning)
   - Insertar en `tenant_db` (host, name, user, pass, charset)
4) Inicializar la BD del tenant:
   - Ejecutar `schema.sql`
   - Ejecutar `seed.sql`
   - Aplicar ajustes secundarios (series/establecimientos) si no van en seed
5) Activar el tenant (`tenants.status = active`)

## Panel administrativo (cuándo se usa)
Una vez configurado `PANEL_HOST` (por ejemplo `admin.grupoatlantiscrm.eu`), al entrar a ese host verás el **panel** (login separado).

### Crear el primer usuario admin del panel
El panel usa la tabla `admin_users` (en la BD master). Para crear el primer usuario:

1) Genera un hash de contraseña (en tu PC con PHP):
   - Ejecuta: `php -r "echo password_hash('TU_PASSWORD', PASSWORD_DEFAULT);"`
2) En phpMyAdmin (sobre la BD master), inserta:
   - `INSERT INTO admin_users (username, password_hash, is_active) VALUES ('admin', 'PEGA_AQUI_EL_HASH', 1);`

Luego entra a `https://admin.grupoatlantiscrm.eu` y usa esas credenciales.

## Paso 4 — Resolver tenant por subdominio (cambio de app)
Se implementa una regla en el arranque de la app:
- Si host = `panel.tudominio.com` → usar BD master
- Si host = `tudominio.com` → usar BD principal (la actual)
- Si host = `clienteX.tudominio.com` → buscar `clienteX` en `tenants` y conectar a su BD

Configuración recomendada (por variables de entorno en `.env` o en config/production.php):
- `PANEL_HOST=admin.grupoatlantiscrm.eu`
- `MAIN_HOST=grupoatlantiscrm.eu`

NOTA: este paso se implementa en código para que el mismo proyecto soporte múltiples hosts.

## Paso 5 — Suspensión y seguridad (mínimo recomendado)
- Si `tenants.status = suspended`: bloquear acceso del subdominio del tenant.
- El panel debe tener login separado de los usuarios del sistema.
- No compartir cookies entre `panel.*` y `*.tudominio.com` (sesiones separadas).
