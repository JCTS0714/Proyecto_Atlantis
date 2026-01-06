<?php
// Archivo: exportar_imagenes_certificados.php
// Exporta en un ZIP todas las imágenes asociadas a certificados.

declare(strict_types=1);

function img_export_fail(int $status, string $message, ?string $logMessage = null): void {
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

// Auth: requiere sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id'])) {
    img_export_fail(401, 'Usuario no autenticado.');
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    img_export_fail(405, 'Método no permitido.');
}

// Dependencias
$conexionPath = __DIR__ . '/modelos/conexion.php';
if (!file_exists($conexionPath)) {
    img_export_fail(500, 'Error interno: no se encontró el archivo de conexión.', 'exportar_imagenes_certificados.php: no existe ' . $conexionPath);
}
require_once $conexionPath;

$bootstrapPath = __DIR__ . '/multitenant/bootstrap.php';
if (file_exists($bootstrapPath)) {
    require_once $bootstrapPath;
}

if (!class_exists('ZipArchive')) {
    img_export_fail(500, 'Error interno: ZipArchive no está disponible en este servidor.');
}

try {
    $pdo = Conexion::conectar();
} catch (Throwable $e) {
    img_export_fail(500, 'Error interno: no se pudo conectar a la base de datos.', 'exportar_imagenes_certificados.php: conectar() ' . $e->getMessage());
}

// Obtener certificados con imagen y tratar de relacionar con cliente.
// En algunos tenants la tabla/columnas de clientes pueden variar; si el JOIN falla, hacemos fallback.
$sqlJoin = "SELECT c.id AS certificado_id, c.ruc, c.imagen, cl.id AS cliente_id, cl.empresa, cl.nombre AS cliente_nombre
            FROM certificados c
            LEFT JOIN clientes cl ON cl.documento = c.ruc
            WHERE c.imagen IS NOT NULL AND c.imagen <> ''
            ORDER BY c.id ASC";

$sqlFallback = "SELECT id AS certificado_id, ruc, imagen
                FROM certificados
                WHERE imagen IS NOT NULL AND imagen <> ''
                ORDER BY id ASC";

try {
    $stmt = $pdo->query($sqlJoin);
    if ($stmt === false) {
        $err = $pdo->errorInfo();
        error_log('exportar_imagenes_certificados.php: JOIN query() devolvió false | err=' . json_encode($err));
        $stmt = $pdo->query($sqlFallback);
        if ($stmt === false) {
            $err2 = $pdo->errorInfo();
            img_export_fail(500, 'Error en la consulta a la base de datos.', 'exportar_imagenes_certificados.php: fallback query() false | err=' . json_encode($err2));
        }
    }
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log('exportar_imagenes_certificados.php: JOIN exception: ' . $e->getMessage());
    try {
        $stmt2 = $pdo->query($sqlFallback);
        if ($stmt2 === false) {
            $err2 = $pdo->errorInfo();
            img_export_fail(500, 'Error en la consulta a la base de datos.', 'exportar_imagenes_certificados.php: fallback exception then false | err=' . json_encode($err2));
        }
        $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e2) {
        img_export_fail(500, 'Error en la consulta a la base de datos.', 'exportar_imagenes_certificados.php: fallback exception: ' . $e2->getMessage());
    }
}

if (empty($rows)) {
    img_export_fail(200, 'No hay imágenes para exportar.');
}

$uploadDir = __DIR__ . '/uploads/certificados/';
if (!is_dir($uploadDir)) {
    img_export_fail(200, 'No hay imágenes para exportar (carpeta de uploads no existe).');
}

$zipName = 'imagenes_certificados_' . date('Ymd_His') . '.zip';
$tmpZip = tempnam(sys_get_temp_dir(), 'certimg_');
if ($tmpZip === false) {
    img_export_fail(500, 'Error interno: no se pudo crear archivo temporal.');
}

$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::OVERWRITE) !== true) {
    @unlink($tmpZip);
    img_export_fail(500, 'Error interno: no se pudo crear el ZIP.');
}

$added = 0;

$sanitize = static function(string $s): string {
    $s = trim($s);
    if ($s === '') return '';
    // Reemplazar caracteres problemáticos para nombres de archivo
    $s = preg_replace('/[^a-zA-Z0-9._-]+/u', '_', $s);
    $s = preg_replace('/_+/', '_', $s);
    return trim($s, '_');
};

foreach ($rows as $r) {
    $certId = isset($r['certificado_id']) ? (int)$r['certificado_id'] : 0;
    $clienteId = isset($r['cliente_id']) && $r['cliente_id'] !== null ? (int)$r['cliente_id'] : 0;
    $ruc = isset($r['ruc']) ? (string)$r['ruc'] : '';

    $imagen = isset($r['imagen']) ? basename((string)$r['imagen']) : '';
    if ($imagen === '') {
        continue;
    }

    // Evitar path traversal (basename ya recorta) + validar existencia
    $fullPath = $uploadDir . $imagen;
    if (!is_file($fullPath)) {
        continue;
    }

    $folder = '';
    if ($clienteId > 0) {
        $folder = 'cliente_' . $clienteId . '/';
    } else {
        $folder = 'sin_cliente/';
    }

    $empresa = $sanitize((string)($r['empresa'] ?? ''));
    $clienteNombre = $sanitize((string)($r['cliente_nombre'] ?? ''));
    $rucSan = $sanitize($ruc);

    $parts = [];
    $parts[] = 'cert_' . $certId;
    if ($clienteId > 0) {
        // Si hay cliente, incluir empresa/nombre si está disponible
        if ($empresa !== '') $parts[] = $empresa;
        elseif ($clienteNombre !== '') $parts[] = $clienteNombre;
    } else {
        if ($rucSan !== '') $parts[] = 'ruc_' . $rucSan;
    }

    $fileNameInZip = implode('_', $parts) . '_' . $sanitize($imagen);
    if ($fileNameInZip === '') {
        $fileNameInZip = 'cert_' . $certId . '_' . $sanitize($imagen);
    }

    $zip->addFile($fullPath, $folder . $fileNameInZip);
    $added++;
}

$zip->close();

if ($added === 0) {
    @unlink($tmpZip);
    img_export_fail(200, 'No se encontraron archivos de imagen válidos para exportar.');
}

// Descargar ZIP
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . (string)filesize($tmpZip));

$fp = fopen($tmpZip, 'rb');
if ($fp === false) {
    @unlink($tmpZip);
    img_export_fail(500, 'Error interno: no se pudo leer el ZIP generado.');
}

while (!feof($fp)) {
    $buf = fread($fp, 8192);
    if ($buf === false) break;
    echo $buf;
}

fclose($fp);
@unlink($tmpZip);
exit;
