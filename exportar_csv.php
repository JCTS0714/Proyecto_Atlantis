<?php
// Archivo: exportar_csv.php

// Conexión a la base de datos
include 'modelos/conexion.php'; // Asegúrate de que la ruta sea correcta

// Verificar si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar que se haya enviado el nombre de la tabla
    $tabla = 'prospectos';

    // Consulta para obtener los datos de la tabla
    $query = "SELECT * FROM $tabla";
    $result = $conn->query($query);

    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        // Nombre del archivo CSV
        $filename = "exportacion_" . $tabla . "_" . date('Ymd') . ".csv";

        // Encabezados para forzar la descarga del archivo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        // Crear un archivo temporal en memoria
        <?php
        // Archivo: exportar_csv.php
        // Versión robusta: usa PDO (Conexion::conectar()), whitelist de tablas y manejo de errores.

        // Evitar cualquier output antes de headers
        try {
            require_once __DIR__ . '/modelos/conexion.php';
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error interno: no se pudo cargar la configuración de conexión.";
            error_log("exportar_csv.php: fallo al incluir conexion.php: " . $e->getMessage());
            exit;
        }

        // Lista blanca de tablas permitidas (ajusta según tus tablas reales)
        $allowedTables = [
            'prospectos' => 'prospectos',
            'clientes' => 'clientes',
            'certificados' => 'certificados',
            'contadores' => 'contadores',
            'ventas' => 'ventas',
            'incidencias' => 'incidencias',
            'reuniones_pasadas' => 'reuniones_pasadas'
        ];

        try {
            $pdo = Conexion::conectar();
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error interno: no se pudo conectar a la base de datos.";
            error_log("exportar_csv.php: fallo al conectar a DB: " . $e->getMessage());
            exit;
        }

        // Solo aceptar POST desde los botones de la UI
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Método no permitido.";
            exit;
        }

        // Obtener tabla enviada por el formulario
        $tablaParam = isset($_POST['tabla']) ? trim($_POST['tabla']) : '';
        if ($tablaParam === '') {
            http_response_code(400);
            echo "Error: no se especificó la tabla a exportar.";
            exit;
        }

        // Validar contra whitelist
        if (!array_key_exists($tablaParam, $allowedTables)) {
            http_response_code(400);
            echo "Error: tabla no permitida para exportación.";
            exit;
        }

        $tableName = $allowedTables[$tablaParam];

        // Preparar y ejecutar la consulta de forma segura (no se pueden enlazar identificadores, por eso la whitelist)
        $sql = "SELECT * FROM `" . str_replace('`','', $tableName) . "`";
        try {
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            http_response_code(500);
            echo "Error en la consulta a la base de datos.";
            error_log("exportar_csv.php: error en consulta ($sql): " . $e->getMessage());
            exit;
        }

        if (empty($rows)) {
            // No hay datos: informar al usuario
            echo "No hay datos para exportar.";
            exit;
        }

        // Enviar headers CSV (sin output previo)
        $filename = "exportacion_" . $tableName . "_" . date('Ymd_His') . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Abrir salida y escribir CSV
        $out = fopen('php://output', 'w');
        // Encabezados de columnas (usar keys del primer registro)
        $headers = array_keys($rows[0]);
        // Opcional: convertir encoding si necesitas ISO-8859-1 en Excel antiguo
        // fputcsv($out, $headers);
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            // Asegurarse de mantener el orden de columnas
            $line = [];
            foreach ($headers as $h) {
                $line[] = isset($row[$h]) ? $row[$h] : '';
            }
            fputcsv($out, $line);
        }

        fclose($out);
        exit;