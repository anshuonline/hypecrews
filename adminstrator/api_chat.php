<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
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
        
        // Update last read timestamp and last active status
        $pdo->exec("UPDATE administrators SET last_chat_read = CURRENT_TIMESTAMP, last_active = CURRENT_TIMESTAMP WHERE id = $admin_id");
        
        // Check if partner is online
        $chat_partner_online = false;
        $chat_with = isset($_GET['chat_with']) ? $_GET['chat_with'] : 'group';
        if ($chat_with !== 'group') {
            $stmt = $pdo->prepare("SELECT last_active FROM administrators WHERE id = ?");
            $stmt->execute([(int)$chat_with]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($partner && !empty($partner['last_active'])) {
                $last_active = strtotime($partner['last_active']);
                if (time() - $last_active <= 30) {
                    $chat_partner_online = true;
                }
            }
        }

        echo json_encode(['status' => 'success', 'data' => $messages, 'partner_online' => $chat_partner_online]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'send';
    
    if ($action === 'pin') {
        $message_id = (int)$_POST['message_id'];
        try {
            // Toggle pin status
            $stmt = $pdo->prepare("UPDATE admin_chats SET is_pinned = NOT is_pinned WHERE id = ?");
            $stmt->execute([$message_id]);
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($action === 'delete') {
        $message_id = (int)$_POST['message_id'];
        try {
            $stmt = $pdo->prepare("SELECT sender_id, created_at FROM admin_chats WHERE id = ?");
            $stmt->execute([$message_id]);
            $msg = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($msg && $msg['sender_id'] == $admin_id) {
                $created_time = strtotime($msg['created_at']);
                $current_time = time();
                $diff = $current_time - $created_time;
                
                // 10 seconds limit for testing
                if ($diff <= 10) {
                    $del_stmt = $pdo->prepare("UPDATE admin_chats SET is_deleted = 1, message = '', image_url = NULL, meeting_time = NULL, meeting_link = NULL, is_pinned = 0 WHERE id = ?");
                    $del_stmt->execute([$message_id]);
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Time limit exceeded (10 seconds)']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    $chat_with = isset($_POST['chat_with']) ? $_POST['chat_with'] : 'group';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    $meeting_time = !empty($_POST['meeting_time']) ? $_POST['meeting_time'] : null;
    $meeting_link = !empty($_POST['meeting_link']) ? $_POST['meeting_link'] : null;
    $image_url = null;
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $max_size = 2 * 1024 * 1024; // 2MB
        if ($file['size'] > $max_size) {
            echo json_encode(['status' => 'error', 'message' => 'Image exceeds 2MB limit']);
            exit;
        }
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid image format']);
            exit;
        }
        $upload_dir = '../uploads/chat_images/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $new_name = uniqid('chat_') . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $upload_dir . $new_name)) {
            $image_url = 'uploads/chat_images/' . $new_name;
        }
    }
    
    if (empty($message) && empty($image_url) && empty($meeting_time)) {
        echo json_encode(['status' => 'error', 'message' => 'Empty message']);
        exit;
    }
    
    try {
        if ($chat_with === 'group') {
            $stmt = $pdo->prepare("INSERT INTO admin_chats (sender_id, receiver_id, message, image_url, meeting_time, meeting_link) VALUES (?, NULL, ?, ?, ?, ?)");
            $stmt->execute([$admin_id, $message, $image_url, $meeting_time, $meeting_link]);
        } else {
            $receiver_id = (int)$chat_with;
            $stmt = $pdo->prepare("INSERT INTO admin_chats (sender_id, receiver_id, message, image_url, meeting_time, meeting_link) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$admin_id, $receiver_id, $message, $image_url, $meeting_time, $meeting_link]);
        }
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
