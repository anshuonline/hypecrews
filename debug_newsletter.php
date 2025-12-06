<?php
// Debug script for newsletter subscription
echo "<h2>Newsletter Debug Script</h2>";

// Test database connection
try {
    require_once 'config/db.php';
    echo "<p>✓ Database connection successful</p>";
    
    // Check if table exists
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'newsletter_subscriptions'");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo "<p>✓ newsletter_subscriptions table exists</p>";
        
        // Describe table structure
        try {
            $descStmt = $pdo->prepare("DESCRIBE newsletter_subscriptions");
            $descStmt->execute();
            $columns = $descStmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<p>Table structure:</p><ul>";
            foreach ($columns as $column) {
                echo "<li>{$column['Field']} - {$column['Type']}</li>";
            }
            echo "</ul>";
        } catch (PDOException $e) {
            echo "<p>✗ Error describing table: " . $e->getMessage() . "</p>";
        }
        
        // Test inserting a sample record
        try {
            $email = 'helloanshu.dev@gmail.com';
            $ip_address = '127.0.0.1';
            $user_agent = 'Debug Script';
            
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                echo "<p>✓ Email already exists in database (this is expected)</p>";
            } else {
                // Insert new record
                $insertStmt = $pdo->prepare("INSERT INTO newsletter_subscriptions (email, ip_address, user_agent) VALUES (?, ?, ?)");
                $insertStmt->execute([$email, $ip_address, $user_agent]);
                echo "<p>✓ Successfully inserted test record</p>";
                
                // Delete the test record
                $deleteStmt = $pdo->prepare("DELETE FROM newsletter_subscriptions WHERE email = ?");
                $deleteStmt->execute([$email]);
                echo "<p>✓ Cleaned up test record</p>";
            }
        } catch (PDOException $e) {
            echo "<p>✗ Error testing insert: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>✗ newsletter_subscriptions table does not exist</p>";
        
        // Try to create the table
        try {
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
            echo "<p>✓ Created newsletter_subscriptions table</p>";
        } catch (PDOException $e) {
            echo "<p>✗ Error creating table: " . $e->getMessage() . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p>✗ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Please check your database configuration in config/db.php</p>";
}

echo "<p><a href='/'>Back to Homepage</a></p>";
?>