<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../controladores/ControladorOportunidad.php';
require_once __DIR__ . '/../modelos/ModeloCRM.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

header('Content-Type: application/json');

switch ($action) {
    case 'getOportunidades':
        $filtrarUltimaSemana = isset($_GET['filtrarUltimaSemana']) ? filter_var($_GET['filtrarUltimaSemana'], FILTER_VALIDATE_BOOLEAN) : false;
        // Collect optional filters
        $filters = [];
        $allowed = ['nombre','telefono','documento','periodo','fecha_inicio','fecha_fin'];
        foreach ($allowed as $k) {
            if (isset($_GET[$k]) && $_GET[$k] !== '') $filters[$k] = $_GET[$k];
        }
        if (empty($filters)) $filters = null;
        echo json_encode(ControladorOportunidad::ctrMostrarOportunidades(null, null, $filtrarUltimaSemana, $filters));
        break;
    case 'crearOportunidad':
        ControladorOportunidad::ctrCrearOportunidad();
        break;
    case 'cambiarEstado':
        ControladorOportunidad::ctrActualizarEstadoOportunidad();
        break;
    case 'eliminarOportunidad':
        $resultado = ControladorOportunidad::ctrEliminarOportunidad();
        if (is_array($resultado) && $resultado['status'] === 'success') {
            echo json_encode(['status' => 'success', 'message' => $resultado['message']]);
        } else if (is_array($resultado)) {
            echo json_encode(['status' => 'error', 'message' => $resultado['message']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error desconocido']);
        }
        break;
    case 'getClientes':
        $estado = $_POST['estado'] ?? null;
        if ($estado !== null) {
            echo json_encode(['status' => 'success', 'clientes' => ControladorOportunidad::ctrMostrarClientes("estado", $estado)]);
        } else {
            echo json_encode(['status' => 'success', 'clientes' => ControladorOportunidad::ctrMostrarClientes()]);
        }
        break;
    case 'getUltimosZonaEspera':
        echo json_encode(ControladorOportunidad::ctrMostrarUltimosZonaEspera());
        break;
    case 'getOportunidad':
        $id = $_GET['id'] ?? null;
        if ($id !== null) {
            echo json_encode(ControladorOportunidad::ctrMostrarOportunidades("id", $id));
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de oportunidad no proporcionado']);
        }
        break;
    case 'getOportunidadByCliente':
        $clienteId = $_GET['clienteId'] ?? null;
        if ($clienteId !== null) {
            echo json_encode(ControladorOportunidad::ctrMostrarOportunidades("cliente_id", $clienteId));
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de cliente no proporcionado']);
        }
        break;
    case 'actualizarOportunidad':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $datos = [
                    'id' => $_POST['id'] ?? null,
                    'titulo' => $_POST['titulo'] ?? '',
                    'descripcion' => $_POST['descripcion'] ?? '',
                    'valor_estimado' => $_POST['valor_estimado'] ?? '',
                    'probabilidad' => $_POST['probabilidad'] ?? 0,
                    'fecha_cierre_estimada' => $_POST['fecha_cierre_estimada'] ?? '',
                    'actividad' => $_POST['actividad'] ?? '',
                    'fecha_actividad' => $_POST['fecha_actividad'] ?? '',
                    'cliente_id' => $_POST['cliente_id'] ?? null,
                    'usuario_id' => $_POST['usuario_id'] ?? null
                ];

                error_log("AJAX actualizarOportunidad - Datos recibidos: " . json_encode($datos));

                if ($datos['id'] === null) {
                    echo json_encode(['status' => 'error', 'message' => 'ID de oportunidad no proporcionado']);
                    break;
                }

                $resultado = ControladorOportunidad::ctrActualizarOportunidad($datos);

                error_log("AJAX actualizarOportunidad - Resultado del controlador: " . $resultado);

                if ($resultado === 'ok') {
                    $response = ['status' => 'success', 'message' => 'Oportunidad actualizada correctamente'];
                    echo json_encode($response);
                } else {
                    // Si el resultado contiene "error:", extraer el mensaje específico
                    if (strpos($resultado, 'error:') === 0) {
                        $mensaje = substr($resultado, 6); // Remover "error:" del inicio
                        echo json_encode(['status' => 'error', 'message' => $mensaje]);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error al actualizar la oportunidad: ' . $resultado]);
                    }
                }
            } catch (Exception $e) {
                error_log("AJAX actualizarOportunidad - Excepción: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        }
        break;
    case 'verificarReunionExistente':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            try {
                $clienteId = $_GET['cliente_id'] ?? null;
                $titulo = $_GET['titulo'] ?? '';
                $fecha = $_GET['fecha'] ?? '';

                if ($clienteId === null || empty($titulo) || empty($fecha)) {
                    echo json_encode(['status' => 'error', 'message' => 'Parámetros incompletos']);
                    break;
                }

                $existe = ControladorOportunidad::ctrVerificarReunionExistente($clienteId, $titulo, $fecha);
                echo json_encode(['status' => 'success', 'existe' => $existe]);
            } catch (Exception $e) {
                error_log("AJAX verificarReunionExistente - Excepción: " . $e->getMessage());
                echo json_encode(['status' => 'error', 'message' => 'Error interno del servidor']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        }
        break;
    case 'verificarOportunidades':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $clienteId = $_GET['clienteId'] ?? null;
            if ($clienteId) {
                $oportunidades = ControladorOportunidad::ctrMostrarOportunidades("cliente_id", $clienteId);
                $hasOportunidades = !empty($oportunidades);
                echo json_encode(['hasOportunidades' => $hasOportunidades]);
            } else {
                echo json_encode(['error' => 'Parámetro clienteId faltante']);
            }
        } else {
            echo json_encode(['error' => 'Método no permitido']);
        }
        break;
    case 'reactivarCliente':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idCliente = $_POST['idCliente'] ?? null;
            if ($idCliente) {
                $resultado = ControladorOportunidad::ctrReactivarCliente($idCliente);
                if ($resultado === true) {
                    echo json_encode(['status' => 'success', 'message' => 'Cliente reactivado correctamente']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => $resultado]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Parámetro idCliente faltante']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}