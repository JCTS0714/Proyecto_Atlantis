<?php
require_once "modelos/conexion.php";

try {
    $pdo = Conexion::conectar();

    // Verificar si la tabla usuarios existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'usuarios'");
    $tableExists = $stmt->rowCount() > 0;

    if (!$tableExists) {
        echo "La tabla 'usuarios' no existe en la base de datos.\n";
        exit;
    }

    echo "Tabla 'usuarios' encontrada.\n\n";

    // Mostrar estructura de la tabla
    echo "Estructura de la tabla 'usuarios':\n";
    $stmt = $pdo->query("DESCRIBE usuarios");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>\n\n";

    // Verificar columnas específicas para remember_token
    $hasRememberToken = false;
    $hasRememberExpires = false;

    foreach ($columns as $column) {
        if ($column['Field'] === 'remember_token') {
            $hasRememberToken = true;
            echo "Columna 'remember_token': " . $column['Type'] . " (" . ($column['Null'] === 'YES' ? 'NULL permitido' : 'NOT NULL') . ")\n";
        }
        if ($column['Field'] === 'remember_expires') {
            $hasRememberExpires = true;
            echo "Columna 'remember_expires': " . $column['Type'] . " (" . ($column['Null'] === 'YES' ? 'NULL permitido' : 'NOT NULL') . ")\n";
        }
    }

    if (!$hasRememberToken) {
        echo "❌ Columna 'remember_token' NO existe. Es necesaria para la sesión persistente.\n";
    } else {
        echo "✅ Columna 'remember_token' existe.\n";
    }

    if (!$hasRememberExpires) {
        echo "❌ Columna 'remember_expires' NO existe. Es necesaria para la sesión persistente.\n";
    } else {
        echo "✅ Columna 'remember_expires' existe.\n";
    }

    // Mostrar algunos registros de ejemplo (sin contraseñas)
    echo "\nPrimeros 3 usuarios (sin contraseñas):\n";
    $stmt = $pdo->query("SELECT id, nombre, usuario, perfil, foto, estado, ultimo_login, sesion_token, remember_token, remember_expires FROM usuarios LIMIT 3");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($usuarios) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Usuario</th><th>Perfil</th><th>Estado</th><th>Último Login</th><th>Remember Token</th><th>Remember Expires</th></tr>";
        foreach ($usuarios as $usuario) {
            echo "<tr>";
            echo "<td>" . $usuario['id'] . "</td>";
            echo "<td>" . $usuario['nombre'] . "</td>";
            echo "<td>" . $usuario['usuario'] . "</td>";
            echo "<td>" . $usuario['perfil'] . "</td>";
            echo "<td>" . $usuario['estado'] . "</td>";
            echo "<td>" . $usuario['ultimo_login'] . "</td>";
            echo "<td>" . (empty($usuario['remember_token']) ? 'NULL' : substr($usuario['remember_token'], 0, 10) . '...') . "</td>";
            echo "<td>" . $usuario['remember_expires'] . "</td>";
            echo "</tr>";
        }
        echo "</table>\n";
    } else {
        echo "No hay usuarios en la tabla.\n";
    }

} catch (PDOException $e) {
    echo "Error de conexión a la base de datos: " . $e->getMessage() . "\n";
}
?>
