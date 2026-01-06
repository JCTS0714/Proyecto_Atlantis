<?php
require_once __DIR__ . '/_error_handler.php';
header('Content-Type: application/json');

// Simple auth: require session user
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuario no autenticado']);
    exit;
}

require_once __DIR__ . '/../modelos/conexion.php';
// Ensure multi-tenant bootstrap runs before models/connections
require_once __DIR__ . '/../multitenant/bootstrap.php';

try{
    $db = Conexion::conectar();
}catch(Exception $e){
    echo json_encode(['success'=>false,'error'=>'DB connection error']);
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'importar_csv'){
    // Import CSV into certificados, excluding imagen column
    if (empty($_FILES['archivo_csv']) || !isset($_FILES['archivo_csv']['tmp_name'])) {
        echo json_encode(['success'=>false,'error'=>'archivo_no_encontrado']);
        exit;
    }

    $f = $_FILES['archivo_csv'];
    if (!empty($f['error'])) {
        echo json_encode(['success'=>false,'error'=>'upload_error_' . intval($f['error'])]);
        exit;
    }
    if (!is_uploaded_file($f['tmp_name'])) {
        echo json_encode(['success'=>false,'error'=>'archivo_invalido']);
        exit;
    }

    $name = (string)($f['name'] ?? '');
    if ($name !== '' && !preg_match('/\.csv$/i', $name)) {
        echo json_encode(['success'=>false,'error'=>'solo_csv']);
        exit;
    }

    $fh = @fopen($f['tmp_name'], 'rb');
    if ($fh === false) {
        echo json_encode(['success'=>false,'error'=>'no_se_pudo_leer']);
        exit;
    }

    // Detect delimiter (comma vs semicolon) using first line
    $firstLine = fgets($fh);
    if ($firstLine === false) {
        fclose($fh);
        echo json_encode(['success'=>false,'error'=>'csv_vacio']);
        exit;
    }
    $commaCount = substr_count($firstLine, ',');
    $semiCount = substr_count($firstLine, ';');
    $delimiter = ($semiCount > $commaCount) ? ';' : ',';
    rewind($fh);

    $normalizeHeader = static function(string $h): string {
        // Remove UTF-8 BOM and normalize spacing
        $h = preg_replace('/^\xEF\xBB\xBF/', '', $h);
        $h = trim($h);
        $h = mb_strtolower($h, 'UTF-8');
        return $h;
    };

    $parseDate = static function($v): ?string {
        $v = trim((string)$v);
        if ($v === '') return null;
        // Accept YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) return $v;
        // Accept DD/MM/YYYY
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $v, $m)) {
            return $m[3] . '-' . $m[2] . '-' . $m[1];
        }
        // Accept YYYY-MM-DD HH:MM:SS
        if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+\d{2}:\d{2}:\d{2}$/', $v, $m)) {
            return $m[1];
        }
        return null;
    };

    $header = fgetcsv($fh, 0, $delimiter);
    if (!is_array($header) || count($header) === 0) {
        fclose($fh);
        echo json_encode(['success'=>false,'error'=>'header_invalido']);
        exit;
    }
    $header = array_map(static fn($h) => (string)$h, $header);
    $normHeader = array_map($normalizeHeader, $header);
    $headerIndex = [];
    foreach ($normHeader as $idx => $col) {
        if ($col === '') continue;
        // Keep first occurrence
        if (!isset($headerIndex[$col])) $headerIndex[$col] = $idx;
    }

    // Allowed input columns for certificados. Explicitly ignore imagen.
    $allowedCols = ['id','nombre','ruc','usuario','clave','fecha_creacion','fecha_vencimiento','estado','observacion','tipo'];

    $imported = 0;
    $updated = 0;
    $skipped = 0;
    $errors = [];
    $rowNum = 1; // header is row 1

    // Pre-prepare statements
    $stmtExists = $db->prepare('SELECT COUNT(*) FROM certificados WHERE id = :id');
    $stmtInsert = $db->prepare('INSERT INTO certificados (nombre, ruc, usuario, clave, fecha_creacion, fecha_vencimiento, estado, observacion, tipo, creado_por, creado_en) VALUES (:nombre,:ruc,:usuario,:clave,:fecha_creacion,:fecha_vencimiento,:estado,:observacion,:tipo,:creado_por,NOW())');

    while (($row = fgetcsv($fh, 0, $delimiter)) !== false) {
        $rowNum++;
        if (!is_array($row)) { $skipped++; continue; }

        // Skip completely empty rows
        $nonEmpty = false;
        foreach ($row as $v) { if (trim((string)$v) !== '') { $nonEmpty = true; break; } }
        if (!$nonEmpty) { $skipped++; continue; }

        $data = [];
        foreach ($allowedCols as $c) {
            if (isset($headerIndex[$c])) {
                $data[$c] = $row[$headerIndex[$c]] ?? '';
            }
        }

        $id = isset($data['id']) ? intval($data['id']) : 0;
        $nombre = trim((string)($data['nombre'] ?? ''));
        $fechaV = $parseDate($data['fecha_vencimiento'] ?? '');
        $fechaC = $parseDate($data['fecha_creacion'] ?? '') ?? date('Y-m-d');

        if ($nombre === '' || $fechaV === null) {
            $skipped++;
            if (count($errors) < 20) {
                $errors[] = 'Fila ' . $rowNum . ': faltan campos requeridos (nombre/fecha_vencimiento)';
            }
            continue;
        }

        $estado = trim((string)($data['estado'] ?? 'activo'));
        if ($estado === '') $estado = 'activo';

        $tipo = trim((string)($data['tipo'] ?? 'OSE'));
        if ($tipo !== 'OSE' && $tipo !== 'PSE') $tipo = 'OSE';

        $payload = [
            ':nombre' => $nombre,
            ':ruc' => (string)($data['ruc'] ?? ''),
            ':usuario' => (string)($data['usuario'] ?? ''),
            ':clave' => (string)($data['clave'] ?? ''),
            ':fecha_creacion' => $fechaC,
            ':fecha_vencimiento' => $fechaV,
            ':estado' => $estado,
            ':observacion' => (string)($data['observacion'] ?? ''),
            ':tipo' => $tipo,
            ':creado_por' => intval($_SESSION['id'])
        ];

        try {
            if ($id > 0) {
                $stmtExists->execute([':id' => $id]);
                $exists = intval($stmtExists->fetchColumn()) > 0;
                if ($exists) {
                    // Update only the non-image business columns
                    $stmtUpdate = $db->prepare('UPDATE certificados SET nombre=:nombre, ruc=:ruc, usuario=:usuario, clave=:clave, fecha_creacion=:fecha_creacion, fecha_vencimiento=:fecha_vencimiento, estado=:estado, observacion=:observacion, tipo=:tipo WHERE id = :id');
                    $payloadUpdate = $payload;
                    $payloadUpdate[':id'] = $id;
                    $stmtUpdate->execute($payloadUpdate);
                    $updated++;
                    continue;
                }
            }

            $stmtInsert->execute($payload);
            $imported++;
        } catch (Exception $e) {
            $skipped++;
            if (count($errors) < 20) {
                $errors[] = 'Fila ' . $rowNum . ': error DB';
            }
        }
    }

    fclose($fh);
    echo json_encode([
        'success' => true,
        'imported' => $imported,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors
    ]);
    exit;
}

