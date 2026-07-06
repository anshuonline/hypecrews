<?php
// check_username.php
header('Content-Type: application/json');
require_once 'config.php';

if (!isset($_GET['username'])) {
    echo json_encode(["success" => false, "message" => "Username not provided"]);
    exit();
}

$username = $conn->real_escape_string($_GET['username']);

// Check if username exists
$device_token = isset($_GET['device_token']) ? $conn->real_escape_string($_GET['device_token']) : null;

$sql = "SELECT id, device_token FROM users WHERE username = '$username' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $db_token = $row['device_token'];
    
    // Username exists
    if ($db_token !== null && $db_token !== "" && $device_token === $db_token) {
        // It's their own username, they can claim it!
        echo json_encode(["success" => true, "available" => true]);
    } else {
        echo json_encode(["success" => true, "available" => false]);
    }
} else {
    // Username is available
    echo json_encode(["success" => true, "available" => true]);
}

$conn->close();
?>
