<?php
require_once "conexion.php";

class ModeloDashboard {

    /*=============================================
    OBTENER MÉTRICAS DE CLIENTES POR ESTADO
    =============================================*/
    static public function mdlObtenerMetricasClientes($periodo = 'mensual') {
        // Calcular rango de fechas en PHP para poder aprovechar índices en la columna fecha_creacion
        $params = [];
        $fechaFiltro = "";
        if ($periodo === 'mensual') {
            $desde = date('Y-m-01');
            $hasta = date('Y-m-t');
            $fechaFiltro = " AND fecha_creacion BETWEEN :desde AND :hasta";
            $params[':desde'] = $desde;
            $params[':hasta'] = $hasta;
        } elseif ($periodo === 'semanal') {
            $desde = date('Y-m-d', strtotime('-6 days')); // últimos 7 días incluyendo hoy
            $hasta = date('Y-m-d');
            $fechaFiltro = " AND fecha_creacion BETWEEN :desde AND :hasta";
            $params[':desde'] = $desde;
            $params[':hasta'] = $hasta;
        }

        $sql = "SELECT 
                estado,
                COUNT(*) as total,
                DATE_FORMAT(fecha_creacion, '%Y-%m') as periodo
            FROM clientes 
            WHERE 1=1 " . $fechaFiltro . "
            GROUP BY estado, DATE_FORMAT(fecha_creacion, '%Y-%m')
            ORDER BY periodo, estado";

        $stmt = Conexion::conectar()->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v, PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=============================================
    OBTENER REUNIONES DE LA SEMANA ACTUAL
    =============================================*/
    static public function mdlObtenerReunionesSemana() {

        $stmt = Conexion::conectar()->prepare(
            "SELECT
                r.id,
                r.titulo,
                r.fecha,
                r.hora_inicio,
                r.hora_fin,
                r.estado,
                c.nombre as cliente_nombre,
                r.ubicacion
            FROM reuniones r
            LEFT JOIN clientes c ON r.cliente_id = c.id
            WHERE r.fecha BETWEEN CURRENT_DATE()
                            AND DATE_ADD(CURRENT_DATE(), INTERVAL (7 - DAYOFWEEK(CURRENT_DATE())) DAY)
            ORDER BY r.fecha, r.hora_inicio"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*=============================================
    OBTENER INDICADORES CLAVE
    =============================================*/
    static public function mdlObtenerIndicadoresClave() {
        // Calcular rangos para mes actual y mes anterior
        $inicioMes = date('Y-m-01');
        $finMes = date('Y-m-t');

        $inicioMesAnterior = date('Y-m-01', strtotime('first day of last month'));
        $finMesAnterior = date('Y-m-t', strtotime('last day of last month'));

        // Clientes ganados este mes (estado 2)
        $stmtClientes = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total 
             FROM clientes 
             WHERE estado = 2 
             AND fecha_creacion BETWEEN :inicioMes AND :finMes"
        );
        $stmtClientes->bindParam(':inicioMes', $inicioMes, PDO::PARAM_STR);
        $stmtClientes->bindParam(':finMes', $finMes, PDO::PARAM_STR);
        $stmtClientes->execute();
        $clientesGanados = $stmtClientes->fetch(PDO::FETCH_ASSOC);

        // Prospectos nuevos vs mes anterior
        $stmtProspectos = Conexion::conectar()->prepare(
            "SELECT 
                (SELECT COUNT(*) FROM clientes WHERE fecha_creacion BETWEEN :inicioMes AND :finMes) as actual,
                (SELECT COUNT(*) FROM clientes WHERE fecha_creacion BETWEEN :inicioMesAnterior AND :finMesAnterior) as anterior"
        );
        $stmtProspectos->bindParam(':inicioMes', $inicioMes, PDO::PARAM_STR);
        $stmtProspectos->bindParam(':finMes', $finMes, PDO::PARAM_STR);
        $stmtProspectos->bindParam(':inicioMesAnterior', $inicioMesAnterior, PDO::PARAM_STR);
        $stmtProspectos->bindParam(':finMesAnterior', $finMesAnterior, PDO::PARAM_STR);
        $stmtProspectos->execute();
        $prospectos = $stmtProspectos->fetch(PDO::FETCH_ASSOC);

        // Clientes perdidos este mes (estado 3)
        $stmtPerdidos = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total 
             FROM clientes 
             WHERE estado = 3 
             AND fecha_creacion BETWEEN :inicioMes AND :finMes"
        );
        $stmtPerdidos->bindParam(':inicioMes', $inicioMes, PDO::PARAM_STR);
        $stmtPerdidos->bindParam(':finMes', $finMes, PDO::PARAM_STR);
        $stmtPerdidos->execute();
        $clientesPerdidos = $stmtPerdidos->fetch(PDO::FETCH_ASSOC);

        // Total de reuniones esta semana (calcular inicio y fin de la semana actual)
        $inicioSemana = date('Y-m-d', strtotime('monday this week'));
        $finSemana = date('Y-m-d', strtotime('sunday this week'));
        $stmtReuniones = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total
             FROM reuniones
             WHERE fecha BETWEEN :inicioSemana AND :finSemana"
        );
        $stmtReuniones->bindParam(':inicioSemana', $inicioSemana, PDO::PARAM_STR);
        $stmtReuniones->bindParam(':finSemana', $finSemana, PDO::PARAM_STR);
        $stmtReuniones->execute();
        $reunionesSemana = $stmtReuniones->fetch(PDO::FETCH_ASSOC);

        return [
            'clientes_ganados' => $clientesGanados['total'] ?? 0,
            'prospectos_actual' => $prospectos['actual'] ?? 0,
            'prospectos_anterior' => $prospectos['anterior'] ?? 0,
            'clientes_perdidos' => $clientesPerdidos['total'] ?? 0,
            'reuniones_semana' => $reunionesSemana['total'] ?? 0
        ];
    }

    /*=============================================
    OBTENER EVOLUCIÓN MENSUAL DE CLIENTES
    =============================================*/
    static public function mdlObtenerEvolucionMensual() {
        
        $stmt = Conexion::conectar()->prepare(
            "SELECT 
                DATE_FORMAT(fecha_creacion, '%Y-%m') as mes,
                estado,
                COUNT(*) as total
            FROM clientes 
            WHERE fecha_creacion >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
            GROUP BY DATE_FORMAT(fecha_creacion, '%Y-%m'), estado
            ORDER BY mes, estado"
        );

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*=============================================
    OBTENER TOTALES PARA EL DASHBOARD
    =============================================*/
    static public function mdlObtenerTotalesDashboard() {
        $stmt = Conexion::conectar()->prepare(
            "SELECT
                (SELECT COUNT(*) FROM clientes WHERE estado = 0) as total_prospectos,
                (SELECT COUNT(*) FROM clientes WHERE estado = 2) as total_clientes,
                (SELECT COUNT(*) FROM reuniones WHERE fecha >= CURRENT_DATE()) as total_reuniones"
        );
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
