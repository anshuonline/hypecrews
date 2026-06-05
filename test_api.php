<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['chat_with'] = 'group';
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
include 'adminstrator/api_chat.php';
