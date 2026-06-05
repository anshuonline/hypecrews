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

// Update last active time for the current admin
if ($admin_id > 0 && isset($pdo)) {
    try {
        $pdo->exec("UPDATE administrators SET last_active = CURRENT_TIMESTAMP WHERE id = $admin_id");
    } catch (PDOException $e) {
        // Ignore errors if DB isn't connected yet or column doesn't exist yet
    }
}
?>