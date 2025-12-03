<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $country = trim($_POST['country']);
    $age = intval($_POST['age']);
    $company_name = trim($_POST['company_name']);
    $company_website = trim($_POST['company_website']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username)) $errors[] = "Username is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($mobile)) $errors[] = "Mobile number is required";
    if (empty($country)) $errors[] = "Country is required";
    if (empty($age) || $age <= 0) $errors[] = "Valid age is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";

    // Check if username or email already exists
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = "Username or email already exists";
            }
        } catch (PDOException $e) {
            // Log the actual error for debugging
            error_log("Database error: " . $e->getMessage());
            // For debugging, show more specific error
            $errors[] = "Database error: " . $e->getMessage();
            // In production, use this instead:
            // $errors[] = "Database error occurred";
        }
    }

    // Register user if no errors
    if (empty($errors)) {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $username,
                $first_name,
                $last_name,
                $email,
                $mobile,
                $country,
                $age,
                $company_name ?: null,
                $company_website ?: null,
                $hashed_password
            ]);

            // Get the inserted user ID and set session
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            // Redirect to profile page
            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            // Log the actual error for debugging (in production, you might want to log this to a file instead)
            error_log("Registration error: " . $e->getMessage());
            // For debugging, you can temporarily show the actual error (remove this in production)
            $errors[] = "Registration failed: " . $e->getMessage();
            // In production, use this instead:
            // $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-dark text-white min-h-screen">
    <?php include 'components/nav.php'; ?>
    
    <!-- Main Content -->
    <div class="min-h-screen pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold mb-2">Create Account</h1>
                    <p class="text-gray-400">Join us today! Please fill in your details</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <ul class="list-disc pl-5 space-y-1">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="bg-light/50 backdrop-blur-sm rounded-xl p-8 border border-gray-700">
                    <form method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Username *</label>
                                <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Email *</label>
                                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">First Name *</label>
                                <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Last Name *</label>
                                <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Mobile Number *</label>
                                <input type="tel" name="mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Country *</label>
                                <input type="text" name="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Age *</label>
                                <input type="number" name="age" min="1" max="120" value="<?php echo isset($_POST['age']) ? intval($_POST['age']) : ''; ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Company Name</label>
                                <input type="text" name="company_name" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-300 mb-1">Company Website</label>
                                <input type="url" name="company_website" value="<?php echo isset($_POST['company_website']) ? htmlspecialchars($_POST['company_website']) : ''; ?>" placeholder="https://example.com" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Password *</label>
                                <input type="password" name="password" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-1">Confirm Password *</label>
                                <input type="password" name="confirm_password" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        
                        <div class="mt-8">
                            <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">Register</button>
                        </div>
                    </form>
                    
                    <div class="mt-6 text-center">
                        <p class="text-gray-400">Already have an account? <a href="login.php" class="text-primary hover:underline">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>