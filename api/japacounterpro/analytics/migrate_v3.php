<?php
// analytics/migrate_v3.php — v3 schema migration for analytics
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../config.php';

$results = [];
$errors  = [];

// 1. analytics_events
$sql1 = "CREATE TABLE IF NOT EXISTS analytics_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_type VARCHAR(20) NOT NULL,
    count_value INT DEFAULT 0,
    event_timestamp BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_time (event_type, created_at),
    INDEX idx_user_time (user_id, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql1) === TRUE) {
    $results[] = "analytics_events: OK";
} else {
    $errors[] = "analytics_events: " . $conn->error;
}

// 2. live_sessions
$sql2 = "CREATE TABLE IF NOT EXISTS live_sessions (
    user_id INT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    last_heartbeat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    session_count INT DEFAULT 0,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql2) === TRUE) {
    $results[] = "live_sessions: OK";
} else {
    $errors[] = "live_sessions: " . $conn->error;
}

// 3. aggregated_stats
$sql3 = "CREATE TABLE IF NOT EXISTS aggregated_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    period_type ENUM('daily', 'weekly', 'monthly', 'yearly') NOT NULL,
    period_key VARCHAR(20) NOT NULL,
    total_counts INT DEFAULT 0,
    active_users INT DEFAULT 0,
    new_users INT DEFAULT 0,
    avg_counts_per_user FLOAT DEFAULT 0,
    peak_hour TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_period (period_type, period_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql3) === TRUE) {
    $results[] = "aggregated_stats: OK";
} else {
    $errors[] = "aggregated_stats: " . $conn->error;
}

// 4. Add created_at to users (safe: ignore error if column already exists)
$sql4 = "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
if ($conn->query($sql4) === TRUE) {
    $results[] = "users.created_at: added";
} else {
    if (strpos($conn->error, 'Duplicate column') !== false) {
        $results[] = "users.created_at: already exists (skipped)";
    } else {
        $errors[] = "users.created_at: " . $conn->error;
    }
}

$conn->close();

$success = empty($errors);
echo json_encode([
    "success"  => $success,
    "results"  => $results,
    "errors"   => $errors,
    "message"  => $success ? "Migration v3 completed successfully" : "Migration v3 completed with errors"
], JSON_PRETTY_PRINT);
?>
