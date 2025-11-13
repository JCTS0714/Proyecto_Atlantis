# 🏋️ EJERCICIO 2: TRABAJO EN EQUIPO - DOS RAMAS EN PARALELO

En este ejercicio vamos a simular que tú y un compañero trabajan en paralelo en dos features diferentes, sin conflictos.

## 📋 ESCENARIO

```
Tú:           Trabajarás en feature/modulo-dashboard
Compañero:    Trabajarás (yo simularé) en feature/modulo-usuarios
Timeline:     Ambos en PARALELO, nunca en la misma rama
Resultado:    Ambas features integradas en main
```

---

## PARTE 1: Tú trabajas en tu feature

### PASO 1: Crear tu rama

```powershell
cd c:\xampp\htdocs\Proyecto_atlantis

# Asegurate de estar en main
git checkout main
git pull origin main

# Crear TU rama
git checkout -b feature/modulo-dashboard
```

**Debería mostrar:**
```
Switched to a new branch 'feature/modulo-dashboard'
```

### PASO 2: Hacer cambios en tu rama

Vamos a simular que estás trabajando en el dashboard. Crea un archivo:

```powershell
# Crear archivo de tu trabajo
@"
# Dashboard Mejorado - Feature

## Cambios realizados:
- Mejorada visualización de gráficos
- Agregado widget de resumen de ventas
- Optimizado tiempo de carga

## Archivos modificados:
- Ventas/vistas/modulos/dashboard.php
- Ventas/vistas/js/dashboard.js

## Estado: EN DESARROLLO

Fecha: $(Get-Date)
"@ | Out-File -Encoding UTF8 "FEATURE_DASHBOARD.md"

# Ver cambios
git status
```

### PASO 3: Guardar cambios en tu rama

```powershell
# Agregar archivo
git add FEATURE_DASHBOARD.md

# Commit
git commit -m "feat: Dashboard - Mejorar visualización y agregar widgets"

# Ver tu commit
git log --oneline -3
```

### PASO 4: Subir tu rama a GitHub

```powershell
git push origin feature/modulo-dashboard
```

**Debería mostrar:**
```
* [new branch]      feature/modulo-dashboard -> feature/modulo-dashboard
```

**IMPORTANTE:** Ahora tu rama está en GitHub, pero `main` sigue sin cambios.

---

## PARTE 2: Simular que tu compañero trabaja

Ahora voy a simular que tu compañero está trabajando en su rama. En realidad, los dos estamos en el mismo repo, pero vamos a hacer como si fueran computadoras diferentes.

### PASO 5: Cambiar a main (antes de que el compañero empiece)

```powershell
# Cambiar a main (está actualizada)
git checkout main
```

### PASO 6: Simular el trabajo del compañero

Vamos a crear su rama:

```powershell
# El compañero crearía su rama
git checkout -b feature/modulo-usuarios

# Crear su archivo
@"
# Módulo de Usuarios - Feature

## Cambios realizados:
- Sistema mejorado de permisos
- Nuevo roles para supervisores
- Auditoría de acciones de usuarios

## Archivos modificados:
- Ventas/controladores/usuarios.controlador.php
- Ventas/modelos/usuarios.modelo.php
- Ventas/vistas/modulos/usuarios.php

## Estado: EN DESARROLLO

Fecha: $(Get-Date)
"@ | Out-File -Encoding UTF8 "FEATURE_USUARIOS.md"

# Ver cambios
git status
```

### PASO 7: El compañero guarda sus cambios

```powershell
git add FEATURE_USUARIOS.md
git commit -m "feat: Usuarios - Mejorar permisos y agregar roles"
git push origin feature/modulo-usuarios
```

---

## PARTE 3: Ahora ambos tienen sus ramas en GitHub

Verifiquemos que las dos ramas existen:

```powershell
# Ver TODAS las ramas (local y remoto)
git branch -a
```

**Debería mostrar:**
```
* feature/modulo-usuarios
  main
  remotes/origin/feature/modulo-dashboard
  remotes/origin/feature/modulo-usuarios
  remotes/origin/main
```

**¿Ves? Tenemos:**
- ✅ Tu rama: `feature/modulo-dashboard`
- ✅ Rama del compañero: `feature/modulo-usuarios`
- ✅ main (sin cambios)

---

## PARTE 4: Integrar TU trabajo a main

Primero vas a integrar tu feature.

### PASO 8: Cambiar a main

```powershell
# Cambiar a main
git checkout main

# Actualizar main con cambios de GitHub (si hay)
git pull origin main
```

