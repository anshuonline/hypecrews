<?php
require 'config/db.php';
try {
    $stmt = $pdo->prepare("SELECT c.*, CASE WHEN c.sender_type = 'user' THEN (SELECT username FROM users WHERE id = c.sender_id) ELSE (SELECT username FROM administrators WHERE id = c.sender_id) END as sender_name, CASE WHEN c.sender_type = 'user' THEN NULL ELSE (SELECT profile_image FROM administrators WHERE id = c.sender_id) END as sender_avatar FROM support_chats c WHERE c.session_id = 6 ORDER BY c.created_at ASC");
    $stmt->execute();
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch(Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
