<?php
// get_pages.php
header('Content-Type: application/json');
require_once 'config.php';

// Fetch all dynamic pages
$sql = "SELECT id, title, content, youtube_url FROM content_pages ORDER BY created_at ASC";
$result = $conn->query($sql);

$pages = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $pages[] = [
            "id" => (int)$row['id'],
            "title" => $row['title'],
            "content" => $row['content'],
            "youtube_url" => isset($row['youtube_url']) ? $row['youtube_url'] : null
        ];
    }
}

echo json_encode(["success" => true, "data" => $pages]);

$conn->close();
?>
