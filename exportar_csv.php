<?php
// Archivo: exportar_csv.php
// Exportador CSV temporal (robusto): PDO, whitelist y manejo de errores.

declare(strict_types=1);

function csv_fail(int $status, string $message, ?string $logMessage = null): void {
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo $message;
    if ($logMessage) {
        error_log($logMessage);
    }
    exit;
}

$conexionPath = __DIR__ . '/modelos/conexion.php';
if (!file_exists($conexionPath)) {
    csv_fail(500, 'Error interno: no se encontró el archivo de conexión.', 'exportar_csv.php: no existe ' . $conexionPath);
}
require_once $conexionPath;

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    csv_fail(405, 'Método no permitido.');
}

$tablaParam = isset($_POST['tabla']) ? trim((string)$_POST['tabla']) : '';
if ($tablaParam === '') {
    csv_fail(400, 'Error: no se especificó la tabla a exportar.');
}

// Lista blanca (evita inyección SQL por identificadores)
$allowedTables = [
    'prospectos' => 'prospectos',
    'clientes' => 'clientes',
    'certificados' => 'certificados',
    'contadores' => 'contadores',
    'ventas' => 'ventas',
    'incidencias' => 'incidencias',
    'reuniones_pasadas' => 'reuniones_pasadas',
];

if (!isset($allowedTables[$tablaParam])) {
    csv_fail(400, 'Error: tabla no permitida para exportación.');
}

$tableName = $allowedTables[$tablaParam];
$safeTableName = str_replace('`', '', $tableName);

try {
    $pdo = Conexion::conectar();
} catch (Throwable $e) {
    csv_fail(500, 'Error interno: no se pudo conectar a la base de datos.', 'exportar_csv.php: conectar() ' . $e->getMessage());
}

$sql = "SELECT * FROM `{$safeTableName}`";
try {
    $stmt = $pdo->query($sql);
    if ($stmt === false) {
        csv_fail(500, 'Error en la consulta a la base de datos.', 'exportar_csv.php: query() devolvió false | SQL=' . $sql);
    }
} catch (Throwable $e) {
    csv_fail(500, 'Error en la consulta a la base de datos.', 'exportar_csv.php: query() ' . $e->getMessage() . ' | SQL=' . $sql);
}

$firstRow = $stmt->fetch(PDO::FETCH_ASSOC);
if ($firstRow === false) {
    csv_fail(200, 'No hay datos para exportar.');
}

// Preparar descarga
$filename = 'exportacion_' . $safeTableName . '_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$out = fopen('php://output', 'w');
if ($out === false) {
    csv_fail(500, 'Error interno: no se pudo escribir el CSV.', 'exportar_csv.php: fopen(php://output) falló');
}

// BOM para Excel (UTF-8)
fwrite($out, "\xEF\xBB\xBF");

$headers = array_keys($firstRow);

// Excluir columna de imagen para certificados
if ($safeTableName === 'certificados') {
    $headers = array_values(array_filter($headers, static fn($h) => $h !== 'imagen'));
}
fputcsv($out, $headers);
fputcsv($out, array_map(static fn($h) => $firstRow[$h] ?? '', $headers));

while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
    $line = [];
    foreach ($headers as $h) {
        $line[] = $row[$h] ?? '';
    }
    fputcsv($out, $line);
}

fclose($out);
exit;