<?php
require_once "modelos/conexion.php";
require_once "modelos/ModeloIncidencias.php";

echo "=== CREANDO DATOS DE PRUEBA PARA INCIDENCIAS ===\n\n";

$clientes = ModeloIncidencias::mdlBuscarClientes('');
if (count($clientes) > 0) {
    $cliente_id = $clientes[0]['id'];
    echo "Cliente disponible: ID {$cliente_id}, Nombre: {$clientes[0]['nombre']}\n\n";

    // Crear 5 incidencias de prueba distribuidas en las columnas del backlog
    $columnas = ['En proceso', 'Validado', 'Terminado'];
    $prioridades = ['baja', 'media', 'alta'];

    for ($i = 1; $i <= 5; $i++) {
        $columna = $columnas[($i - 1) % count($columnas)];
        $prioridad = $prioridades[($i - 1) % count($prioridades)];

        $datos = array(
            'correlativo' => 'TEST' . str_pad($i + 10, 3, '0', STR_PAD_LEFT), // Cambiar a TEST011, TEST012, etc.
            'nombre_incidencia' => 'Incidencia de prueba ' . $i,
            'cliente_id' => $cliente_id,
            'usuario_id' => 1, // Asumiendo que existe el usuario 1
            'fecha' => date('Y-m-d'),
            'prioridad' => $prioridad,
            'observaciones' => 'Observaciones de prueba para la incidencia ' . $i
        );

        $resultado = ModeloIncidencias::mdlCrearIncidencia($datos);
        if ($resultado == 'ok') {
            // Obtener el ID de la incidencia recién creada
            $incidencias = ModeloIncidencias::mdlMostrarIncidencias();
            $ultimaIncidencia = end($incidencias);
            $idIncidencia = $ultimaIncidencia['id'];

            // Actualizar columna del backlog
            ModeloIncidencias::mdlActualizarColumnaBacklog($idIncidencia, $columna);
            echo "Incidencia $i: Creada en columna '$columna'\n";
        } else {
            echo "Incidencia $i: Error al crear\n";
        }
    }

    echo "\nDatos de prueba creados exitosamente.\n";
} else {
    echo "No hay clientes disponibles para crear incidencias de prueba.\n";
}
?>
