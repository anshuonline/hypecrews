<?php
require_once 'config/db.php';

echo "<h2>Setting up Support Chats Database Table...</h2>";

try {
    // Drop existing table if needed (optional, keeping it safe for now)
    // $pdo->exec("DROP TABLE IF EXISTS support_chats");

    $sql = "CREATE TABLE IF NOT EXISTS support_chats (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        sender_type ENUM('user', 'admin') NOT NULL,
        sender_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "<p style='color:green;'>Successfully created 'support_chats' table.</p>";

} catch (PDOException $e) {
    echo "<p style='color:red;'>Error creating table: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>Return to Home</a>";
?>
