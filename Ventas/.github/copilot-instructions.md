<!-- Copilot / AI agent instructions for Proyecto_Atlantis/Ventas -->
# Proyecto_Atlantis — AI coding assistant guide

Resumen: aplicación PHP (sin framework) estilo MVC para CRM/ventas. Front-end con jQuery/Bootstrap y assets en `vistas/` (incluye `bower_components/`). DB MySQL, conexión en `modelos/conexion.php` que carga variables desde `.env` si existe.

Quick map (what to open first):
- `vistas/` — plantillas, vistas parciales, `vistas/js/*.js` front-end.
- `ajax/*.ajax.php` — endpoints HTTP consumidos por JS. Typical flow: `vistas/js/*` -> `ajax/*.ajax.php` -> `controladores/*` -> `modelos/*` -> DB.
- `controladores/` — lógica que genera respuestas HTML/JS (Swal alerts, redirects).
- `modelos/` — consultas PDO; central `modelos/conexion.php` (usa `.env` if present).

Important patterns & examples (copyable):
- AJAX endpoint pattern: front-end posts to `ajax/clientes.ajax.php`. Example request handled there:

  - `vistas/js/clientes.js` performs `$.ajax({ url: 'ajax/clientes.ajax.php', method: 'POST', data: formData })`.
  - `ajax/clientes.ajax.php` validates `$_POST` keys then calls `ControladorCliente::...` or `ModeloCliente::...`.

- Controller → Model example: `controladores/clientes.controlador.php` calls `ModeloCliente::mdlRegistrarCliente($tabla,$datos)` to insert.
- DB access: `modelos/clientes.modelo.php` uses `Conexion::conectar()` (PDO) and prepared statements. Use try/catch and check `error_log()` messages present in code.

Dev environment & workflows (discoverable):
- Server: runs under XAMPP/Apache in Windows. Open `http://localhost/Proyecto_Atlantis/Ventas` after starting Apache/MySQL.
- DB config: put `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_CHARSET` in `.env` at project root or in `Ventas/.env`. `modelos/conexion.php` will load it.
- Migrations / SQL: look in `scripts/` for SQL to create tables or alter schema (e.g. `create_table_contador.sql`). There is no automated migration tool.
- Assets: front-end libs are vendored in `vistas/bower_components/` — no Node/npm build step required for basic work.

Project-specific conventions & gotchas:
- Sessions and permissions: many endpoints check `$_SESSION['perfil']` (e.g., `Vendedor`, `Administrador`) to allow/disallow actions — preserve these checks when refactoring.
- No test suite: there are no PHPUnit tests. Validate changes manually: start server and exercise UI flows.
- SQL safety: models use prepared statements but some dynamic SQL is built (e.g., filters in `mdlMostrarClientesFiltrados`). When adding filters, keep parameter binding.
- Limiting queries: methods for select2/select lists intentionally LIMIT results (e.g., `mdlMostrarClientesParaOportunidad`) to avoid heavy loads — keep that behavior.

Where to add changes for common tasks:
- Add new AJAX action: create endpoint in `ajax/yourfile.ajax.php` and call model/controller. Follow existing pattern for `action` POST keys and JSON responses.
- Add back-end logic: put business logic in `controladores/` and DB calls in `modelos/`.
- Add front-end handlers: modify `vistas/js/*.js` and call `ajax/*.ajax.php`.

Commit & branch hints (observed):
- Branches may use names like `configuracion_local`. Create feature branches from current default branch and push. There is no enforced PR template discovered.

If unclear, open these files for context:
- `ajax/clientes.ajax.php`, `controladores/clientes.controlador.php`, `modelos/clientes.modelo.php`, `modelos/conexion.php`, `vistas/js/clientes.js`.

Question for reviewer: ¿Quieres que incluya ejemplos de PR description y checklist (lint/run manual steps) o prefieres mantener el archivo minimalista y técnico?
