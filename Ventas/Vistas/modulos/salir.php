<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Evitar incluir múltiples veces la clase Conexion
if (!class_exists('ModeloUsuarios')) {
    require_once "../../../Ventas/modelos/usuarios.modelo.php";
}

if (isset($_SESSION["id"])) {
    ModeloUsuarios::mdlActualizarUsuario("usuarios", "sesion_token", null, "id", $_SESSION["id"]);
    ModeloUsuarios::mdlActualizarUsuario("usuarios", "estado", 1, "id", $_SESSION["id"]);
}

session_destroy();

echo '<script>
      window.location = "login";
 </script>';
