<?php
require_once 'auth.php';
require_once '../config/db.php';

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile_number = trim($_POST['mobile_number'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $company_website = trim($_POST['company_website'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || 
        empty($mobile_number) || empty($country) || empty($age) || empty($password)) {
        $error = 'All required fields must be filled.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (!is_numeric($age) || $age <= 0) {
        $error = 'Age must be a positive number.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username or email already exists.';
            } else {
                // Insert new user with MD5 hashed password
                $hashed_password = md5($password);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $username,
                    $first_name,
                    $last_name,
                    $email,
                    $mobile_number,
                    $country,
                    $age,
                    $company_name ?: null,
                    $company_website ?: null,
                    $hashed_password
                ]);
                
                $success = 'User added successfully!';
                
                // Clear form data
                $username = $first_name = $last_name = $email = $mobile_number = 
                $country = $age = $company_name = $company_website = $password = '';
            }
        } catch (PDOException $e) {
            $error = "Error adding user: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Hypecrews Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0f172a',
                        light: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-dark border-b border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Add New User</h2>
                    <a href="users.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Users
                    </a>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-300"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="bg-light rounded-xl p-6">
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Username -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Username *</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- First Name -->
                            <div>
                                <label class="block text-sm font-medium mb-2">First Name *</label>
                                <input type="text" name="first_name" value="<?php echo htmlspecialchars($first_name ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Last Name -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Last Name *</label>
                                <input type="text" name="last_name" value="<?php echo htmlspecialchars($last_name ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Email *</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Mobile Number -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Mobile Number *</label>
                                <input type="text" name="mobile_number" value="<?php echo htmlspecialchars($mobile_number ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Country -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Country *</label>
                                <input type="text" name="country" value="<?php echo htmlspecialchars($country ?? ''); ?>" required class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Age -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Age *</label>
                                <input type="number" name="age" value="<?php echo htmlspecialchars($age ?? ''); ?>" required min="1" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Company Name -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Company Name</label>
                                <input type="text" name="company_name" value="<?php echo htmlspecialchars($company_name ?? ''); ?>" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Company Website -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Company Website</label>
                                <input type="url" name="company_website" value="<?php echo htmlspecialchars($company_website ?? ''); ?>" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            
                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium mb-2">Password *</label>
                                <input type="password" name="password" required minlength="6" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                                <p class="text-xs text-gray-400 mt-1">Minimum 6 characters</p>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="bg-primary hover:bg-indigo-700 px-6 py-3 rounded-lg font-medium">
                                <i class="fas fa-user-plus mr-2"></i> Add User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>