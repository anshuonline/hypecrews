<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['action'] = 'reopen_session';
$_POST['session_id'] = 1; // Assuming session 1 exists
$_SESSION['admin_id'] = 1;

require_once 'config/db.php';

try {
    $session_id = $_POST['session_id'];
    $stmt = $pdo->prepare("SELECT user_exported FROM support_sessions WHERE id = ?");
    $stmt->execute([$session_id]);
    $session = $stmt->fetch();
    var_dump($session);
    
    $pdo->prepare("UPDATE support_sessions SET status = 'open' WHERE id = ?")->execute([$session_id]);
    echo "Success";
} catch (PDOException $e) {
    echo "DB Error: " . $e->getMessage();
}
