Production deployment checklist — Atlantis CRM

1) Create production config (never commit credentials)
   - Copy `config/production.example.php` -> `config/production.php` and fill DB credentials and optional overrides.
   - Ensure `config/production.php` is in `.gitignore`.

2) Environment variables (alternative to production.php)
   - Set at host/serving layer: `APP_ENV=production`, `BASE_HOST=yourdomain.com`, `FORCE_HTTP=0`.

3) PHP settings
   - Disable display_errors in production.
   - Ensure `error_log` path exists and is writable by the webserver (e.g., `logs/`).

4) File permissions
   - Set webserver-owned user for `logs/`, `uploads/` (if any) and cache directories.

5) HTTPS
   - Use HTTPS and set secure cookies (see `SESSION_SECURE_ONLY` in production config).

6) Remove or archive dev-only files
   - In this repo `archive_dev/` contains moved files that should not be served: `.env`, `bd.txt`, backups, `scripts/` and `sql/` migration files. Keep them outside `public`.

7) Frontend assets
   - Ensure `BASE_URL` resolves correctly. If deploying in a subdirectory, update `BASE_HOST` or `BASE_URL` accordingly.

8) Security
   - Ensure `.git` is not publicly accessible.
   - Ensure `Documentacion/` and `bd.txt` not present in public.

9) Optional: enable caching / minification
   - Use asset pipeline or serve built/minified assets for performance.

Quick smoke test:
- Visit the app root and a protected route (login). Verify assets load, login works, and errors are not printed to the screen.
