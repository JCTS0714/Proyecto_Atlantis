# 🏋️ EJERCICIOS PRÁCTICOS: TRABAJAR CON RAMAS

## EJERCICIO 1: Tu Primer Ciclo Completo 

Vamos a hacer un ciclo REAL desde crear rama hasta integrar a main.

### PASO 1: Preparar el entorno

```powershell
cd c:\xampp\htdocs\Proyecto_atlantis

# Asegurate que estás en main
git checkout main

# Actualiza main con cambios de GitHub
git pull origin main
```

**Debería mostrar:**
```
Already up to date.
```

### PASO 2: Crear rama nueva

```powershell
git checkout -b feature/prueba-ramas
```

**Debería mostrar:**
```
Switched to a new branch 'feature/prueba-ramas'
```

Verifica que estás en la rama:
```powershell
git branch
```

**Debería mostrar:**
```
* feature/prueba-ramas
  main
```

### PASO 3: Hacer un cambio en la rama

Crea un archivo nuevo para documentar esta prueba:

```powershell
# Crear archivo
"# Prueba de Rama - $(Get-Date)" > PRUEBA_RAMA.md

# Ver que se creó
git status
```

**Debería mostrar:**
```
Untracked files:
  PRUEBA_RAMA.md
```

### PASO 4: Guardar cambios

```powershell
# Agregar archivo
git add PRUEBA_RAMA.md

# Hacer commit
git commit -m "feat: Agregar archivo de prueba en rama feature/prueba-ramas"

# Ver commits
git log --oneline -3
```

### PASO 5: Subir rama a GitHub

```powershell
git push origin feature/prueba-ramas
```

**Debería mostrar:**
```
To https://github.com/JCTS0714/Proyecto_Atlantis.git
 * [new branch]      feature/prueba-ramas -> feature/prueba-ramas
```

### PASO 6: Verificar en GitHub

1. Abre: https://github.com/JCTS0714/Proyecto_Atlantis
2. Deberías ver un botón: "Feature/prueba-ramas" o menú de ramas
3. Verifica que PRUEBA_RAMA.md está en esa rama

### PASO 7: Fusionar a main (Merge)

```powershell
# Cambiar a main
git checkout main

# Bajar cambios de GitHub (si hay)
git pull origin main

# Fusionar rama en main
git merge feature/prueba-ramas

# Ver resultado
git log --oneline -3
```

**Debería mostrar:**
```
Merge made by the 'ort' strategy.
 PRUEBA_RAMA.md | 1 +
```

### PASO 8: Subir main a GitHub

```powershell
git push origin main
```

### PASO 9: Limpiar rama (opcional pero recomendado)

```powershell
# Borrar rama local
git branch -d feature/prueba-ramas

# Borrar rama remota
git push origin --delete feature/prueba-ramas

# Verificar que se borraron
git branch -a
```

### PASO 10: Celebrar! 🎉

```powershell
git log --oneline -3
```

Debería mostrar:
- Tu commit de main actualizado
- Sin la rama feature/prueba-ramas (ya está integrada)

---

## EJERCICIO 2: Trabaja en Rama Mientras Otro Actualiza Main

**Objetivo:** Simular trabajo paralelo (como si dos personas trabajaran simultáneamente)

### Parte A: Crear rama de trabajo
```powershell
git checkout main
git pull origin main
git checkout -b feature/agregar-documentacion
```

### Parte B: Hacer cambios en tu rama
```powershell
# Crear archivo de doc
"## Mi Nueva Documentación" > NUEVA_DOC.md

git add NUEVA_DOC.md
git commit -m "docs: Agregar nueva documentación"
git push origin feature/agregar-documentacion
```

### Parte C: Alguien más actualiza main
```powershell
# Simular que alguien más subió cambios a main
git checkout main
git pull origin main

# (en realidad en GitHub alguien hizo merge de otra rama)
```

### Parte D: Vuelves a tu rama sin problemas
```powershell
git checkout feature/agregar-documentacion

# Tu rama no fue afectada! Todo tranquilo
```

---

## EJERCICIO 3: Trabajar En Múltiples Ramas

**Objetivo:** Alternar entre ramas mientras trabajas en diferentes cosas

### Crear primera rama
```powershell
git checkout main
git checkout -b feature/reportes-v1

# Haz cambios...
git add .
git commit -m "Agregar estructura de reportes"
git push origin feature/reportes-v1
```

### Cambiar a segunda rama (sin terminar la primera)
```powershell
git checkout main
git checkout -b bugfix/corregir-email

# Haz cambios...
git add .
git commit -m "fix: Corregir validación de email"
git push origin bugfix/corregir-email
```

### Volver a la primera rama
```powershell
git checkout feature/reportes-v1

# Continúas donde dejaste!
# Tu trabajo de reportes sigue ahí
```

### Volver a la segunda rama
```powershell
git checkout bugfix/corregir-email

# Tu trabajo de email sigue aquí!
```

