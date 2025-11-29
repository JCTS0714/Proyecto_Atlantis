<?php
require_once "conexion.php";

class ModeloCRM {
    static public function mdlMostrarOportunidades($tabla, $item = null, $valor = null, $filtrarUltimaSemana = false, $filters = null) {
        if ($filtrarUltimaSemana) {
            $fechaLimite = date('Y-m-d', strtotime('-7 days'));
            if ($item != null) {
                if ($item === 'id') {
                    $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id WHERE o.id = :id AND o.fecha_modificacion >= :fechaLimite");
                    $stmt->bindParam(":id", $valor, PDO::PARAM_INT);
                    $stmt->bindParam(":fechaLimite", $fechaLimite, PDO::PARAM_STR);
                } else {
                    $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id WHERE $item = :$item AND o.fecha_modificacion >= :fechaLimite");
                    $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
                    $stmt->bindParam(":fechaLimite", $fechaLimite, PDO::PARAM_STR);
                }
            } else {
                // Limitar resultados y ordenar por fecha de modificación para evitar cargas masivas
                $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id WHERE o.fecha_modificacion >= :fechaLimite ORDER BY o.fecha_modificacion DESC LIMIT 200");
                $stmt->bindParam(":fechaLimite", $fechaLimite, PDO::PARAM_STR);
            }
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // If filters are provided and no specific item lookup, apply filter logic
            if ($filters && is_array($filters) && empty($item)) {
                $where = [];
                $params = [];

                // Filter by cliente fields (joined table c)
                if (!empty($filters['nombre'])) {
                    $where[] = "c.nombre LIKE :nombre";
                    $params[':nombre'] = '%' . $filters['nombre'] . '%';
                }
                if (!empty($filters['telefono'])) {
                    $where[] = "c.telefono LIKE :telefono";
                    $params[':telefono'] = '%' . $filters['telefono'] . '%';
                }
                if (!empty($filters['documento'])) {
                    $where[] = "c.documento LIKE :documento";
                    $params[':documento'] = '%' . $filters['documento'] . '%';
                }

                // Date filters: either periodo shortcuts or explicit fecha_inicio/fecha_fin
                $fechaInicio = null; $fechaFin = null;
                if (!empty($filters['periodo']) && $filters['periodo'] !== '') {
                    switch ($filters['periodo']) {
                        case 'today':
                            $fechaInicio = date('Y-m-d');
                            $fechaFin = $fechaInicio;
                            break;
                        case 'this_week':
                            $fechaInicio = date('Y-m-d', strtotime('monday this week'));
                            $fechaFin = date('Y-m-d', strtotime('sunday this week'));
                            break;
                        case 'this_month':
                            $fechaInicio = date('Y-m-01');
                            $fechaFin = date('Y-m-t');
                            break;
                        case 'custom':
                            if (!empty($filters['fecha_inicio'])) $fechaInicio = $filters['fecha_inicio'];
                            if (!empty($filters['fecha_fin'])) $fechaFin = $filters['fecha_fin'];
                            break;
                        default:
                            break;
                    }
                } else {
                    if (!empty($filters['fecha_inicio'])) $fechaInicio = $filters['fecha_inicio'];
                    if (!empty($filters['fecha_fin'])) $fechaFin = $filters['fecha_fin'];
                }

                if ($fechaInicio && $fechaFin) {
                    // apply to fecha_apertura if available, fallback to fecha_creacion
                    $where[] = "(COALESCE(o.fecha_apertura, o.fecha_creacion) BETWEEN :fechaInicio AND :fechaFin)";
                    $params[':fechaInicio'] = $fechaInicio;
                    $params[':fechaFin'] = $fechaFin;
                }

                $sql = "SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id";
                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }
                $sql .= " ORDER BY o.fecha_modificacion DESC LIMIT 200";

                $stmt = Conexion::conectar()->prepare($sql);
                foreach ($params as $k => $v) {
                    $stmt->bindValue($k, $v, PDO::PARAM_STR);
                }
                $stmt->execute();
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $stmt = null;
                return $resultado;
            }

            if ($item != null) {
                // Especificar explícitamente la tabla para evitar ambigüedad en la columna 'id'
                if ($item === 'id') {
                    $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id WHERE o.id = :id");
                    $stmt->bindParam(":id", $valor, PDO::PARAM_INT);
                } else {
                    $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id WHERE $item = :$item");
                    $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
                }
                $stmt->execute();
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } else {
                // Cuando se solicitan todas las oportunidades, limitar para evitar sobrecarga
                $stmt = Conexion::conectar()->prepare("SELECT o.*, c.nombre AS nombre_cliente FROM $tabla o LEFT JOIN clientes c ON o.cliente_id = c.id ORDER BY o.fecha_modificacion DESC LIMIT 200");
                $stmt->execute();
                $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        $stmt->closeCursor();
        $stmt = null;
        return $resultado;
    }

    static public function mdlRegistrarOportunidad($tabla, $datos) {
        $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (cliente_id, usuario_id, titulo, descripcion, valor_estimado, probabilidad, estado, fecha_cierre_estimada) VALUES (:cliente_id, :usuario_id, :titulo, :descripcion, :valor_estimado, :probabilidad, :estado, :fecha_cierre_estimada)");

        $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
        $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
        $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
        $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
        $stmt->bindParam(":valor_estimado", $datos["valor_estimado"], PDO::PARAM_STR);
        $stmt->bindParam(":probabilidad", $datos["probabilidad"], PDO::PARAM_INT);
        $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_INT);
        $stmt->bindParam(":fecha_cierre_estimada", $datos["fecha_cierre_estimada"], PDO::PARAM_STR);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlActualizarEstado($tabla, $id, $estado) {
        $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");

        $stmt->bindParam(":estado", $estado, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlEliminarOportunidad($tabla, $id) {
        $stmt = Conexion::conectar()->prepare("DELETE FROM $tabla WHERE id = :id");

        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $respuesta = $stmt->execute() ? "ok" : "error";
        $stmt->closeCursor();
        $stmt = null;
        return $respuesta;
    }

    static public function mdlMostrarClientes($tabla, $item = null, $valor = null) {
        if ($item != null) {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla WHERE $item = :$item ORDER BY fecha_creacion DESC");
            $stmt->bindParam(":".$item, $valor, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = Conexion::conectar()->prepare("SELECT * FROM $tabla ORDER BY fecha_creacion DESC");
            $stmt->execute();
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        $stmt->closeCursor();
        $stmt = null;
        return $resultado;
    }

    // New method to fetch clients with recent activities (last 15 days)
    static public function mdlMostrarClientesOrdenados($tabla) {
        $fechaLimite = date('Y-m-d', strtotime('-15 days'));
        $sql = "SELECT c.*, COUNT(r.id) as total_actividades
                FROM $tabla c
                INNER JOIN reuniones r ON c.id = r.cliente_id
                WHERE r.fecha >= :fechaLimite
                GROUP BY c.id
                ORDER BY MAX(r.fecha) DESC";
        $stmt = Conexion::conectar()->prepare($sql);
        $stmt->bindParam(':fechaLimite', $fechaLimite, PDO::PARAM_STR);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        $stmt = null;
        return $resultado;
    }

    static public function mdlActualizarOportunidad($tabla, $datos) {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET titulo = :titulo, descripcion = :descripcion, valor_estimado = :valor_estimado, probabilidad = :probabilidad, fecha_cierre_estimada = :fecha_cierre_estimada, actividad = :actividad, fecha_actividad = :fecha_actividad, fecha_modificacion = NOW() WHERE id = :id");

            $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":valor_estimado", $datos["valor_estimado"], PDO::PARAM_STR);
            $stmt->bindParam(":probabilidad", $datos["probabilidad"], PDO::PARAM_INT);
            $stmt->bindParam(":fecha_cierre_estimada", $datos["fecha_cierre_estimada"], PDO::PARAM_STR);
            $stmt->bindParam(":actividad", $datos["actividad"], PDO::PARAM_STR); // Fixed: actividad is string, not int
            $stmt->bindParam(":fecha_actividad", $datos["fecha_actividad"], PDO::PARAM_STR);
            $stmt->bindParam(":id", $datos["id"], PDO::PARAM_INT);

            $resultado = $stmt->execute();

            $stmt->closeCursor();
            $stmt = null;

            error_log("mdlActualizarOportunidad - Resultado: " . ($resultado ? "ok" : "error"));
            return $resultado ? "ok" : "error";
        } catch (Exception $e) {
            error_log("Error en mdlActualizarOportunidad: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }

    static public function mdlCrearReunion($tabla, $datos) {
        try {
            $stmt = Conexion::conectar()->prepare("INSERT INTO $tabla (cliente_id, usuario_id, titulo, descripcion, fecha, hora_inicio, hora_fin, ubicacion, estado, recordatorio, observaciones) VALUES (:cliente_id, :usuario_id, :titulo, :descripcion, :fecha, :hora_inicio, :hora_fin, :ubicacion, :estado, :recordatorio, :observaciones)");

            $stmt->bindParam(":cliente_id", $datos["cliente_id"], PDO::PARAM_INT);
            $stmt->bindParam(":usuario_id", $datos["usuario_id"], PDO::PARAM_INT);
            $stmt->bindParam(":titulo", $datos["titulo"], PDO::PARAM_STR);
            $stmt->bindParam(":descripcion", $datos["descripcion"], PDO::PARAM_STR);
            $stmt->bindParam(":fecha", $datos["fecha"], PDO::PARAM_STR);
            $stmt->bindParam(":hora_inicio", $datos["hora_inicio"], PDO::PARAM_STR);
            $stmt->bindParam(":hora_fin", $datos["hora_fin"], PDO::PARAM_STR);
            $stmt->bindParam(":ubicacion", $datos["ubicacion"], PDO::PARAM_STR);
            $stmt->bindParam(":estado", $datos["estado"], PDO::PARAM_STR);
            $stmt->bindParam(":recordatorio", $datos["recordatorio"], PDO::PARAM_INT);
            $stmt->bindParam(":observaciones", $datos["observaciones"], PDO::PARAM_STR);

            $resultado = $stmt->execute();

            $stmt->closeCursor();
            $stmt = null;

            error_log("mdlCrearReunion - Resultado: " . ($resultado ? "ok" : "error"));
            return $resultado ? "ok" : "error";
        } catch (Exception $e) {
            error_log("Error en mdlCrearReunion: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }
    static public function mdlActualizarEstadoOportunidad($tabla, $datos) {
        try {
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");

            $stmt->bindParam(":estado", $datos['estado'], PDO::PARAM_INT);
            $stmt->bindParam(":id", $datos['id'], PDO::PARAM_INT);

            $resultado = $stmt->execute();

            $stmt->closeCursor();
            $stmt = null;

            return $resultado ? "ok" : "error";
        } catch (Exception $e) {
            error_log("Error en mdlActualizarEstadoOportunidad: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }

    static public function mdlActualizarEstadoCliente($tabla, $idOportunidad, $nuevoEstado) {
        try {
            // First, get the client_id from the opportunity
            $stmt = Conexion::conectar()->prepare("SELECT cliente_id FROM oportunidades WHERE id = :id");
            $stmt->bindParam(":id", $idOportunidad, PDO::PARAM_INT);
            $stmt->execute();
            $oportunidad = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            if (!$oportunidad) {
                return "error: Oportunidad no encontrada";
            }

            $clienteId = $oportunidad['cliente_id'];

            // Now update the client state
            $stmt = Conexion::conectar()->prepare("UPDATE $tabla SET estado = :estado WHERE id = :id");
            $stmt->bindParam(":estado", $nuevoEstado, PDO::PARAM_INT);
            $stmt->bindParam(":id", $clienteId, PDO::PARAM_INT);

            $resultado = $stmt->execute();

            $stmt->closeCursor();
            $stmt = null;

            return $resultado ? "ok" : "error";
        } catch (Exception $e) {
            error_log("Error en mdlActualizarEstadoCliente: " . $e->getMessage());
            return "error: " . $e->getMessage();
        }
    }
}