if ($accion === 'crear'){
    // Required fields: nombre, fecha_vencimiento
    $nombre = $_POST['nombre'] ?? '';
    $ruc = $_POST['ruc'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $clave = $_POST['clave'] ?? '';
    $fecha_creacion = $_POST['fecha_creacion'] ?: date('Y-m-d');
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
    $estado = $_POST['estado'] ?? 'activo';
    $observacion = $_POST['observacion'] ?? '';
    $tipo = $_POST['tipo'] ?? 'OSE';

    if (empty($nombre) || empty($fecha_vencimiento)){
        echo json_encode(['success'=>false,'error'=>'Faltan campos requeridos']); exit;
    }

    try{
        error_log('certificados.crear: START');
        // Additional debug file inside workspace for environments where PHP log is not accessible
        $dbgFile = __DIR__ . '/../logs/certificados_debug.log';
        if (!is_dir(dirname($dbgFile))) @mkdir(dirname($dbgFile), 0755, true);
        @file_put_contents($dbgFile, date('[Y-m-d H:i:s] ') . "START crear\n", FILE_APPEND);
        @file_put_contents($dbgFile, "POST keys: " . json_encode(array_keys($_POST)) . "\n", FILE_APPEND);
        $filesInfo = [];
        foreach($_FILES as $k=>$v){ $filesInfo[$k] = ['name'=>$v['name'] ?? null, 'size'=>$v['size'] ?? null, 'tmp'=>$v['tmp_name'] ?? null, 'error'=>$v['error'] ?? null]; }
        @file_put_contents($dbgFile, "FILES: " . json_encode($filesInfo) . "\n", FILE_APPEND);
        // Prevención de duplicados: si ya existe un certificado con mismo nombre + fecha_vencimiento
        // creado por el mismo usuario en los últimos 60 segundos, considerarlo duplicado y no insertar.
        $check = $db->prepare("SELECT COUNT(*) FROM certificados WHERE nombre = :nombre AND fecha_vencimiento = :fecha_vencimiento AND creado_por = :creado_por AND creado_en >= (NOW() - INTERVAL 60 SECOND)");
        $check->execute([':nombre'=>$nombre, ':fecha_vencimiento'=>$fecha_vencimiento, ':creado_por'=>$_SESSION['id']]);
        $cnt = intval($check->fetchColumn());
        if ($cnt > 0){
            // Ya se creó un registro similar muy recientemente: responder éxito pero con nota de duplicado.
            echo json_encode(['success'=>true,'duplicate'=>true,'message'=>'registro_duplicado_reciente']);
            exit;
        }

        // Handle optional file upload
        $imagenFilename = null;
            $lastUploadedPath = null;
        if (!empty($_FILES['imagen']) && isset($_FILES['imagen']['tmp_name']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            error_log('certificados.crear: file upload detected, size=' . intval($_FILES['imagen']['size']));
            $uploadDir = __DIR__ . '/../uploads/certificados/';
            if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
            $allowed = ['image/jpeg','image/png','image/gif'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowed)) { echo json_encode(['success'=>false,'error'=>'tipo_archivo_no_permitido']); exit; }
            if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) { echo json_encode(['success'=>false,'error'=>'archivo_demasiado_grande']); exit; }
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagenFilename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $dest = $uploadDir . $imagenFilename;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { error_log('certificados.crear: move_uploaded_file failed dest=' . $dest); @file_put_contents($dbgFile, "move_uploaded_file FAILED dest=$dest\n", FILE_APPEND); echo json_encode(['success'=>false,'error'=>'upload_failed']); exit; }
                $lastUploadedPath = $dest;
            error_log('certificados.crear: moved uploaded file to ' . $dest . ' as ' . $imagenFilename);
            @file_put_contents($dbgFile, "moved file to $dest as $imagenFilename\n", FILE_APPEND);
        }

        $db->beginTransaction();
        if ($imagenFilename !== null) {
            $sql = "INSERT INTO certificados (nombre, ruc, usuario, clave, fecha_creacion, fecha_vencimiento, estado, observacion, tipo, imagen, creado_por, creado_en) VALUES (:nombre,:ruc,:usuario,:clave,:fecha_creacion,:fecha_vencimiento,:estado,:observacion,:tipo,:imagen,:creado_por,NOW())";
            $stmt = $db->prepare($sql);
            error_log('certificados.crear: executing INSERT WITH imagen=' . $imagenFilename);
            @file_put_contents($dbgFile, "EXEC INSERT WITH imagen=$imagenFilename\n", FILE_APPEND);
            $ok = $stmt->execute([
                ':nombre'=>$nombre, ':ruc'=>$ruc, ':usuario'=>$usuario, ':clave'=>$clave,
                ':fecha_creacion'=>$fecha_creacion, ':fecha_vencimiento'=>$fecha_vencimiento, ':estado'=>$estado, ':observacion'=>$observacion,
                ':tipo'=>$tipo, ':imagen'=>$imagenFilename, ':creado_por'=>$_SESSION['id']
            ]);
        } else {
            $sql = "INSERT INTO certificados (nombre, ruc, usuario, clave, fecha_creacion, fecha_vencimiento, estado, observacion, tipo, creado_por, creado_en) VALUES (:nombre,:ruc,:usuario,:clave,:fecha_creacion,:fecha_vencimiento,:estado,:observacion,:tipo,:creado_por,NOW())";
            $stmt = $db->prepare($sql);
            error_log('certificados.crear: executing INSERT WITHOUT imagen');
            @file_put_contents($dbgFile, "EXEC INSERT WITHOUT imagen\n", FILE_APPEND);
            $ok = $stmt->execute([
                ':nombre'=>$nombre, ':ruc'=>$ruc, ':usuario'=>$usuario, ':clave'=>$clave,
                ':fecha_creacion'=>$fecha_creacion, ':fecha_vencimiento'=>$fecha_vencimiento, ':estado'=>$estado, ':observacion'=>$observacion,
                ':tipo'=>$tipo, ':creado_por'=>$_SESSION['id']
            ]);
        }
        if ($ok) {
            $db->commit();
            error_log('certificados.crear: INSERT ok, created by ' . $_SESSION['id']);
            @file_put_contents($dbgFile, "INSERT OK imagen=" . ($imagenFilename ?? 'NULL') . "\n\n", FILE_APPEND);
            echo json_encode(['success'=>true,'duplicate'=>false,'imagen'=> $imagenFilename]);
        } else {
            $db->rollBack();
            error_log('certificados.crear INSERT failed: ' . json_encode($stmt->errorInfo()));
            @file_put_contents($dbgFile, "INSERT FAILED: " . json_encode($stmt->errorInfo()) . "\n\n", FILE_APPEND);
                // if we uploaded a file but DB insert failed, remove the uploaded file to avoid orphan files
                if (!empty($lastUploadedPath) && file_exists($lastUploadedPath)){
                    @unlink($lastUploadedPath);
                    @file_put_contents($dbgFile, "REMOVED_UPLOADED_FILE: $lastUploadedPath\n\n", FILE_APPEND);
                }
            echo json_encode(['success'=>false,'error'=>'insert_failed','info'=>$stmt->errorInfo()]);
        }
    } catch (Exception $e){
        try{ if($db->inTransaction()) $db->rollBack(); } catch(Exception $x){}
        error_log('certificados.crear EXCEPTION: '.$e->getMessage());
        echo json_encode(['success'=>false,'error'=>'exception']);
    }
    exit;
}

