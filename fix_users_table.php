<?php
require_once 'config/db.php';

try {
    // Check if the users table exists and its structure
    echo "<h2>Checking current users table structure...</h2>\n";
    
    $stmt = $pdo->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // Check if mobile column exists
    $hasMobile = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'mobile') {
            $hasMobile = true;
            break;
        }
    }
    
    if (!$hasMobile) {
        echo "<p>Mobile column is missing. Adding it now...</p>\n";
        
        // Add the missing columns
        $alterSql = "ALTER TABLE users 
                     ADD COLUMN mobile VARCHAR(20) NOT NULL AFTER email,
                     ADD COLUMN country VARCHAR(50) NOT NULL AFTER mobile,
                     ADD COLUMN age INT NOT NULL AFTER country,
                     ADD COLUMN company_name VARCHAR(100) AFTER age,
                     ADD COLUMN company_website VARCHAR(200) AFTER company_name";
        
        $pdo->exec($alterSql);
        echo "<p>Successfully added missing columns to users table.</p>\n";
    } else {
        echo "<p>All required columns are present.</p>\n";
    }
    
    echo "<p>Users table structure is now correct.</p>\n";
    
} catch(PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>\n";
    
    // If the table doesn't exist, create it with the correct structure
    if (strpos($e->getMessage(), 'Base table or view not found') !== false) {
        echo "<p>Users table doesn't exist. Creating it now...</p>\n";
        
        try {
            $createSql = "CREATE TABLE users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                first_name VARCHAR(50) NOT NULL,
                last_name VARCHAR(50) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                mobile VARCHAR(20) NOT NULL,
                country VARCHAR(50) NOT NULL,
                age INT NOT NULL,
                company_name VARCHAR(100),
                company_website VARCHAR(200),
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            
            $pdo->exec($createSql);
            echo "<p>Users table created successfully with correct structure.</p>\n";
        } catch(PDOException $createError) {
            echo "<p>Failed to create users table: " . $createError->getMessage() . "</p>\n";
        }
    }
}
?>