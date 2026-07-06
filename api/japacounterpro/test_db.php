<?php
require_once 'config.php';
$res = $conn->query("SELECT * FROM user_challenges");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
