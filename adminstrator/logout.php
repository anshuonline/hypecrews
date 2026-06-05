<?php
session_start();

require_once '../config/db.php';
require_once 'components/logger.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    logAdminActivity($pdo, 'LOGOUT', 'Admin logged out.');
}

session_destroy();
header('Location: login.php');
exit;
?>