<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Send a message
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Message is empty']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO support_chats (user_id, sender_type, sender_id, message) VALUES (?, 'user', ?, ?)");
        $stmt->execute([$user_id, $user_id, $message]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch messages
    try {
        // Mark admin messages as read by the user
        $pdo->prepare("UPDATE support_chats SET is_read = 1 WHERE user_id = ? AND sender_type = 'admin' AND is_read = 0")
            ->execute([$user_id]);
            
        $stmt = $pdo->prepare("SELECT c.*, 
            CASE 
                WHEN c.sender_type = 'user' THEN (SELECT username FROM users WHERE id = c.sender_id)
                ELSE (SELECT username FROM administrators WHERE id = c.sender_id)
            END as sender_name,
            CASE 
                WHEN c.sender_type = 'user' THEN NULL
                ELSE (SELECT profile_image FROM administrators WHERE id = c.sender_id)
            END as sender_avatar
            FROM support_chats c 
            WHERE c.user_id = ? 
            ORDER BY c.created_at ASC");
        $stmt->execute([$user_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format time
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_type'] === 'user');
        }
        
        echo json_encode(['status' => 'success', 'data' => $messages]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}
