<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching user data.";
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile_number = trim($_POST['mobile_number']);
    $country = trim($_POST['country']);
    $age = (int)$_POST['age'];
    $company_name = trim($_POST['company_name']);
    $company_website = trim($_POST['company_website']);
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || 
        empty($mobile_number) || empty($country) || empty($age)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // Check if email is already used by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error = "Email already used by another account.";
            } else {
                // Update user data
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, country = ?, age = ?, company_name = ?, company_website = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $email, $mobile_number, $country, $age, $company_name, $company_website, $user_id]);
                
                // Update session data
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                
                $success = "Profile updated successfully!";
                
                // Refresh user data
                $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, created_at FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = "Error updating profile.";
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        try {
            // Verify current password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();
            
            if ($user_data && password_verify($current_password, $user_data['password'])) {
                // Update password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);
                
                $success = "Password changed successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Error changing password.";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Hypecrews</title>
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
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>
    
    <!-- Main Content -->
    <div class="min-h-screen pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold">My Profile</h1>
                        <p class="text-gray-400">Manage your account information</p>
                    </div>
                    <a href="?logout=1" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
                
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
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Profile Info Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                            <div class="text-center mb-6">
                                <div class="w-24 h-24 rounded-full bg-primary/20 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user text-3xl text-primary"></i>
                                </div>
                                <h2 class="text-xl font-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                                <p class="text-gray-400">@<?php echo htmlspecialchars($user['username']); ?></p>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-400 text-sm">Member Since</p>
                                    <p><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-400 text-sm">Email</p>
                                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-400 text-sm">Mobile</p>
                                    <p><?php echo htmlspecialchars($user['mobile_number']); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-400 text-sm">Location</p>
                                    <p><?php echo htmlspecialchars($user['country']); ?></p>
                                </div>
                                
                                <div>
                                    <p class="text-gray-400 text-sm">Age</p>
                                    <p><?php echo htmlspecialchars($user['age']); ?></p>
                                </div>
                                
                                <?php if (!empty($user['company_name'])): ?>
                                <div>
                                    <p class="text-gray-400 text-sm">Company</p>
                                    <p><?php echo htmlspecialchars($user['company_name']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($user['company_website'])): ?>
                                <div>
                                    <p class="text-gray-400 text-sm">Website</p>
                                    <p><a href="<?php echo htmlspecialchars($user['company_website']); ?>" target="_blank" class="text-primary hover:underline"><?php echo htmlspecialchars($user['company_website']); ?></a></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profile Edit Forms -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Edit Profile Form -->
                        <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                            <h3 class="text-xl font-bold mb-6">Edit Profile</h3>
                            <form method="POST">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">First Name *</label>
                                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Last Name *</label>
                                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Username</label>
                                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-gray-500 focus:outline-none">
                                        <p class="text-sm text-gray-500 mt-1">Username cannot be changed</p>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Email *</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Mobile Number *</label>
                                            <input type="tel" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Age *</label>
                                            <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required min="13" max="120" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Country *</label>
                                        <input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Company Name (Optional)</label>
                                        <input type="text" name="company_name" value="<?php echo htmlspecialchars($user['company_name']); ?>" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Company Website (Optional)</label>
                                        <input type="url" name="company_website" value="<?php echo htmlspecialchars($user['company_website']); ?>" placeholder="https://example.com" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <button type="submit" class="bg-primary hover:bg-indigo-700 text-white font-medium py-2 px-6 rounded-lg transition">Update Profile</button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Change Password Form -->
                        <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                            <h3 class="text-xl font-bold mb-6">Change Password</h3>
                            <form method="POST">
                                <input type="hidden" name="change_password" value="1">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-300 mb-1">Current Password</label>
                                        <input type="password" name="current_password" required class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                                            <input type="password" name="new_password" required minlength="6" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                                            <input type="password" name="confirm_password" required minlength="6" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-2 px-6 rounded-lg transition">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>