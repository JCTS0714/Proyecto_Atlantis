<?php
// DIAGNÓSTICO COMPLETO
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DIAGNÓSTICO DEL SISTEMA</h1>";
echo "<hr>";

echo "<h2>1. Información del Servidor</h2>";
echo "<pre>";
echo "URL Actual: " . $_SERVER['REQUEST_URI'] . "\n";
echo "Directorio de Script: " . dirname(__FILE__) . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "</pre>";

echo "<h2>2. Verificación de Archivos</h2>";
$files = [
    'config/estados.php',
    'modelos/conexion.php',
    'controladores/plantilla.controlador.php',
    'vistas/plantilla.php'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "✓ $file <br>";
    } else {
        echo "✗ <strong>$file NO ENCONTRADO</strong> <br>";
    }
}

echo "<h2>3. Prueba de Conexión a BD</h2>";
try {
    require_once 'modelos/conexion.php';
    echo "✓ Archivo conexion.php cargado <br>";
    
    // Intentar conectar
    $conexion = Conexion::conectar();
    if ($conexion) {
        echo "✓ Conexión a base de datos: <strong>EXITOSA</strong><br>";
    } else {
        echo "✗ Conexión a base de datos: <strong>FALLIDA</strong><br>";
    }
} catch (Exception $e) {
    echo "✗ Error en conexión: " . $e->getMessage() . "<br>";
}

echo "<h2>4. Verificar Sesión</h2>";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
echo "Sesión iniciada: ✓ Sí<br>";
echo "Usuario en sesión: " . (isset($_SESSION['iniciarSesion']) ? "✓ " . $_SESSION['iniciarSesion'] : "✗ No hay sesión") . "<br>";

echo "<h2>5. GET/POST Recibido</h2>";
echo "<pre>";
echo "GET: " . print_r($_GET, true);
echo "</pre>";

echo "<hr>";
echo "<h3>Si ves este mensaje, PHP está funcionando correctamente.</h3>";
echo "<p><a href='index.php?ruta=inicio'>Ir a Inicio</a></p>";
?>