### PASO 9: Fusionar TU rama

```powershell
# Fusionar tu feature a main
git merge feature/modulo-dashboard

# Debería mostrar:
# Updating ... (hash)
# Fast-forward
# FEATURE_DASHBOARD.md | ...
```

### PASO 10: Subir a GitHub

```powershell
git push origin main
```

**Resultado:** main ahora tiene tu feature integrada.

---

## PARTE 5: Tu compañero integra su trabajo

Ahora vamos a integrar la rama del compañero.

### PASO 11: Tu compañero se actualiza desde main

```powershell
# El compañero cambiaría a main
git checkout main

# Actualizar desde GitHub (traería TUS cambios)
git pull origin main
```

**Importante:** Su `main` local ahora incluye tus cambios (el dashboard).

### PASO 12: El compañero fusiona su rama

```powershell
# Fusionar la rama del compañero
git merge feature/modulo-usuarios
```

**Resultado:**
```
Updating e3ef1b4..abc1234
Fast-forward
 FEATURE_USUARIOS.md | ...
```

### PASO 13: El compañero sube a GitHub

```powershell
git push origin main
```

---

## PARTE 6: Verificación final

### PASO 14: Ver el resultado

```powershell
# Ver todos los cambios en main
git log --oneline -5
```

**Debería mostrar algo como:**
```
abc1234 feat: Usuarios - Mejorar permisos y agregar roles
e3ef1b4 feat: Dashboard - Mejorar visualización y agregar widgets
d4e5f67 feat: Prueba de rama - Ejercicio 1 completo
...
```

### PASO 15: Ver los archivos

```powershell
# Listar archivos en main
ls

# Deberías ver AMBOS archivos:
# - FEATURE_DASHBOARD.md (tu trabajo)
# - FEATURE_USUARIOS.md (del compañero)
```

### PASO 16: Ver el árbol de commits (Bonito)

```powershell
git log --graph --oneline --all
```

**Mostrará:**
```
* abc1234 feat: Usuarios - Mejorar permisos y agregar roles
* e3ef1b4 feat: Dashboard - Mejorar visualización y agregar widgets
* d4e5f67 feat: Prueba de rama - Ejercicio 1 completo
* ...
```

---

## PARTE 7: Limpiar ramas (Opcional)

### PASO 17: Eliminar ramas que ya se mergearon

```powershell
# Ver qué ramas ya fueron mergeadas
git branch --merged

# Eliminar rama local
git branch -d feature/modulo-dashboard
git branch -d feature/modulo-usuarios

# Eliminar de GitHub
git push origin --delete feature/modulo-dashboard
git push origin --delete feature/modulo-usuarios
```

---

## ✅ RESUMEN DE LO QUE APRENDISTE

### Conceptos clave:

1. **Ramas independientes**: Cada feature en su propia rama
2. **Trabajo paralelo**: Dos ramas sin interferencia
3. **SIN conflictos**: Porque trabajan en archivos diferentes
4. **Integración secuencial**: Primero uno, luego el otro
5. **main como línea de tiempo**: Registra cada feature integrada

### Comando clave: `git branch -a`

Muestra todas las ramas locales y remotas. Útil para ver qué está pasando.

### Diferencia entre:
- `git branch` = solo ramas locales
- `git branch -a` = local + remoto
- `git branch -r` = solo remoto

---

## 🎯 PRÓXIMO: ¿QUÉ PASA SI HAY CONFLICTOS?

En el Ejercicio 3, vamos a trabajar en el MISMO archivo desde dos ramas diferentes para ver cómo Git maneja conflictos.

---

## 📝 CHECKLIST DE EJERCICIO 2

- [ ] Creé `feature/modulo-dashboard`
- [ ] Agregué `FEATURE_DASHBOARD.md`
- [ ] Hice commit en mi rama
- [ ] Pusheé a GitHub
- [ ] Creé `feature/modulo-usuarios`
- [ ] Agregué `FEATURE_USUARIOS.md`
- [ ] Hice commit en la otra rama
- [ ] Pusheé a GitHub
- [ ] Fusioné `feature/modulo-dashboard` a main
- [ ] Pusheé main
- [ ] Fusioné `feature/modulo-usuarios` a main
- [ ] Pusheé main nuevamente
- [ ] Verifiqué que ambos archivos están en main
- [ ] Verifiqué el historial con `git log --oneline`
- [ ] Limpié las ramas

**¿Ejecuto el ejercicio?** 🚀
