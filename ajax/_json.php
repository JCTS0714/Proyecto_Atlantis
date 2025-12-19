<?php

/**
 * Helpers for consistent JSON responses across ajax/* endpoints.
 * Contract (new):
 *   - ok: boolean
 *   - data: mixed
 *   - message?: string
 *   - error?: { code: string, message: string }
 * Compatibility (legacy):
 *   - success: boolean
 *   - status: "success"|"error"|...
 */

if (!function_exists('ajax_json_normalize')) {
    function ajax_json_normalize($data, $ok = null, $httpStatus = null) {
        $computedOk = $ok;

        if ($computedOk === null) {
            if (is_array($data) && array_key_exists('ok', $data)) {
                $computedOk = (bool)$data['ok'];
            } elseif (is_array($data) && array_key_exists('success', $data)) {
                $computedOk = (bool)$data['success'];
            } elseif (is_array($data) && array_key_exists('status', $data)) {
                $st = is_string($data['status']) ? strtolower(trim($data['status'])) : '';
                if ($st === 'success' || $st === 'ok') $computedOk = true;
                elseif ($st === 'error') $computedOk = false;
            }
        }

        if ($computedOk === null && $httpStatus !== null) {
            // Infer ok from HTTP status when caller forgets to pass $ok
            $computedOk = ($httpStatus < 400);
        }

        if ($computedOk === null) {
            // Default: assume ok when no explicit signal exists
            $computedOk = true;
        }

        $response = array(
            'ok' => (bool)$computedOk,
            'data' => $data,
        );

        // If payload is an associative array (not a list), expose its keys at top-level
        // for backwards compatibility with existing JS expecting fields like response.correlativo.
        if (is_array($data)) {
            $isList = array_keys($data) === range(0, count($data) - 1);
            if (!$isList) {
                $skipKeys = array('raw_data', 'debug', 'debug_muestra', 'query_debug');
                foreach ($data as $k => $v) {
                    if (in_array($k, $skipKeys, true)) continue;
                    if ($k === 'ok' || $k === 'data') continue;
                    if (!array_key_exists($k, $response)) {
                        $response[$k] = $v;
                    }
                }
            }
        }

        // Legacy compatibility fields
        if (is_array($data)) {
            if (array_key_exists('success', $data)) $response['success'] = (bool)$data['success'];
            if (array_key_exists('status', $data)) $response['status'] = $data['status'];
            if (array_key_exists('message', $data)) $response['message'] = $data['message'];
        }

        if (!array_key_exists('success', $response)) {
            $response['success'] = (bool)$response['ok'];
        }

        if (!array_key_exists('status', $response)) {
            $response['status'] = $response['ok'] ? 'success' : 'error';
        }

        if ($response['ok'] === false) {
            $msg = null;
            if (isset($response['message']) && is_string($response['message']) && $response['message'] !== '') {
                $msg = $response['message'];
            }

            if ($msg === null && is_array($data) && isset($data['error']) && is_string($data['error'])) {
                $msg = $data['error'];
            }

            if ($msg === null) $msg = 'Error';

            if (!isset($response['error']) || !is_array($response['error'])) {
                $response['error'] = array(
                    'code' => ($httpStatus === 401) ? 'AUTH_REQUIRED' : 'ERROR',
                    'message' => $msg,
                );
            }

            if (!isset($response['message'])) {
                $response['message'] = $msg;
            }
        }

        return $response;
    }
}

if (!function_exists('ajax_json_error')) {
    function ajax_json_error($code, $message, $data = null) {
        $payload = array(
            'ok' => false,
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'error' => array(
                'code' => $code,
                'message' => $message,
            ),
            'data' => $data,
        );
        return $payload;
    }
}

if (!function_exists('ajax_json_respond')) {
    function ajax_json_respond($payload, $httpStatus = 200) {
        if (!headers_sent()) {
            http_response_code($httpStatus);
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        }
        echo json_encode($payload);
        exit;
    }
}

?>
