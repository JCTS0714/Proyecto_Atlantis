<?php
require_once "../controladores/calendario.controlador.php";
require_once "../modelos/calendario.modelo.php";

class AjaxCalendario {

    public $id;
    public $datos;

    // Crear reunión
    public function ajaxCrearReunion() {
        // Log para depuración: mostrar datos recibidos antes de crear reunión
        error_log("Datos recibidos para crear reunión: " . print_r($this->datos, true));

        // Validar cliente_id
        if (isset($this->datos['cliente_id']) && !empty($this->datos['cliente_id'])) {
            $clienteId = intval($this->datos['cliente_id']);
            $existeCliente = ModeloCalendario::mdlExisteCliente($clienteId);
            if (!$existeCliente) {
                echo json_encode(['error' => 'El cliente_id no existe']);
                return;
            }
        } else {
            echo json_encode(['error' => 'El cliente_id es obligatorio']);
            return;
        }

        // Validar que no exista una actividad duplicada para el mismo cliente
        if (isset($this->datos['titulo']) && !empty($this->datos['titulo'])) {
            $titulo = trim($this->datos['titulo']);
            $existeDuplicada = ModeloCalendario::mdlExisteActividadDuplicada($clienteId, $titulo);
            if ($existeDuplicada) {
                echo json_encode(['error' => 'Ya existe una actividad "' . $titulo . '" para este cliente. No se permiten actividades duplicadas.']);
                return;
            }
        }

        // Inicializar ultima_notificacion en null para nueva reunión
        $this->datos['ultima_notificacion'] = null;

        $respuesta = ControladorCalendario::ctrCrearReunion($this->datos);
        echo json_encode($respuesta);
    }

    // Actualizar reunión
    public function ajaxActualizarReunion() {
        // Validar cliente_id
        if (isset($this->datos['cliente_id']) && !empty($this->datos['cliente_id'])) {
            $clienteId = intval($this->datos['cliente_id']);
            $existeCliente = ModeloCalendario::mdlExisteCliente($clienteId);
            if (!$existeCliente) {
                echo json_encode(['error' => 'El cliente_id no existe']);
                return;
            }
        } else {
            echo json_encode(['error' => 'El cliente_id es obligatorio']);
            return;
        }

        // Validar que no exista una actividad duplicada para el mismo cliente (excluyendo la reunión actual)
        if (isset($this->datos['titulo']) && !empty($this->datos['titulo']) && isset($this->datos['id'])) {
            $titulo = trim($this->datos['titulo']);
            $reunionId = intval($this->datos['id']);

            // Verificar si existe otra reunión con el mismo título para este cliente
            $stmt = Conexion::conectar()->prepare("SELECT id FROM reuniones WHERE cliente_id = :cliente_id AND titulo = :titulo AND id != :id");
            $stmt->bindParam(":cliente_id", $clienteId, PDO::PARAM_INT);
            $stmt->bindParam(":titulo", $titulo, PDO::PARAM_STR);
            $stmt->bindParam(":id", $reunionId, PDO::PARAM_INT);
            $stmt->execute();
            $existeDuplicada = $stmt->fetch();

            if ($existeDuplicada) {
                echo json_encode(['error' => 'Ya existe otra actividad "' . $titulo . '" para este cliente. No se permiten actividades duplicadas.']);
                return;
            }
        }

        $respuesta = ControladorCalendario::ctrEditarReunion($this->datos);
            // Devolver la respuesta real del controlador/modelo (generalmente JSON con success)
            if (is_string($respuesta)) {
                // respuesta ya viene serializada (json_encode en el modelo)
                header('Content-Type: application/json');
                echo $respuesta;
            } else {
                echo json_encode($respuesta);
            }
    }

    // Actualizar solo fecha de reunión
    public function ajaxActualizarFechaReunion() {
        $respuesta = ControladorCalendario::ctrActualizarFechaReunion($this->datos);
        echo json_encode($respuesta);
    }

    // Eliminar reunión
    public function ajaxEliminarReunion() {
        $respuesta = ControladorCalendario::ctrEliminarReunion($this->id);
        echo json_encode($respuesta);
    }