### Integrar ambas a main
```powershell
git checkout main
git pull origin main

# Integrar primero el bugfix
git merge bugfix/corregir-email
git push origin main

# Luego integrar la feature
git merge feature/reportes-v1
git push origin main

# Limpiar ramas
git branch -D feature/reportes-v1 bugfix/corregir-email
git push origin --delete feature/reportes-v1 bugfix/corregir-email
```

---

## EJERCICIO 4: Si Algo Sale Mal

### Escenario: Cometiste un error en tu rama

```powershell
git checkout feature/mi-rama

# Ver commits
git log --oneline -5

# Deshacer último commit (pero mantiene cambios)
git reset --soft HEAD~1

# O deshacer último commit (PIERDE cambios)
git reset --hard HEAD~1
```

### Escenario: Querías cambiar de rama pero tienes cambios sin guardar

```powershell
git checkout feature/rama1

# Editas archivo...
# Pero necesitas cambiar de rama urgente

# Opción 1: Guardar temporalmente
git stash              # Guarda cambios
git checkout main      # Cambias de rama
git checkout feature/rama1  # Vuelves
git stash pop         # Recuperas cambios

# Opción 2: Hacer commit
git add .
git commit -m "WIP: En progreso"
git checkout main
```

### Escenario: Eliminaste rama por accidente

```powershell
# Ver historial de ramas
git reflog

# Encontrar tu rama en la lista
# Ejemplo: abc1234 your-branch-name

# Recuperar
git checkout -b rama-recuperada abc1234
```

---

## EJERCICIO 5: Entender Conflictos

**Objetivo:** Provocar un conflicto e intentar resolverlo

### PASO 1: Crear situación de conflicto

```powershell
# Asegurate en main
git checkout main

# Edita el archivo RESUMEN_FINAL_CAMBIOS.md
# Cambia la línea 1 a: "# VERSIÓN 1.0 MAIN"

git add RESUMEN_FINAL_CAMBIOS.md
git commit -m "Actualizar versión en main"
```

### PASO 2: Crear rama que modifica el mismo archivo

```powershell
git checkout -b feature/otra-version
```

### PASO 3: Editar el mismo archivo en la rama

```powershell
# En feature/otra-version, edita RESUMEN_FINAL_CAMBIOS.md
# Cambia la línea 1 a: "# VERSIÓN 1.0 FEATURE"

git add RESUMEN_FINAL_CAMBIOS.md
git commit -m "Actualizar versión en feature"
```

### PASO 4: Intentar fusionar (esto causará conflicto)

```powershell
git checkout main
git merge feature/otra-version

# CONFLICTO!
git status
```

### PASO 5: Ver el conflicto

```powershell
# Abre el archivo con conflicto
cat RESUMEN_FINAL_CAMBIOS.md

# Verás algo como:
# <<<<<<< HEAD
# # VERSIÓN 1.0 MAIN
# =======
# # VERSIÓN 1.0 FEATURE
# >>>>>>> feature/otra-version
```

### PASO 6: Resolver el conflicto

```powershell
# Edita manualmente el archivo
# Elige qué versión quieres (o combina ambas)
# Borra las líneas de conflicto: <<<<, ====, >>>>

# Guardas los cambios...

git add RESUMEN_FINAL_CAMBIOS.md
git commit -m "Resolver conflicto de versión"

# Listo! Conflicto resuelto
```

---

## 🎯 CHECKLIST DE EJERCICIOS

Cuando hayas completado todos:

- [ ] Ejercicio 1: Ciclo completo feature/prueba-ramas
- [ ] Ejercicio 2: Trabajo paralelo
- [ ] Ejercicio 3: Múltiples ramas simultáneas
- [ ] Ejercicio 4: Manejo de errores
- [ ] Ejercicio 5: Resolver conflictos

---

## 💡 TIPS FINALES

### Nombra tus ramas claramente
```
✅ BIEN:
feature/agregar-sistema-reportes
bugfix/corregir-modal-cliente
hotfix/error-produccion-critico

❌ MAL:
rama1, feature2, bugfix_nuevo
```

### Haz commits descriptivos
```
✅ BIEN:
"feat: Agregar tabla de reportes mensuales"
"fix: Modal se cierra correctamente"

❌ MAL:
"cambios", "actualizar", "fix"
```

### Mantén ramas cortas
```
✅ BIEN:
Rama dura 1 semana
1-3 cambios relacionados

❌ MAL:
Rama dura 2 meses
20 cambios diferentes
```

### Actualiza antes de empezar
```powershell
git checkout main
git pull origin main
git checkout -b feature/nueva

# Ahora sí, comenzar a trabajar
```

---

## 🚀 PRÓXIMO PASO

¿Quieres que ejecutemos juntos el **EJERCICIO 1** ahora mismo?

Puedo:
1. Creo rama `feature/prueba-ramas`
2. Hago cambio
3. Sube a GitHub
4. Fusiono a main
5. Verificamos en GitHub que funciona

¿Lo hacemos?

