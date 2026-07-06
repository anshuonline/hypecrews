<?php
header('Content-Type: application/json');
require_once 'config.php';

// Auto-create announcements table if it doesn't exist
try {
    $conn->query("CREATE TABLE IF NOT EXISTS announcements (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'info',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Throwable $e) {
    // Suppress if no privileges
}

$announcements = [];
try {
    $res = $conn->query("SELECT id, title, message, type, created_at FROM announcements ORDER BY created_at DESC");
    if ($res && $res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            $announcements[] = $row;
        }
    }
} catch (Throwable $e) {
    // Return empty array if error
}

echo json_encode([
    "success" => true,
    "announcements" => $announcements
]);

$conn->close();
?>
