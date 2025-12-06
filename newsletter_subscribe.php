<?php
// Handle newsletter subscription via AJAX
header('Content-Type: application/json');

// Simple response for testing
echo json_encode(['status' => 'success', 'message' => 'Simple test successful!']);
exit;

// Include database connection
// require_once 'config/db.php';

// Rest of the code...
/*
// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get email from POST data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';

// Validate email
if (empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Email is required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'success', 'message' => 'You are already subscribed to our newsletter!']);
        exit;
    }
} catch (PDOException $e) {
    // If table doesn't exist, try to create it
    try {
        $createTable = "
            CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                ip_address VARCHAR(45),
                user_agent TEXT
            )
        ";
        $pdo->exec($createTable);
        
        // Now try the select query again
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscriptions WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'success', 'message' => 'You are already subscribed to our newsletter!']);
            exit;
        }
    } catch (PDOException $e2) {
        echo json_encode(['status' => 'error', 'message' => 'Database error occurred. Please try again.']);
        exit;
    }
}

try {
    // Insert new subscription
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $pdo->prepare("INSERT INTO newsletter_subscriptions (email, ip_address, user_agent) VALUES (?, ?, ?)");
    $stmt->execute([$email, $ip_address, $user_agent]);
    
    echo json_encode(['status' => 'success', 'message' => 'Thank you for subscribing to our newsletter!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error occurred. Please try again.']);
}
*/
?>