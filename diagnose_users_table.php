<?php
require_once 'config/db.php';

echo "<h2>Database Structure Diagnosis</h2>\n";

try {
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'hypecrews'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>ERROR: Database 'hypecrews' does not exist.</p>\n";
        exit;
    }
    echo "<p style='color: green;'>SUCCESS: Database 'hypecrews' exists.</p>\n";
    
    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "<p style='color: red;'>ERROR: Users table does not exist.</p>\n";
        echo "<p>Please run <a href='setup_db.php'>setup_db.php</a> to create the necessary tables.</p>\n";
        exit;
    }
    echo "<p style='color: green;'>SUCCESS: Users table exists.</p>\n";
    
    // Get current table structure
    echo "<h3>Current Users Table Structure:</h3>\n";
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse;'>\n";
    echo "<tr style='background-color: #f0f0f0;'>";
    foreach (array_keys($columns[0]) as $header) {
        echo "<th style='padding: 8px; border: 1px solid #ccc;'>$header</th>";
    }
    echo "</tr>\n";
    
    foreach ($columns as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td style='padding: 8px; border: 1px solid #ccc;'>$cell</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
    
    // Check for required columns
    $requiredColumns = ['id', 'username', 'first_name', 'last_name', 'email', 'mobile', 'country', 'age', 'password'];
    $missingColumns = [];
    
    $existingColumns = array_column($columns, 'Field');
    
    foreach ($requiredColumns as $column) {
        if (!in_array($column, $existingColumns)) {
            $missingColumns[] = $column;
        }
    }
    
    if (empty($missingColumns)) {
        echo "<p style='color: green;'>SUCCESS: All required columns are present.</p>\n";
        echo "<p>You should be able to register now. If you're still having issues, please try registering again.</p>\n";
    } else {
        echo "<p style='color: red;'>ERROR: Missing columns: " . implode(', ', $missingColumns) . "</p>\n";
        echo "<p>Run <a href='fix_users_table.php'>fix_users_table.php</a> to add the missing columns.</p>\n";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>DATABASE ERROR: " . $e->getMessage() . "</p>\n";
}
?>