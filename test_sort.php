<?php
require 'config/db.php';
$stmt = $pdo->query('SELECT id, status, created_at, updated_at FROM support_sessions ORDER BY id DESC');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
