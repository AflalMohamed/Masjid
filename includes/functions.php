<?php
// includes/functions.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user']);
}

function require_login($role = null) {
    if (!is_logged_in()) {
        header('Location: /masjid-management/auth/login.php');
        exit;
    }
    if ($role && $_SESSION['user']['role'] !== $role) {
        // not allowed
        header('HTTP/1.1 403 Forbidden');
        echo "403 Forbidden";
        exit;
    }
}

function flash($key, $message = null) {
    if ($message === null) {
        // get
        if (isset($_SESSION['flash'][$key])) {
            $m = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $m;
        }
        return null;
    }
    // set
    $_SESSION['flash'][$key] = $message;
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
?>
