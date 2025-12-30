<?php
// Archivo: exportar_csv.php

// Conexión a la base de datos
include 'modelos/conexion.php'; // Asegúrate de que la ruta sea correcta

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Consulta para obtener los datos de la tabla
    $query = "SELECT * FROM tu_tabla"; // Cambia "tu_tabla" por el nombre de tu tabla
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        // Nombre del archivo CSV
        $filename = "exportacion_" . date('Ymd') . ".csv";

        // Encabezados para forzar la descarga del archivo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        // Crear un archivo temporal en memoria
        $output = fopen('php://output', 'w');

        // Escribir la fila de encabezados
        $columnas = array();
        while ($fieldinfo = $result->fetch_field()) {
            $columnas[] = $fieldinfo->name;
        }
        fputcsv($output, $columnas);

        // Escribir los datos de la tabla
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    } else {
        echo "No hay datos para exportar.";
    }
} else {
    echo "Acceso no permitido.";
}
?>