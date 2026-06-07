<?php
session_start();
$_SESSION['admin_id'] = 1;
$_POST['action'] = 'assign_session';
$_POST['session_id'] = 7;
$_SERVER['REQUEST_METHOD'] = 'POST';
require 'api_support_chat.php';
