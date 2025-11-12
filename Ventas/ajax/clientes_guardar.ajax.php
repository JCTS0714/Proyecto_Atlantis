<?php
require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datos = array(
        "nombre" => $_POST["nuevoNombre"] ?? '',
        "tipo" => $_POST["nuevoTipo"] ?? '',
        "documento" => $_POST["nuevoDocumento"] ?? '',
        "telefono" => $_POST["nuevoTelefono"] ?? '',
        "correo" => $_POST["nuevoCorreo"] ?? '',
        "ciudad" => $_POST["nuevoCiudad"] ?? '',
        "migracion" => $_POST["nuevoMigracion"] ?? '',
        "referencia" => $_POST["nuevoReferencia"] ?? '',
        "fecha_contacto" => $_POST["nuevoFechaContacto"] ?? '',
        "empresa" => $_POST["nuevoEmpresa"] ?? '',
    );

    $tabla = "clientes";

    $respuesta = ModeloCliente::mdlRegistrarCliente($tabla, $datos);

    header('Content-Type: application/json');

    if ($respuesta == "ok") {
        // Obtener último ID insertado usando PDO
        $conexion = Conexion::conectar();
        $ultimoId = $conexion->lastInsertId();
        echo json_encode(["status" => "ok", "id" => $ultimoId, "nombre" => $datos["nombre"]]);
    } else {
        echo json_encode(["status" => "error", "message" => "No se pudo guardar el cliente. Intente nuevamente."]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Método no permitido"]);
}