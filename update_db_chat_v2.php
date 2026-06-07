<?php
require_once 'config/db.php';

try {
    // Add attachment to support_chats
    try {
        $pdo->exec("ALTER TABLE support_chats ADD COLUMN attachment VARCHAR(255) NULL AFTER message");
        echo "Added attachment to support_chats.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "attachment column already exists.<br>";
        } else {
            throw $e;
        }
    }
    
    // Add exported_at to support_sessions
    try {
        $pdo->exec("ALTER TABLE support_sessions ADD COLUMN exported_at TIMESTAMP NULL AFTER status");
        echo "Added exported_at to support_sessions.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "exported_at column already exists.<br>";
        } else {
            throw $e;
        }
    }
    
    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads/chat_attachments')) {
        mkdir('uploads/chat_attachments', 0777, true);
        echo "Created uploads/chat_attachments directory.<br>";
    }
    
    echo "Database updated successfully.";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
