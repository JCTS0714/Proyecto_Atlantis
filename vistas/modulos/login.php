<!-- Login con diseño moderno - Grupo Atlantis (Azules) -->
<style>
/* ========================================
   ESTILOS DEL LOGIN - GRUPO ATLANTIS
======================================== */
.login-page-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: url('<?php echo BASE_URL; ?>/vistas/img/plantilla/ondulaciones-en-2-colores-abstracto_2912x1632_xtrafondos.com.jpg') no-repeat center center;
    background-size: cover;
    position: relative;
    overflow: hidden;
}

/* Overlay oscuro para mejor legibilidad */
.login-page-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 20, 40, 0.4);
    z-index: 1;
}

/* Caja del login - Glassmorphism Azul */
.login-card {
    background: rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 24px;
    padding: 45px 40px;
    width: 100%;
    max-width: 400px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.15);
    z-index: 10;
    position: relative;
}

.login-card h2 {
    color: #fff;
    text-align: center;
    margin-bottom: 35px;
    font-size: 32px;
    font-weight: 300;
    letter-spacing: 3px;
    text-transform: uppercase;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}

/* Grupos de input */
.input-group-login {
    position: relative;
    margin-bottom: 22px;
}

.input-group-login input {
    width: 100%;
    padding: 16px 50px 16px 22px;
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 30px;
    color: #fff;
    font-size: 15px;
    outline: none;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.input-group-login input::placeholder {
    color: rgba(255, 255, 255, 0.7);
}

.input-group-login input:focus {
    background: rgba(255, 255, 255, 0.22);
    border-color: rgba(59, 130, 246, 0.8);
    box-shadow: 0 0 20px rgba(59, 130, 246, 0.35);
}

.input-group-login .input-icon {
    position: absolute;
    right: 22px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.7);
    font-size: 16px;
    pointer-events: none;
}

/* Opciones (recordar) */
.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    font-size: 13px;
}

.login-options label {
    color: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 400;
    margin: 0;
}

.login-options input[type="checkbox"] {
    width: 16px;
    height: 16px;
    margin-right: 8px;
    accent-color: #3b82f6;
    cursor: pointer;
}

/* Botón de login - Azul Atlantis */
.btn-login {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #1e88e5 0%, #1565c0 50%, #0d47a1 100%);
    background-size: 200% 200%;
    border: none;
    border-radius: 30px;
    color: #fff;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.4s ease;
    text-transform: uppercase;
    letter-spacing: 2px;
    box-shadow: 0 4px 15px rgba(30, 136, 229, 0.4);
}

.btn-login:hover {
    background-position: 100% 0;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(30, 136, 229, 0.5);
}

.btn-login:active {
    transform: translateY(-1px);
}

/* Mensaje de error */
.login-error {
    background: rgba(239, 68, 68, 0.2);
    border: 1px solid rgba(239, 68, 68, 0.5);
    color: #fecaca;
    padding: 14px 20px;
    border-radius: 12px;
    margin-bottom: 22px;
    text-align: center;
    font-size: 14px;
    backdrop-filter: blur(10px);
}

/* Logo */
.login-logo-container {
    text-align: center;
    margin-bottom: 10px;
}

.login-logo-img {
    max-width: 140px;
    height: auto;
    filter: drop-shadow(0 2px 15px rgba(0, 0, 0, 0.4));
}

/* Texto de bienvenida */
.welcome-text {
    color: rgba(255, 255, 255, 0.7);
    text-align: center;
    margin-bottom: 25px;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 480px) {
    .login-card {
        margin: 20px;
        padding: 35px 25px;
        border-radius: 20px;
    }
    
    .login-card h2 {
        font-size: 26px;
        letter-spacing: 2px;
    }
    
    .input-group-login input {
        padding: 14px 45px 14px 18px;
        font-size: 14px;
    }
}

/* Ocultar elementos de AdminLTE que no necesitamos */
body.login-page {
    background: transparent !important;
}
</style>

<div class="login-page-wrapper">
    <!-- Tarjeta de Login -->
    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo-container">
            <img src="<?php echo BASE_URL; ?>/vistas/img/plantilla/LOGO-ATLANTIS.png" alt="Atlantis CRM" class="login-logo-img" onerror="this.style.display='none'">
        </div>
        
        <h2>Login</h2>
        <p class="welcome-text">Bienvenido al sistema CRM</p>
        
        <?php
        // Mostrar mensaje de error si existe
        $login = new ControladorUsuarios();
        $login->ctrIngresoUsuario();
        ?>
        
        <form method="post" class="login-form" autocomplete="off">
            <div class="input-group-login">
                <input type="text" name="ingUsuario" placeholder="Usuario" required autocomplete="username">
                <span class="input-icon"><i class="fa fa-user"></i></span>
            </div>
            
            <div class="input-group-login">
                <input type="password" name="ingPassword" placeholder="Contraseña" required autocomplete="current-password">
                <span class="input-icon"><i class="fa fa-lock"></i></span>
            </div>
            
            <div class="login-options">
                <label>
                    <input type="checkbox" name="remember_me" value="on"> Recordarme
                </label>
            </div>
            
            <button type="submit" class="btn-login">Ingresar</button>
        </form>
    </div>
</div>
