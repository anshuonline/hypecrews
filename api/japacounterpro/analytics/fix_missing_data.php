<?php
// fix_missing_data.php
header('Content-Type: text/plain');
require_once __DIR__ . '/../config.php';

// Set PHP Timezone to India
ini_set('display_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Kolkata');
// Set MySQL Timezone to India
$conn->query("SET time_zone = '+05:30'");

echo "Fixing missing daily_counts data from analytics_events...\n\n";

$query = "
    INSERT INTO daily_counts (user_id, date, daily_count)
    SELECT 
        user_id, 
        DATE(FROM_UNIXTIME(event_timestamp / 1000)) as event_date,
        SUM(count_value) as total_taps
    FROM analytics_events
    WHERE event_type = 'count_tap'
    GROUP BY user_id, event_date
    ON DUPLICATE KEY UPDATE 
        daily_count = VALUES(daily_count);
";

if ($conn->query($query) === TRUE) {
    echo "SUCCESS: Missing data has been recovered and added to daily_counts!\n";
    echo "Total rows affected (daily_counts): " . $conn->affected_rows . "\n\n";
    
    // Now sync users.total_counts
    $sync_users_query = "
        UPDATE users u
        JOIN (
            SELECT user_id, SUM(daily_count) as actual_total 
            FROM daily_counts 
            GROUP BY user_id
        ) dc ON u.id = dc.user_id
        SET u.total_counts = dc.actual_total;
    ";
    
    if ($conn->query($sync_users_query) === TRUE) {
        echo "SUCCESS: 'All Time' stats in users table have been resynchronized!\n";
        echo "Total rows affected (users): " . $conn->affected_rows . "\n";
    } else {
        echo "ERROR syncing users table: " . $conn->error . "\n";
    }
    
} else {
    echo "ERROR: " . $conn->error . "\n";
}

$conn->close();
?>
