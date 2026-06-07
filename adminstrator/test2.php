<?php
session_start();
$_SESSION['admin_id'] = 1;
$_GET['action'] = 'list_threads';
require 'api_support_chat.php';
