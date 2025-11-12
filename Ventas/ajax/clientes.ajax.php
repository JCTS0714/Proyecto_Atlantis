<?php
session_start();

require_once "../controladores/clientes.controlador.php";
require_once "../modelos/clientes.modelo.php";

class AjaxClientes {

    public $activarEstado;
    public $activarId;

    public function ajaxActivarEstado() {
        $tabla = "clientes";

        $item1 = "estado";
        $valor1 = $this->activarEstado;

        $item2 = "id";
        $valor2 = $this->activarId;

        $respuesta = ModeloCliente::mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2);

        echo $respuesta;
    }
}

// ========================================
// ENDPOINT PARA ELIMINAR CLIENTE (primero, antes de otros checks)
// ========================================
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"]) && isset($_POST["ruta"]) && !isset($_POST["activarId"])) {
    session_start();
    require_once "../modelos/clientes.modelo.php";
    
    $idCliente = $_POST["idCliente"];
    $ruta = $_POST["ruta"];
    
    if (!is_numeric($idCliente) || empty($idCliente)) {
        echo "error";
        exit;
    }
    
    // Verificar permisos: solo Administrador puede eliminar
    if (isset($_SESSION["perfil"]) && $_SESSION["perfil"] == "Vendedor") {
        echo "error: No tienes permisos para eliminar";
        exit;
    }
    
    // Verificar si el cliente tiene oportunidades asociadas
    $tieneOportunidades = ModeloCliente::mdlVerificarOportunidades($idCliente);
    if ($tieneOportunidades) {
        echo "error: No se puede eliminar porque tiene oportunidades asociadas";
        exit;
    }
    
    // Proceder a eliminar
    $tabla = "clientes";
    $respuesta = ModeloCliente::mdlEliminarCliente($tabla, $idCliente);
    
    if ($respuesta == "ok") {
        echo "ok";
    } else {
        echo "error";
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"]) && !isset($_POST["ruta"]) && !isset($_POST["activarId"])) {
    $item = "id";
    $valor = $_POST["idCliente"];
    $cliente = ModeloCliente::MdlMostrarCliente("clientes", $item, $valor);

    header('Content-Type: application/json');
    if (is_array($cliente) && count($cliente) > 0) {
        echo json_encode($cliente[0]);
    } else {
        echo json_encode(new stdClass());
    }
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["activarId"]) && isset($_POST["activarEstado"])) {
    $activarEstado = new AjaxClientes();
    $activarEstado->activarId = $_POST["activarId"];
    $activarEstado->activarEstado = $_POST["activarEstado"];
    $activarEstado->ajaxActivarEstado();
    exit;
}

$q = $_GET['q'] ?? '';

if ($q !== '') {
    $item = 'nombre';
    $valor = $q;
    $clientes = ControladorCliente::ctrMostrarCliente($item, $valor);

    // Filtrar clientes que contengan el término de búsqueda (insensible a mayúsculas)
    $result = [];
    if (is_array($clientes)) {
        foreach ($clientes as $cliente) {
            if (stripos($cliente['nombre'], $q) !== false) {
                $result[] = $cliente;
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode($result);
} else {
    // Si no hay término de búsqueda, devolver array vacío
    header('Content-Type: application/json');
    echo json_encode([]);
}

// Nuevo endpoint para filtrar clientes
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "filtrar_clientes") {
    $filtros = isset($_POST["filtros"]) ? $_POST["filtros"] : [];

    // Llamar al controlador con filtros
    $clientes = ControladorCliente::ctrMostrarClientesFiltrados($filtros);

    header('Content-Type: application/json');
    echo json_encode($clientes);
    exit;
}