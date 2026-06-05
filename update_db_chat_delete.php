<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE admin_chats ADD COLUMN is_deleted TINYINT(1) DEFAULT 0 AFTER is_pinned";
    $pdo->exec($sql);
    echo "Column is_deleted added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
