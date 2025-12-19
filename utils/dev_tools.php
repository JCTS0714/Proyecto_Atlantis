<?php

if (!function_exists('is_dev_user')) {
    function is_dev_user(): bool {
        // Solo habilitado para el usuario con id=28
        return isset($_SESSION['id']) && (int)$_SESSION['id'] === 28;
    }
}

