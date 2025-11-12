# 🚀 GUÍA RÁPIDA: SUBIR CAMBIOS A MAIN Y CREAR NUEVAS RAMAS

## TU SITUACIÓN ACTUAL
✅ Ya tienes un repositorio en GitHub con rama `main`
✅ Ya subiste el proyecto a producción en infinityfree
✅ Quieres: Subir cambios a main y luego trabajar con ramas separadas

---

## PASO 1️⃣: VERIFICAR QUE TODO ESTÁ CONFIGURADO

Abre PowerShell (en Windows) o Terminal (Mac/Linux) en la carpeta de tu proyecto:

```powershell
# Ir a la carpeta del proyecto
cd c:\xampp\htdocs\Proyecto_atlantis

# Verificar que es un repositorio Git
git status
```

**Deberías ver algo como:**
```
On branch main
Your branch is up to date with 'origin/main'.
```

Si no, probablemente necesites inicializar:
```powershell
git init
git remote add origin https://github.com/tu-usuario/Proyecto_atlantis.git
```

---

## PASO 2️⃣: ACTUALIZAR MAIN CON TUS CAMBIOS ACTUALES

### 2.1 - Ver qué cambios hay
```powershell
git status
```

Verás archivos marcados como:
- `modified:` (modificados)
- `untracked:` (nuevos archivos no seguidos)

### 2.2 - Agregar todos los cambios
```powershell
git add .
```

Verifica que se agregaron:
```powershell
git status
```

Deberías ver `Changes to be committed:` con archivos en verde.

### 2.3 - Crear un commit con descripción clara
```powershell
git commit -m "feat: Deploy a producción exitoso - Botones editar/eliminar funcionales"
```

O si quieres descripción más larga:
```powershell
git commit -m "feat: Deploy a producción exitoso" -m "
- Arreglar botones editar y eliminar en seguimiento.php
- Agregar modales para editar clientes
- Implementar handlers JavaScript para AJAX
- Convertir rutas a absolutas para soporte con .htaccess
- Corregir error 404 en módulos
- Desplegar a infinityfreeapp.com exitosamente
"
```

### 2.4 - Subir cambios a GitHub (main)
```powershell
git push origin main
```

**Debería decir:**
```
Enumerating objects: XX, done.
...
To https://github.com/tu-usuario/Proyecto_atlantis.git
   XXXXX..XXXXX  main -> main
```

### 2.5 - Verificar en GitHub
1. Abre: https://github.com/tu-usuario/Proyecto_atlantis
2. Verifica que los cambios están ahí
3. Click en "commits" y deberías ver tu nuevo commit

✅ **¡LISTO! Tus cambios están en main**

---

## PASO 3️⃣: CREAR NUEVAS RAMAS PARA FUTURAS FUNCIONALIDADES

### Flujo General
```
main (producción - ESTABLE)
  ↓ (cuando terminas una funcionalidad)
  ├── feature/nueva-funcionalidad (desarrollo)
  ├── bugfix/corregir-error (correcciones)
  └── feature/otra-funcionalidad (desarrollo)
```

---

## 📌 CASOS DE USO

### CASO A: Quiero trabajar en una NUEVA FUNCIONALIDAD

**Ejemplo:** Agregar sistema de reportes

```powershell
# 1. Asegurate de estar en main actualizado
git checkout main
git pull origin main

# 2. Crea rama para tu funcionalidad
git checkout -b feature/sistema-reportes

# Ahora estás en la rama: feature/sistema-reportes
# Cualquier cambio que hagas solo afecta esta rama, NO main
```

**Luego, mientras trabajas:**
```powershell
# Ver en qué rama estás
git branch

# Haz cambios en tus archivos...

# Ver cambios realizados
git status
git diff

# Agregar cambios
git add .

# Guardar cambios (commit)
git commit -m "feat: Agregar vista de reportes mensuales"

# Subir rama a GitHub
git push origin feature/sistema-reportes
```

**Cuando terminas la funcionalidad:**
```powershell
# Opción 1: Fusionar manualmente desde GitHub
# (abre GitHub, crea Pull Request, aprueba y fusiona)

# Opción 2: Fusionar desde Terminal
git checkout main
git pull origin main
git merge feature/sistema-reportes
git push origin main

# Eliminar rama (ya no la necesitas)
git branch -d feature/sistema-reportes
git push origin --delete feature/sistema-reportes
```

---

### CASO B: Necesito CORREGIR UN ERROR (sin afectar main)

**Ejemplo:** Corregir error en modal de cliente

```powershell
# 1. Crea rama para bugfix
git checkout main
git pull origin main
git checkout -b bugfix/modal-cliente-error

# 2. Haz los cambios necesarios
# (editas los archivos para corregir...)

# 3. Prueba en local que funcione

# 4. Ver cambios
git status
git diff

# 5. Guardar cambios
git add .
git commit -m "fix: Corregir modal que no se cierra correctamente"

# 6. Subir rama
git push origin bugfix/modal-cliente-error

# 7. En GitHub: Crear Pull Request, revisar y fusionar

# 8. Actualizar main localmente
git checkout main
git pull origin main

# 9. Eliminar rama local
git branch -d bugfix/modal-cliente-error
git push origin --delete bugfix/modal-cliente-error
```

