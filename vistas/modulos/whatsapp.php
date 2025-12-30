<!-- =============================================== -->
<?php
require_once __DIR__ . '/../../utils/env_file.php';

if (!isset($_SESSION['perfil'])) {
    include "404.php";
    return;
}

// Solo Admin (bloquea Vendedor)
if ($_SESSION['perfil'] === 'Vendedor') {
    echo '<div class="content-wrapper"><section class="content-header"><h1>WhatsApp</h1></section><section class="content"><div class="alert alert-warning">No tienes permisos para acceder a esta sección.</div></section></div>';
    return;
}

function wa_base_url(): string {
    if (defined('BASE_URL') && BASE_URL) {
        return rtrim((string)BASE_URL, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ((int)($_SERVER['SERVER_PORT'] ?? 80) === 443);
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

function wa_curl_json(string $url, string $method, array $headers, ?array $body = null, int $timeout = 25): array {
    $ch = curl_init($url);
    $opts = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
    ];
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE);
    }
    curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'http' => $http,
        'curl_error' => $err ?: null,
        'raw' => $resp,
        'json' => is_string($resp) ? json_decode($resp, true) : null,
    ];
}

$projectRoot = dirname(__DIR__, 2);
$envPath = $projectRoot . '/.env';
$env = envfile_read($envPath);

$callbackUrl = wa_base_url() . '/tools/whatsapp_webhook.php';

