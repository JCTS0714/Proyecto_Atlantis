<?php
require_once "conexion.php";

class ModeloDashboard {

    /*=============================================
    OBTENER MÉTRICAS DE CLIENTES POR ESTADO
    =============================================*/
    static public function mdlObtenerMetricasClientes($periodo = 'mensual') {
        
        $filtroFecha = "";
        if ($periodo === 'mensual') {
            $filtroFecha = "AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
                           AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())";
        } elseif ($periodo === 'semanal') {
            $filtroFecha = "AND fecha_creacion >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
        }

        $stmt = Conexion::conectar()->prepare(
            "SELECT 
                estado,
                COUNT(*) as total,
                DATE_FORMAT(fecha_creacion, '%Y-%m') as periodo
            FROM clientes 
            WHERE 1=1 $filtroFecha
            GROUP BY estado, DATE_FORMAT(fecha_creacion, '%Y-%m')
            ORDER BY periodo, estado"
        );

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
        
        // Clientes ganados este mes (estado 2)
        $stmtClientes = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total 
             FROM clientes 
             WHERE estado = 2 
             AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
             AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())"
        );
        $stmtClientes->execute();
        $clientesGanados = $stmtClientes->fetch(PDO::FETCH_ASSOC);

        // Prospectos nuevos vs mes anterior
        $stmtProspectos = Conexion::conectar()->prepare(
            "SELECT 
                (SELECT COUNT(*) 
                 FROM clientes 
                 WHERE MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
                 AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())) as actual,
                (SELECT COUNT(*) 
                 FROM clientes 
                 WHERE MONTH(fecha_creacion) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) 
                 AND YEAR(fecha_creacion) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))) as anterior"
        );
        $stmtProspectos->execute();
        $prospectos = $stmtProspectos->fetch(PDO::FETCH_ASSOC);

        // Clientes perdidos este mes (estado 3)
        $stmtPerdidos = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total 
             FROM clientes 
             WHERE estado = 3 
             AND MONTH(fecha_creacion) = MONTH(CURRENT_DATE()) 
             AND YEAR(fecha_creacion) = YEAR(CURRENT_DATE())"
        );
        $stmtPerdidos->execute();
        $clientesPerdidos = $stmtPerdidos->fetch(PDO::FETCH_ASSOC);

        // Total de reuniones esta semana
        $stmtReuniones = Conexion::conectar()->prepare(
            "SELECT COUNT(*) as total
             FROM reuniones
             WHERE fecha BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL (DAYOFWEEK(CURRENT_DATE()) - 1) DAY)
                           AND DATE_ADD(CURRENT_DATE(), INTERVAL (7 - DAYOFWEEK(CURRENT_DATE())) DAY)"
        );
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
