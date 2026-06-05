<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'profile';

$error = '';
$success = '';

// Fetch current admin details
try {
    $stmt = $pdo->prepare("SELECT * FROM administrators WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        // Fallback or logout if somehow admin doesn't exist
        header('Location: logout.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Error fetching profile: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Directory for admin profile images
    $upload_dir = '../uploads/admins/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Validation
    if (empty($username)) {
        $error = "Username is required.";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (!empty($new_password) && strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Check if username is already taken by another admin
            $stmt = $pdo->prepare("SELECT id FROM administrators WHERE username = ? AND id != ?");
            $stmt->execute([$username, $_SESSION['admin_id']]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Username already exists. Please choose another.");
            }
            
            // Handle Profile Image Upload
            $profile_image = $admin['profile_image'];
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $image_name = uniqid('admin_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $image_name)) {
                        // Delete old image if exists
                        if (!empty($profile_image) && file_exists('../' . $profile_image)) {
                            unlink('../' . $profile_image);
                        }
                        $profile_image = 'uploads/admins/' . $image_name;
                    }
                } else {
                    throw new Exception("Invalid image format. Only JPG, PNG, WEBP allowed.");
                }
            }
            
            // Update Query
            if (!empty($new_password)) {
                // Update with password
                $stmt = $pdo->prepare("UPDATE administrators SET username = ?, password = ?, profile_image = ? WHERE id = ?");
                $stmt->execute([$username, md5($new_password), $profile_image, $_SESSION['admin_id']]);
                $log_action = "Updated profile (changed username/password)";
            } else {
                // Update without password
                $stmt = $pdo->prepare("UPDATE administrators SET username = ?, profile_image = ? WHERE id = ?");
                $stmt->execute([$username, $profile_image, $_SESSION['admin_id']]);
                $log_action = "Updated profile details";
            }
            
            // Update session if username changed
            $_SESSION['admin_username'] = $username;
            
            $pdo->commit();
            
            logAdminActivity($pdo, 'UPDATE_PROFILE', $log_action);
            
            $success = "Profile updated successfully.";
            
            // Refresh admin data
            $stmt = $pdo->prepare("SELECT * FROM administrators WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Hypecrews</title>
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
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
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
                    <h2 class="text-2xl font-bold">Admin Profile</h2>
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
                
                <div class="max-w-2xl mx-auto bg-light rounded-xl p-8 shadow-xl border border-gray-800">
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        
                        <!-- Profile Image Section -->
                        <div class="flex flex-col items-center mb-8">
                            <div class="relative w-32 h-32 rounded-full overflow-hidden border-4 border-gray-700 mb-4 bg-dark flex items-center justify-center shadow-lg">
                                <?php if(!empty($admin['profile_image'])): ?>
                                    <img src="../<?php echo htmlspecialchars($admin['profile_image']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-5xl text-gray-500"></i>
                                <?php endif; ?>
                                
                                <label for="profile_upload" class="absolute bottom-0 left-0 right-0 bg-black/60 text-center py-2 cursor-pointer hover:bg-primary/80 transition-colors">
                                    <i class="fas fa-camera text-sm"></i>
                                </label>
                            </div>
                            <input type="file" id="profile_upload" name="profile_image" accept="image/*" class="hidden">
                            <p class="text-sm text-gray-400">Click camera icon to change picture</p>
                        </div>
                        
                        <!-- Username -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Username *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required class="w-full bg-dark border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-700 my-6 pt-6">
                            <h3 class="text-lg font-medium text-white mb-4">Change Password</h3>
                            <p class="text-sm text-gray-400 mb-4">Leave blank if you don't want to change your password.</p>
                            
                            <!-- New Password -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-300 mb-2">New Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-500"></i>
                                    </div>
                                    <input type="password" name="new_password" class="w-full bg-dark border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Enter new password">
                                </div>
                            </div>
                            
                            <!-- Confirm Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Confirm New Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-500"></i>
                                    </div>
                                    <input type="password" name="confirm_password" class="w-full bg-dark border border-gray-700 rounded-lg pl-10 pr-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" placeholder="Confirm new password">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-transform hover:-translate-y-1 shadow-lg">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
