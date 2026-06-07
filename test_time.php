<?php
require 'config/db.php';
$stmt = $pdo->query('SELECT id, status, created_at, updated_at, (SELECT COUNT(*) FROM support_chats WHERE session_id = support_sessions.id AND sender_type = "user" AND is_read = 0) as unread FROM support_sessions ORDER BY status ASC, updated_at DESC');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
