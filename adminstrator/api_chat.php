<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$admin_id = $_SESSION['admin_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $chat_with = isset($_GET['chat_with']) ? $_GET['chat_with'] : 'group';
    
    try {
        if ($chat_with === 'group') {
            $stmt = $pdo->prepare("
                SELECT c.*, a.username, a.profile_image 
                FROM admin_chats c 
                JOIN administrators a ON c.sender_id = a.id 
                WHERE c.receiver_id IS NULL 
                ORDER BY c.created_at ASC
            ");
            $stmt->execute();
        } else {
            $other_id = (int)$chat_with;
            $stmt = $pdo->prepare("
                SELECT c.*, a.username, a.profile_image 
                FROM admin_chats c 
                JOIN administrators a ON c.sender_id = a.id 
                WHERE (c.sender_id = ? AND c.receiver_id = ?) 
                   OR (c.sender_id = ? AND c.receiver_id = ?) 
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$admin_id, $other_id, $other_id, $admin_id]);
        }
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format timestamps
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_id'] == $admin_id);
            if (empty($msg['profile_image'])) {
                $msg['initial'] = substr($msg['username'], 0, 1);
            }
        }
        
        echo json_encode(['status' => 'success', 'data' => $messages]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $chat_with = isset($_POST['chat_with']) ? $_POST['chat_with'] : 'group';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($message)) {
        echo json_encode(['status' => 'error', 'message' => 'Empty message']);
        exit;
    }
    
    try {
        if ($chat_with === 'group') {
            $stmt = $pdo->prepare("INSERT INTO admin_chats (sender_id, receiver_id, message) VALUES (?, NULL, ?)");
            $stmt->execute([$admin_id, $message]);
        } else {
            $receiver_id = (int)$chat_with;
            $stmt = $pdo->prepare("INSERT INTO admin_chats (sender_id, receiver_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$admin_id, $receiver_id, $message]);
        }
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
