<?php
// admin_login.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
// Try to find config.php safely
if (file_exists('../config.php')) {
    require_once '../config.php';
} elseif (file_exists('config.php')) {
    require_once 'config.php';
} else {
    die(json_encode(["success" => false, "message" => "config.php not found. Make sure it exists."]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Support both JSON (from Android app) and standard POST (from Web panel)
    $json_data = json_decode(file_get_contents('php://input'), true);
    
    // Default table and column names. Change these if your online DB uses different names!
    $tableName = "admins";
    $usernameCol = "username";
    $passwordCol = "password_md5";
    
    $username = $json_data['username'] ?? $_POST['username'] ?? '';
    $password = $json_data['password'] ?? $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "Please enter username and password"]);
        exit;
    }
    
    // Check if table exists first
    $checkTable = $conn->query("SHOW TABLES LIKE '$tableName'");
    if ($checkTable && $checkTable->num_rows == 0) {
        echo json_encode(["success" => false, "message" => "Admin table '$tableName' not found. Please check your DB."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM $tableName WHERE $usernameCol = ?");
    if (!$stmt) {
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit;
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $dbPassword = $row[$passwordCol];
        
        // Verify password. Supports both plain-text and hashed passwords
        if ($password === $dbPassword || password_verify($password, $dbPassword) || md5($password) === $dbPassword) {
            echo json_encode(["success" => true, "message" => "Login successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Admin not found"]);
    }
    
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

$conn->close();
?>
