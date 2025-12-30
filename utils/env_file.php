<?php

function envfile_parse_lines(array $lines): array {
    $values = [];
    foreach ($lines as $line) {
        $trim = trim($line);
        if ($trim === '' || str_starts_with($trim, '#')) {
            continue;
        }
        $pos = strpos($trim, '=');
        if ($pos === false) {
            continue;
        }
        $name = trim(substr($trim, 0, $pos));
        $value = trim(substr($trim, $pos + 1));
        if ($name === '') {
            continue;
        }
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        $values[$name] = $value;
    }
    return $values;
}

function envfile_read(string $path): array {
    if (!file_exists($path)) {
        return [];
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if (!is_array($lines)) {
        return [];
    }
    return envfile_parse_lines($lines);
}

function envfile_set_values(string $path, array $updates): array {
    $result = [
        'ok' => false,
        'path' => $path,
        'updated' => [],
        'skipped' => [],
        'error' => null,
    ];

    // Only allow scalar string-ish values
    foreach ($updates as $k => $v) {
        if (!is_string($k) || $k === '') {
            unset($updates[$k]);
            continue;
        }
        if ($v === null) {
            unset($updates[$k]);
            continue;
        }
        if (!is_scalar($v)) {
            unset($updates[$k]);
            continue;
        }
        $updates[$k] = (string)$v;
    }

    if (!file_exists($path)) {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        @file_put_contents($path, "", LOCK_EX);
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES);
    if (!is_array($lines)) {
        $result['error'] = 'No se pudo leer el archivo .env';
        return $result;
    }

    $remaining = $updates;
    $newLines = [];

    foreach ($lines as $line) {
        $originalLine = $line;
        $trim = trim($line);

        if ($trim === '' || str_starts_with($trim, '#') || strpos($trim, '=') === false) {
            $newLines[] = $originalLine;
            continue;
        }

        [$name] = explode('=', $trim, 2);
        $name = trim($name);

        if ($name !== '' && array_key_exists($name, $remaining)) {
            $val = $remaining[$name];
            unset($remaining[$name]);

            // Preserve empty values (DB_PASS=)
            $newLines[] = $name . '=' . $val;
            $result['updated'][] = $name;
            continue;
        }

        $newLines[] = $originalLine;
    }

    // Append any new keys at the end
    if (!empty($remaining)) {
        $newLines[] = '';
        $newLines[] = '# WhatsApp (autogenerado por UI)';
        foreach ($remaining as $k => $v) {
            $newLines[] = $k . '=' . $v;
            $result['updated'][] = $k;
        }
    }

    $payload = implode(PHP_EOL, $newLines) . PHP_EOL;
    $ok = @file_put_contents($path, $payload, LOCK_EX);
    if ($ok === false) {
        $result['error'] = 'No se pudo escribir el archivo .env (permisos)';
        return $result;
    }

    $result['ok'] = true;
    return $result;
}
