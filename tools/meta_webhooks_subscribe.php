<?php
// Configure Meta Webhooks (App Subscriptions) via Graph API.
// This is an alternative to the App Dashboard UI when it fails to load.
//
// It subscribes the app to the whatsapp_business_account object's 'messages' field
// and sets the callback URL + verify token.
//
// Requires in .env:
// - WA_APP_ID
// - WA_APP_SECRET
// - WA_WEBHOOK_VERIFY_TOKEN
//
// Usage:
//   php tools/meta_webhooks_subscribe.php https://xxxx.ngrok-free.dev/tools/whatsapp_webhook.php

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

$callbackUrl = $argv[1] ?? null;
if (!$callbackUrl) {
    out([
        'ok' => false,
        'error' => 'Missing callback URL argument',
        'usage' => 'php tools/meta_webhooks_subscribe.php https://xxxx.ngrok-free.dev/tools/whatsapp_webhook.php'
    ], 1);
}

$appId = env('WA_APP_ID');
$appSecret = env('WA_APP_SECRET');
$verifyToken = env('WA_WEBHOOK_VERIFY_TOKEN');

if (!$appId || !$appSecret) {
    out(['ok' => false, 'error' => 'Missing WA_APP_ID or WA_APP_SECRET in .env'], 1);
}
if (!$verifyToken) {
    out(['ok' => false, 'error' => 'Missing WA_WEBHOOK_VERIFY_TOKEN in .env'], 1);
}

$graphVersion = 'v21.0';
$url = "https://graph.facebook.com/{$graphVersion}/{$appId}/subscriptions";

// App access token format is app_id|app_secret
$appAccessToken = $appId . '|' . $appSecret;

$form = http_build_query([
    'object' => 'whatsapp_business_account',
    'callback_url' => (string)$callbackUrl,
    'verify_token' => (string)$verifyToken,
    'fields' => 'messages',
    'access_token' => $appAccessToken,
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $form,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/x-www-form-urlencoded',
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

out([
    'ok' => ($response !== false && $http >= 200 && $http < 300),
    'http' => $http,
    'graph_version' => $graphVersion,
    'app_id' => $appId,
    'callback_url' => (string)$callbackUrl,
    'curl_error' => $curlErr ?: null,
    'response_raw' => $response,
], ($http >= 200 && $http < 300) ? 0 : 1);