if ($accion === 'mostrar'){
    // Return all certificados
    try{
        $sql = "SELECT id,nombre,ruc,usuario,clave,fecha_creacion,fecha_vencimiento,estado,observacion,tipo,imagen FROM certificados ORDER BY id DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success'=>true,'eventos'=>$rows]);
    }catch(Exception $e){
        echo json_encode(['success'=>false,'error'=>'query_failed']);
    }
    exit;
}

if ($accion === 'eliminar'){
    $id = intval($_POST['id'] ?? 0);
    if ($id<=0){ echo json_encode(['success'=>false,'error'=>'id_invalid']); exit; }
    try{
        $stmt = $db->prepare('DELETE FROM certificados WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        echo json_encode(['success'=>true]);
    }catch(Exception $e){ echo json_encode(['success'=>false,'error'=>'delete_failed']); }
    exit;
}

if ($accion === 'actualizar'){
    $id = intval($_POST['id'] ?? 0);
    if ($id<=0){ echo json_encode(['success'=>false,'error'=>'id_invalid']); exit; }
    $fields = ['nombre','ruc','usuario','clave','fecha_creacion','fecha_vencimiento','estado','observacion','tipo'];
    $sets = [];
    $params = [':id'=>$id];
    foreach($fields as $f){ if (isset($_POST[$f])){ $sets[] = "{$f} = :{$f}"; $params[":{$f}"] = $_POST[$f]; } }
    // Handle optional uploaded image for update
    if (!empty($_FILES['imagen']) && isset($_FILES['imagen']['tmp_name']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
        $uploadDir = __DIR__ . '/../uploads/certificados/';
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
        $allowed = ['image/jpeg','image/png','image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['imagen']['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, $allowed)) { echo json_encode(['success'=>false,'error'=>'tipo_archivo_no_permitido']); exit; }
        if ($_FILES['imagen']['size'] > 5 * 1024 * 1024) { echo json_encode(['success'=>false,'error'=>'archivo_demasiado_grande']); exit; }
        $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $imagenFilename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = $uploadDir . $imagenFilename;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { error_log('certificados.actualizar: move_uploaded_file failed'); echo json_encode(['success'=>false,'error'=>'upload_failed']); exit; }
        $sets[] = "imagen = :imagen";
        $params[':imagen'] = $imagenFilename;
    }
    if (empty($sets)){ echo json_encode(['success'=>false,'error'=>'no_fields']); exit; }
    $sql = 'UPDATE certificados SET ' . implode(',', $sets) . ' WHERE id = :id';
    try{ $stmt = $db->prepare($sql); $stmt->execute($params); echo json_encode(['success'=>true]); } catch(Exception $e){ echo json_encode(['success'=>false,'error'=>'update_failed']); }
    exit;
}

if ($accion === 'obtener_para_notificar'){
    // Devuelve certificados cuya fecha_vencimiento está a 7 o 3 días y limita a 2 envíos por día por certificado
    try{
        $sql = "SELECT id,nombre,ruc,usuario,clave,fecha_creacion,fecha_vencimiento,estado,observacion,tipo, DATEDIFF(fecha_vencimiento, CURDATE()) as dias_restantes FROM certificados WHERE estado = 'activo' AND DATEDIFF(fecha_vencimiento, CURDATE()) IN (7,3) ORDER BY fecha_vencimiento ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $out = [];
        foreach($rows as $r){
            $id = intval($r['id']);
            $tipo = intval($r['dias_restantes']); // 7 o 3
            // Contar envíos hoy
            $cstmt = $db->prepare("SELECT COUNT(*) as total FROM notificaciones_certificados WHERE certificado_id = :cid AND DATE(fecha_envio) = CURDATE()");
            $cstmt->execute([':cid'=>$id]);
            $count = intval($cstmt->fetchColumn());
            if ($count < 2) {
                // Registrar envío (esto evita repetidos infinitos; se cuenta como un envío)
                $ist = $db->prepare("INSERT INTO notificaciones_certificados (certificado_id, tipo, fecha_envio, usuario_id) VALUES (:cid, :tipo, NOW(), :uid)");
                $ist->execute([':cid'=>$id, ':tipo'=>$tipo, ':uid'=>$_SESSION['id']]);
                // Preparar objeto de notificación
                $out[] = [ 'id'=>$r['id'], 'nombre'=>$r['nombre'], 'ruc'=>$r['ruc'], 'fecha_vencimiento'=>$r['fecha_vencimiento'], 'observacion'=>$r['observacion'], 'tipo'=>$tipo ];
            }
        }
        echo json_encode(['success'=>true,'eventos'=>$out]);
    }catch(Exception $e){
        error_log('certificados.obtener_para_notificar ERROR: '.$e->getMessage());
        echo json_encode(['success'=>false,'eventos'=>[]]);
    }
    exit;
}

if ($accion === 'marcar_notificaciones_vistas'){
    // Recibe ids: JSON array de ids de certificados y fecha
    $idsJson = $_POST['ids'] ?? '[]';
    $fecha = $_POST['fecha'] ?? date('Y-m-d H:i:s');
    $ids = json_decode($idsJson, true);
    if (!is_array($ids)) $ids = [];
    try{
        $stmt = $db->prepare('UPDATE certificados SET ultima_notificacion = :fecha WHERE id = :id');
        foreach($ids as $id){
            $stmt->execute([':fecha'=>$fecha, ':id'=>intval($id)]);
        }
        echo json_encode(['success'=>true]);
    }catch(Exception $e){
        error_log('certificados.marcar_notificaciones_vistas ERROR: '.$e->getMessage());
        echo json_encode(['success'=>false]);
    }
    exit;
}

// Default
echo json_encode(['success'=>false,'error'=>'accion_no_valida']);

?>
