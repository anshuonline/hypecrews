<?php
require_once 'config/db.php';

try {
    // Check if users table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'users'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "SUCCESS: Users table exists in the database.\n";
    } else {
        echo "ERROR: Users table does not exist in the database.\n";
        echo "Please run setup_db.php to create the necessary tables.\n";
    }
    
    // Check if admins table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'admins'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "SUCCESS: Admins table exists in the database.\n";
    } else {
        echo "WARNING: Admins table does not exist in the database.\n";
    }
    
} catch(PDOException $e) {
    echo "DATABASE CONNECTION ERROR: " . $e->getMessage() . "\n";
}
?>