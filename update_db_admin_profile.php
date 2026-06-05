<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE administrators ADD COLUMN profile_image VARCHAR(255) NULL AFTER password";
    $pdo->exec($sql);
    echo "Added profile_image column to administrators table.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
