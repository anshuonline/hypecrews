<?php
require_once 'config/db.php';

try {
    // Create support_sessions table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS support_sessions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            topic VARCHAR(255) NOT NULL,
            urgency ENUM('low', 'normal', 'urgent') NOT NULL DEFAULT 'normal',
            status ENUM('open', 'resolved') DEFAULT 'open',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
    echo "support_sessions table created or already exists.\n";

    // Add session_id to support_chats if it doesn't exist
    $stmt = $pdo->prepare("SHOW COLUMNS FROM support_chats LIKE 'session_id'");
    $stmt->execute();
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE support_chats ADD COLUMN session_id INT NULL");
        $pdo->exec("ALTER TABLE support_chats ADD FOREIGN KEY (session_id) REFERENCES support_sessions(id) ON DELETE CASCADE");
        echo "Added session_id to support_chats.\n";
        
        // Handle existing chats by creating a default session for users who have chats
        $stmt = $pdo->query("SELECT DISTINCT user_id FROM support_chats");
        $users = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($users as $uid) {
            $pdo->prepare("INSERT INTO support_sessions (user_id, topic, urgency, status) VALUES (?, 'Legacy Conversation', 'normal', 'resolved')")->execute([$uid]);
            $session_id = $pdo->lastInsertId();
            $pdo->prepare("UPDATE support_chats SET session_id = ? WHERE user_id = ?")->execute([$session_id, $uid]);
        }
        echo "Migrated existing chats to legacy sessions.\n";
    } else {
        echo "session_id column already exists.\n";
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
