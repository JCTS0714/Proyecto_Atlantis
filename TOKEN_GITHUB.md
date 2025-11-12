# 🔑 SOLUCIÓN RÁPIDA: USA UN TOKEN DE GITHUB

Hay un conflicto con las credenciales guardadas. La solución más simple es usar un **Personal Access Token**.

## PASO 1: CREAR TOKEN EN GITHUB

1. Abre: https://github.com/settings/tokens/new
2. **Nombre:** `git-atlantis-token`
3. **Expiration:** Elige 90 days (o lo que prefieras)
4. **Scopes:** Marca solo ✅ **repo** (full control of private repositories)
5. Haz click **"Generate token"**
6. **COPIA EL TOKEN COMPLETO** (es la única vez que lo verás)

El token se ve algo como:
```
ghp_1234567890abcdefghijklmnopqrstuvwxyz
```

## PASO 2: USAR EL TOKEN PARA SUBIR

Una vez tengas el token, envíamelo y ejecutaré este comando (reemplazando TOKEN):

```powershell
cd c:\xampp\htdocs\Proyecto_atlantis

# Actualizar URL remota con token
git remote set-url origin https://davidididi888-ai:TOKEN@github.com/davidididi888-ai/Proyecto_atlantis.git

# Subir cambios
git push -u origin main
```

## ⚠️ SEGURIDAD
- NO COMPARTAS el token públicamente
- Solo dámelo a mí en esta conversación
- Puedes regenerarlo en: https://github.com/settings/tokens

## ¿LISTO?

1. Crea el token en GitHub
2. Copia el token completo
3. Envíamelo aquí
4. Yo ejecuto los comandos

¿Ya lo tienes?