$opTitle = null;
$opResult = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['wa_action'] ?? '';

    // Recargar env siempre desde disco antes de operar
    $env = envfile_read($envPath);

    if ($action === 'save') {
        $updates = [];

        $fields = [
            'WA_PHONE_NUMBER_ID',
            'WA_WABA_ID',
            'WA_WEBHOOK_VERIFY_TOKEN',
            'WA_APP_ID',
        ];

        foreach ($fields as $k) {
            $v = isset($_POST[$k]) ? trim((string)$_POST[$k]) : '';
            if ($v !== '') {
                $updates[$k] = $v;
            }
        }

        // Sensibles: solo actualiza si viene algo
        $token = isset($_POST['WA_ACCESS_TOKEN']) ? trim((string)$_POST['WA_ACCESS_TOKEN']) : '';
        if ($token !== '') {
            $updates['WA_ACCESS_TOKEN'] = $token;
        }

        $appSecret = isset($_POST['WA_APP_SECRET']) ? trim((string)$_POST['WA_APP_SECRET']) : '';
        if ($appSecret !== '') {
            $updates['WA_APP_SECRET'] = $appSecret;
        }

        $write = envfile_set_values($envPath, $updates);
        $env = envfile_read($envPath);

        $opTitle = 'Guardar configuración';
        $opResult = $write;

        $_SESSION['mensaje'] = [
            'tipo' => $write['ok'] ? 'success' : 'error',
            'titulo' => $write['ok'] ? 'Configuración guardada' : 'No se pudo guardar',
            'texto' => $write['ok'] ? ('Actualizado: ' . implode(', ', $write['updated'])) : ($write['error'] ?? 'Error desconocido'),
        ];
    }

    if ($action === 'verify_endpoint') {
        $verifyToken = $env['WA_WEBHOOK_VERIFY_TOKEN'] ?? '';
        $challenge = '123';
        $url = $callbackUrl . '?hub.mode=subscribe&hub.verify_token=' . urlencode($verifyToken) . '&hub.challenge=' . urlencode($challenge);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $opTitle = 'Verificar endpoint webhook';
        $opResult = [
            'url' => $url,
            'expected' => $challenge,
            'http' => $http,
            'curl_error' => $err ?: null,
            'response' => $resp,
            'ok' => ($http >= 200 && $http < 300 && trim((string)$resp) === $challenge),
        ];

        $_SESSION['mensaje'] = [
            'tipo' => $opResult['ok'] ? 'success' : 'error',
            'titulo' => $opResult['ok'] ? 'Webhook verificado' : 'Fallo en verificación',
            'texto' => $opResult['ok'] ? 'El endpoint responde correctamente.' : 'Revisa verify_token o accesibilidad pública.',
        ];
    }

    if ($action === 'subscribe') {
        $graphVersion = 'v21.0';

        $appId = $env['WA_APP_ID'] ?? '';
        $appSecret = $env['WA_APP_SECRET'] ?? '';
        $wabaId = $env['WA_WABA_ID'] ?? '';
        $verifyToken = $env['WA_WEBHOOK_VERIFY_TOKEN'] ?? '';
        $userToken = $env['WA_ACCESS_TOKEN'] ?? '';

        $errors = [];
        if (!$appId || !$appSecret) $errors[] = 'WA_APP_ID / WA_APP_SECRET';
        if (!$wabaId) $errors[] = 'WA_WABA_ID';
        if (!$verifyToken) $errors[] = 'WA_WEBHOOK_VERIFY_TOKEN';
        if (!$userToken) $errors[] = 'WA_ACCESS_TOKEN';

        if (!empty($errors)) {
            $opTitle = 'Configurar webhooks';
            $opResult = ['ok' => false, 'error' => 'Faltan variables: ' . implode(', ', $errors)];
            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'titulo' => 'Faltan datos',
                'texto' => $opResult['error'],
            ];
        } else {
            // 1) App-level subscription
            $appAccessToken = $appId . '|' . $appSecret;
            $subUrl = "https://graph.facebook.com/{$graphVersion}/{$appId}/subscriptions?access_token=" . urlencode($appAccessToken);

            $subResp = wa_curl_json($subUrl, 'POST', ['Content-Type: application/json'], [
                'object' => 'whatsapp_business_account',
                'callback_url' => $callbackUrl,
                'verify_token' => $verifyToken,
                'fields' => 'messages',
            ]);

            // 2) WABA-level override
            $wabaUrl = "https://graph.facebook.com/{$graphVersion}/{$wabaId}/subscribed_apps?access_token=" . urlencode($userToken);
            $wabaResp = wa_curl_json($wabaUrl, 'POST', ['Content-Type: application/json'], [
                'override_callback_uri' => $callbackUrl,
                'verify_token' => $verifyToken,
            ]);

            $ok = ($subResp['http'] >= 200 && $subResp['http'] < 300) && ($wabaResp['http'] >= 200 && $wabaResp['http'] < 300);

            $opTitle = 'Configurar webhooks';
            $opResult = [
                'ok' => $ok,
                'callback_url' => $callbackUrl,
                'app_subscription' => $subResp,
                'waba_subscription' => $wabaResp,
            ];

            $_SESSION['mensaje'] = [
                'tipo' => $ok ? 'success' : 'error',
                'titulo' => $ok ? 'Webhooks configurados' : 'Error configurando webhooks',
                'texto' => $ok ? 'Suscripción app + WABA completada.' : 'Revisa tokens/permisos y la salida en detalle.',
            ];
        }
    }

    if ($action === 'send_test') {
        $graphVersion = 'v21.0';
        $phoneNumberId = $env['WA_PHONE_NUMBER_ID'] ?? '';
        $token = $env['WA_ACCESS_TOKEN'] ?? '';

        $to = isset($_POST['to']) ? preg_replace('/\s+/', '', (string)$_POST['to']) : '';
        $mode = isset($_POST['mode']) ? (string)$_POST['mode'] : 'template';
        $text = isset($_POST['text']) ? (string)$_POST['text'] : '';
        $template = isset($_POST['template']) ? trim((string)$_POST['template']) : 'hello_world';
        $lang = isset($_POST['lang']) ? trim((string)$_POST['lang']) : 'en_US';

        $errors = [];
        if (!$phoneNumberId) $errors[] = 'WA_PHONE_NUMBER_ID';
        if (!$token) $errors[] = 'WA_ACCESS_TOKEN';
        if (!$to) $errors[] = 'Destinatario';

        if ($mode === 'text' && trim($text) === '') $errors[] = 'Mensaje';
        if ($mode === 'template' && (!$template || !$lang)) $errors[] = 'Template/lang';

        if (!empty($errors)) {
            $opTitle = 'Probar envío';
            $opResult = ['ok' => false, 'error' => 'Faltan datos: ' . implode(', ', $errors)];
            $_SESSION['mensaje'] = [
                'tipo' => 'error',
                'titulo' => 'No se pudo enviar',
                'texto' => $opResult['error'],
            ];
        } else {
            $url = "https://graph.facebook.com/{$graphVersion}/{$phoneNumberId}/messages";
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
            ];

            if ($mode === 'text') {
                $payload['type'] = 'text';
                $payload['text'] = ['body' => $text];
            } else {
                $payload['type'] = 'template';
                $payload['template'] = [
                    'name' => $template,
                    'language' => ['code' => $lang],
                ];
            }

            $resp = wa_curl_json($url, 'POST', [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ], $payload);

            $ok = ($resp['http'] >= 200 && $resp['http'] < 300);

            $opTitle = 'Probar envío';
            $opResult = [
                'ok' => $ok,
                'mode' => $mode,
                'to' => $to,
                'request' => $payload,
                'response' => $resp,
            ];

            $_SESSION['mensaje'] = [
                'tipo' => $ok ? 'success' : 'error',
                'titulo' => $ok ? 'Solicitud enviada' : 'Fallo al enviar',
                'texto' => $ok ? 'Revisa el webhook para el estado final.' : 'Mira el detalle en la salida.',
            ];
        }
    }

    // Evitar re-POST al recargar
    header('Location: ' . wa_base_url() . '/whatsapp');
    exit;
}

