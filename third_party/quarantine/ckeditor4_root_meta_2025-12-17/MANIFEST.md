# CKEditor root meta quarantine (2025-12-17)

Purpose: move CKEditor tooling/metadata files out of the web root to reduce clutter.

Moved files (from repo root):
- README.md
- composer.json
- package.json
- bower.json
- gulpfile.js
- Gruntfile.js

Notes:
- No application references to CKEditor were found under `vistas/`.
- These files are not required for runtime execution of the Atlantis PHP app.
