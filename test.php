<?php
require 'config/db.php';
try { 
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
    $stmt->execute([1]);
    $messages = $stmt->fetchAll();
    echo 'Success'; 
} catch(PDOException $e) { 
    echo $e->getMessage(); 
}
