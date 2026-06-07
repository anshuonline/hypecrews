<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit();
}

$admin_id = $_SESSION['admin_id'];
$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($action === 'list_threads') {
    // Get list of users who have chat messages
    try {
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.first_name, u.last_name, u.profile_image, 
            (SELECT message FROM support_chats WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT created_at FROM support_chats WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_activity,
            (SELECT COUNT(*) FROM support_chats WHERE user_id = u.id AND sender_type = 'user' AND is_read = 0) as unread_count
            FROM users u
            WHERE EXISTS (SELECT 1 FROM support_chats WHERE user_id = u.id)
            ORDER BY last_activity DESC
        ");
        $stmt->execute();
        $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($threads as &$t) {
            $t['time_formatted'] = date('M d, h:i A', strtotime($t['last_activity']));
        }
        
        echo json_encode(['status' => 'success', 'data' => $threads]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($action === 'get_messages') {
    $user_id = $_GET['user_id'] ?? 0;
    
    try {
        // Mark as read
        $pdo->prepare("UPDATE support_chats SET is_read = 1 WHERE user_id = ? AND sender_type = 'user' AND is_read = 0")
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
        
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_type'] === 'admin');
        }
        
        echo json_encode(['status' => 'success', 'data' => $messages]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'send_message') {
    $user_id = $_POST['user_id'] ?? 0;
    $message = trim($_POST['message'] ?? '');
    
    if (empty($message) || empty($user_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO support_chats (user_id, sender_type, sender_id, message) VALUES (?, 'admin', ?, ?)");
        $stmt->execute([$user_id, $admin_id, $message]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
