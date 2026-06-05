<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE admin_chats 
            ADD COLUMN image_url VARCHAR(255) NULL AFTER message,
            ADD COLUMN meeting_time DATETIME NULL AFTER image_url,
            ADD COLUMN meeting_link VARCHAR(255) NULL AFTER meeting_time,
            ADD COLUMN is_pinned TINYINT(1) DEFAULT 0 AFTER meeting_link";
            
    $pdo->exec($sql);
    echo "Chat features columns added successfully.";
} catch (PDOException $e) {
    // If columns already exist, this will error, which is fine to ignore or handle
    echo "Error or already exists: " . $e->getMessage();
}
?>
