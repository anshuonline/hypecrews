<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get admin info
$admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Administrator';
$admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : 0;
?>