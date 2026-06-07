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
    $filter = $_GET['filter'] ?? 'all';
    $whereClause = "";
    if ($filter === 'open') {
        $whereClause = "WHERE s.status = 'open'";
    } else if ($filter === 'resolved') {
        $whereClause = "WHERE s.status = 'resolved'";
    }
    
    // Get list of support sessions
    try {
        $stmt = $pdo->prepare("
            SELECT s.id as session_id, s.topic, s.urgency, s.status, s.updated_at as last_activity,
            u.id as user_id, u.username, u.first_name, u.last_name, 
            (SELECT message FROM support_chats WHERE session_id = s.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT COUNT(*) FROM support_chats WHERE session_id = s.id AND sender_type = 'user' AND is_read = 0) as unread_count
            FROM support_sessions s
            JOIN users u ON s.user_id = u.id
            $whereClause
            ORDER BY s.status ASC, s.id DESC
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
    $session_id = $_GET['session_id'] ?? 0;
    
    try {
        // Mark as read
        $pdo->prepare("UPDATE support_chats SET is_read = 1 WHERE session_id = ? AND sender_type = 'user' AND is_read = 0")
            ->execute([$session_id]);
            
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
        $stmt->execute([$session_id]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_type'] === 'admin');
        }
        
        // Also fetch session details
        $stmt = $pdo->prepare("SELECT * FROM support_sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $messages, 'session' => $session]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'send_message') {
    $session_id = $_POST['session_id'] ?? 0;
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
        $filename = uniqid('chat_admin_') . '.' . $ext;
        // Upload path relative to root
        $upload_dir = '../uploads/chat_attachments/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
            $attachment_path = 'uploads/chat_attachments/' . $filename;
        }
    }
    
    if (empty($message) && empty($session_id) && !$attachment_path) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }
    
    try {
        // Get user_id for this session
        $stmt = $pdo->prepare("SELECT user_id, status FROM support_sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        if (!$session || $session['status'] === 'resolved') {
            echo json_encode(['status' => 'error', 'message' => 'Session is closed or invalid']);
            exit();
        }
        
        $stmt = $pdo->prepare("INSERT INTO support_chats (user_id, sender_type, sender_id, message, session_id, attachment) VALUES (?, 'admin', ?, ?, ?, ?)");
        $stmt->execute([$session['user_id'], $admin_id, $message, $session_id, $attachment_path]);
        
        // Update session timestamp
        $pdo->prepare("UPDATE support_sessions SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$session_id]);
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'resolve_session') {
    $session_id = $_POST['session_id'] ?? 0;
    if (empty($session_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        exit();
    }
    
    try {
        $pdo->prepare("UPDATE support_sessions SET status = 'resolved', updated_at = CURRENT_TIMESTAMP WHERE id = ?")->execute([$session_id]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'export_session') {
    $session_id = $_POST['session_id'] ?? 0;
    try {
        $pdo->prepare("UPDATE support_sessions SET exported_at = CURRENT_TIMESTAMP WHERE id = ? AND exported_at IS NULL")->execute([$session_id]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