---

### CASO C: Estoy trabajando en algo y necesito cambiar de rama

**Problema:** Estabas trabajando en `feature/reportes` y necesitas arreglar un bug urgente

```powershell
# Opción 1: Guardar cambios temporales (Stash)
git stash                              # Guarda tus cambios temporalmente
git checkout -b bugfix/error-urgente   # Crea rama para el bug
# ... arreglas el bug ...
git add .
git commit -m "fix: Error urgente"
git push origin bugfix/error-urgente

# Volver a tu trabajo anterior
git checkout feature/reportes
git stash pop                          # Recupera tus cambios

# Opción 2: Hacer commit primero
git add .
git commit -m "WIP: En progreso - reportes"  # WIP = Work In Progress
git checkout -b bugfix/error-urgente
# ... arreglas el bug ...
```

---

### CASO D: Dos personas trabajan en diferentes ramas

```
CARLOS: Working on feature/dashboard-mejorado
├── git checkout -b feature/dashboard-mejorado
├── Hace cambios...
├── git push origin feature/dashboard-mejorado
└── Crea Pull Request

JUAN: Working on feature/sistema-usuarios
├── git checkout -b feature/sistema-usuarios
├── Hace cambios...
├── git push origin feature/sistema-usuarios
└── Crea Pull Request

Resultado: Main se mantiene limpio
Ambas funcionalidades se pueden revisar y fusionar sin conflictos
```

---

## 📋 COMANDOS RÁPIDOS (RESUMEN)

```powershell
# VER ESTADO
git status              # Estado actual
git branch              # Ramas locales
git branch -a           # Todas las ramas
git log --oneline       # Historial simplificado

# CREAR Y CAMBIAR RAMAS
git checkout -b feature/nombre    # Crear y cambiar a rama
git checkout nombre-rama          # Cambiar a rama existente
git checkout main                 # Volver a main

# GUARDAR CAMBIOS
git add .                         # Agregar todos los cambios
git commit -m "descripción"       # Guardar cambios
git push origin nombre-rama       # Subir a GitHub

# ACTUALIZAR DESDE GITHUB
git pull origin nombre-rama       # Bajar cambios

# FUSIONAR
git merge nombre-rama             # Fusionar rama actual
git merge feature/nombre          # Fusionar feature en main

# ELIMINAR RAMAS
git branch -d nombre-rama         # Eliminar rama local
git push origin --delete nombre-rama  # Eliminar rama remota

# DESHACER CAMBIOS
git checkout -- archivo.php       # Descartar cambios en archivo
git reset --hard                  # Descartar TODOS los cambios
```

---

## ✅ CHECKLIST: TU PRIMER SETUP

- [ ] Abres Terminal/PowerShell en tu proyecto
- [ ] Ejecutas `git status` y ves cambios
- [ ] Ejecutas `git add .`
- [ ] Ejecutas `git commit -m "descripción"`
- [ ] Ejecutas `git push origin main`
- [ ] Verificas en GitHub que los cambios están ahí
- [ ] Creas una rama nueva: `git checkout -b feature/mi-funcionalidad`
- [ ] Haces cambios y confirmas: `git add .` → `git commit -m "..."` → `git push origin feature/mi-funcionalidad`
- [ ] Ves tu rama en GitHub
- [ ] Creas Pull Request (PR) en GitHub
- [ ] Haces merge del PR a main

---

## 🎯 PRÓXIMAS ACCIONES SUGERIDAS

1. **Sube cambios actuales a main** (Paso 2 arriba)
2. **Crea ramas para nuevas funcionalidades** (Caso A)
3. **Aprende a hacer Pull Requests** (revisión en GitHub)
4. **Establece reglas en main** (requiere revisión antes de merge)

---

## 🆘 PROBLEMAS COMUNES

### "Quiero descartar mis cambios locales"
```powershell
git reset --hard origin/main
```

### "Hice cambios pero en rama equivocada"
```powershell
git stash                           # Guarda cambios
git checkout -b rama-correcta       # Crea rama correcta
git stash pop                       # Recupera cambios
```

### "Quiero ver qué cambios hay entre ramas"
```powershell
git diff main feature/mi-rama       # Diferencias
git log main..feature/mi-rama       # Commits que tiene feature pero no main
```

### "Accidentalmente borraba una rama"
```powershell
git reflog                          # Ver historial
git checkout -b rama-recuperada COMMIT_ID
```

---

## 💡 RECOMENDACIÓN FINAL

**Estrategia de trabajo recomendada:**

```
main (SIEMPRE ESTABLE - PRODUCCIÓN)
  ↓
feature/X (haces cambios aquí)
  ↓
Pull Request (en GitHub se revisa)
  ↓
Merge a main (se fusiona cuando está aprobado)
  ↓
Deploy a producción (infinityfree)
```

Esto garantiza que main siempre tenga código funcional.

