<?php
require 'config/db.php';
$stmt = $pdo->query('SHOW CREATE TABLE users');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt = $pdo->query('SHOW CREATE TABLE orders');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
