<?php
require 'config/db.php';
$stmt = $pdo->query('SHOW CREATE TABLE support_sessions');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
