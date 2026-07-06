<?php
// get_users.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once '../config.php';

$res = $conn->query("SELECT id, username, total_counts, level, last_active FROM users ORDER BY total_counts DESC");
$users = [];
while ($row = $res->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode(["success" => true, "data" => $users]);
$conn->close();
?>
