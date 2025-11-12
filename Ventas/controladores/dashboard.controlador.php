<?php
require_once __DIR__ . '/../modelos/dashboard.modelo.php';

class ControladorDashboard {

    /*=============================================
    OBTENER MÉTRICAS DE CLIENTES
    =============================================*/
    static public function ctrObtenerMetricasClientes() {
        
        $periodo = $_POST['periodo'] ?? 'mensual';
        error_log("ctrObtenerMetricasClientes called with periodo: " . $periodo);
        
        try {
            $metricas = ModeloDashboard::mdlObtenerMetricasClientes($periodo);
            error_log("Metricas count: " . count($metricas));
            error_log("Metricas data: " . json_encode($metricas));
            
            return [
                'status' => 'success',
                'data' => $metricas,
                'periodo' => $periodo
            ];
            
        } catch (Exception $e) {
            error_log("Error in ctrObtenerMetricasClientes: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error al obtener métricas: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    OBTENER REUNIONES DE LA SEMANA
    =============================================*/
    static public function ctrObtenerReunionesSemana() {
        
        try {
            $reuniones = ModeloDashboard::mdlObtenerReunionesSemana();
            
            return [
                'status' => 'success',
                'data' => $reuniones,
                'total' => count($reuniones)
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al obtener reuniones: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    OBTENER INDICADORES CLAVE
    =============================================*/
    static public function ctrObtenerIndicadoresClave() {
        
        try {
            $indicadores = ModeloDashboard::mdlObtenerIndicadoresClave();
            
            // Calcular variación de prospectos
            $variacionProspectos = 0;
            if ($indicadores['prospectos_anterior'] > 0) {
                $variacionProspectos = (($indicadores['prospectos_actual'] - $indicadores['prospectos_anterior']) / $indicadores['prospectos_anterior']) * 100;
            } elseif ($indicadores['prospectos_actual'] > 0) {
                $variacionProspectos = 100; // Crecimiento del 100% si no había prospectos antes
            }
            
            $indicadores['variacion_prospectos'] = round($variacionProspectos, 2);
            
            return [
                'status' => 'success',
                'data' => $indicadores
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al obtener indicadores: ' . $e->getMessage()
            ];
        }
    }

    /*=============================================
    OBTENER EVOLUCIÓN MENSUAL
    =============================================*/
    static public function ctrObtenerEvolucionMensual() {
        
        try {
            $evolucion = ModeloDashboard::mdlObtenerEvolucionMensual();
            
            // Procesar datos para formato de gráfico
            $datosProcesados = [];
            foreach ($evolucion as $registro) {
                $mes = $registro['mes'];
                $estado = $registro['estado'];
                $total = $registro['total'];
                
                if (!isset($datosProcesados[$mes])) {
                    $datosProcesados[$mes] = [
                        'mes' => $mes,
                        'prospectos' => 0,
                        'seguimiento' => 0,
                        'clientes' => 0,
                        'no_clientes' => 0,
                        'espera' => 0
                    ];
                }
                
                switch ($estado) {
                    case 0: $datosProcesados[$mes]['prospectos'] = $total; break;
                    case 1: $datosProcesados[$mes]['seguimiento'] = $total; break;
                    case 2: $datosProcesados[$mes]['clientes'] = $total; break;
                    case 3: $datosProcesados[$mes]['no_clientes'] = $total; break;
                    case 4: $datosProcesados[$mes]['espera'] = $total; break;
                }
            }
            
            return [
                'status' => 'success',
                'data' => array_values($datosProcesados)
            ];
            
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al obtener evolución: ' . $e->getMessage()
            ];
        }
    }
    /*=============================================
    OBTENER TOTALES PARA EL DASHBOARD
    =============================================*/
    static public function ctrObtenerTotalesDashboard() {
        try {
            $totales = ModeloDashboard::mdlObtenerTotalesDashboard();
            return [
                'status' => 'success',
                'data' => $totales
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Error al obtener totales: ' . $e->getMessage()
            ];
        }
    }
}
?>
