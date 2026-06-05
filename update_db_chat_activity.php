<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE administrators ADD COLUMN last_active DATETIME NULL AFTER last_chat_read";
    $pdo->exec($sql);
    echo "Column last_active added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
