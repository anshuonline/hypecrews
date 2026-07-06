<?php
// get_dashboard_stats.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config.php';

$stats = [
    "total_users" => 0,
    "total_counts" => 0,
    "today_counts" => 0
];

// Total users
$res = $conn->query("SELECT COUNT(*) as count FROM users");
if ($row = $res->fetch_assoc()) $stats['total_users'] = (int)$row['count'];

// Total counts
$res = $conn->query("SELECT SUM(total_counts) as total FROM users");
if ($row = $res->fetch_assoc()) $stats['total_counts'] = (int)$row['total'];

// Today's counts
$today = date('Y-m-d');
$res = $conn->query("SELECT SUM(daily_count) as today FROM daily_counts WHERE date = '$today'");
if ($row = $res->fetch_assoc()) $stats['today_counts'] = (int)$row['today'];

echo json_encode(["success" => true, "data" => $stats]);
$conn->close();
?>
