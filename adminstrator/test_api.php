<?php
session_start();
$_SESSION['admin_id'] = 1;
$_GET['action'] = 'get_messages';
$_GET['session_id'] = 6;
require 'api_support_chat.php';
?>
