<?php
// Minimal WhatsApp Cloud API webhook receiver.
// - GET: verification handshake (hub.challenge)
// - POST: receives events (messages/statuses) and logs them
//
// Configure in .env:
// - WA_WEBHOOK_VERIFY_TOKEN=some-random-string
// Optional signature validation (recommended):
// - WA_APP_SECRET=your-meta-app-secret

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

function respond(int $httpCode, string $body, string $contentType = 'text/plain; charset=utf-8'): void {
    http_response_code($httpCode);
    header('Content-Type: ' . $contentType);
    echo $body;
    exit;
}

function appendLog(string $message): void {
    $logDir = dirname(__DIR__) . '/logs';
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    $logFile = $logDir . '/whatsapp_webhook.log';
    $ts = date('Y-m-d H:i:s');
    @file_put_contents($logFile, "[$ts] $message\n", FILE_APPEND);
}

// Load .env from project root
loadEnvFile(dirname(__DIR__) . '/.env');

$verifyToken = env('WA_WEBHOOK_VERIFY_TOKEN');
$appSecret = env('WA_APP_SECRET');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'GET') {
    // Webhook verification handshake
    // Meta sends: hub.mode, hub.verify_token, hub.challenge
    $mode = $_GET['hub_mode'] ?? $_GET['hub.mode'] ?? null;
    $token = $_GET['hub_verify_token'] ?? $_GET['hub.verify_token'] ?? null;
    $challenge = $_GET['hub_challenge'] ?? $_GET['hub.challenge'] ?? null;

    if ($mode !== 'subscribe' || $challenge === null) {
        respond(400, "Bad Request\n");
    }

    if (!$verifyToken) {
        appendLog('GET verification failed: WA_WEBHOOK_VERIFY_TOKEN missing');
        respond(500, "Server misconfigured\n");
    }

    if (!hash_equals($verifyToken, (string)$token)) {
        appendLog('GET verification failed: token mismatch');
        respond(403, "Forbidden\n");
    }

    appendLog('GET verification ok');
    respond(200, (string)$challenge);
}

if ($method !== 'POST') {
    respond(405, "Method Not Allowed\n");
}

$raw = file_get_contents('php://input');
if ($raw === false) {
    respond(400, "Bad Request\n");
}

// Optional signature validation
// For Graph webhooks, Meta sends X-Hub-Signature-256: sha256=<hex>
if ($appSecret) {
    $sigHeader = $_SERVER['HTTP_X_HUB_SIGNATURE_256'] ?? '';
    $prefix = 'sha256=';
    if (str_starts_with($sigHeader, $prefix)) {
        $sig = substr($sigHeader, strlen($prefix));
        $expected = hash_hmac('sha256', $raw, $appSecret);
        if (!hash_equals($expected, $sig)) {
            appendLog('POST rejected: invalid signature');
            respond(403, "Forbidden\n");
        }
    } else {
        // If app secret is set but signature header missing, reject to avoid accepting spoofed calls.
        appendLog('POST rejected: missing X-Hub-Signature-256');
        respond(403, "Forbidden\n");
    }
}

$decoded = json_decode($raw, true);
if (!is_array($decoded)) {
    appendLog('POST received non-JSON payload: ' . substr($raw, 0, 500));
    respond(200, "OK\n");
}

// Keep logs compact but useful
$summary = [
    'object' => $decoded['object'] ?? null,
];

// WhatsApp sends payloads with entry[].changes[].value
if (isset($decoded['entry'][0]['changes'][0]['value'])) {
    $value = $decoded['entry'][0]['changes'][0]['value'];

    if (isset($value['messages'][0])) {
        $msg = $value['messages'][0];
        $summary['type'] = 'message';
        $summary['from'] = $msg['from'] ?? null;
        $summary['id'] = $msg['id'] ?? null;
        $summary['timestamp'] = $msg['timestamp'] ?? null;
        $summary['msg_type'] = $msg['type'] ?? null;
        if (($msg['type'] ?? null) === 'text') {
            $summary['text'] = $msg['text']['body'] ?? null;
        }
    } elseif (isset($value['statuses'][0])) {
        $st = $value['statuses'][0];
        $summary['type'] = 'status';
        $summary['id'] = $st['id'] ?? null;
        $summary['status'] = $st['status'] ?? null;
        $summary['timestamp'] = $st['timestamp'] ?? null;
        $summary['recipient_id'] = $st['recipient_id'] ?? null;
        if (isset($st['errors'][0])) {
            $summary['error_code'] = $st['errors'][0]['code'] ?? null;
            $summary['error_title'] = $st['errors'][0]['title'] ?? null;
            $summary['error_message'] = $st['errors'][0]['message'] ?? null;
        }
    }
}

appendLog('POST ' . json_encode($summary, JSON_UNESCAPED_UNICODE));

// For debugging, also keep the full payload (trim to avoid huge logs)
appendLog('POST raw ' . substr($raw, 0, 8000));

respond(200, "OK\n");
