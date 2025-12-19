<?php
require_once __DIR__ . '/../modelos/conexion.php';

// Panel error handling: avoid blank screens in production.
// Logs to /logs/panel_errors.log (relative to project root).
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/panel_errors.log');

if (!defined('PANEL_ERROR_HANDLER_INSTALLED')) {
    define('PANEL_ERROR_HANDLER_INSTALLED', true);

    set_exception_handler(function($e) {
        error_log("Uncaught exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=utf-8');
        }
        echo "Panel error. Revisa logs/panel_errors.log";
        exit;
    });

    set_error_handler(function($severity, $message, $file, $line) {
        // No tumbar el panel por warnings/notices (muy comunes en hosting compartido).
        $ignored = [E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED, E_STRICT, E_WARNING, E_USER_WARNING];
        error_log("PHP error [$severity]: $message in $file:$line");
        if (in_array($severity, $ignored, true)) {
            return true;
        }
        // Errores más severos: convertir a excepción
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    register_shutdown_function(function() {
        $err = error_get_last();
        if ($err && in_array($err['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            error_log('Shutdown fatal: ' . print_r($err, true));
        }
    });
}

// Allow running as its own docroot (recommended on shared hosting):
// admin.grupoatlantiscrm.eu -> /public_html/panel
// In that case index.php isn't executed, so we start the session here.

function panel_env(string $key, string $default = ''): string {
    $v = getenv($key);
    if ($v !== false && $v !== '') return (string)$v;
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') return (string)$_ENV[$key];
    return $default;
}

$expectedHost = strtolower(panel_env('PANEL_HOST', 'admin.grupoatlantiscrm.eu'));
$currentHost = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
if ($currentHost !== '' && $expectedHost !== '' && $currentHost !== $expectedHost) {
    http_response_code(404);
    echo 'Not found';
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => $currentHost,
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    } else {
        session_set_cookie_params(0, '/', $currentHost, $secure, true);
    }
    session_start();
}

function panel_is_logged_in(): bool {
    return !empty($_SESSION['panel_admin_id']);
}

function panel_redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function panel_h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function panel_csrf_token(): string {
    if (empty($_SESSION['panel_csrf'])) {
        $_SESSION['panel_csrf'] = bin2hex(random_bytes(16));
    }
    return (string)$_SESSION['panel_csrf'];
}

function panel_csrf_check(): void {
    $sent = (string)($_POST['csrf'] ?? '');
    $real = (string)($_SESSION['panel_csrf'] ?? '');
    if ($sent === '' || $real === '' || !hash_equals($real, $sent)) {
        http_response_code(400);
        echo 'Bad request';
        exit;
    }
}

function panel_base_url(): string {
    // BASE_URL is defined by config/paths.php; for panel we can compute quickly too.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $proto = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
    return $proto . '://' . $host . $scriptDir;
}

$baseUrl = panel_base_url();

function panel_main_host(): string {
    $main = strtolower(trim(panel_env('MAIN_HOST', '')));
    if ($main !== '') return $main;
    // fallback simple: infer from PANEL_HOST (admin.xxx -> xxx)
    $panel = strtolower(trim(panel_env('PANEL_HOST', '')));
    if ($panel === '') return '';
    $parts = explode('.', $panel);
    if (count($parts) < 3) return '';
    array_shift($parts);
    return implode('.', $parts);
}

function panel_audit(PDO $pdo, int $adminId, ?int $tenantId, string $action, array $details = []): void {
    $stmt = $pdo->prepare('INSERT INTO audit_log (admin_user_id, tenant_id, action, details, ip) VALUES (:a,:t,:ac,:d,:ip)');
    $stmt->execute([
        ':a' => $adminId,
        ':t' => $tenantId,
        ':ac' => $action,
        ':d' => json_encode($details, JSON_UNESCAPED_UNICODE),
        ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
    ]);
}

function panel_test_tenant_db(array $dbRow): array {
    $host = (string)($dbRow['db_host'] ?? 'localhost');
    $name = (string)($dbRow['db_name'] ?? '');
    $user = (string)($dbRow['db_user'] ?? '');
    $pass = (string)($dbRow['db_pass'] ?? '');
    $charset = (string)($dbRow['db_charset'] ?? 'utf8mb4');

    if ($name === '' || $user === '') {
        return ['ok' => false, 'message' => 'Credenciales incompletas.'];
    }
    $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    $one = $pdo->query('SELECT 1 AS ok')->fetchColumn();
    $db = $pdo->query('SELECT DATABASE()')->fetchColumn();
    return ['ok' => ((int)$one === 1), 'message' => 'Conexión OK', 'db' => $db];
}

// Routing (panel only)
$p = $_GET['p'] ?? 'tenants';
$action = $_POST['action'] ?? null;

// Handle logout
if ($p === 'logout') {
    unset($_SESSION['panel_admin_id'], $_SESSION['panel_admin_username']);
    panel_redirect($baseUrl . '/');
}

// Handle login
if ($action === 'login') {
    panel_csrf_check();
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    try {
        $pdo = Conexion::conectar();
        $stmt = $pdo->prepare('SELECT id, username, password_hash, is_active FROM admin_users WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || (int)$admin['is_active'] !== 1 || !password_verify($password, $admin['password_hash'])) {
            $_SESSION['panel_flash_error'] = 'Credenciales inválidas.';
            panel_redirect($baseUrl . '/?p=login');
        }

        $_SESSION['panel_admin_id'] = (int)$admin['id'];
        $_SESSION['panel_admin_username'] = $admin['username'];

        // Update last login
        $pdo->prepare('UPDATE admin_users SET last_login_at = NOW() WHERE id = :id')->execute([':id' => (int)$admin['id']]);

        panel_redirect($baseUrl . '/');

    } catch (Exception $e) {
        error_log('Panel login error: ' . $e->getMessage());
        $_SESSION['panel_flash_error'] = 'Error interno.';
        panel_redirect($baseUrl . '/?p=login');
    }
}

// If not logged, show login
if (!panel_is_logged_in()) {
    $err = $_SESSION['panel_flash_error'] ?? null;
    unset($_SESSION['panel_flash_error']);

    echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    echo '<title>Panel Admin</title>';
    echo '<link rel="stylesheet" href="/bootstrap.min.css">';
    echo '</head><body style="padding:24px">';
    echo '<div class="container" style="max-width:520px">';
    echo '<h3>Panel Admin</h3>';
    if ($err) {
        echo '<div class="alert alert-danger">' . panel_h($err) . '</div>';
    }
    echo '<form method="post">';
    echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
    echo '<input type="hidden" name="action" value="login">';
    echo '<div class="form-group"><label>Usuario</label><input class="form-control" name="username" required></div>';
    echo '<div class="form-group"><label>Contraseña</label><input class="form-control" name="password" type="password" required></div>';
    echo '<button class="btn btn-primary" type="submit">Entrar</button>';
    echo '</form>';
    echo '<hr><p style="color:#666">Si es la primera vez, crea un usuario en <strong>admin_users</strong> en la BD master.</p>';
    echo '</div></body></html>';
    exit;
}

// Authenticated below
$adminId = (int)($_SESSION['panel_admin_id'] ?? 0);
$adminUser = (string)($_SESSION['panel_admin_username'] ?? '');

// Actions
if ($action === 'create_tenant') {
    panel_csrf_check();
    $ruc = trim((string)($_POST['ruc'] ?? ''));
    $subdomain = strtolower(trim((string)($_POST['subdomain'] ?? '')));
    $dbHost = trim((string)($_POST['db_host'] ?? 'localhost'));
    $dbName = trim((string)($_POST['db_name'] ?? ''));
    $dbUser = trim((string)($_POST['db_user'] ?? ''));
    $dbPass = (string)($_POST['db_pass'] ?? '');
    $dbCharset = trim((string)($_POST['db_charset'] ?? 'utf8mb4'));

    // Simple validation
    if ($ruc === '' || $subdomain === '' || $dbName === '' || $dbUser === '' || $dbPass === '') {
        $_SESSION['panel_flash_error'] = 'Completa todos los campos.';
        panel_redirect($baseUrl . '/?p=create');
    }
    if (!preg_match('/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain)) {
        $_SESSION['panel_flash_error'] = 'Subdominio inválido.';
        panel_redirect($baseUrl . '/?p=create');
    }
    $reserved = ['www', 'admin', 'panel', 'api'];
    if (in_array($subdomain, $reserved, true)) {
        $_SESSION['panel_flash_error'] = 'Subdominio reservado.';
        panel_redirect($baseUrl . '/?p=create');
    }

    try {
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO tenants (ruc, subdomain, status) VALUES (:ruc, :sub, 'active')");
        $stmt->execute([':ruc' => $ruc, ':sub' => $subdomain]);
        $tenantId = (int)$pdo->lastInsertId();

        $stmt2 = $pdo->prepare('INSERT INTO tenant_db (tenant_id, db_host, db_name, db_user, db_pass, db_charset) VALUES (:tid,:h,:n,:u,:p,:c)');
        $stmt2->execute([
            ':tid' => $tenantId,
            ':h' => $dbHost,
            ':n' => $dbName,
            ':u' => $dbUser,
            ':p' => $dbPass,
            ':c' => $dbCharset ?: 'utf8mb4',
        ]);

        panel_audit($pdo, $adminId, $tenantId, 'create_tenant', ['subdomain' => $subdomain, 'db_name' => $dbName]);

        $pdo->commit();
        $_SESSION['panel_flash_ok'] = 'Tenant creado y activado.';
        panel_redirect($baseUrl . '/');

    } catch (Exception $e) {
        try { Conexion::conectar()->rollBack(); } catch (Exception $ignored) {}
        error_log('Panel create tenant error: ' . $e->getMessage());
        $_SESSION['panel_flash_error'] = 'No se pudo crear el tenant (revisa RUC/subdominio duplicado y credenciales).';
        panel_redirect($baseUrl . '/?p=create');
    }
}

if ($action === 'toggle_status') {
    panel_csrf_check();
    $tenantId = (int)($_POST['tenant_id'] ?? 0);
    if ($tenantId <= 0) {
        $_SESSION['panel_flash_error'] = 'Tenant inválido.';
        panel_redirect($baseUrl . '/');
    }
    try {
        $pdo = Conexion::conectar();
        $row = $pdo->prepare('SELECT id, status, subdomain FROM tenants WHERE id = :id LIMIT 1');
        $row->execute([':id' => $tenantId]);
        $t = $row->fetch(PDO::FETCH_ASSOC);
        if (!$t) {
            $_SESSION['panel_flash_error'] = 'Tenant no encontrado.';
            panel_redirect($baseUrl . '/');
        }
        $current = (string)$t['status'];
        $next = ($current === 'active') ? 'suspended' : 'active';
        $pdo->prepare('UPDATE tenants SET status = :s, updated_at = NOW() WHERE id = :id')->execute([':s' => $next, ':id' => $tenantId]);
        panel_audit($pdo, $adminId, $tenantId, 'toggle_status', ['from' => $current, 'to' => $next, 'subdomain' => $t['subdomain']]);
        $_SESSION['panel_flash_ok'] = 'Estado actualizado: ' . $next;
        panel_redirect($baseUrl . '/');
    } catch (Exception $e) {
        error_log('Panel toggle status error: ' . $e->getMessage());
        $_SESSION['panel_flash_error'] = 'No se pudo actualizar el estado.';
        panel_redirect($baseUrl . '/');
    }
}

if ($action === 'update_tenant') {
    panel_csrf_check();
    $tenantId = (int)($_POST['tenant_id'] ?? 0);
    $ruc = trim((string)($_POST['ruc'] ?? ''));
    $dbHost = trim((string)($_POST['db_host'] ?? 'localhost'));
    $dbName = trim((string)($_POST['db_name'] ?? ''));
    $dbUser = trim((string)($_POST['db_user'] ?? ''));
    $dbPassNew = (string)($_POST['db_pass'] ?? '');
    $dbCharset = trim((string)($_POST['db_charset'] ?? 'utf8mb4'));

    if ($tenantId <= 0 || $ruc === '' || $dbName === '' || $dbUser === '' || $dbHost === '') {
        $_SESSION['panel_flash_error'] = 'Completa todos los campos requeridos.';
        panel_redirect($baseUrl . '/?p=edit&id=' . $tenantId);
    }

    try {
        $pdo = Conexion::conectar();
        $pdo->beginTransaction();

        $pdo->prepare('UPDATE tenants SET ruc = :ruc, updated_at = NOW() WHERE id = :id')->execute([':ruc' => $ruc, ':id' => $tenantId]);
        if ($dbPassNew !== '') {
            $pdo->prepare('UPDATE tenant_db SET db_host=:h, db_name=:n, db_user=:u, db_pass=:p, db_charset=:c, updated_at=NOW() WHERE tenant_id=:id')
                ->execute([':h' => $dbHost, ':n' => $dbName, ':u' => $dbUser, ':p' => $dbPassNew, ':c' => $dbCharset ?: 'utf8mb4', ':id' => $tenantId]);
        } else {
            $pdo->prepare('UPDATE tenant_db SET db_host=:h, db_name=:n, db_user=:u, db_charset=:c, updated_at=NOW() WHERE tenant_id=:id')
                ->execute([':h' => $dbHost, ':n' => $dbName, ':u' => $dbUser, ':c' => $dbCharset ?: 'utf8mb4', ':id' => $tenantId]);
        }

        panel_audit($pdo, $adminId, $tenantId, 'update_tenant', ['db_name' => $dbName, 'db_user' => $dbUser, 'db_host' => $dbHost, 'db_pass_changed' => ($dbPassNew !== '')]);

        $pdo->commit();
        $_SESSION['panel_flash_ok'] = 'Tenant actualizado.';
        panel_redirect($baseUrl . '/?p=edit&id=' . $tenantId);

    } catch (Exception $e) {
        try { Conexion::conectar()->rollBack(); } catch (Exception $ignored) {}
        error_log('Panel update tenant error: ' . $e->getMessage());
        $_SESSION['panel_flash_error'] = 'No se pudo actualizar el tenant.';
        panel_redirect($baseUrl . '/?p=edit&id=' . $tenantId);
    }
}

if ($action === 'test_db') {
    panel_csrf_check();
    $tenantId = (int)($_POST['tenant_id'] ?? 0);
    if ($tenantId <= 0) {
        $_SESSION['panel_flash_error'] = 'Tenant inválido.';
        panel_redirect($baseUrl . '/');
    }
    try {
        $pdo = Conexion::conectar();
        $stmt = $pdo->prepare('SELECT t.id, t.subdomain, d.db_host, d.db_name, d.db_user, d.db_pass, d.db_charset FROM tenants t JOIN tenant_db d ON d.tenant_id=t.id WHERE t.id=:id LIMIT 1');
        $stmt->execute([':id' => $tenantId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $_SESSION['panel_flash_error'] = 'Tenant no encontrado.';
            panel_redirect($baseUrl . '/');
        }
        $result = panel_test_tenant_db($row);
        panel_audit($pdo, $adminId, $tenantId, 'test_db', ['ok' => $result['ok'] ?? false, 'db' => $result['db'] ?? null]);
        $_SESSION['panel_flash_' . (($result['ok'] ?? false) ? 'ok' : 'error')] = ($result['ok'] ?? false) ? ('Conexión OK. DB=' . ($result['db'] ?? '')) : ('Fallo conexión: ' . ($result['message'] ?? ''));
        panel_redirect($baseUrl . '/?p=edit&id=' . $tenantId);
    } catch (Exception $e) {
        error_log('Panel test db error: ' . $e->getMessage());
        $_SESSION['panel_flash_error'] = 'Fallo conexión: ' . $e->getMessage();
        panel_redirect($baseUrl . '/?p=edit&id=' . $tenantId);
    }
}

// Views
$flashOk = $_SESSION['panel_flash_ok'] ?? null;
$flashErr = $_SESSION['panel_flash_error'] ?? null;
unset($_SESSION['panel_flash_ok'], $_SESSION['panel_flash_error']);

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
echo '<title>Panel Admin</title>';
echo '<link rel="stylesheet" href="/bootstrap.min.css">';
echo '<link rel="stylesheet" href="/AdminLTE.min.css">';
echo '<link rel="stylesheet" href="/_all-skins.min.css">';
echo '</head><body style="background:#f4f6f9">';

echo '<nav class="navbar navbar-default" style="border-radius:0">';
echo '<div class="container">';
echo '<div class="navbar-header"><a class="navbar-brand" href="' . panel_h($baseUrl) . '/">Panel Admin</a></div>';
echo '<ul class="nav navbar-nav">';
echo '<li' . (($p === 'tenants' || $p === '') ? ' class="active"' : '') . '><a href="?p=tenants">Tenants</a></li>';
echo '<li' . (($p === 'create') ? ' class="active"' : '') . '><a href="?p=create">Crear</a></li>';
echo '<li' . (($p === 'audit') ? ' class="active"' : '') . '><a href="?p=audit">Auditoría</a></li>';
echo '<li' . (($p === 'logs') ? ' class="active"' : '') . '><a href="?p=logs">Logs</a></li>';
echo '</ul>';
echo '<ul class="nav navbar-nav navbar-right">';
echo '<li><a href="#">' . panel_h($adminUser) . '</a></li>';
echo '<li><a href="?p=logout">Salir</a></li>';
echo '</ul>';
echo '</div></nav>';

echo '<div class="container">';

if ($flashOk) echo '<div class="alert alert-success">' . panel_h($flashOk) . '</div>';
if ($flashErr) echo '<div class="alert alert-danger">' . panel_h($flashErr) . '</div>';

if ($p === 'create') {
    echo '<h4>Crear tenant</h4>';
    echo '<form method="post">';
    echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
    echo '<input type="hidden" name="action" value="create_tenant">';
    echo '<div class="row">';
    echo '<div class="col-md-6"><div class="form-group"><label>RUC</label><input class="form-control" name="ruc" required></div></div>';
    echo '<div class="col-md-6"><div class="form-group"><label>Subdominio</label><input class="form-control" name="subdomain" placeholder="cliente1" required></div></div>';
    echo '</div>';
    echo '<h5>Credenciales BD del tenant</h5>';
    echo '<div class="row">';
    echo '<div class="col-md-3"><div class="form-group"><label>Host</label><input class="form-control" name="db_host" value="localhost" required></div></div>';
    echo '<div class="col-md-3"><div class="form-group"><label>DB Name</label><input class="form-control" name="db_name" required></div></div>';
    echo '<div class="col-md-3"><div class="form-group"><label>DB User</label><input class="form-control" name="db_user" required></div></div>';
    echo '<div class="col-md-3"><div class="form-group"><label>DB Pass</label><input class="form-control" name="db_pass" type="password" required></div></div>';
    echo '</div>';
    echo '<div class="row">';
    echo '<div class="col-md-3"><div class="form-group"><label>Charset</label><input class="form-control" name="db_charset" value="utf8mb4"></div></div>';
    echo '</div>';
    echo '<button class="btn btn-primary" type="submit">Crear</button> ';
    echo '<a class="btn btn-default" href="?p=tenants">Cancelar</a>';
    echo '</form>';
    echo '<hr><p style="color:#666">Nota: la BD del tenant debe existir (creada en hPanel) y tener la estructura importada.</p>';
} elseif ($p === 'edit') {
    $tenantId = (int)($_GET['id'] ?? 0);
    if ($tenantId <= 0) {
        echo '<div class="alert alert-danger">Tenant inválido.</div>';
    } else {
        try {
            $pdo = Conexion::conectar();
            $stmt = $pdo->prepare('SELECT t.id, t.ruc, t.subdomain, t.status, t.created_at, d.db_host, d.db_name, d.db_user, d.db_charset FROM tenants t LEFT JOIN tenant_db d ON d.tenant_id=t.id WHERE t.id=:id LIMIT 1');
            $stmt->execute([':id' => $tenantId]);
            $t = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $t = null;
        }

        if (!$t) {
            echo '<div class="alert alert-danger">Tenant no encontrado.</div>';
        } else {
            $mainHost = panel_main_host();
            $tenantUrl = ($mainHost !== '') ? ('https://' . $t['subdomain'] . '.' . $mainHost . '/') : '';

            echo '<div style="display:flex;justify-content:space-between;align-items:center">';
            echo '<h4>Editar tenant: ' . panel_h((string)$t['subdomain']) . '</h4>';
            if ($tenantUrl !== '') {
                echo '<a class="btn btn-default" target="_blank" href="' . panel_h($tenantUrl) . '">Abrir tenant</a>';
            }
            echo '</div>';

            echo '<div class="panel panel-default"><div class="panel-heading">Datos</div><div class="panel-body">';
            echo '<form method="post">';
            echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
            echo '<input type="hidden" name="action" value="update_tenant">';
            echo '<input type="hidden" name="tenant_id" value="' . (int)$t['id'] . '">';

            echo '<div class="row">';
            echo '<div class="col-md-4"><div class="form-group"><label>Subdominio</label><input class="form-control" value="' . panel_h((string)$t['subdomain']) . '" disabled></div></div>';
            echo '<div class="col-md-4"><div class="form-group"><label>Status</label><input class="form-control" value="' . panel_h((string)$t['status']) . '" disabled></div></div>';
            echo '<div class="col-md-4"><div class="form-group"><label>Creado</label><input class="form-control" value="' . panel_h((string)$t['created_at']) . '" disabled></div></div>';
            echo '</div>';

            echo '<div class="row">';
            echo '<div class="col-md-6"><div class="form-group"><label>RUC</label><input class="form-control" name="ruc" value="' . panel_h((string)$t['ruc']) . '" required></div></div>';
            echo '</div>';

            echo '<h5>Credenciales BD del tenant</h5>';
            echo '<div class="row">';
            echo '<div class="col-md-3"><div class="form-group"><label>Host</label><input class="form-control" name="db_host" value="' . panel_h((string)($t['db_host'] ?? 'localhost')) . '" required></div></div>';
            echo '<div class="col-md-3"><div class="form-group"><label>DB Name</label><input class="form-control" name="db_name" value="' . panel_h((string)($t['db_name'] ?? '')) . '" required></div></div>';
            echo '<div class="col-md-3"><div class="form-group"><label>DB User</label><input class="form-control" name="db_user" value="' . panel_h((string)($t['db_user'] ?? '')) . '" required></div></div>';
            echo '<div class="col-md-3"><div class="form-group"><label>DB Pass (solo si cambia)</label><input class="form-control" name="db_pass" type="password" value=""></div></div>';
            echo '</div>';
            echo '<div class="row">';
            echo '<div class="col-md-3"><div class="form-group"><label>Charset</label><input class="form-control" name="db_charset" value="' . panel_h((string)($t['db_charset'] ?? 'utf8mb4')) . '"></div></div>';
            echo '</div>';

            echo '<button class="btn btn-primary" type="submit">Guardar cambios</button> ';
            echo '<a class="btn btn-default" href="?p=tenants">Volver</a>';
            echo '</form>';

            echo '<hr>';
            echo '<form method="post" style="display:inline-block;margin-right:8px">';
            echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
            echo '<input type="hidden" name="action" value="test_db">';
            echo '<input type="hidden" name="tenant_id" value="' . (int)$t['id'] . '">';
            echo '<button class="btn btn-info" type="submit">Test conexión BD</button>';
            echo '</form>';

            echo '<form method="post" style="display:inline-block">';
            echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
            echo '<input type="hidden" name="action" value="toggle_status">';
            echo '<input type="hidden" name="tenant_id" value="' . (int)$t['id'] . '">';
            $btn = ((string)$t['status'] === 'active') ? 'Suspender' : 'Activar';
            $cls = ((string)$t['status'] === 'active') ? 'btn btn-warning' : 'btn btn-success';
            echo '<button class="' . $cls . '" type="submit">' . $btn . '</button>';
            echo '</form>';

            echo '</div></div>';
        }
    }
} elseif ($p === 'audit') {
    echo '<h4>Auditoría</h4>';
    try {
        $pdo = Conexion::conectar();
        $rows = $pdo->query('SELECT a.id, a.created_at, a.action, a.tenant_id, u.username, a.ip, a.details FROM audit_log a LEFT JOIN admin_users u ON u.id=a.admin_user_id ORDER BY a.id DESC LIMIT 100')->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $rows = [];
        echo '<div class="alert alert-danger">No se pudo cargar auditoría.</div>';
    }
    echo '<table class="table table-bordered table-striped">';
    echo '<thead><tr><th>ID</th><th>Fecha</th><th>Acción</th><th>Tenant</th><th>Admin</th><th>IP</th><th>Detalles</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td>' . (int)$r['id'] . '</td>';
        echo '<td>' . panel_h((string)$r['created_at']) . '</td>';
        echo '<td>' . panel_h((string)$r['action']) . '</td>';
        echo '<td>' . panel_h((string)($r['tenant_id'] ?? '')) . '</td>';
        echo '<td>' . panel_h((string)($r['username'] ?? '')) . '</td>';
        echo '<td>' . panel_h((string)($r['ip'] ?? '')) . '</td>';
        $details = (string)($r['details'] ?? '');
        if ($details !== '' && strlen($details) > 220) {
            $details = substr($details, 0, 220) . '...';
        }
        echo '<td><code>' . panel_h($details) . '</code></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} elseif ($p === 'logs') {
    echo '<h4>Logs</h4>';
    $logPath = __DIR__ . '/../logs/panel_errors.log';
    if (!file_exists($logPath)) {
        echo '<div class="alert alert-info">No existe logs/panel_errors.log todavía.</div>';
    } else {
        $content = @file_get_contents($logPath);
        if ($content === false) {
            echo '<div class="alert alert-danger">No se pudo leer el log.</div>';
        } else {
            $max = 20000;
            if (strlen($content) > $max) {
                $content = substr($content, -$max);
                $content = "(Mostrando últimos {$max} bytes)\n\n" . $content;
            }
            echo '<pre style="background:#111;color:#eee;padding:12px;max-height:70vh;overflow:auto">' . panel_h($content) . '</pre>';
        }
    }
} else {
    $p = 'tenants';
    echo '<div style="margin:12px 0"><a class="btn btn-primary" href="?p=create">Crear tenant</a></div>';

    try {
        $pdo = Conexion::conectar();
        $rows = $pdo->query("SELECT t.id, t.ruc, t.subdomain, t.status, t.created_at, d.db_name FROM tenants t LEFT JOIN tenant_db d ON d.tenant_id = t.id ORDER BY t.id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $rows = [];
        echo '<div class="alert alert-danger">No se pudo cargar tenants.</div>';
    }

    $mainHost = panel_main_host();
    echo '<table class="table table-bordered table-striped">';
    echo '<thead><tr><th>ID</th><th>RUC</th><th>Subdominio</th><th>Status</th><th>DB</th><th>Creado</th><th style="width:260px">Acciones</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        $tenantId = (int)$r['id'];
        $sub = (string)$r['subdomain'];
        $tenantUrl = ($mainHost !== '' && $sub !== '') ? ('https://' . $sub . '.' . $mainHost . '/') : '';
        echo '<tr>';
        echo '<td>' . $tenantId . '</td>';
        echo '<td>' . panel_h((string)$r['ruc']) . '</td>';
        echo '<td>' . panel_h((string)$r['subdomain']) . '</td>';
        echo '<td>' . panel_h((string)$r['status']) . '</td>';
        echo '<td>' . panel_h((string)($r['db_name'] ?? '')) . '</td>';
        echo '<td>' . panel_h((string)$r['created_at']) . '</td>';

        echo '<td>';
        echo '<a class="btn btn-default btn-xs" href="?p=edit&id=' . $tenantId . '">Editar</a> ';
        echo '<form method="post" style="display:inline-block">';
        echo '<input type="hidden" name="csrf" value="' . panel_h(panel_csrf_token()) . '">';
        echo '<input type="hidden" name="action" value="toggle_status">';
        echo '<input type="hidden" name="tenant_id" value="' . $tenantId . '">';
        $btn = ((string)$r['status'] === 'active') ? 'Suspender' : 'Activar';
        $cls = ((string)$r['status'] === 'active') ? 'btn btn-warning btn-xs' : 'btn btn-success btn-xs';
        echo '<button class="' . $cls . '" type="submit">' . $btn . '</button>';
        echo '</form> ';
        if ($tenantUrl !== '') {
            echo '<a class="btn btn-info btn-xs" target="_blank" href="' . panel_h($tenantUrl) . '">Abrir</a>';
        }
        echo '</td>';

        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '<p style="color:#666">El panel solo registra el tenant. La creación de subdominio y BD se hace en hPanel (semi-automático).</p>';
}

echo '</div></body></html>';
