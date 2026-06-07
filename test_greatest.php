<?php
require 'config/db.php';
$stmt = $pdo->query("SELECT GREATEST('0000-00-00 00:00:00', '2026-06-07 10:00:00') as res");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
