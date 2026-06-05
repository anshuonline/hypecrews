<?php
require 'config/db.php';
$stmt=$pdo->query('DESCRIBE administrators');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
