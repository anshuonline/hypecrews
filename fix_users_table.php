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
    
    // Check if all required columns exist
    $requiredColumns = ['mobile_number', 'country', 'age', 'company_name', 'company_website'];
    $missingColumns = [];
    
    // Create a map of existing column names
    $existingColumns = [];
    foreach ($columns as $column) {
        $existingColumns[] = $column['Field'];
    }
    
    // Check which required columns are missing
    foreach ($requiredColumns as $requiredColumn) {
        if (!in_array($requiredColumn, $existingColumns)) {
            $missingColumns[] = $requiredColumn;
        }
    }
    
    if (count($missingColumns) > 0) {
        echo "<p>Missing columns: " . implode(', ', $missingColumns) . ". Adding them now...</p>\n";
        
        // Add the missing columns one by one to avoid conflicts
        foreach ($missingColumns as $column) {
            try {
                switch ($column) {
                    case 'mobile_number':
                        $sql = "ALTER TABLE users ADD COLUMN mobile_number VARCHAR(20) NOT NULL AFTER email";
                        break;
                    case 'country':
                        $sql = "ALTER TABLE users ADD COLUMN country VARCHAR(50) NOT NULL AFTER mobile_number";
                        break;
                    case 'age':
                        $sql = "ALTER TABLE users ADD COLUMN age INT NOT NULL AFTER country";
                        break;
                    case 'company_name':
                        $sql = "ALTER TABLE users ADD COLUMN company_name VARCHAR(100) AFTER age";
                        break;
                    case 'company_website':
                        $sql = "ALTER TABLE users ADD COLUMN company_website VARCHAR(200) AFTER company_name";
                        break;
                    default:
                        continue 2; // Skip unknown columns
                }
                $pdo->exec($sql);
                echo "<p>Successfully added column '$column' to users table.</p>\n";
            } catch (PDOException $e) {
                echo "<p>Warning: Could not add column '$column': " . $e->getMessage() . "</p>\n";
            }
        }
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
                mobile_number VARCHAR(20) NOT NULL,
                country VARCHAR(50) NOT NULL,
                age INT NOT NULL,
                company_name VARCHAR(100),
                company_website VARCHAR(200),
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";
            
            $pdo->exec($createSql);
            echo "<p>Users table created successfully with correct structure.</p>\n";
        } catch(PDOException $createError) {
            echo "<p>Failed to create users table: " . $createError->getMessage() . "</p>\n";
        }
    }
}
?>