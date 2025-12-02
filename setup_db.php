<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-4">Database Setup</h1>
            
            <?php
            require_once 'config/db.php';

            try {
                // Create users table
                $sql = "CREATE TABLE IF NOT EXISTS users (
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
                
                $pdo->exec($sql);
                echo "Users table created successfully.\n";
                
                // Create admins table (based on what I saw in the admin login)
                $sql = "CREATE TABLE IF NOT EXISTS admins (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    password VARCHAR(50) NOT NULL,
                    google_auth_secret VARCHAR(50),
                    google_auth_enabled TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )";
                
                $pdo->exec($sql);
                echo "Admins table created successfully.\n";
                
                // Insert default admin user if it doesn't exist
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
                $stmt->execute();
                
                if ($stmt->fetchColumn() == 0) {
                    $sql = "INSERT INTO admins (username, password, google_auth_enabled) VALUES ('admin', '" . md5('admin123') . "', 0)";
                    $pdo->exec($sql);
                    echo "Default admin user created (username: admin, password: admin123).\n";
                }
                
                echo "Database setup completed successfully!\n";
                
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
            ?>
        </div>
    </div>
</body>
</html>