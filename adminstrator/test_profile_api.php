<?php
$_SERVER['REQUEST_METHOD'] = 'POST';
$_GET['action'] = 'add_guest_note';
$_POST['session_id'] = '8'; 
$_POST['note'] = 'This is a test note';
session_start();
$_SESSION['admin_id'] = 1;
require 'api_support_chat.php';
