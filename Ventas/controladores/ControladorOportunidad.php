<?php
require_once __DIR__ . '/../config/estados.php';
require_once __DIR__ . '/../modelos/conexion.php';
require_once __DIR__ . '/../modelos/ModeloCRM.php';
require_once __DIR__ . '/../modelos/clientes.modelo.php';

class ControladorOportunidad {

    const TABLA_OPORTUNIDADES = "oportunidades";
    const TABLA_CLIENTES = "clientes";

    static public function ctrMostrarOportunidades($item = null, $valor = null, $filtrarUltimaSemana = false) {
        $tabla = "oportunidades";
        return ModeloCRM::mdlMostrarOportunidades($tabla, $item, $valor, $filtrarUltimaSemana);
    }

    static public function ctrActualizarOportunidad($datos) {
        // Validate required fields
        if (empty($datos["id"]) || !is_numeric($datos["id"])) {
            return "error: ID de oportunidad inválido o faltante";
        }

        if (empty($datos["titulo"]) || trim($datos["titulo"]) === '') {
            return "error: El título es obligatorio";
        }

        if (!isset($datos["probabilidad"]) || !is_numeric($datos["probabilidad"]) || $datos["probabilidad"] < 0 || $datos["probabilidad"] > 100) {
            return "error: Probabilidad debe ser un número entre 0 y 100";
        }

        if (empty($datos["fecha_cierre_estimada"])) {
            return "error: Fecha de cierre estimada es obligatoria";
        }

        // Validate fecha_cierre_estimada format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos["fecha_cierre_estimada"])) {
            return "error: Formato de fecha inválido (debe ser YYYY-MM-DD)";
        }

        // Validate valor_estimado if provided
        if (!empty($datos["valor_estimado"]) && !is_numeric($datos["valor_estimado"])) {
            return "error: Valor estimado debe ser un número";
        }

