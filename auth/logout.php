<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Destroy session and redirect
session_start();
session_unset();
session_destroy();

header('Location: ../auth/login.php');
exit;
