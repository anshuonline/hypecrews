<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
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
                

                
                echo "Database setup completed successfully!\n";
                
            } catch(PDOException $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
            ?>
        </div>
    </div>
</body>
</html>