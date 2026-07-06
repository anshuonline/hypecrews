<?php
// manage_content.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
require_once '../config.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $res = $conn->query("SELECT * FROM content_pages ORDER BY created_at DESC");
    $pages = [];
    while ($row = $res->fetch_assoc()) {
        $pages[] = $row;
    }
    echo json_encode(["success" => true, "data" => $pages]);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['id'])) {
        // Update
        $stmt = $conn->prepare("UPDATE content_pages SET title=?, content=?, youtube_url=? WHERE id=?");
        $stmt->bind_param("sssi", $data['title'], $data['content'], $data['youtube_url'], $data['id']);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Updated successfully"]);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO content_pages (title, content, youtube_url) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $data['title'], $data['content'], $data['youtube_url']);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Added successfully"]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $conn->prepare("DELETE FROM content_pages WHERE id=?");
    $stmt->bind_param("i", $data['id']);
    $stmt->execute();
    echo json_encode(["success" => true, "message" => "Deleted successfully"]);
}

$conn->close();
?>
