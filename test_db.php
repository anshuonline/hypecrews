<?php
require 'config/db.php';
try {
    $pdo->query('SELECT profile_image FROM administrators');
    echo 'Works';
} catch(Exception $e) {
    echo $e->getMessage();
}
?>
