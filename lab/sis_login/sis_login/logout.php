<?php
require 'config.php';

// Logout only works through the POST form (with CSRF token) on the dashboards
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $_SESSION = [];
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    session_destroy();
}
redirect('index.php');
