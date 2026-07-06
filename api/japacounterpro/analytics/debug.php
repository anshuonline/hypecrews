<?php
header('Content-Type: text/plain');
require_once __DIR__ . '/../config.php';

echo "=== SERVER TIME DEBUG ===\n";
echo "Default Timezone: " . date_default_timezone_get() . "\n";
echo "Current Date (No TZ set): " . date('Y-m-d H:i:s') . "\n";

date_default_timezone_set('Asia/Kolkata');
echo "Current Date (Asia/Kolkata): " . date('Y-m-d H:i:s') . "\n\n";

echo "=== LAST 5 ANALYTICS EVENTS ===\n";
$r = $conn->query("SELECT * FROM analytics_events ORDER BY id DESC LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "ID: {$row['id']} | User: {$row['user_id']} | Type: {$row['event_type']} | Count: {$row['count_value']} | TS: {$row['event_timestamp']} | Inserted At: {$row['created_at']}\n";
}

echo "\n=== LAST 5 DAILY COUNTS ===\n";
$r = $conn->query("SELECT * FROM daily_counts ORDER BY date DESC, user_id DESC LIMIT 5");
while ($row = $r->fetch_assoc()) {
    echo "Date: {$row['date']} | User: {$row['user_id']} | Count: {$row['daily_count']}\n";
}
?>
