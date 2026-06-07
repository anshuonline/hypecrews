<?php
require 'config/db.php';
$pdo->query("INSERT IGNORE INTO users (id, username, email) VALUES (1, 'test', 'test@example.com')");
$pdo->query("INSERT INTO support_sessions (id, user_id, topic, urgency, status) VALUES (6, 1, 'General', 'normal', 'open') ON DUPLICATE KEY UPDATE status='open'");
$pdo->query("INSERT INTO support_chats (session_id, user_id, sender_type, sender_id, message) VALUES (6, 1, 'user', 1, 'Hello')");
echo "Inserted";
?>
