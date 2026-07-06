<?php
// analytics/ingest.php — batch event ingestion endpoint
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

// Set PHP Timezone to India
date_default_timezone_set('Asia/Kolkata');
// Set MySQL Timezone to India
$conn->query("SET time_zone = '+05:30'");

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['username']) || !isset($data['device_token']) || !isset($data['events'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields: username, device_token, events"]);
    exit;
}

$username     = $data['username'];
$device_token = $data['device_token'];
$events       = $data['events'];

if (!is_array($events) || count($events) === 0) {
    echo json_encode(["success" => true, "ingested" => 0, "message" => "No events to ingest"]);
    exit;
}

// Check Leaderboard Status
$leaderboard_status = 'ACTIVE';
try {
    $cfg_query = "SELECT status, challenge_start, challenge_end FROM leaderboard_config LIMIT 1";
    $cfg_res = $conn->query($cfg_query);
    if ($cfg_res && $cfg_res->num_rows > 0) {
        $cfg_row = $cfg_res->fetch_assoc();
        $leaderboard_status = $cfg_row['status'];
        
        $current_time = date('Y-m-d H:i:s');
        if ($current_time < $cfg_row['challenge_start'] || $current_time > $cfg_row['challenge_end']) {
            $leaderboard_status = 'RESULTS'; // Locally locked
        }
    }
} catch (Throwable $e) {
    // Ignore error
}

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

// Batch insert events using prepared statement
$insert_stmt = $conn->prepare(
    "INSERT INTO analytics_events (user_id, event_type, count_value, event_timestamp) VALUES (?, ?, ?, ?)"
);

// Upsert into legacy daily_counts for backward compatibility and dashboard stats
$daily_upsert = $conn->prepare(
    "INSERT INTO daily_counts (user_id, date, daily_count) 
     VALUES (?, ?, ?)
     ON DUPLICATE KEY UPDATE 
     daily_count = daily_count + VALUES(daily_count)"
);

// Update user's all-time total_counts
$user_total_update = $conn->prepare(
    "UPDATE users SET total_counts = total_counts + ? WHERE id = ?"
);

if (!$insert_stmt || !$daily_upsert) {
    $conn->close();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to prepare insert statement: " . $conn->error]);
    exit;
}

$ingested = 0;
$skipped  = 0;

// Allowed event types (whitelist for safety)
$allowed_types = ['count_tap', 'app_open', 'app_close', 'session_end', 'milestone', 'count_reset'];

$conn->begin_transaction();

try {
    foreach ($events as $event) {
        $event_type = isset($event['event_type']) ? $event['event_type'] : null;
        $count_value = isset($event['count_value']) ? (int)$event['count_value'] : 0;
        $timestamp = isset($event['timestamp']) ? (int)$event['timestamp'] : 0;

        // Validate event
        if (!$event_type || !in_array($event_type, $allowed_types) || $timestamp <= 0) {
            $skipped++;
            continue;
        }

        // 1. Insert into new analytics table
        $insert_stmt->bind_param("isii", $user_id, $event_type, $count_value, $timestamp);
        $insert_stmt->execute();

        // 2. Upsert into legacy daily_counts and total_counts ONLY IF ACTIVE
        if ($event_type === 'count_tap' && $count_value > 0) {
            if ($leaderboard_status === 'ACTIVE') {
                // Force use of Server's current India Time date to prevent any client-side timezone mismatches
                $date = date('Y-m-d');
                $daily_upsert->bind_param("isi", $user_id, $date, $count_value);
                $daily_upsert->execute();

                // 3. Increment users.total_counts
                $user_total_update->bind_param("ii", $count_value, $user_id);
                $user_total_update->execute();
            }
        }

        $ingested++;
    }

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    $insert_stmt->close();
    if (isset($daily_upsert)) $daily_upsert->close();
    if (isset($user_total_update)) $user_total_update->close();
    $conn->close();
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Insert failed: " . $e->getMessage()]);
    exit;
}

$insert_stmt->close();
$daily_upsert->close();
$user_total_update->close();
$conn->close();

echo json_encode([
    "success"  => true,
    "ingested" => $ingested,
    "skipped"  => $skipped
]);
?>