    // Mostrar reuniones
    public function ajaxMostrarReuniones() {
        try {
            $reuniones = ControladorCalendario::ctrMostrarReuniones();

            if (empty($reuniones)) {
                echo json_encode(['error' => 'No hay reuniones en la base de datos', 'debug' => 'Tabla reuniones vacía']);
                return;
            }

            $debug_data = array_slice($reuniones, 0, 3);

            $eventos = [];
            foreach ($reuniones as $reunion) {
                $evento = [
                    'id' => $reunion['id'],
                    'title' => $reunion['titulo'],
                    'start' => $reunion['fecha'] . 'T' . $reunion['hora_inicio'],
                    'end' => $reunion['fecha'] . 'T' . $reunion['hora_fin'],
                    'color' => !empty($reunion['color']) ? $reunion['color'] : '#3c8dbc',
                    'descripcion' => isset($reunion['descripcion']) ? $reunion['descripcion'] : '',
                    'cliente_id' => isset($reunion['cliente_id']) ? $reunion['cliente_id'] : '',
                    'usuario_id' => isset($reunion['usuario_id']) ? $reunion['usuario_id'] : '',
                    'ubicacion' => isset($reunion['ubicacion']) ? $reunion['ubicacion'] : '',
                    'observaciones' => isset($reunion['observaciones']) ? $reunion['observaciones'] : '',
                    'debug_color' => isset($reunion['color']) ? $reunion['color'] : 'SIN COLOR'
                ];
                $eventos[] = $evento;
            }

            $response = [
                'eventos' => $eventos,
                'total_reuniones' => count($reuniones),
                'debug_muestra' => $debug_data,
                'query_debug' => 'SELECT * FROM reuniones',
                'raw_data' => $reuniones
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al obtener reuniones', 'message' => $e->getMessage()]);
        }
    }
}

// Crear reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "crear") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxCrearReunion();
}

// Actualizar reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxActualizarReunion();
}

// Eliminar reunión
if(isset($_POST["accion"]) && $_POST["accion"] == "eliminar") {
    $ajax = new AjaxCalendario();
    $ajax->id = $_POST["id"];
    $ajax->ajaxEliminarReunion();
}

// Mostrar reuniones
if(isset($_POST["accion"]) && $_POST["accion"] == "mostrar") {
    $ajax = new AjaxCalendario();
    $ajax->ajaxMostrarReuniones();
}

if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar_fecha") {
    $ajax = new AjaxCalendario();
    $ajax->datos = $_POST;
    $ajax->ajaxActualizarFechaReunion();
}

if(isset($_POST["accion"]) && $_POST["accion"] == "obtener_para_notificar") {
    $usuario_id = intval($_POST['usuario_id']);
    error_log("AJAX obtener_para_notificar para usuario_id: $usuario_id");
    if ($usuario_id <= 0) {
        error_log("Usuario ID inválido: $usuario_id");
        echo json_encode(['eventos' => []]);
        exit;
    }
    $reuniones = ControladorCalendario::ctrObtenerReunionesParaNotificar($usuario_id);
    error_log("Reuniones obtenidas: " . print_r($reuniones, true));

    // Obtener perfil del usuario para debug
    $stmtPerfil = Conexion::conectar()->prepare("SELECT perfil FROM usuarios WHERE id = :usuario_id");
    $stmtPerfil->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
    $stmtPerfil->execute();
    $usuario = $stmtPerfil->fetch();
    $perfil = $usuario ? $usuario['perfil'] : 'DESCONOCIDO';

    // Filtrar reuniones que no hayan pasado fecha y hora_fin
    $ahora = new DateTime();
    $reuniones_filtradas = array_filter($reuniones, function($r) use ($ahora) {
        // Ajuste para manejar formatos de hora_fin sin segundos
        $fechaHoraFinStr = $r['fecha'] . ' ' . $r['hora_fin'];
        error_log("Procesando reunión ID {$r['id']} con hora_fin raw: '{$r['hora_fin']}' y fechaHoraFinStr: '$fechaHoraFinStr'");
        $fechaHoraFin = DateTime::createFromFormat('Y-m-d H:i:s', $fechaHoraFinStr);
        if (!$fechaHoraFin) {
            error_log("Fallo al parsear con formato 'Y-m-d H:i:s', intentando con 'Y-m-d H:i'");
            $fechaHoraFin = DateTime::createFromFormat('Y-m-d H:i', $fechaHoraFinStr);
        }
        if (!$fechaHoraFin) {
            error_log("Fallo al parsear fechaHoraFinStr para reunión ID {$r['id']}");
            return false;
        }
        // Permitir reuniones del día actual aunque la hora haya pasado
        $fechaReunion = DateTime::createFromFormat('Y-m-d', $r['fecha']);
        $fechaActual = new DateTime();
        $fechaActual->setTime(0,0,0);
        $fechaReunion->setTime(0,0,0);
        $esMismoDia = ($fechaReunion == $fechaActual);
        $resultado = $fechaHoraFin >= $ahora || $esMismoDia;
        error_log("Reunión ID {$r['id']} fechaHoraFin: $fechaHoraFinStr, ahora: {$ahora->format('Y-m-d H:i:s')}, esMismoDia: " . ($esMismoDia ? 'true' : 'false') . ", resultado filtro: " . ($resultado ? 'true' : 'false'));
        return $resultado;
    });
    error_log("Reuniones filtradas: " . print_r($reuniones_filtradas, true));
    echo json_encode(['eventos' => array_values($reuniones_filtradas), 'debug_perfil' => $perfil, 'debug_usuario_id' => $usuario_id, 'debug_total_reuniones' => count($reuniones)]);
}

