<?php
require_once __DIR__ . '/../modelos/usuarios.modelo.php';
require_once __DIR__ . '/../modelos/conexion.php';

// Report
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Login test script\n";

try {
    $db = Conexion::conectar();
    echo "DB connection: OK\n";
} catch (Exception $e) {
    echo "DB connection: FAILED - " . $e->getMessage() . "\n";
    exit(1);
}

// Fetch first user
$users = ModeloUsuarios::mdlMostrarUsuarios('usuarios', null, null);
if (!$users || count($users) == 0) {
    echo "No users found in 'usuarios' table.\n";
    exit(1);
}

$first = $users[0];
if (is_array($first)) {
    echo "Found first user (array).\n";
} else {
    // If mdlMostrarUsuarios returned associative single row when item provided, handle
    echo "Found user.\n";
}

// Normalize first user data
$u = $users[0];
$id = isset($u['id']) ? $u['id'] : '(no id)';
$user = isset($u['usuario']) ? $u['usuario'] : '(no usuario)';
$hash = isset($u['password']) ? $u['password'] : '';

echo "id: $id\n";
echo "usuario: $user\n";
echo "password field length: " . strlen($hash) . "\n";
echo "password raw: " . ($hash === '' ? '(empty)' : $hash) . "\n";

// Detect hash type
$type = 'unknown';
if ($hash === '') $type = 'empty';
elseif (preg_match('/^\$2[ayb]\$/', $hash)) $type = 'bcrypt';
elseif (preg_match('/^\$argon2/', $hash)) $type = 'argon2';
elseif (strlen($hash) === 32 && preg_match('/^[a-f0-9]{32}$/i', $hash)) $type = 'md5';
elseif (preg_match('/^[0-9a-f]{60}$/i', $hash) && strpos($hash, '$2y$') !== false) $type = 'bcrypt';
else $type = 'possibly-plain';

echo "Detected password type: $type\n";

// Emulate controller verification with a test password
$testPassword = getenv('TEST_PASSWORD') ?: 'password123';

echo "Testing with password: $testPassword\n";

$verified = false;

if ($hash !== '' && password_verify($testPassword, $hash)) {
    echo "password_verify(): OK\n";
    $verified = true;
}

if (!$verified && $hash !== '' && strlen($hash) === 32) {
    if (hash_equals($hash, md5($testPassword))) {
        echo "MD5 legacy match: OK\n";
        $verified = true;
    }
}

if (!$verified && $hash !== '') {
    // try legacy crypt as in controller
    $legacy_salt = '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$';
    if (hash_equals($hash, crypt($testPassword, $legacy_salt))) {
        echo "legacy crypt match: OK\n";
        $verified = true;
    }
}

if ($verified) {
    echo "LOGIN SIMULATION: SUCCESS\n";
} else {
    echo "LOGIN SIMULATION: FAILED\n";
}

// For debugging show a few users summary
echo "\nList of first 5 users (id, usuario, hash_len):\n";
for ($i = 0; $i < min(5, count($users)); $i++) {
    $uu = $users[$i];
    echo ($i+1) . ") id=" . ($uu['id'] ?? '') . " usuario=" . ($uu['usuario'] ?? '') . " len=" . strlen($uu['password'] ?? '') . "\n";
}

?>