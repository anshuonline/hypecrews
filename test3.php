<?php
$headers = [
    'x-session-token' => 'abc'
];
var_dump($headers['X-Session-Token'] ?? null);