// Mensaje pendiente (Swal)
$mensajePendiente = null;
if (isset($_SESSION['mensaje'])) {
    $mensajePendiente = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

$hasToken = !empty($env['WA_ACCESS_TOKEN']);
$hasAppSecret = !empty($env['WA_APP_SECRET']);
?>

<div class="content-wrapper">
  <section class="content-header">
    <h1>WhatsApp <small>Conectar y probar</small></h1>
    <ol class="breadcrumb">
      <li><a href="<?php echo BASE_URL; ?>/inicio"><i class="fa fa-dashboard"></i> Inicio</a></li>
      <li class="active">WhatsApp</li>
    </ol>
  </section>

  <section class="content">

    <div class="box box-primary">
      <div class="box-header with-border">
        <h3 class="box-title">1) Credenciales</h3>
      </div>

      <form method="post" class="form-horizontal">
        <input type="hidden" name="wa_action" value="save">

        <div class="box-body">

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_PHONE_NUMBER_ID</label>
            <div class="col-sm-9">
              <input type="text" name="WA_PHONE_NUMBER_ID" class="form-control" value="<?php echo htmlspecialchars($env['WA_PHONE_NUMBER_ID'] ?? ''); ?>" placeholder="845605275313233">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_WABA_ID</label>
            <div class="col-sm-9">
              <input type="text" name="WA_WABA_ID" class="form-control" value="<?php echo htmlspecialchars($env['WA_WABA_ID'] ?? ''); ?>" placeholder="1175...">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_WEBHOOK_VERIFY_TOKEN</label>
            <div class="col-sm-9">
              <input type="text" name="WA_WEBHOOK_VERIFY_TOKEN" class="form-control" value="<?php echo htmlspecialchars($env['WA_WEBHOOK_VERIFY_TOKEN'] ?? ''); ?>" placeholder="atlantis_webhook_verify_...">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_APP_ID</label>
            <div class="col-sm-9">
              <input type="text" name="WA_APP_ID" class="form-control" value="<?php echo htmlspecialchars($env['WA_APP_ID'] ?? ''); ?>" placeholder="8569...">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_APP_SECRET</label>
            <div class="col-sm-9">
              <input type="password" name="WA_APP_SECRET" class="form-control" value="" placeholder="<?php echo $hasAppSecret ? 'Guardado (dejar vacío para mantener)' : 'Pegar aquí'; ?>">
              <p class="help-block">Si lo dejas vacío, se mantiene el valor existente.</p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">WA_ACCESS_TOKEN</label>
            <div class="col-sm-9">
              <input type="password" name="WA_ACCESS_TOKEN" class="form-control" value="" placeholder="<?php echo $hasToken ? 'Guardado (dejar vacío para mantener)' : 'Pegar aquí'; ?>">
              <p class="help-block">Si lo dejas vacío, se mantiene el valor existente.</p>
            </div>
          </div>

        </div>

        <div class="box-footer">
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>

    <div class="box box-info">
      <div class="box-header with-border">
        <h3 class="box-title">2) Webhooks</h3>
      </div>

      <div class="box-body">
        <div class="form-group">
          <label>Callback URL</label>
          <input type="text" class="form-control" readonly value="<?php echo htmlspecialchars($callbackUrl); ?>">
          <p class="help-block">Este URL es el que se registra en Meta (app-level y WABA-level).</p>
        </div>

        <div class="row">
          <div class="col-sm-6">
            <form method="post">
              <input type="hidden" name="wa_action" value="verify_endpoint">
              <button type="submit" class="btn btn-default">Verificar endpoint</button>
            </form>
          </div>
          <div class="col-sm-6" style="text-align:right;">
            <form method="post">
              <input type="hidden" name="wa_action" value="subscribe">
              <button type="submit" class="btn btn-info">Configurar webhooks</button>
            </form>
          </div>
        </div>
      </div>

      <div class="box-footer">
        <small>Log local: <code>/logs/whatsapp_webhook.log</code></small>
      </div>
    </div>

    <div class="box box-success">
      <div class="box-header with-border">
        <h3 class="box-title">3) Probar envío</h3>
      </div>

      <form method="post" class="form-horizontal">
        <input type="hidden" name="wa_action" value="send_test">
        <div class="box-body">

          <div class="form-group">
            <label class="col-sm-3 control-label">Para (E.164 sin +)</label>
            <div class="col-sm-9">
              <input type="text" name="to" class="form-control" placeholder="519XXXXXXXX" required>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">Modo</label>
            <div class="col-sm-9">
              <select name="mode" class="form-control">
                <option value="template" selected>Template</option>
                <option value="text">Texto</option>
              </select>
              <p class="help-block">Recomendado: Template para iniciar conversación / modo prueba.</p>
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">Template</label>
            <div class="col-sm-5">
              <input type="text" name="template" class="form-control" value="hello_world">
            </div>
            <div class="col-sm-4">
              <input type="text" name="lang" class="form-control" value="en_US">
            </div>
          </div>

          <div class="form-group">
            <label class="col-sm-3 control-label">Texto</label>
            <div class="col-sm-9">
              <input type="text" name="text" class="form-control" placeholder="Hola... (solo si eliges modo Texto)">
            </div>
          </div>

        </div>

        <div class="box-footer">
          <button type="submit" class="btn btn-success">Enviar prueba</button>
        </div>
      </form>
    </div>

  </section>
</div>

<?php
if (isset($mensajePendiente) && $mensajePendiente !== null) {
    $tipo = isset($mensajePendiente['tipo']) ? $mensajePendiente['tipo'] : 'info';
    $titulo = isset($mensajePendiente['titulo']) ? $mensajePendiente['titulo'] : '';
    $texto = isset($mensajePendiente['texto']) ? $mensajePendiente['texto'] : '';
?>
<script>
(function() {
  function mostrarMensaje() {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        icon: '<?php echo $tipo; ?>',
        title: '<?php echo addslashes($titulo); ?>',
        <?php if ($texto): ?>text: '<?php echo addslashes($texto); ?>',<?php endif; ?>
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'OK'
      });
    }
  }
  window.addEventListener('load', function(){ setTimeout(mostrarMensaje, 300); });
})();
</script>
<?php } ?>
