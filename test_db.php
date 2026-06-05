<?php
require 'config/db.php';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM softwares");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
