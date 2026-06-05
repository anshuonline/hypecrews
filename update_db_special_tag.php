<?php
require_once 'config/db.php';

try {
    $sql = "ALTER TABLE administrators ADD COLUMN special_tag VARCHAR(50) NULL AFTER username";
    $pdo->exec($sql);
    echo "Column special_tag added successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
