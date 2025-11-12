# 📚 GUÍA COMPLETA: GIT Y GITHUB PARA TU PROYECTO

## 📋 ÍNDICE
1. [Conceptos Básicos](#conceptos-básicos)
2. [Configuración Inicial](#configuración-inicial)
3. [Workflow con Ramas](#workflow-con-ramas)
4. [Comandos Esenciales](#comandos-esenciales)
5. [Casos Prácticos](#casos-prácticos)
6. [Solución de Problemas](#solución-de-problemas)

---

## 🎯 CONCEPTOS BÁSICOS

### ¿Qué es Git?
Git es un sistema de control de versiones que te permite:
- 📝 Guardar historial de cambios
- 🌿 Trabajar en múltiples versiones simultáneamente (ramas)
- 👥 Colaborar con otros sin perder código
- ⏮️ Volver a versiones anteriores si algo falla

### ¿Qué es una rama (branch)?
Una rama es una línea de desarrollo independiente:
```
main (rama principal)
├── feature/editar-clientes (rama de funcionalidad)
├── bugfix/error-404 (rama de corrección)
└── feature/nuevo-dashboard (rama de funcionalidad)
```

### Estados de un archivo en Git
```
Working Directory (tu carpeta local)
        ↓ git add
Staging Area (preparado para guardar)
        ↓ git commit
Repository (historial guardado)
        ↓ git push
GitHub (servidor remoto)
```

---

## ⚙️ CONFIGURACIÓN INICIAL

### Paso 1: Instalar Git
Descarga desde: https://git-scm.com/download

Después de instalar, verifica en terminal:
```bash
git --version
```

### Paso 2: Configurar tu identidad
```bash
git config --global user.name "Tu Nombre"
git config --global user.email "tu@email.com"
```

Verifica la configuración:
```bash
git config --global --list
```

### Paso 3: Clonar tu repositorio desde GitHub
```bash
git clone https://github.com/tu-usuario/Proyecto_atlantis.git
cd Proyecto_atlantis
```

---

## 🌿 WORKFLOW CON RAMAS

### Estructura de Ramas Recomendada
```
main (producción - ESTABLE)
  ↑
  ├── develop (desarrollo - punto de integración)
  ↑
  ├── feature/nombres-funcionalidades (nuevas funcionalidades)
  ├── bugfix/nombres-errores (correcciones de bugs)
  └── hotfix/nombres-urgentes (correcciones urgentes)
```

### El Ciclo de Vida Ideal

```
1. Estás trabajando en main
   git checkout main
   
2. Creas una rama nueva para una funcionalidad
   git checkout -b feature/editar-productos
   
3. Trabajas y haces cambios locales
   (editas archivos...)
   
4. Guardas los cambios con commit
   git add .
   git commit -m "Agregar funcionalidad X"
   
5. Subes la rama a GitHub
   git push origin feature/editar-productos
   
6. Haces un Pull Request en GitHub
   (solicitas fusión a main)
   
7. Se revisa y se fusiona a main
   (tu código ahora está en producción)
   
8. Borras la rama local y remota (opcional)
   git branch -d feature/editar-productos
   git push origin --delete feature/editar-productos
```

---

## 🛠️ COMANDOS ESENCIALES

### Ver Estado Actual
```bash
# Ver estado de los archivos
git status

# Ver historial de commits
git log

# Ver commits de forma más visual
git log --oneline --graph --all
```

### Trabajar con Ramas
```bash
# Ver todas las ramas locales
git branch

# Ver todas las ramas (local y remota)
git branch -a

# Crear una rama nueva (y moverse a ella)
git checkout -b feature/nombre-funcionalidad

# Moverse a una rama existente
git checkout main

# Eliminar una rama local
git branch -d nombre-rama

# Eliminar una rama remota
git push origin --delete nombre-rama

# Renombrar una rama local
git branch -m nombre-viejo nombre-nuevo
```

### Guardar Cambios
```bash
# Ver cambios realizados
git diff

# Agregar archivo específico
git add archivo.php

# Agregar todos los cambios
git add .

# Hacer commit
git commit -m "Descripción clara del cambio"

# Commit con descripción larga
git commit -m "Título breve" -m "Descripción detallada del cambio"
```

### Sincronizar con GitHub
```bash
# Subir cambios de tu rama actual
git push origin nombre-rama

# Bajar cambios del servidor (sin fusionar)
git fetch

# Bajar y fusionar cambios
git pull origin nombre-rama

# Forzar sincronización (cuidado!)
git push origin nombre-rama --force
```

### Fusionar Ramas (Merge)
```bash
# Moverse a main
git checkout main

# Fusionar rama en main
git merge feature/nombre-funcionalidad

# Fusionar sin hacer commit automático
git merge --no-commit --no-ff feature/nombre-funcionalidad

# Ver diferencias antes de fusionar
git diff main feature/nombre-funcionalidad
```

### Deshacer Cambios
```bash
# Descartar cambios en un archivo (sin guardar)
git checkout -- archivo.php

# Descartar TODOS los cambios locales
git reset --hard

# Deshacer el último commit (pero mantener cambios)
git reset --soft HEAD~1

# Deshacer el último commit (y perder cambios)
git reset --hard HEAD~1

# Ver cambios que se desharían
git diff HEAD
```

---

## 💼 CASOS PRÁCTICOS

### CASO 1: Subir cambios actuales a main (PRIMERA VEZ)

```bash
# 1. Ver estado
git status

# 2. Agregar todos los cambios
git add .

# 3. Crear commit descriptivo
git commit -m "Actualizar proyecto: Arreglar botones editar/eliminar y desplegar a producción"

# 4. Subir a main
git push origin main

# 5. Verificar en GitHub
# (abre GitHub y verifica que los cambios están ahí)
```

### CASO 2: Crear nueva funcionalidad (SIN afectar main)

```bash
# 1. Asegurate de estar en main actualizado
git checkout main
git pull origin main

# 2. Crea rama para nueva funcionalidad
git checkout -b feature/agregar-reportes

# 3. Trabaja en tu funcionalidad
# (editas archivos...)

# 4. Verifica cambios
git status
git diff

# 5. Guarda cambios con commit descriptivo
git add .
git commit -m "Agregar sistema de reportes de ventas"

# 6. Sube la rama a GitHub
git push origin feature/agregar-reportes

# 7. En GitHub, crea Pull Request (PR)
#    (botón "Compare & pull request")
#    (escribe descripción del cambio)
#    (espera revisión)

# 8. Cuando se aprueba, MERGE en GitHub (o desde terminal)
git checkout main
git pull origin main
git merge feature/agregar-reportes

# 9. Borra la rama (ya no la necesitas)
git branch -d feature/agregar-reportes
git push origin --delete feature/agregar-reportes
```

### CASO 3: Corregir un bug sin afectar main

```bash
# 1. Crea rama para bugfix
git checkout -b bugfix/error-en-modal-cliente

# 2. Haz los cambios necesarios
# (editas archivos para corregir el error...)

# 3. Prueba en local que funcione

# 4. Guarda cambios
git add .
git commit -m "Corregir error: modal no se cierra después de eliminar cliente"

# 5. Sube la rama
git push origin bugfix/error-en-modal-cliente

# 6. Crea Pull Request en GitHub

# 7. Cuando se aprueba y fusiona, elimina rama
git checkout main
git pull origin main
git branch -d bugfix/error-en-modal-cliente
git push origin --delete bugfix/error-en-modal-cliente
```

### CASO 4: Trabajar en múltiples funcionalidades simultáneamente

```bash
# PERSONA 1: Trabajando en reportes
git checkout -b feature/reportes-ventas
git add .
git commit -m "Agregar reportes"
git push origin feature/reportes-ventas

# PERSONA 2: Trabajando en otra funcionalidad (al mismo tiempo)
git checkout main
git checkout -b feature/mejorar-dashboard
git add .
git commit -m "Mejorar diseño del dashboard"
git push origin feature/mejorar-dashboard

# Ambos hacen PR, se revisan, y se fusionan a main
# Sin conflictos porque trabajaron en ramas diferentes
```

---

## 🚨 SOLUCIÓN DE PROBLEMAS

### Problema: "Tu rama está adelante del origin"
```bash
# Significa que hiciste commits pero no los subiste
# Solución:
git push origin nombre-rama
```

### Problema: "Tu rama está atrás del origin"
```bash
# Significa que hay cambios en GitHub que no tienes localmente
# Solución:
git pull origin nombre-rama
```

### Problema: "Conflicto de merge"
```
Ocurre cuando dos ramas modificaron el mismo archivo

# Git marca con <<<<< ===== >>>>>>> los conflictos
# Edita el archivo y resuelve manualmente
# Luego:
git add archivo-conflictivo.php
git commit -m "Resolver conflicto"
git push origin nombre-rama
```

### Problema: "Hice cambios y quiero crear una rama"
```bash
# Si ya hiciste cambios pero quieres crearlos en una rama:

# Opción 1: Stash (guardar temporalmente)
git stash                           # Guarda cambios temporalmente
git checkout -b feature/nueva       # Crea rama
git stash pop                       # Recupera cambios en nueva rama

# Opción 2: Commit en rama actual
git add .
git commit -m "WIP: cambios temporales"
git checkout -b feature/nueva       # Crea rama (los cambios van con ella)
```

### Problema: "Quiero ver cambios antes de hacer commit"
```bash
git diff                            # Ver cambios en archivos modificados
git diff --staged                   # Ver cambios ya preparados (staged)
git diff main origin/main           # Ver diferencia con rama remota
```

### Problema: "Quiero deshacer un commit que ya subí"
```bash
# OPCIÓN 1: Si nadie más ha trabajado en main (seguro)
git reset --hard HEAD~1
git push origin main --force

# OPCIÓN 2: Si otros están trabajando (seguro para todos)
# Crea nuevo commit que revierte el cambio
git revert HEAD
git push origin main

# OPCIÓN 3: Usar "Reset" y "Push" juntos (cuidado)
git reset --hard COMMIT_ID_ANTERIOR
git push origin main --force
```

---

## 📝 BUENAS PRÁCTICAS PARA COMMITS

### Escribir mensajes claros
```
❌ MAL:
git commit -m "fix"
git commit -m "actualizar archivos"

✅ BIEN:
git commit -m "Corregir: Modal no se abre al hacer click en editar"
git commit -m "Agregar: Validación de email en formulario de clientes"
git commit -m "Refactorizar: Mejorar función de eliminación de clientes"
```

### Formato recomendado
```
<tipo>: <descripción breve>

<descripción detallada (opcional)>

Tipos comunes:
- feature: Nueva funcionalidad
- bugfix: Corrección de error
- hotfix: Corrección urgente
- refactor: Mejora de código (sin cambiar funcionalidad)
- docs: Cambios en documentación
- style: Cambios de formato/estilos
- perf: Mejoras de rendimiento

Ejemplo:
feature: Agregar sistema de reportes mensuales
- Crear tabla de reportes en BD
- Agregar endpoint AJAX para consultas
- Implementar gráficos con Chart.js
```

---

## 🎓 RESUMEN RÁPIDO: TU PRIMER PUSH A MAIN

Para subir los cambios actuales a main:

```bash
# 1. Verifica que estás en la carpeta del proyecto
cd c:\ruta\a\tu\Proyecto_atlantis

# 2. Ver estado
git status

# 3. Agregar todos los cambios
git add .

# 4. Crear commit
git commit -m "feat: Deploy exitoso a producción con botones editar/eliminar funcionales"

# 5. Subir a GitHub
git push origin main

# 6. Verifica en GitHub que todo está ahí
# (abre https://github.com/tu-usuario/Proyecto_atlantis)
```

---

## 🌳 PRÓXIMOS PASOS: WORKFLOW DE DESARROLLO

Después de subir a main, usa este flujo:

```
Para cada nueva funcionalidad:
1. git checkout main && git pull origin main
2. git checkout -b feature/nombre
3. Haz cambios y prueba
4. git add . && git commit -m "descripción"
5. git push origin feature/nombre
6. Crea Pull Request en GitHub
7. Cuando se aprueba: Merge en GitHub
8. Vuelve a main: git checkout main && git pull
```

---

## 💡 TIPS ÚTILES

### Ver cambios recientes
```bash
git log --oneline -10              # Últimos 10 commits
git log --graph --oneline --all    # Árbol visual de ramas
git reflog                         # Historial de cambios HEAD
```

### Limpiar ramas locales no usadas
```bash
git branch -D rama-que-no-quiero   # Borrar rama local
git remote prune origin            # Limpiar referencias remotas
```

### Crear alias para comandos largos
```bash
git config --global alias.st status
git config --global alias.co checkout
git config --global alias.br branch
git config --global alias.cm commit

# Luego puedes usar:
git st        # En lugar de git status
git co -b ...  # En lugar de git checkout -b
```

---

## 🔗 RECURSOS ADICIONALES

- Documentación oficial: https://git-scm.com/doc
- GitHub Guides: https://guides.github.com/
- Git Cheat Sheet: https://github.github.com/training-kit/downloads/github-git-cheat-sheet.pdf
- Interactive Learning: https://learngitbranching.js.org/

