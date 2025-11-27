<?php
// Common AJAX bootstrap: session, JSON header, and exception handler
if (session_status() == PHP_SESSION_NONE) {
    @session_start();
}

// Force JSON responses for AJAX endpoints
if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');

// Exception handler: log and return JSON with 500 status
set_exception_handler(function($e){
    error_log("AJAX Exception: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    if (!headers_sent()) http_response_code(500);
    echo json_encode(["success" => false, "error" => "Internal server error", "message" => $e->getMessage()]);
    exit;
});

// Error handler convert warnings/notices to exceptions for consistent handling
set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Shutdown handler to catch fatal errors
register_shutdown_function(function() {
    $err = error_get_last();
    if ($err && ($err['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR))) {
        error_log("AJAX Fatal: " . print_r($err, true));
        if (!headers_sent()) http_response_code(500);
        echo json_encode(["success" => false, "error" => "Fatal error on server", "details" => $err]);
        exit;
    }
});

?>
