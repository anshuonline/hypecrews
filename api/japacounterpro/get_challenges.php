<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
require_once 'config.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['username'])) {
        echo json_encode(["success" => false, "message" => "Username required"]);
        exit;
    }

    $username = $conn->real_escape_string($data['username']);

    // Get user id using bind_result (compatible with all servers)
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    if (!$stmt->fetch()) {
        $user_id = null;
    }
    $stmt->close();

    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "User not found"]);
        exit;
    }

    $today = date('Y-m-d');

    // Fetch all visible challenges that haven't expired
    // Count joined as anyone with status active OR accepted (both are valid "joined" states)
    $sql = "SELECT c.*, uc.status, uc.progress,
            (SELECT COUNT(*) FROM user_challenges WHERE challenge_id = c.id AND status IN ('active','accepted')) as joined_count,
            (SELECT COUNT(*) FROM user_challenges WHERE challenge_id = c.id AND status = 'completed') as completed_count
            FROM challenges c 
            LEFT JOIN user_challenges uc ON c.id = uc.challenge_id AND uc.user_id = $user_id
            WHERE c.end_date >= '$today' 
            ORDER BY c.id DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        // If SQL query fails, return the error
        echo json_encode(["success" => false, "message" => "SQL Error: " . $conn->error]);
        exit;
    }

    $challenges = [];

    while ($row = $result->fetch_assoc()) {
        $challenges[] = $row;
    }

    echo json_encode(["success" => true, "data" => $challenges]);
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Fatal Error: " . $e->getMessage()]);
}

$conn->close();
?>
