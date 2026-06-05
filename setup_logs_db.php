<?php
require_once 'config/db.php';

try {
    // Create Admin Logs Table
    $sql = "CREATE TABLE IF NOT EXISTS admin_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NULL,
        admin_username VARCHAR(100) NOT NULL,
        action_type VARCHAR(50) NOT NULL,
        description TEXT NOT NULL,
        ip_address VARCHAR(45) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "admin_logs table created successfully.<br>";
    echo "<h3 style='color:green;'>Logs Database Setup Complete! Ready for Production.</h3>";
    echo "<p>Important: Please delete this file from the server after running it for security reasons.</p>";
    
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
?>
