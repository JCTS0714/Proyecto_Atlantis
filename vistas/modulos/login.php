<!-- Login con diseño moderno - Montañas y cielo estrellado -->
<style>
/* ========================================
   ESTILOS DEL LOGIN MODERNO
======================================== */
.login-page-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 30%, #1a1a2e 60%, #0f0f1a 100%);
    position: relative;
    overflow: hidden;
}

/* Montañas SVG en CSS */
.mountains {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.mountain-back {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 45%;
    background: linear-gradient(165deg, transparent 30%, #2d1b4e 30%);
}

.mountain-back::before {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(195deg, transparent 40%, #3d2a5c 40%);
}

.mountain-mid {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 35%;
    background: linear-gradient(160deg, transparent 25%, #1e1236 25%);
}

.mountain-mid::before {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(200deg, transparent 35%, #261842 35%);
}

.mountain-front {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 20%;
    background: #0a0a12;
}

/* Pinos */
.trees {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 25%;
    background-image: 
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Cpath d='M50 10 L30 50 L40 50 L25 80 L75 80 L60 50 L70 50 Z' fill='%230a0a12'/%3E%3C/svg%3E");
    background-repeat: repeat-x;
    background-size: 60px 80px;
    background-position: bottom;
}

/* Estrellas */
.stars {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 60%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, white, transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.8), transparent),
        radial-gradient(1px 1px at 90px 40px, white, transparent),
        radial-gradient(2px 2px at 160px 120px, rgba(255,255,255,0.9), transparent),
        radial-gradient(1px 1px at 230px 80px, white, transparent),
        radial-gradient(2px 2px at 300px 150px, rgba(255,255,255,0.7), transparent),
        radial-gradient(1px 1px at 350px 50px, white, transparent),
        radial-gradient(2px 2px at 420px 90px, rgba(255,255,255,0.8), transparent),
        radial-gradient(1px 1px at 500px 130px, white, transparent),
        radial-gradient(2px 2px at 580px 60px, rgba(255,255,255,0.9), transparent),
        radial-gradient(1px 1px at 650px 100px, white, transparent),
        radial-gradient(2px 2px at 720px 140px, rgba(255,255,255,0.7), transparent),
        radial-gradient(1px 1px at 800px 40px, white, transparent),
        radial-gradient(2px 2px at 880px 110px, rgba(255,255,255,0.8), transparent),
        radial-gradient(1px 1px at 950px 70px, white, transparent),
        radial-gradient(2px 2px at 100px 160px, rgba(255,255,255,0.6), transparent),
        radial-gradient(1px 1px at 200px 200px, white, transparent),
        radial-gradient(2px 2px at 400px 180px, rgba(255,255,255,0.7), transparent),
        radial-gradient(1px 1px at 600px 190px, white, transparent),
        radial-gradient(2px 2px at 750px 170px, rgba(255,255,255,0.8), transparent),
        radial-gradient(3px 3px at 120px 50px, #a78bfa, transparent),
        radial-gradient(3px 3px at 450px 100px, #c4b5fd, transparent),
        radial-gradient(3px 3px at 800px 80px, #ddd6fe, transparent);
    background-repeat: repeat;
    background-size: 1000px 250px;
    animation: twinkle 4s ease-in-out infinite alternate;
}

@keyframes twinkle {
    0% { opacity: 1; }
    100% { opacity: 0.6; }
}

/* Aurora sutil */
.aurora {
    position: absolute;
    top: 5%;
    left: 10%;
    width: 80%;
    height: 30%;
    background: radial-gradient(ellipse at center, rgba(138, 43, 226, 0.15) 0%, transparent 70%);
    filter: blur(40px);
    animation: aurora 8s ease-in-out infinite alternate;
}

@keyframes aurora {
    0% { transform: translateX(-10%) scale(1); opacity: 0.5; }
    100% { transform: translateX(10%) scale(1.1); opacity: 0.3; }
}

/* Caja del login - Glassmorphism */
.login-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 24px;
    padding: 45px 40px;
    width: 100%;
    max-width: 400px;
    box-shadow: 
        0 8px 32px rgba(0, 0, 0, 0.4),
        inset 0 1px 0 rgba(255, 255, 255, 0.1);
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
}

/* Grupos de input */
.input-group-login {
    position: relative;
    margin-bottom: 22px;
}

.input-group-login input {
    width: 100%;
    padding: 16px 50px 16px 22px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 30px;
    color: #fff;
    font-size: 15px;
    outline: none;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.input-group-login input::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.input-group-login input:focus {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(167, 139, 250, 0.6);
    box-shadow: 0 0 20px rgba(138, 43, 226, 0.25);
}

.input-group-login .input-icon {
    position: absolute;
    right: 22px;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.6);
    font-size: 16px;
    pointer-events: none;
}

/* Opciones (recordar y olvidé) */
.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
    font-size: 13px;
}

.login-options label {
    color: rgba(255, 255, 255, 0.7);
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
    accent-color: #8b5cf6;
    cursor: pointer;
}

.login-options a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.3s;
}

.login-options a:hover {
    color: #a78bfa;
}

/* Botón de login */
.btn-login {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 50%, #8b5cf6 100%);
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
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.4);
}

.btn-login:hover {
    background-position: 100% 0;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
}

.btn-login:active {
    transform: translateY(-1px);
}

/* Mensaje de error */
.login-error {
    background: rgba(239, 68, 68, 0.15);
    border: 1px solid rgba(239, 68, 68, 0.4);
    color: #fca5a5;
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
    max-width: 120px;
    height: auto;
    filter: drop-shadow(0 2px 10px rgba(0, 0, 0, 0.3));
}

/* Texto de bienvenida */
.welcome-text {
    color: rgba(255, 255, 255, 0.6);
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
    <!-- Efectos de fondo -->
    <div class="stars"></div>
    <div class="aurora"></div>
    
    <!-- Montañas -->
    <div class="mountains">
        <div class="mountain-back"></div>
        <div class="mountain-mid"></div>
        <div class="trees"></div>
        <div class="mountain-front"></div>
    </div>
    
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
