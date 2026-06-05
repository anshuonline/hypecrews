<?php
// adminstrator/components/logger.php

function logAdminActivity($pdo, $action_type, $description) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if an admin is logged in
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;
    $admin_username = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Administrator';
    
    // Get IP Address
    $ip_address = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, admin_username, action_type, description, ip_address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$admin_id, $admin_username, $action_type, $description, $ip_address]);
        return true;
    } catch (PDOException $e) {
        // Silently fail or log to file if DB fails
        error_log("Failed to insert admin log: " . $e->getMessage());
        return false;
    }
}
?>
