# 🔐 IMPORTANTE: GitHub ya no permite contraseña en Git

GitHub deshabilitó la autenticación por contraseña en Git hace años por seguridad. 

**Necesitamos crear un Personal Access Token.**

---

## PASO 1: CREAR TOKEN EN GITHUB

### Opción A: Link automático
Abre este link: https://github.com/settings/tokens/new?scopes=repo&description=git-atlantis

### Opción B: Manual
1. Ve a: https://github.com/settings/tokens
2. Click en **"Generate new token"** → **"Generate new token (classic)"**
3. Completa:
   - **Token name:** `git-atlantis`
   - **Expiration:** 90 days
   - **Select scopes:** ✅ Marca `repo`
4. Click **"Generate token"** (al final de la página)

---

## PASO 2: COPIAR EL TOKEN

GitHub te mostrará el token **UNA SOLA VEZ**. Se ve así:
```
ghp_1A2B3C4D5E6F7G8H9I0J1K2L3M4N5O6P7Q8R
```

**CÓPIALO COMPLETO** (Ctrl+C sobre el campo)

---

## PASO 3: ENVÍAMELO

Una vez lo tengas copiado, envíamelo aquí en formato:

```
ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

Yo ejecutaré:
```powershell
git remote set-url origin https://davidididi888-ai:TOKEN@github.com/davidididi888-ai/Proyecto_atlantis.git
git push -u origin main
```

---

## ⚠️ NOTAS DE SEGURIDAD

- El token es como una contraseña - NO LO COMPARTAS públicamente
- Solo comparte conmigo en esta conversación privada
- Puedes eliminarlo después desde: https://github.com/settings/tokens
- Si lo expones, GitHub te lo notificará automáticamente

---

## 🎯 PRÓXIMO PASO

1. Ve a: https://github.com/settings/tokens/new?scopes=repo&description=git-atlantis
2. Click **"Generate token"** al final
3. COPIA el token
4. Envíamelo aquí
5. ¡Listo! Subiremos todo a GitHub

¿Ya lo hiciste?

