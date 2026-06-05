<?php
require_once 'config/db.php';

echo "<h2>Fixing Softwares Database...</h2>";
echo "<pre>";

try {
    // First, ensure the softwares table exists with basic columns
    $pdo->exec("CREATE TABLE IF NOT EXISTS softwares (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "✅ softwares table exists.\n";

    // List of ALL columns we need with their definitions
    $required_columns = [
        'keywords'           => 'VARCHAR(500) NULL',
        'version'            => 'VARCHAR(50) NULL',
        'platform'           => 'VARCHAR(255) NULL',
        'logo_path'          => 'VARCHAR(255) NULL',
        'banner_path'        => 'VARCHAR(255) NULL',
        'file_type'          => "ENUM('upload','google_drive') DEFAULT 'google_drive'",
        'file_path'          => 'VARCHAR(500) NULL',
        'playstore_link'     => 'VARCHAR(500) NULL',
        'appstore_link'      => 'VARCHAR(500) NULL',
        'windows_store_link' => 'VARCHAR(500) NULL',
        'is_paid'            => 'TINYINT(1) DEFAULT 0',
        'price'              => 'DECIMAL(10,2) DEFAULT 0.00',
        'payment_link'       => 'VARCHAR(500) NULL',
    ];

    foreach ($required_columns as $col => $def) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'softwares' AND COLUMN_NAME = ?");
        $stmt->execute([$col]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            $pdo->exec("ALTER TABLE softwares ADD COLUMN `$col` $def");
            echo "✅ Added missing column: $col\n";
        } else {
            echo "✔ Column already exists: $col\n";
        }
    }

    // Ensure software_screenshots table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS software_screenshots (
        id INT AUTO_INCREMENT PRIMARY KEY,
        software_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (software_id) REFERENCES softwares(id) ON DELETE CASCADE
    )");
    echo "✅ software_screenshots table exists.\n";

    echo "</pre>";
    echo "<h2 style='color:green;'>✅ Database fix complete! You can now <a href='adminstrator/softwares.php'>go back to Manage Softwares</a>.</h2>";

} catch (PDOException $e) {
    echo "</pre>";
    echo "<h2 style='color:red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
?>