        // Validate fecha_actividad if provided and not empty
        if (!empty($datos["fecha_actividad"]) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $datos["fecha_actividad"])) {
            return "error: Formato de fecha_actividad inválido (debe ser YYYY-MM-DD)";
        }

        // Validate actividad if provided and not empty
        if (!empty($datos["actividad"]) && !is_string($datos["actividad"])) {
            return "error: Actividad debe ser un texto válido";
        }

        $tabla = "oportunidades";
        $resultado = ModeloCRM::mdlActualizarOportunidad($tabla, $datos);

        // No crear reunión automáticamente - se creará desde el calendario cuando el usuario complete el formulario

        return $resultado;
    }

    static public function ctrCrearOportunidad() {
        if (isset($_POST["nuevoTitulo"])) {
            // Validate required fields
            if (empty($_POST["idCliente"]) || empty($_POST["idUsuario"]) || empty($_POST["nuevoTitulo"])) {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "¡Faltan campos obligatorios!"]);
                return;
            }

            try {
                // Si la creación viene marcada como "prospect-only", verificar que el cliente exista y sea prospecto (estado = 0)
                if (isset($_POST['prospect_only']) && $_POST['prospect_only'] == '1') {
                    $clienteIdCheck = $_POST['idCliente'] ?? null;
                    if (!$clienteIdCheck) {
                        header('Content-Type: application/json');
                        echo json_encode(["status" => "error", "message" => "Cliente prospecto no proporcionado."]);
                        return;
                    }
                    $clienteData = ModeloCliente::MdlMostrarCliente('clientes', 'id', $clienteIdCheck);
                    if (empty($clienteData) || !isset($clienteData[0]['estado']) || (int)$clienteData[0]['estado'] !== 0) {
                        header('Content-Type: application/json');
                        echo json_encode(["status" => "error", "message" => "El cliente seleccionado no es un prospecto válido."]);
                        return;
                    }
                }
                $tabla = self::TABLA_OPORTUNIDADES;

                $datos = array(
                    "cliente_id" => $_POST["idCliente"],
                    "usuario_id" => $_POST["idUsuario"],
                    "titulo" => $_POST["nuevoTitulo"],
                    "descripcion" => $_POST["nuevaDescripcion"],
                    "valor_estimado" => $_POST["nuevoValorEstimado"],
                    "probabilidad" => $_POST["nuevaProbabilidad"],
                    "estado" => $_POST["nuevoEstado"],
                    "fecha_cierre_estimada" => $_POST["nuevaFechaCierre"]
                );

                $respuesta = ModeloCRM::mdlRegistrarOportunidad($tabla, $datos);

                // NUEVO: Cambiar estado del cliente a 1 (seguimiento) cuando se crea oportunidad
                if ($respuesta == "ok") {
                    require_once '../modelos/conexion.php';
                    require_once __DIR__ . '/../config/estados.php';
                    $tablaClientes = self::TABLA_CLIENTES;
                    $stmt = Conexion::conectar()->prepare("UPDATE $tablaClientes SET estado = " . ESTADO_SEGUIMIENTO . " WHERE id = :id");
                    $stmt->bindParam(":id", $_POST["idCliente"], PDO::PARAM_INT);
                    $stmt->execute();
                }

                header('Content-Type: application/json');
                if ($respuesta == "ok") {
                    echo json_encode(array(
                        "status" => "success",
                        "message" => "¡La oportunidad ha sido registrada correctamente!"
                    ));
                } else {
                    echo json_encode(array(
                        "status" => "error",
                        "message" => "¡Error al registrar la oportunidad!"
                    ));
                }
            } catch (Exception $e) {
                error_log("Error en ctrCrearOportunidad: " . $e->getMessage());
                echo json_encode(["status" => "error", "message" => "¡Error al registrar la oportunidad!", "debug" => $e->getMessage()]);
            }
        }
    }

    static public function ctrActualizarEstadoOportunidad() {
        // El header Content-Type ya se establece en el archivo AJAX, no es necesario aquí

        error_log("=== ctrActualizarEstadoOportunidad: Inicio de función ===");
        error_log("Datos POST recibidos: " . json_encode($_POST));
        error_log("Datos GET recibidos: " . json_encode($_GET));

        // Ignorar advertencias y continuar
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

        try {
            if (isset($_POST["idOportunidad"]) && isset($_POST["nuevoEstado"])) {
                error_log("Parámetros válidos encontrados - idOportunidad: " . $_POST["idOportunidad"] . ", nuevoEstado: " . $_POST["nuevoEstado"]);

                $tabla = self::TABLA_OPORTUNIDADES;
                $respuesta = ModeloCRM::mdlActualizarEstado($tabla, $_POST["idOportunidad"], $_POST["nuevoEstado"]);
                error_log("mdlActualizarEstado respuesta: " . $respuesta);

                if ($respuesta == "ok") {
                    error_log("Actualización de oportunidad exitosa, obteniendo datos de oportunidad");
                    $oportunidad = self::ctrMostrarOportunidades("id", $_POST["idOportunidad"]);
                    error_log("Oportunidad obtenida: " . json_encode($oportunidad));

                    if (!empty($oportunidad)) {
                        $clienteId = $oportunidad[0]["cliente_id"];
                        error_log("Cliente ID obtenido: " . $clienteId);

                        require_once __DIR__ . '/../config/estados.php';

                        // Manejo especial para estado 5 (Perdido) - cambia cliente a estado 3 (No Cliente)
                        if ($_POST["nuevoEstado"] == KANBAN_PERDIDO) {
                            $nuevoEstadoCliente = ESTADO_NO_CLIENTE; // No Cliente
                            error_log("Estado nuevo es PERDIDO, cliente estado cambiado a NO_CLIENTE");
                        } else {
                            $nuevoEstadoCliente = self::obtenerEstadoDesdeKanban($_POST["nuevoEstado"]);
                            error_log("Estado nuevo cliente mapeado: " . $nuevoEstadoCliente);
                        }

                        // Actualizar estado del cliente (pero no afectar la respuesta principal)
                        $resultadoCliente = self::actualizarEstadoCliente($clienteId, $nuevoEstadoCliente);
                        error_log("Estado cliente actualizado para clienteId: $clienteId, resultado: " . $resultadoCliente);
                    } else {
                        error_log("No se encontró la oportunidad con ID: " . $_POST["idOportunidad"]);
                    }

                    // Siempre responde éxito si la oportunidad se actualizó correctamente
                    $response = [
                        'status' => 'success',
                        'message' => 'Estado actualizado correctamente'
                    ];
                    error_log("Enviando respuesta exitosa: " . json_encode($response));
                    echo json_encode($response);
                    return;
                } else {
                    error_log("Error al actualizar estado de oportunidad");
                    $response = ['status' => 'error', 'message' => 'No se pudo actualizar el estado'];
                    error_log("Enviando respuesta de error: " . json_encode($response));
                    echo json_encode($response);
                }
            } else {
                error_log("Parámetros incompletos en ctrActualizarEstadoOportunidad");
                $response = ['status' => 'error', 'message' => 'Parámetros incompletos'];
                error_log("Enviando respuesta de parámetros incompletos: " . json_encode($response));
                echo json_encode($response);
            }
        } catch (Exception $e) {
            error_log("Excepción en ctrActualizarEstadoOportunidad: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $response = ['status' => 'error', 'message' => 'Error interno al actualizar el estado'];
            error_log("Enviando respuesta de excepción: " . json_encode($response));
            echo json_encode($response);
        }
    }

    static public function ctrEliminarOportunidad() {
        if (isset($_POST["id"])) {

            // Verificar permisos: solo Administrador puede eliminar oportunidades
            if(isset($_SESSION["perfil"]) && $_SESSION["perfil"] == "Vendedor"){
                return [
                    "status" => "error",
                    "message" => "¡No tienes permisos para eliminar oportunidades!"
                ];
            }

            $tabla = self::TABLA_OPORTUNIDADES;
            $respuesta = ModeloCRM::mdlEliminarOportunidad($tabla, $_POST["id"]);

            if ($respuesta == "ok") {
                return [
                    "status" => "success",
                    "message" => "¡Oportunidad eliminada correctamente!"
                ];
            } else {
                return [
                    "status" => "error",
                    "message" => "¡Error al eliminar la oportunidad!"
                ];
            }
        } else {
            return [
                "status" => "error",
                "message" => "Parámetro id no recibido"
            ];
        }
    }

    static public function ctrMostrarClientes($item = null, $valor = null) {
        $tabla = "clientes";
        return ModeloCRM::mdlMostrarClientes($tabla, $item, $valor);
    }

    // New method to fetch clients ordered by most recent
    static public function ctrMostrarClientesOrdenados() {
        $tabla = "clientes";
        return ModeloCRM::mdlMostrarClientesOrdenados($tabla);
    }

    static public function ctrMostrarUltimosZonaEspera() {
        require_once __DIR__ . '/../modelos/conexion.php';
        require_once __DIR__ . '/../config/estados.php';
        
        try {
            $stmt = Conexion::conectar()->prepare(
                "SELECT o.*, c.nombre as nombre_cliente 
                 FROM oportunidades o 
                 LEFT JOIN clientes c ON o.cliente_id = c.id 
                 WHERE o.estado = :estado 
                 ORDER BY o.fecha_modificacion DESC 
                 LIMIT 10"
            );
            $estadoZonaEspera = KANBAN_ZONA_ESPERA;
            $stmt->bindParam(":estado", $estadoZonaEspera, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error en ctrMostrarUltimosZonaEspera: " . $e->getMessage());
            return [];
        }
    }

    /* ============================================
     * MÉTODOS HELPER PARA MAPEO DE ESTADOS KANBAN
     * ============================================ */

    static private function obtenerEstadoDesdeKanban($estadoKanban) {
        $estadoKanban = (int)$estadoKanban;
        
        require_once __DIR__ . '/../config/estados.php';
        
        // Mapeo de estados kanban a estados de cliente según la lógica proporcionada:
        // 1:1 - Columna Nuevo -> Lista Seguimiento
        // 2:1 - Columna Calificado -> Lista Seguimiento
        // 3:1 - Columna Propuesto -> Lista Seguimiento
        // 4:2 - Columna Ganado -> Lista Clientes
        // 5:3 - Columna imaginaria Perdido -> Lista No Clientes
        // 6:4 - Columna imaginaria Zona de Espera -> Lista Zona de Espera
        
        switch ($estadoKanban) {
            case KANBAN_NUEVO:        // 1
            case KANBAN_CALIFICADO:   // 2
            case KANBAN_PROPUESTO:    // 3
                return ESTADO_SEGUIMIENTO; // Mantiene en lista de seguimiento
                
            case KANBAN_GANADO:       // 4
                return ESTADO_CLIENTE; // Mueve a lista de clientes
                
            case KANBAN_PERDIDO:      // 5
                return ESTADO_NO_CLIENTE; // Mueve a lista de no clientes
                
            case KANBAN_ZONA_ESPERA:  // 6
                return ESTADO_EN_ESPERA; // Mueve a lista de zona de espera
                
            default:
                return ESTADO_SEGUIMIENTO; // Por defecto, mantiene en seguimiento
        }
    }

    // Método público para testing
    static public function testObtenerEstadoDesdeKanban($estadoKanban) {
        return self::obtenerEstadoDesdeKanban($estadoKanban);
    }

    // Método público para testing del mapeo de actividades
    static public function testMapearActividadATitulo($actividad) {
        return self::mapearActividadATitulo($actividad);
    }

    static private function actualizarEstadoCliente($clienteId, $nuevoEstado) {
        require_once __DIR__ . '/../modelos/conexion.php';
        $stmtCheck = Conexion::conectar()->prepare("SELECT estado FROM clientes WHERE id = :id");
        $stmtCheck->bindParam(":id", $clienteId, PDO::PARAM_INT);
        $stmtCheck->execute();
        $cliente = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$cliente) {
            error_log("actualizarEstadoCliente: Cliente no encontrado (id: $clienteId)");
            return -1; // Cliente no existe
        }

        error_log("actualizarEstadoCliente: Estado actual={$cliente['estado']}, Nuevo estado={$nuevoEstado}");

        // Si el estado ya es el mismo, considera éxito
        if ((int)$cliente['estado'] === (int)$nuevoEstado) {
            error_log("actualizarEstadoCliente: Estado ya es el mismo para cliente $clienteId");
            return 0; // 0 filas afectadas pero éxito
        }

        $stmt = Conexion::conectar()->prepare(
            "UPDATE clientes SET estado = :estado WHERE id = :id"
        );
        $stmt->bindParam(":estado", $nuevoEstado, PDO::PARAM_INT);
        $stmt->bindParam(":id", $clienteId, PDO::PARAM_INT);
        $resultado = $stmt->execute();
        error_log("Filas afectadas en clientes: " . $stmt->rowCount());
        if (!$resultado) {
            error_log("Error al actualizar estado del cliente: " . print_r($stmt->errorInfo(), true));
            return -2; // Error SQL
        }
        return $stmt->rowCount(); // Retorna número de filas afectadas
    }

static private function mapearActividadATitulo($actividad) {
    $mapeo = [
        'llamada' => 'Llamada',
        'reunion' => 'Reunión',
        'mensaje de whatsapp' => 'Mensaje WhatsApp',
        'seguimiento de presupuesto' => 'Seguimiento de Presupuesto',
        'ofertar' => 'Ofertar',
        'llamada para demostracion' => 'Llamada para Demostración',
        'otros' => 'Otros'
    ];
    return $mapeo[strtolower($actividad)] ?? $actividad;
}

static public function ctrVerificarReunionExistente($clienteId, $actividad, $fecha) {
    require_once __DIR__ . '/../modelos/conexion.php';

    try {
        $titulo = self::mapearActividadATitulo($actividad);

        $stmt = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total FROM reuniones
             WHERE cliente_id = :cliente_id
             AND titulo = :titulo
             AND DATE(fecha) = :fecha"
        );
        $stmt->bindParam(":cliente_id", $clienteId, PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
        $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    } catch (Exception $e) {
        error_log("Error en ctrVerificarReunionExistente: " . $e->getMessage());
        return false;
    }
}
}