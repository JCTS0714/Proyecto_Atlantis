<?php
// Manage WABA webhook subscription via Graph API.
// Avoids copying tokens into terminal commands.
//
// Requires in .env:
// - WA_ACCESS_TOKEN
// - WA_WABA_ID
// - WA_WEBHOOK_VERIFY_TOKEN
// Optional CLI args:
// - action: subscribe|subscribe-basic|get|unsubscribe (default: subscribe)
// - callback URL (required for subscribe): https://xxxx.ngrok-free.dev/tools/whatsapp_webhook.php
//
// Examples:
//   php tools/whatsapp_waba_subscribe.php subscribe-basic
//   php tools/whatsapp_waba_subscribe.php subscribe https://xxxx.ngrok-free.dev/tools/whatsapp_webhook.php
//   php tools/whatsapp_waba_subscribe.php get
//   php tools/whatsapp_waba_subscribe.php unsubscribe

function loadEnvFile(string $path): void {
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
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

        $_ENV[$name] = $value;
        if (function_exists('putenv')) {
            @putenv("$name=$value");
        }
    }
}

function env(string $key, ?string $default = null): ?string {
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return $val;
    }
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string)$_ENV[$key];
    }
    return $default;
}

function out(array $data, int $exitCode = 0): void {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    exit($exitCode);
}

loadEnvFile(dirname(__DIR__) . '/.env');

$action = $argv[1] ?? 'subscribe';
$action = strtolower(trim((string)$action));
$callbackUrl = $argv[2] ?? null;

$token = env('WA_ACCESS_TOKEN');
$wabaId = env('WA_WABA_ID');
$verifyToken = env('WA_WEBHOOK_VERIFY_TOKEN');

if (!$token) {
    out(['ok' => false, 'error' => 'Missing WA_ACCESS_TOKEN in .env'], 1);
}
if (!$wabaId) {
    out(['ok' => false, 'error' => 'Missing WA_WABA_ID in .env'], 1);
}

$graphVersion = 'v21.0';
$url = "https://graph.facebook.com/{$graphVersion}/{$wabaId}/subscribed_apps";

$method = 'GET';
$payload = null;

if ($action === 'subscribe-basic') {
    $method = 'POST';
    $payload = null;
} elseif ($action === 'subscribe') {
    if (!$verifyToken) {
        out(['ok' => false, 'error' => 'Missing WA_WEBHOOK_VERIFY_TOKEN in .env'], 1);
    }
    if (!$callbackUrl) {
        out([
            'ok' => false,
            'error' => 'Missing callback URL argument',
            'usage' => 'php tools/whatsapp_waba_subscribe.php subscribe https://xxxx.ngrok-free.dev/tools/whatsapp_webhook.php'
        ], 1);
    }
    $method = 'POST';
    $payload = [
        'override_callback_uri' => (string)$callbackUrl,
        'verify_token' => (string)$verifyToken,
    ];
} elseif ($action === 'get') {
    $method = 'GET';
} elseif ($action === 'unsubscribe') {
    $method = 'DELETE';
} else {
    out(['ok' => false, 'error' => 'Unknown action', 'allowed' => ['subscribe-basic', 'subscribe', 'get', 'unsubscribe']], 1);
}

$ch = curl_init($url);
$headers = [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
];

curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_CUSTOMREQUEST => $method,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
]);

if ($payload !== null) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
}

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

out([
    'ok' => ($response !== false && $http >= 200 && $http < 300),
    'http' => $http,
    'action' => $action,
    'graph_version' => $graphVersion,
    'waba_id' => $wabaId,
    'callback_url' => $action === 'subscribe' ? $callbackUrl : null,
    'curl_error' => $curlErr ?: null,
    'response_raw' => $response,
], ($http >= 200 && $http < 300) ? 0 : 1);
