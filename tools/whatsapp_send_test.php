<?php
// Minimal WhatsApp Cloud API send test.
// Usage (browser):
// - Text:     http://localhost/tools/whatsapp_send_test.php?to=519XXXXXXXX&text=Hola
// - Template: http://localhost/tools/whatsapp_send_test.php?to=519XXXXXXXX&template=hello_world&lang=en_US
// Usage (CLI):
// - Text:     php tools/whatsapp_send_test.php 519XXXXXXXX "Hola"
// - Template: php tools/whatsapp_send_test.php --template 519XXXXXXXX hello_world en_US

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

function isLocalRequest(): bool {
    if (PHP_SAPI === 'cli') {
        return true;
    }
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return $ip === '127.0.0.1' || $ip === '::1';
}

function fail(int $httpCode, string $message): void {
    if (PHP_SAPI !== 'cli') {
        http_response_code($httpCode);
        header('Content-Type: text/plain; charset=utf-8');
    }
    echo $message;
    exit(1);
}

// Load .env from project root
$envPath = dirname(__DIR__) . '/.env';
loadEnvFile($envPath);

$appEnv = strtolower((string)env('APP_ENV', ''));
if (!$appEnv) {
    $appEnv = 'unknown';
}

if ($appEnv !== 'local' && !isLocalRequest()) {
    fail(403, "Forbidden: this test script is intended for local use.\n");
}

$token = env('WA_ACCESS_TOKEN');
$phoneNumberId = env('WA_PHONE_NUMBER_ID');

if (!$token || !$phoneNumberId) {
    fail(500, "Missing env vars. Please set WA_ACCESS_TOKEN and WA_PHONE_NUMBER_ID in .env\n");
}

$to = null;
$text = null;
$templateName = null;
$templateLang = null;
$mode = 'text';

if (PHP_SAPI === 'cli') {
    $arg1 = $argv[1] ?? null;
    if ($arg1 === '--template') {
        $mode = 'template';
        $to = $argv[2] ?? null;
        $templateName = $argv[3] ?? null;
        $templateLang = $argv[4] ?? null;
    } else {
        $to = $arg1;
        $text = $argv[2] ?? null;
    }
} else {
    $to = $_GET['to'] ?? $_POST['to'] ?? null;
    $text = $_GET['text'] ?? $_POST['text'] ?? null;
    $templateName = $_GET['template'] ?? $_POST['template'] ?? null;
    $templateLang = $_GET['lang'] ?? $_POST['lang'] ?? null;
    if ($templateName) {
        $mode = 'template';
    }
}

$to = $to ? preg_replace('/\s+/', '', (string)$to) : null;
$text = $text ? (string)$text : null;
$templateName = $templateName ? trim((string)$templateName) : null;
$templateLang = $templateLang ? trim((string)$templateLang) : null;

if (!$to || ($mode === 'text' && !$text) || ($mode === 'template' && (!$templateName || !$templateLang))) {
    $usage = "Usage:\n";
    $usage .= "- Browser: /tools/whatsapp_send_test.php?to=519XXXXXXXX&text=Hola\n";
    $usage .= "- Browser (template): /tools/whatsapp_send_test.php?to=519XXXXXXXX&template=hello_world&lang=en_US\n";
    $usage .= "- CLI: php tools/whatsapp_send_test.php 519XXXXXXXX \"Hola\"\n";
    $usage .= "- CLI (template): php tools/whatsapp_send_test.php --template 519XXXXXXXX hello_world en_US\n";
    fail(400, $usage);
}

$url = "https://graph.facebook.com/v21.0/{$phoneNumberId}/messages";

$payload = [
    'messaging_product' => 'whatsapp',
    'to' => $to,
];

if ($mode === 'template') {
    $payload['type'] = 'template';
    $payload['template'] = [
        'name' => $templateName,
        'language' => ['code' => $templateLang],
    ];
} else {
    $payload['type'] = 'text';
    $payload['text'] = ['body' => $text];
}

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 25,
]);

$response = curl_exec($ch);
$curlErr = curl_error($ch);
$http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (PHP_SAPI !== 'cli') {
    header('Content-Type: application/json; charset=utf-8');
}

echo json_encode([
    'ok' => ($response !== false && $http >= 200 && $http < 300),
    'http' => $http,
    'curl_error' => $curlErr ?: null,
    'request' => [
        'to' => $to,
        'mode' => $mode,
        'text' => $mode === 'text' ? $text : null,
        'template' => $mode === 'template' ? ['name' => $templateName, 'lang' => $templateLang] : null,
        'phone_number_id' => $phoneNumberId,
        'graph_version' => 'v21.0',
    ],
    'response_raw' => $response,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
