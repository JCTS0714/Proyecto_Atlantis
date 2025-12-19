<?php
require_once __DIR__ . '/../modelos/conexion.php';

if (!defined('APP_MODE') || APP_MODE !== 'panel') {
    http_response_code(404);
    echo 'Not found';
    exit;
}

// Session already started by main index.php.

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

function panel_base_url(): string {
    // BASE_URL is defined by config/paths.php; for panel we can compute quickly too.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $proto = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\\/');
    return $proto . '://' . $host . $scriptDir;
}

$baseUrl = panel_base_url();

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

        $stmt3 = $pdo->prepare('INSERT INTO audit_log (admin_user_id, tenant_id, action, details, ip) VALUES (:a,:t,:ac,:d,:ip)');
        $stmt3->execute([
            ':a' => $adminId,
            ':t' => $tenantId,
            ':ac' => 'create_tenant',
            ':d' => json_encode(['subdomain' => $subdomain, 'db_name' => $dbName], JSON_UNESCAPED_UNICODE),
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

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

// Views
$flashOk = $_SESSION['panel_flash_ok'] ?? null;
$flashErr = $_SESSION['panel_flash_error'] ?? null;
unset($_SESSION['panel_flash_ok'], $_SESSION['panel_flash_error']);

echo '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
echo '<title>Panel Admin</title>';
echo '<link rel="stylesheet" href="/bootstrap.min.css">';
echo '</head><body style="padding:24px">';
echo '<div class="container">';
echo '<div style="display:flex;justify-content:space-between;align-items:center">';
echo '<h3>Panel Admin</h3>';
echo '<div><span style="margin-right:12px">' . panel_h($adminUser) . '</span><a class="btn btn-default btn-sm" href="?p=logout">Salir</a></div>';
echo '</div>';

if ($flashOk) echo '<div class="alert alert-success">' . panel_h($flashOk) . '</div>';
if ($flashErr) echo '<div class="alert alert-danger">' . panel_h($flashErr) . '</div>';

if ($p === 'create') {
    echo '<h4>Crear tenant</h4>';
    echo '<form method="post">';
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
    echo '<a class="btn btn-default" href="/">Cancelar</a>';
    echo '</form>';
    echo '<hr><p style="color:#666">Nota: la BD del tenant debe existir (creada en hPanel) y tener la estructura importada.</p>';
} else {
    echo '<div style="margin:12px 0"><a class="btn btn-primary" href="?p=create">Crear tenant</a></div>';

    try {
        $pdo = Conexion::conectar();
        $rows = $pdo->query("SELECT t.id, t.ruc, t.subdomain, t.status, t.created_at, d.db_name FROM tenants t LEFT JOIN tenant_db d ON d.tenant_id = t.id ORDER BY t.id DESC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $rows = [];
        echo '<div class="alert alert-danger">No se pudo cargar tenants.</div>';
    }

    echo '<table class="table table-bordered table-striped">';
    echo '<thead><tr><th>ID</th><th>RUC</th><th>Subdominio</th><th>Status</th><th>DB</th><th>Creado</th></tr></thead><tbody>';
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td>' . (int)$r['id'] . '</td>';
        echo '<td>' . panel_h((string)$r['ruc']) . '</td>';
        echo '<td>' . panel_h((string)$r['subdomain']) . '</td>';
        echo '<td>' . panel_h((string)$r['status']) . '</td>';
        echo '<td>' . panel_h((string)($r['db_name'] ?? '')) . '</td>';
        echo '<td>' . panel_h((string)$r['created_at']) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';

    echo '<p style="color:#666">El panel solo registra el tenant. La creación de subdominio y BD se hace en hPanel (semi-automático).</p>';
}

echo '</div></body></html>';
