<?php
require_once "../modelos/usuarios.modelo.php";

if (!isset($_GET['usuario'])) {
    echo "Por favor, proporciona el parámetro 'usuario' en la URL.";
    exit;
}

$usuario = $_GET['usuario'];
$tabla = "usuarios";

// Obtener datos del usuario
$datosUsuario = ModeloUsuarios::MdlMostrarUsuarios($tabla, "usuario", $usuario);

if (!$datosUsuario) {
    echo "Usuario no encontrado.";
    exit;
}

echo "<h2>Datos del usuario: " . htmlspecialchars($usuario) . "</h2>";
echo "<ul>";
echo "<li>ID: " . $datosUsuario['id'] . "</li>";
echo "<li>Remember Token: " . $datosUsuario['remember_token'] . "</li>";
echo "<li>Remember Expires: " . $datosUsuario['remember_expires'] . "</li>";
echo "<li>Zona horaria del servidor: " . date_default_timezone_get() . "</li>";
echo "<li>Fecha y hora actual del servidor: " . date('Y-m-d H:i:s') . "</li>";
echo "</ul>";

// Comparar fecha de expiración con fecha actual
if (strtotime($datosUsuario['remember_expires']) > time()) {
    echo "<p>El token de recuerdo está vigente.</p>";
} else {
    echo "<p>El token de recuerdo ha expirado o no está establecido.</p>";
}
?>
