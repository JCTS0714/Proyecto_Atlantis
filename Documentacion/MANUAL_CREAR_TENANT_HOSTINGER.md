# Manual — Crear un nuevo tenant (Hostinger + Panel)

Este manual asume:
- Hosting: Hostinger Premium Web Hosting (shared)
- Patrón: multi-tenant por subdominio + **1 base de datos por tenant**
- Panel: accesible como `https://admin.TU_DOMINIO/panel/`

## 0) Prerrequisitos (una sola vez)
1) Ejecuta la migración master en la BD principal (la actual):
- `scripts/migrations/2025-12-19_multitenant_master_tables.sql`

2) Configura `.env` en el docroot del proyecto (archivo `.env` al lado de `index.php`):
- `PANEL_HOST=admin.TU_DOMINIO`
- `MAIN_HOST=TU_DOMINIO`
- `DB_HOST=...` `DB_NAME=...` `DB_USER=...` `DB_PASS=...` (credenciales de la BD master)

3) Crea el primer usuario del panel (en BD master):
- Genera hash: `php -r "echo password_hash('TU_PASSWORD', PASSWORD_DEFAULT), PHP_EOL;"`
- Inserta en `admin_users`:
  - `INSERT INTO admin_users (username, password_hash, is_active) VALUES ('admin', 'HASH', 1);`

4) Verifica que el panel abre:
- `https://admin.TU_DOMINIO/panel/`

> Nota Hostinger: si tu subdominio `admin` no permite cambiar docroot, usa siempre `/panel/`.

---

## 1) Crear el subdominio del cliente (hPanel)
Ejemplo: cliente `cliente2`

1) Entra a Hostinger hPanel → **Domains** → **Subdomains**
2) Crea subdominio: `cliente2` (quedará `cliente2.TU_DOMINIO`)
3) **Document root / carpeta**:
- Si Hostinger no permite elegir otra, déjalo apuntando al docroot principal (public_html).
- No necesitas clonar archivos: el mismo código sirve para todos los subdominios.

Resultado esperado:
- `https://cliente2.TU_DOMINIO` ya apunta a tu aplicación.

---

## 2) Crear la base de datos del tenant (hPanel)
En Hostinger: **Databases** → **MySQL Databases**

1) Crea una nueva base de datos (ejemplo):
- DB name: `uXXXXX_cliente2`

2) Crea (o asigna) un usuario MySQL para esa BD:
- User: `uXXXXX_cliente2`
- Password: una contraseña fuerte

3) Asigna privilegios:
- Debe tener **ALL PRIVILEGES** sobre esa BD.

Guarda estos 4 datos, los necesitarás en el panel:
- `db_host` (normalmente `localhost`)
- `db_name`
- `db_user`
- `db_pass`

---

## 3) Inicializar la BD del tenant (phpMyAdmin)
1) Entra a phpMyAdmin desde hPanel
2) Selecciona la BD del tenant (ej: `uXXXXX_cliente2`)
3) Importa estructura/datos base

Opciones recomendadas:
- Si tienes export “estructura + seed” preparado:
  - Importa primero `schema.sql`
  - Importa luego `seed.sql`

Si todavía no tienes schema/seed separados, alternativa práctica:
- Exporta desde tu BD principal “Structure only” y lo importas al tenant.
- Luego agrega los datos mínimos necesarios para arrancar (usuarios/parametría mínima).

Validación rápida:
- Asegúrate de que existan las tablas clave que tu app usa al entrar.

---

## 4) Registrar el tenant en el Panel Administrativo
1) Abre el panel:
- `https://admin.TU_DOMINIO/panel/`

2) Login con tu usuario admin del panel.

3) Crear tenant:
- Subdomain: `cliente2` (solo el subdominio, sin el dominio)
- RUC / identificador: según tu negocio
- Status: dejar como `active` (si ya está lista la BD) o usar `provisioning` mientras importas

4) Registrar datos de BD del tenant:
- DB Host: `localhost` (o el host que te da Hostinger)
- DB Name: `uXXXXX_cliente2`
- DB User: `uXXXXX_cliente2`
- DB Pass: (la contraseña que creaste)
- Charset: `utf8mb4`

5) Guardar.

---

## 5) Verificación (muy importante)
1) Prueba el check del tenant:
- `https://cliente2.TU_DOMINIO/tenant_health.php`

Debe mostrar:
- `ok: true`
- `app_mode: tenant`
- `tenant_subdomain: "cliente2"`
- `db_current: "uXXXXX_cliente2"`

2) Prueba la web del tenant:
- `https://cliente2.TU_DOMINIO/`

Si sale en blanco o 500:
- La conexión multi-tenant está OK (si tenant_health ok), entonces falta:
  - tablas en la BD del tenant
  - datos base (seed)
  - o hay un error en PHP/consultas por datos inexistentes

---

## 6) Suspender / activar tenant
- Suspender: cambia `tenants.status` a `suspended`.
- Activar: cambia `tenants.status` a `active`.

Efecto:
- Si está `suspended`, el subdominio devuelve 403 `Account inactive`.

---

## 7) Checklist rápido (copiable)
- [ ] Crear subdominio en hPanel (`clienteX`)
- [ ] Crear BD + usuario + ALL PRIVILEGES
- [ ] Importar `schema.sql` + `seed.sql` (o estructura + datos mínimos)
- [ ] En panel: crear tenant + configurar credenciales de BD
- [ ] Probar `tenant_health.php`
- [ ] Probar home del tenant