// Actualizar última notificación
if(isset($_POST["accion"]) && $_POST["accion"] == "actualizar_ultima_notificacion") {
    $id = intval($_POST['id']);
    $fecha = $_POST['fecha'];
    $respuesta = ControladorCalendario::ctrActualizarUltimaNotificacion($id, $fecha);
    echo json_encode(['success' => $respuesta]);
}

// Marcar varias notificaciones vistas en lote
if(isset($_POST["accion"]) && $_POST["accion"] == "marcar_notificaciones_vistas") {
    $idsRaw = isset($_POST['ids']) ? $_POST['ids'] : null;
    $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;

    // Aceptar JSON string o array
    $ids = [];
    if (is_string($idsRaw)) {
        $decoded = json_decode($idsRaw, true);
        if (is_array($decoded)) $ids = $decoded;
    } elseif (is_array($idsRaw)) {
        $ids = $idsRaw;
    }

    // Sanitizar a enteros
    $ids = array_map('intval', $ids);
    $ids = array_filter($ids, function($v) { return $v > 0; });

    if (empty($ids) || !$fecha) {
        echo json_encode(['success' => false, 'error' => 'Faltan parámetros (ids/fecha)']);
        exit;
    }

    $respuesta = ControladorCalendario::ctrActualizarUltimaNotificacionLote($ids, $fecha);
    echo json_encode(['success' => (bool)$respuesta, 'updated_count' => $respuesta ? count($ids) : 0]);
}

// Verificar actividades duplicadas para un cliente
if(isset($_POST["accion"]) && $_POST["accion"] == "verificar_duplicados") {
    $clienteId = intval($_POST['cliente_id']);
    $stmt = Conexion::conectar()->prepare("SELECT DISTINCT titulo FROM reuniones WHERE cliente_id = :cliente_id ORDER BY titulo");
    $stmt->bindParam(":cliente_id", $clienteId, PDO::PARAM_INT);
    $stmt->execute();
    $actividades = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['duplicados' => $actividades]);
}

// Listar reuniones pasadas no archivadas
if(isset($_POST["accion"]) && $_POST["accion"] == "listar_pasadas") {
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 100;
    $reuniones = ControladorCalendario::ctrListarReunionesPasadas($limit);
    echo json_encode(['eventos' => $reuniones]);
}

// Archivar o marcar como concretado
if(isset($_POST["accion"]) && ($_POST["accion"] == "archivar_reunion" || $_POST["accion"] == "marcar_concretado")) {
    $id = intval($_POST['id']);
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : null;
    $estado = null;
    if ($_POST["accion"] == "marcar_concretado") $estado = 'concretado';
    if ($_POST["accion"] == "archivar_reunion") $estado = 'archivado';
    $ok = ControladorCalendario::ctrArchivarReunion($id, $estado, $usuario_id);
    echo json_encode(['success' => (bool)$ok]);
}

// Marcar reunión como no concretada (nueva acción)
if(isset($_POST["accion"]) && $_POST["accion"] == "marcar_no_concretado") {
    $id = intval($_POST['id']);
    $usuario_id = isset($_POST['usuario_id']) ? intval($_POST['usuario_id']) : null;
    // Reutilizar la lógica de archivar pero marcar estado 'no_concretado'
    $ok = ControladorCalendario::ctrArchivarReunion($id, 'no_concretado', $usuario_id);
    echo json_encode(['success' => (bool)$ok]);
}

// Obtener reuniones archivadas (para interfaz completa)
if(isset($_POST["accion"]) && $_POST["accion"] == "obtener_archivadas") {
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 500;
    $reuniones = ControladorCalendario::ctrObtenerReunionesArchivadas($limit);
    echo json_encode(['eventos' => $reuniones]);
}