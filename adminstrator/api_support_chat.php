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
    $search = $_GET['search'] ?? '';
    
    $whereClause = "WHERE 1=1";
    if ($filter === 'open') {
        $whereClause .= " AND s.status = 'open'";
    } else if ($filter === 'resolved') {
        $whereClause .= " AND s.status = 'resolved'";
    } else if ($filter === 'mine') {
        $whereClause .= " AND s.assigned_admin_id = ?";
    }
    
    $params = [];
    if ($filter === 'mine') {
        $params[] = $admin_id;
    }
    if (!empty($search)) {
        if (strpos($search, '#') === 0 || is_numeric($search)) {
            // Search by ticket ID
            $searchId = ltrim($search, '#');
            $whereClause .= " AND s.id = ?";
            $params[] = $searchId;
        } else {
            // Search by name
            $whereClause .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.username LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    }
    
    // Get list of support sessions
    try {
        $stmt = $pdo->prepare("
            SELECT s.id as session_id, s.topic, s.urgency, s.status, s.updated_at as last_activity, s.assigned_admin_id,
            u.id as user_id, u.username, u.first_name, u.last_name, u.email,
            a.username as assigned_admin_name,
            (SELECT message FROM support_chats WHERE session_id = s.id ORDER BY created_at DESC LIMIT 1) as last_message,
            (SELECT COUNT(*) FROM support_chats WHERE session_id = s.id AND sender_type = 'user' AND is_read = 0) as unread_count
            FROM support_sessions s
            JOIN users u ON s.user_id = u.id
            LEFT JOIN administrators a ON s.assigned_admin_id = a.id
            $whereClause
            ORDER BY CASE WHEN s.status = 'open' THEN 1 ELSE 2 END ASC, unread_count DESC, s.updated_at DESC, s.id DESC
        ");
        $stmt->execute($params);
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
    try {
        $pdo->prepare("UPDATE support_sessions SET status = 'resolved' WHERE id = ?")->execute([$session_id]);
        echo json_encode(['status' => 'success', 'message' => 'Session resolved']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'assign_session') {
    $session_id = $_POST['session_id'] ?? 0;
    try {
        $pdo->prepare("UPDATE support_sessions SET assigned_admin_id = ? WHERE id = ?")->execute([$admin_id, $session_id]);
        
        // Fetch session's user_id and admin's username to insert a system message
        $stmt = $pdo->prepare("SELECT user_id FROM support_sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $sess = $stmt->fetch();
        
        $a_stmt = $pdo->prepare("SELECT username FROM administrators WHERE id = ?");
        $a_stmt->execute([$admin_id]);
        $adm = $a_stmt->fetch();
        
        if ($sess && $adm) {
            $msg = "Admin " . $adm['username'] . " has joined the chat and will assist you.";
            $pdo->prepare("INSERT INTO support_chats (session_id, user_id, sender_type, sender_id, message) VALUES (?, ?, 'system', 0, ?)")
                ->execute([$session_id, $sess['user_id'], $msg]);
        }
        
        echo json_encode(['status' => 'success', 'message' => 'Session assigned successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reopen_session') {
    $session_id = $_POST['session_id'] ?? 0;
    try {
        // Check if user exported it already
        $stmt = $pdo->prepare("SELECT user_exported FROM support_sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        if ($session && $session['user_exported']) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot reopen: User has already exported and closed this chat.']);
        } else {
            $pdo->prepare("UPDATE support_sessions SET status = 'open' WHERE id = ?")->execute([$session_id]);
            echo json_encode(['status' => 'success', 'message' => 'Session reopened successfully']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($action === 'get_user_profile') {
    $user_id = $_GET['user_id'] ?? 0;
    
    try {
        // Get User Basic Info
        $u_stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, ip_address, last_active_at, mobile_number, company_name, country FROM users WHERE id = ?");
        $u_stmt->execute([$user_id]);
        $user = $u_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
            exit();
        }
        
        // Determine Active Status
        $is_active = false;
        if ($user['last_active_at']) {
            $last_active = strtotime($user['last_active_at']);
            if (time() - $last_active < 300) { // 5 minutes
                $is_active = true;
            }
        }
        
        // Fetch Location from IP
        $location = 'Unknown';
        if (!empty($user['ip_address']) && filter_var($user['ip_address'], FILTER_VALIDATE_IP)) {
            // Check if it's a local IP
            if ($user['ip_address'] === '::1' || $user['ip_address'] === '127.0.0.1') {
                $location = 'Localhost';
            } else {
                $ch = curl_init("http://ip-api.com/json/" . $user['ip_address']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                $response = curl_exec($ch);
                curl_close($ch);
                
                if ($response) {
                    $geo = json_decode($response, true);
                    if ($geo && $geo['status'] === 'success') {
                        $location = $geo['city'] . ', ' . $geo['country'];
                    }
                }
            }
        }
        
        // Fetch Order History
        $stmt = $pdo->prepare("SELECT id, order_title, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Past Chats
        $stmt = $pdo->prepare("SELECT id, topic, status, created_at FROM support_sessions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
        $stmt->execute([$user_id]);
        $past_chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch Admin Notes
        $stmt = $pdo->prepare("
            SELECT n.id, n.note, n.created_at, a.username as admin_name 
            FROM user_notes n 
            JOIN administrators a ON n.admin_id = a.id 
            WHERE n.user_id = ? 
            ORDER BY n.created_at DESC
        ");
        $stmt->execute([$user_id]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'phone' => $user['mobile_number'] ?? 'N/A',
                    'company' => $user['company_name'] ?? 'N/A',
                    'country' => $user['country'],
                    'ip_address' => $user['ip_address'] ?? 'N/A',
                    'location' => $location,
                    'is_active' => $is_active,
                    'last_active' => $user['last_active_at'] ? date('M d, Y h:i A', strtotime($user['last_active_at'])) : 'Never'
                ],
                'orders' => $orders,
                'past_chats' => $past_chats,
                'notes' => $notes
            ]
        ]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add_user_note') {
    $user_id = $_POST['user_id'] ?? 0;
    $note = trim($_POST['note'] ?? '');
    
    if (empty($note)) {
        echo json_encode(['status' => 'error', 'message' => 'Note cannot be empty']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO user_notes (user_id, admin_id, note) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $admin_id, $note]);
        
        echo json_encode(['status' => 'success', 'message' => 'Note added successfully']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'export_session') {
    $session_id = $_POST['session_id'] ?? 0;
    try {
        $pdo->prepare("UPDATE support_sessions SET admin_exported = 1, exported_at = COALESCE(exported_at, CURRENT_TIMESTAMP) WHERE id = ?")->execute([$session_id]);
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
?>
