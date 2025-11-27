<?php
// Ensure session is started for read and then closed to avoid session file locking
if (session_status() == PHP_SESSION_NONE) {
    // use read_and_close when possible to prevent blocking other concurrent AJAX calls
    if (version_compare(PHP_VERSION, '7.0.0', '>=')) {
        session_start(['read_and_close' => true]);
    } else {
        @session_start();
        // Immediately close write access to session to reduce lock time
        if (function_exists('session_write_close')) {
            session_write_close();
        }
    }
}

// Force application timezone for AJAX requests
date_default_timezone_set('America/Lima');
