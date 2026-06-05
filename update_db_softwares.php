<?php
require_once 'config/db.php';

try {
    // Array of columns to add with their definitions
    $columns = [
        'keywords' => 'VARCHAR(500)',
        'version' => 'VARCHAR(50)',
        'platform' => 'VARCHAR(255)',
        'logo_path' => 'VARCHAR(255)',
        'banner_path' => 'VARCHAR(255)',
        'file_type' => "ENUM('upload', 'google_drive') DEFAULT 'upload'",
        'file_path' => 'VARCHAR(500)',
        'playstore_link' => 'VARCHAR(500)',
        'appstore_link' => 'VARCHAR(500)',
        'windows_store_link' => 'VARCHAR(500)',
        'is_paid' => 'TINYINT(1) DEFAULT 0',
        'price' => 'DECIMAL(10,2) DEFAULT 0.00',
        'payment_link' => 'VARCHAR(500)'
    ];

    foreach ($columns as $column => $definition) {
        // Check if column exists
        $stmt = $pdo->prepare("SHOW COLUMNS FROM softwares LIKE ?");
        $stmt->execute([$column]);
        $exists = $stmt->fetch();

        if (!$exists) {
            $pdo->exec("ALTER TABLE softwares ADD COLUMN $column $definition");
            echo "Added column: $column <br>";
        } else {
            echo "Column $column already exists.<br>";
        }
    }
    
    // Also check for software_screenshots table
    $pdo->exec("CREATE TABLE IF NOT EXISTS software_screenshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        software_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (software_id) REFERENCES softwares(id) ON DELETE CASCADE
    )");
    echo "software_screenshots table checked/created.<br>";

    echo "<br><b>Database update completed successfully!</b>";

} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?>
