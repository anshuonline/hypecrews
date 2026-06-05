<?php
require_once 'config/db.php';

try {
    // 1. Create Softwares Table
    $sql1 = "CREATE TABLE IF NOT EXISTS softwares (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        keywords VARCHAR(500),
        version VARCHAR(50),
        platform VARCHAR(255),
        logo_path VARCHAR(255),
        banner_path VARCHAR(255),
        file_type ENUM('upload', 'google_drive') DEFAULT 'upload',
        file_path VARCHAR(500),
        playstore_link VARCHAR(500),
        appstore_link VARCHAR(500),
        windows_store_link VARCHAR(500),
        is_paid TINYINT(1) DEFAULT 0,
        price DECIMAL(10,2) DEFAULT 0.00,
        payment_link VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql1);
    echo "Table 'softwares' verified/created successfully.<br>";

    // 2. Create Screenshots Table
    $sql2 = "CREATE TABLE IF NOT EXISTS software_screenshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        software_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (software_id) REFERENCES softwares(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql2);
    echo "Table 'software_screenshots' verified/created successfully.<br>";

    // 3. Create Purchases Table (For future use if needed)
    $sql3 = "CREATE TABLE IF NOT EXISTS software_purchases (
        id INT AUTO_INCREMENT PRIMARY KEY,
        software_id INT NOT NULL,
        user_id INT NOT NULL,
        payment_id VARCHAR(255) NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(50) DEFAULT 'completed',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (software_id) REFERENCES softwares(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql3);
    echo "Table 'software_purchases' verified/created successfully.<br>";

    echo "<br><b>Database is ready for production!</b>";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
