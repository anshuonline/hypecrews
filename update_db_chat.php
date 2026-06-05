<?php
require_once 'config/db.php';

try {
    // Create admin_chats table
    $sql = "CREATE TABLE IF NOT EXISTS admin_chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES administrators(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES administrators(id) ON DELETE CASCADE
    )";
    
    $pdo->exec($sql);
    echo "admin_chats table created successfully.<br>";
    echo "<h3 style='color:green;'>Chat Database Setup Complete! Ready for Production.</h3>";
    echo "<p>Important: Please delete this file from the server after running it for security reasons.</p>";
    
} catch (PDOException $e) {
    echo "Error setting up chat database: " . $e->getMessage();
}
?>
