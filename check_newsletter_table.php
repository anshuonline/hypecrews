<?php
require_once 'config/db.php';

try {
    // Check if table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'newsletter_subscriptions'");
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Table doesn't exist, create it
        $createTable = "
            CREATE TABLE newsletter_subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45),
                user_agent TEXT
            )
        ";
        
        $pdo->exec($createTable);
        echo "Table 'newsletter_subscriptions' created successfully.";
    } else {
        echo "Table 'newsletter_subscriptions' already exists.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>