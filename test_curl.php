<?php
$ch = curl_init('http://localhost/Hypecrews/adminstrator/api_support_chat.php?action=get_messages&session_id=6');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// We need admin session cookie
curl_setopt($ch, CURLOPT_COOKIE, 'PHPSESSID=test_session_id');
echo curl_exec($ch);
?>
