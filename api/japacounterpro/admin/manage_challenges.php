<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

try {
    require_once '../config.php'; 

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        $sql = "SELECT c.*, 
                (SELECT COUNT(*) FROM user_challenges WHERE challenge_id = c.id) as joined_count,
                (SELECT COUNT(*) FROM user_challenges WHERE challenge_id = c.id AND status = 'completed') as completed_count
                FROM challenges c ORDER BY c.id DESC";
        $res = $conn->query($sql);
        $challenges = [];
        if($res) {
            while ($row = $res->fetch_assoc()) {
                $challenges[] = $row;
            }
        }
        echo json_encode(["success" => true, "data" => $challenges]);
    } elseif ($method === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $title = $conn->real_escape_string($data['title']);
        $desc = $conn->real_escape_string($data['description']);
        $target = (int)$data['target_count'];
        $start = $conn->real_escape_string($data['start_date']);
        $end = $conn->real_escape_string($data['end_date']);
        
        if (isset($data['id']) && !empty($data['id'])) {
            $id = (int)$data['id'];
            $sql = "UPDATE challenges SET title='$title', description='$desc', target_count=$target, start_date='$start', end_date='$end' WHERE id=$id";
            if($conn->query($sql)) {
                echo json_encode(["success" => true, "message" => "Updated successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "DB Error: " . $conn->error]);
            }
        } else {
            $sql = "INSERT INTO challenges (title, description, target_count, start_date, end_date) VALUES ('$title', '$desc', $target, '$start', '$end')";
            if($conn->query($sql)) {
                echo json_encode(["success" => true, "message" => "Added successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "DB Error: " . $conn->error]);
            }
        }
    } elseif ($method === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        if(isset($data['id'])) {
            $id = (int)$data['id'];
            if($conn->query("DELETE FROM challenges WHERE id=$id")) {
                echo json_encode(["success" => true, "message" => "Deleted successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "DB Error: " . $conn->error]);
            }
        }
    }
} catch (Throwable $e) {
    echo json_encode(["success" => false, "message" => "Fatal Error: " . $e->getMessage()]);
}
?>
