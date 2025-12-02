<?php
// Test database connection and check if database exists
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if hypecrews database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'hypecrews'");
    if ($stmt->rowCount() > 0) {
        echo "SUCCESS: Database 'hypecrews' exists.\n";
        
        // Connect to the hypecrews database
        $pdo = new PDO("mysql:host=$host;dbname=hypecrews;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Check tables
        echo "Checking tables...\n";
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (in_array('users', $tables)) {
            echo "SUCCESS: Users table exists.\n";
        } else {
            echo "ERROR: Users table does not exist.\n";
        }
        
        if (in_array('admins', $tables)) {
            echo "SUCCESS: Admins table exists.\n";
        } else {
            echo "WARNING: Admins table does not exist.\n";
        }
        
    } else {
        echo "ERROR: Database 'hypecrews' does not exist.\n";
        echo "Please create the database first.\n";
    }
    
} catch(PDOException $e) {
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
}
?>