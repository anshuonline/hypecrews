<?php
// analytics/heartbeat.php — live session heartbeat endpoint
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit;
}

require_once __DIR__ . '/../config.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields: username"]);
    exit;
}

$username      = $data['username'];
$device_token  = isset($data['device_token']) ? $data['device_token'] : null;
$session_count = isset($data['session_count']) ? (int)$data['session_count'] : 0;

// Look up user and verify device_token
$stmt = $conn->prepare("SELECT id, device_token FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id, $db_token);
if (!$stmt->fetch()) {
    $stmt->close();
    $conn->close();
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "User not found"]);
    exit;
}
$stmt->close();

// Verify device_token
if ($db_token !== null && $db_token !== "" && $device_token !== $db_token) {
    $conn->close();
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized device token"]);
    exit;
}

// Upsert into live_sessions
$upsert = $conn->prepare(
    "INSERT INTO live_sessions (user_id, username, session_count, last_heartbeat, started_at)
     VALUES (?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
     ON DUPLICATE KEY UPDATE
       started_at = IF(last_heartbeat < CURRENT_TIMESTAMP - INTERVAL 60 SECOND, CURRENT_TIMESTAMP, started_at),
       last_heartbeat = CURRENT_TIMESTAMP,
       session_count = VALUES(session_count),
       username = VALUES(username)"
);

if (!$upsert) {
    $conn->close();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]);
    exit;
}

$upsert->bind_param("isi", $user_id, $username, $session_count);

if ($upsert->execute()) {
    $upsert->close();
    $conn->close();
    echo json_encode(["success" => true]);
} else {
    $error = $upsert->error;
    $upsert->close();
    $conn->close();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Heartbeat failed: " . $error]);
}
?>
