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

try{
    $db = Conexion::conectar();
}catch(Exception $e){
    echo json_encode(['success'=>false,'error'=>'DB connection error']);
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

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
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) { error_log('certificados.crear: move_uploaded_file failed'); echo json_encode(['success'=>false,'error'=>'upload_failed']); exit; }
        }

        $db->beginTransaction();
        $sql = "INSERT INTO certificados (nombre, ruc, usuario, clave, fecha_creacion, fecha_vencimiento, estado, observacion, tipo, imagen, creado_por, creado_en) VALUES (:nombre,:ruc,:usuario,:clave,:fecha_creacion,:fecha_vencimiento,:estado,:observacion,:tipo,:imagen,:creado_por,NOW())";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute([
            ':nombre'=>$nombre, ':ruc'=>$ruc, ':usuario'=>$usuario, ':clave'=>$clave,
            ':fecha_creacion'=>$fecha_creacion, ':fecha_vencimiento'=>$fecha_vencimiento, ':estado'=>$estado, ':observacion'=>$observacion,
            ':tipo'=>$tipo, ':imagen'=>$imagenFilename, ':creado_por'=>$_SESSION['id']
        ]);
        if ($ok) {
            $db->commit();
            echo json_encode(['success'=>true,'duplicate'=>false]);
        } else {
            $db->rollBack();
            error_log('certificados.crear INSERT failed: ' . json_encode($stmt->errorInfo()));
            echo json_encode(['success'=>false,'error'=>'insert_failed']);
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
        $sql = "SELECT id,nombre,ruc,usuario,clave,fecha_creacion,fecha_vencimiento,estado,observacion,tipo FROM certificados ORDER BY id DESC";
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
