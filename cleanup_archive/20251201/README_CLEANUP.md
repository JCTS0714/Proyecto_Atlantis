# Cleanup archive (created 2025-12-01)

This folder contains files moved out of the repository root to reduce noise and duplicates.

Files moved (if present):
- index.html : duplicate/unused HTML entrypoint — archived to avoid confusion with index.php.
- index.rst : documentation source file (archived).
- _translationstatus.txt : transient translation status file (archived).
- 	mp_paths.php : temporary paths/test file (archived).
- entas_debug.php : debug helper (archived).
- package.js : legacy/duplicate package descriptor (archived).
- .bower.json : legacy bower config (archived).
- MyGet.ps1 : packaging script (archived).
- ootstrap.less.nuspec, ootstrap.nuspec : NuGet packaging metadata (archived).

To restore a file: git checkout cleanup/20251201 -- <path/to/file> or merge this branch.
