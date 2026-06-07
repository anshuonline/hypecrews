<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['action'] = 'get_session_profile';
$_GET['session_id'] = '8'; // Assuming guest session
session_start();
$_SESSION['admin_id'] = 1;
require 'api_support_chat.php';
