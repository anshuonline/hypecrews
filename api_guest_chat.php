<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

// Check token from headers or POST body
$session_token = $_SERVER['HTTP_X_SESSION_TOKEN'] ?? $_POST['session_token'] ?? $_GET['session_token'] ?? null;

$action = $_POST['action'] ?? $_GET['action'] ?? 'get_messages';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Create new guest session
    if ($action === 'create_session') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $topic = trim($_POST['topic'] ?? 'General Inquiry');
        $urgency = trim($_POST['urgency'] ?? 'normal');
        
        if (empty($name) || empty($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Name and Email are required']);
            exit;
        }
        
        $token = bin2hex(random_bytes(32));
        
        try {
            $stmt = $pdo->prepare("INSERT INTO support_sessions (session_token, guest_name, guest_email, guest_phone, topic, urgency) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$token, $name, $email, $phone, $topic, $urgency]);
            $session_id = $pdo->lastInsertId();
            
            // Insert initial welcome message
            $welcome = "Hi $name! Thanks for reaching out. An admin will join the chat shortly.";
            $pdo->prepare("INSERT INTO support_chats (session_id, sender_type, message) VALUES (?, 'system', ?)")
                ->execute([$session_id, $welcome]);
                
            echo json_encode(['status' => 'success', 'token' => $token, 'session_id' => $session_id]);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    // All other POST actions require a token
    if (!$session_token) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, status FROM support_sessions WHERE session_token = ?");
        $stmt->execute([$session_token]);
        $session = $stmt->fetch();
        
        if (!$session) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            exit;
        }
        
        if ($action === 'send_message') {
            if ($session['status'] !== 'open') {
                echo json_encode(['status' => 'error', 'message' => 'Chat is closed']);
                exit;
            }
            
            $message = trim($_POST['message'] ?? '');
            
            if (empty($message)) {
                echo json_encode(['status' => 'error', 'message' => 'Empty message']);
                exit;
            }
            
            $pdo->prepare("INSERT INTO support_chats (session_id, sender_type, message) VALUES (?, 'user', ?)")
                ->execute([$session['id'], $message]);
                
            $pdo->prepare("UPDATE support_sessions SET updated_at = CURRENT_TIMESTAMP WHERE id = ?")
                ->execute([$session['id']]);
                
            echo json_encode(['status' => 'success']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$session_token) {
        echo json_encode(['status' => 'success', 'data' => []]);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, status FROM support_sessions WHERE session_token = ?");
        $stmt->execute([$session_token]);
        $session = $stmt->fetch();
        
        if (!$session) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid session']);
            exit;
        }
        
        // Mark admin messages as read
        $pdo->prepare("UPDATE support_chats SET is_read = 1 WHERE session_id = ? AND sender_type = 'admin' AND is_read = 0")
            ->execute([$session['id']]);
            
        $stmt = $pdo->prepare("SELECT c.*, 
            CASE 
                WHEN c.sender_type = 'admin' THEN (SELECT username FROM administrators WHERE id = c.sender_id)
                WHEN c.sender_type = 'user' THEN (SELECT guest_name FROM support_sessions WHERE id = c.session_id)
            END as sender_name,
            CASE 
                WHEN c.sender_type = 'admin' THEN (SELECT profile_image FROM administrators WHERE id = c.sender_id)
                ELSE NULL
            END as sender_avatar
            FROM support_chats c 
            WHERE c.session_id = ? 
            ORDER BY c.created_at ASC");
        $stmt->execute([$session['id']]);
        $messages = $stmt->fetchAll();
        
        // Format
        foreach ($messages as &$msg) {
            $msg['time'] = date('h:i A', strtotime($msg['created_at']));
            $msg['is_mine'] = ($msg['sender_type'] === 'user');
        }
        
        echo json_encode([
            'status' => 'success', 
            'data' => $messages,
            'session' => [
                'status' => $session['status']
            ]
        ]);
        
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}
