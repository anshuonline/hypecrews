<?php
// migrate_v2.php
// Run this script once on your server to add the device_token column to your users table
require_once 'config.php';

$sql = "ALTER TABLE users ADD COLUMN device_token VARCHAR(255) DEFAULT NULL";

if ($conn->query($sql) === TRUE) {
    echo "Table users altered successfully. device_token added.";
} else {
    echo "Error altering table (it might already exist): " . $conn->error;
}

$conn->close();
?>
