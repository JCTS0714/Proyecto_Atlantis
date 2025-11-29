<?php
// Ensure consistent timezone + session for AJAX
require_once __DIR__ . '/_timezone.php';
require_once "../controladores/ControladorOportunidad.php";
require_once "../modelos/calendario.modelo.php";
require_once "../modelos/ModeloCRM.php";

if(isset($_POST["accion"]) && $_POST["accion"] == "obtener_lista_clientes") {
    // Controlador ya incluido globalmente en index.php
    $clientes = ControladorOportunidad::ctrMostrarClientesOrdenados();

    // Obtener ids de todos los clientes para consultar reuniones activas (archivado = 0)
    $allClienteIds = array_column($clientes, 'id');
    $reuniones = [];
    if (!empty($allClienteIds)) {
        $reuniones = ModeloCalendario::mdlMostrarReunionesPorClientes($allClienteIds);
    }

    // Agrupar reuniones por cliente_id
    $reunionesPorCliente = [];
    foreach ($reuniones as $reunion) {
        $reunionesPorCliente[$reunion['cliente_id']][] = $reunion['titulo'];
    }

    // Filtrar clientes para mostrar sólo aquellos que tengan reuniones activas
    $clientesConReuniones = array_filter($clientes, function($c) use ($reunionesPorCliente) {
        return isset($reunionesPorCliente[$c['id']]) && !empty($reunionesPorCliente[$c['id']]);
    });

    // Limitar a 5
    $clientesLimit = array_slice(array_values($clientesConReuniones), 0, 5);

    $html = '';
    if (!empty($clientesLimit)) {
        foreach ($clientesLimit as $cliente) {
            $html .= '<li class="list-group-item">';
            $html .= '<strong>' . htmlspecialchars($cliente['nombre']) . '</strong><br>';
            $html .= '<small>';
            $actividades = isset($reunionesPorCliente[$cliente['id']]) ? $reunionesPorCliente[$cliente['id']] : [];
            if (!empty($actividades)) {
                // Mostrar botones para cada actividad
                foreach ($actividades as $index => $actividad) {
                    $html .= '<button type="button" class="btn btn-default btn-xs actividad-btn" data-cliente-id="' . htmlspecialchars($cliente['id']) . '" data-actividad="' . htmlspecialchars($actividad) . '" style="margin-right: 5px; margin-bottom: 3px;">' . htmlspecialchars($actividad) . '</button>';
                }
                // Mostrar contador de actividades
                $html .= '<br><small class="text-muted">Total actividades: ' . count($actividades) . '</small>';
            } else {
                $html .= 'Sin actividades';
            }
            $html .= '</small>';
            $html .= '</li>';
        }
    } else {
        $html = '<li class="list-group-item">No hay clientes con reuniones activas.</li>';
    }

    echo $html;
}