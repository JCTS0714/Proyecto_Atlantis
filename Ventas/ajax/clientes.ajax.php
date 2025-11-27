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

// Si la petición POST incluye 'action', dejar que los endpoints específicos la manejen.
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["idCliente"]) && !isset($_POST["ruta"]) && !isset($_POST["activarId"]) && !isset($_POST["action"])) {
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

// Nuevo endpoint para actualizar estado de cliente desde select en prospectos
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "actualizarEstadoCliente") {
    // Validar parámetros
    $idCliente = isset($_POST['idCliente']) ? intval($_POST['idCliente']) : 0;
    $nuevoEstado = isset($_POST['nuevoEstado']) ? intval($_POST['nuevoEstado']) : null;

    header('Content-Type: application/json');

    if ($idCliente <= 0 || $nuevoEstado === null) {
        echo json_encode(['status' => 'error', 'message' => 'Parámetros inválidos']);
        exit;
    }

    // Verificar permisos básicos (opcional): permitir a Vendedor cambiar estado puede estar restringido
    if (isset($_SESSION['perfil']) && $_SESSION['perfil'] === 'Vendedor') {
        // Permitimos que Vendedor actualice a ciertos estados si es necesario, de lo contrario bloquear
        // Aquí se permite la acción; ajustar según políticas
    }

    // Actualizar en la base de datos
    $tabla = 'clientes';
    $item1 = 'estado';
    $valor1 = $nuevoEstado;
    $item2 = 'id';
    $valor2 = $idCliente;

    $respuesta = ModeloCliente::mdlActualizarCliente($tabla, $item1, $valor1, $item2, $valor2);

    if ($respuesta === 'ok') {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar']);
    }
    exit;
}

// Endpoint AJAX: actualizar campos específicos de cliente (usado por modal de detalles de oportunidad)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === 'actualizarCampos') {
    header('Content-Type: application/json');

    $idCliente = isset($_POST['idCliente']) ? intval($_POST['idCliente']) : 0;
    if ($idCliente <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID de cliente inválido']);
        exit;
    }

    // Campos permitidos para actualizar desde este endpoint
    $allowed = ['nombre','tipo','documento','telefono','correo','ciudad','migracion','referencia','fecha_contacto','empresa'];

    // Logging entrada para depuración temporal
    error_log("[ajax/clientes.ajax.php] actualizarCampos payload: " . json_encode($_POST));

    $results = ['success' => [], 'failed' => []];
    foreach ($allowed as $campo) {
        if (isset($_POST[$campo])) {
            $valor = $_POST[$campo];
            try {
                $respuesta = ModeloCliente::mdlActualizarCliente('clientes', $campo, $valor, 'id', $idCliente);
            } catch (Exception $e) {
                $respuesta = 'error';
                error_log("[ajax/clientes.ajax.php] Exception updating $campo for client $idCliente: " . $e->getMessage());
            }

            if ($respuesta === 'ok') {
                $results['success'][] = $campo;
            } else {
                $results['failed'][] = ['field' => $campo, 'result' => $respuesta];
                error_log("[ajax/clientes.ajax.php] Failed updating $campo for client $idCliente. Response: " . json_encode($respuesta));
            }
        }
    }

    // Determinar estado general
    if (count($results['success']) > 0 && count($results['failed']) === 0) {
        echo json_encode(['status' => 'ok', 'updated' => $results['success']]);
    } elseif (count($results['success']) > 0 && count($results['failed']) > 0) {
        echo json_encode(['status' => 'partial', 'updated' => $results['success'], 'failed' => $results['failed'], 'message' => 'Algunos campos no se pudieron actualizar']);
    } else {
        echo json_encode(['status' => 'error', 'failed' => $results['failed'], 'message' => 'No se actualizaron campos']);
    }
    exit;
}