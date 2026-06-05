<?php
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT file_type, file_path FROM softwares WHERE id = ?");
$stmt->execute([$id]);
$software = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$software) {
    die("Software not found.");
}

if ($software['file_type'] == 'upload') {
    // If it's a direct upload, redirect to the file path
    if (file_exists($software['file_path'])) {
        header("Location: " . $software['file_path']);
        exit;
    } else {
        die("File is missing on the server.");
    }
} elseif ($software['file_type'] == 'google_drive') {
    // Extract file ID from google drive link or use as is
    $link = $software['file_path'];
    $file_id = '';
    
    // Check if it's a full URL
    if (filter_var($link, FILTER_VALIDATE_URL)) {
        // Try to extract ID
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $link, $matches);
        if (isset($matches[1])) {
            $file_id = $matches[1];
        } else {
            // It might be a mega link or other external link, just redirect
            header("Location: " . $link);
            exit;
        }
    } else {
        // Assume they just pasted the File ID
        $file_id = $link;
    }
    
    if (!empty($file_id)) {
        // Construct direct download link for Google Drive
        $direct_link = "https://drive.google.com/uc?export=download&id=" . $file_id;
        header("Location: " . $direct_link);
        exit;
    } else {
        die("Invalid Google Drive Link.");
    }
}
?>
