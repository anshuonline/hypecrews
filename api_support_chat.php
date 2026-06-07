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
    $action = $_POST['action'] ?? 'send_message';
    
    if ($action === 'export_session') {
        $session_id = $_POST['session_id'] ?? 0;
        try {
            $pdo->prepare("UPDATE support_sessions SET user_exported = 1, exported_at = COALESCE(exported_at, CURRENT_TIMESTAMP) WHERE id = ? AND user_id = ?")->execute([$session_id, $user_id]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        exit();
    }
    
    if ($action === 'dismiss_session') {
        $session_id = $_POST['session_id'] ?? 0;
        try {
            $pdo->prepare("UPDATE support_sessions SET status = 'archived', exported_at = COALESCE(exported_at, CURRENT_TIMESTAMP) WHERE id = ? AND user_id = ?")->execute([$session_id, $user_id]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        exit();
    }
    
    if ($action === 'reopen_session') {
        $session_id = $_POST['session_id'] ?? 0;
        try {
            $pdo->prepare("UPDATE support_sessions SET status = 'open', exported_at = NULL WHERE id = ? AND user_id = ?")->execute([$session_id, $user_id]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error']);
        }
        exit();
    }
    
    $message = trim($_POST['message'] ?? '');
    $attachment_path = null;
    
    // Handle attachment
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if ($file['size'] > 2 * 1024 * 1024) {
            echo json_encode(['status' => 'error', 'message' => 'Image size must be less than 2MB']);
            exit();
        }
        
        if (!in_array($file['type'], $allowed_types)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.']);
            exit();
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('chat_') . '.' . $ext;
        $upload_dir = 'uploads/chat_attachments/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            $attachment_path = $upload_dir . $filename;
        }
    }
    
    if (empty($message) && !$attachment_path) {
        echo json_encode(['status' => 'error', 'message' => 'Message and attachment cannot both be empty']);
        exit();
    }
    
    try {
        // Find active session
        $stmt = $pdo->prepare("SELECT id FROM support_sessions WHERE user_id = ? AND status = 'open' ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$user_id]);
        $session = $stmt->fetch();
        
        if (!$session) {
            echo json_encode(['status' => 'error', 'message' => 'No active session']);
            exit();
        }
        
        $stmt = $pdo->prepare("INSERT INTO support_chats (user_id, sender_type, sender_id, message, session_id, attachment) VALUES (?, 'user', ?, ?, ?, ?)");
        $stmt->execute([$user_id, $user_id, $message, $session['id'], $attachment_path]);
        
        // Update session timestamp
        $pdo->prepare("UPDATE support_sessions SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$session['id']]);
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $session_id = $_GET['session_id'] ?? null;
        
        if ($session_id) {
            $stmt = $pdo->prepare("SELECT id FROM support_sessions WHERE id = ? AND user_id = ?");
            $stmt->execute([$session_id, $user_id]);
            $session = $stmt->fetch();
        } else {
            // Find active session
            $stmt = $pdo->prepare("SELECT id FROM support_sessions WHERE user_id = ? AND status = 'open' ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$user_id]);
            $session = $stmt->fetch();
        }
        
        if (!$session) {
            echo json_encode(['status' => 'success', 'data' => []]);
            exit();
        }
        
        // Mark admin messages as read by the user
        $pdo->prepare("UPDATE support_chats SET is_read = 1 WHERE session_id = ? AND sender_type = 'admin' AND is_read = 0")
            ->execute([$session['id']]);
            
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
            WHERE c.session_id = ? 
            ORDER BY c.created_at ASC");
        $stmt->execute([$session['id']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format time
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_type'] === 'user');
        }
        
        // Fetch full session info
        $stmt = $pdo->prepare("SELECT status FROM support_sessions WHERE id = ?");
        $stmt->execute([$session['id']]);
        $session_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $messages, 'session' => $session_info]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}
