<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE administrators ADD COLUMN last_chat_read TIMESTAMP NULL DEFAULT NULL AFTER profile_image";
    $pdo->exec($sql);
    echo "Added last_chat_read column.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
