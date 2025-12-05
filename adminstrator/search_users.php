<?php
require_once '../config/db.php';

header('Content-Type: application/json');

// Check if search term is provided
if (!isset($_GET['term']) || empty(trim($_GET['term']))) {
    echo json_encode([]);
    exit;
}

$search_term = trim($_GET['term']);

try {
    // Search users by username, email, mobile number, or ID
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, mobile_number FROM users WHERE username LIKE ? OR email LIKE ? OR mobile_number LIKE ? OR id = ? ORDER BY first_name, last_name LIMIT 10");
    $searchTerm = "%{$search_term}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm, is_numeric($search_term) ? $search_term : 0]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode([]);
}
?>