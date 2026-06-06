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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066cc', // Apple Blue
                        apple_text: '#1d1d1f', // Apple Dark text
                        apple_muted: '#86868b', // Apple Muted text
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'Roboto', 'sans-serif']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f5f5f7; 
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            position: relative;
        }
        
        /* The colorful blurred background that makes the glassmorphism visible */
        .glass-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: #f5f5f9;
            overflow: hidden;
            pointer-events: none;
        }
        
        .glass-bg::before, .glass-bg::after, .glass-blob {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
        }
        
        .glass-bg::before {
            background: #dbeafe; /* Light blue */
            width: 600px;
            height: 600px;
            top: -100px;
            right: -100px;
        }
        
        .glass-bg::after {
            background: #f3e8ff; /* Light purple */
            width: 500px;
            height: 500px;
            bottom: -100px;
            left: 10%;
        }
        
        .glass-blob {
            background: #e0f2fe; /* Sky blue */
            width: 400px;
            height: 400px;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Apple-style thin, invisible scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        
        /* Glass panel utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }

        input[type="text"], input[type="password"] { 
            background-color: rgba(255, 255, 255, 0.8) !important; 
            border-color: rgba(0, 0, 0, 0.1) !important; 
            color: #1d1d1f !important; 
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02) inset;
        }
        input:focus { 
            border-color: #0066cc !important; 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15) !important; 
            background-color: #ffffff !important;
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-apple_text">

    <!-- Abstract blurred colorful background -->
    <div class="glass-bg">
        <div class="glass-blob"></div>
    </div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Apple-style Glass Header -->
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div>
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Admin Profile</h1>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                
                <?php if ($error): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center max-w-3xl mx-auto">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center max-w-3xl mx-auto">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="max-w-3xl mx-auto glass-panel rounded-[2rem] p-8 md:p-12 shadow-sm relative overflow-hidden">
                    <!-- Background Decorator -->
                    <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-100 rounded-bl-full opacity-40 blur-2xl pointer-events-none"></div>
                    
                    <form method="POST" enctype="multipart/form-data" class="space-y-8 relative z-10">
                        
                        <!-- Profile Image Section -->
                        <div class="flex flex-col items-center mb-10">
                            <div class="relative w-36 h-36 rounded-full overflow-hidden border-[6px] border-white mb-5 bg-white flex items-center justify-center shadow-md group">
                                <?php if(!empty($admin['profile_image'])): ?>
                                    <img id="profile_preview" src="../<?php echo htmlspecialchars($admin['profile_image']); ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img id="profile_preview" src="" class="w-full h-full object-cover hidden">
                                    <i id="profile_icon" class="fas fa-user text-6xl text-gray-300"></i>
                                <?php endif; ?>
                                
                                <label for="profile_upload" class="absolute inset-0 bg-black/40 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity backdrop-blur-sm">
                                    <i class="fas fa-camera text-2xl mb-1"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider">Change</span>
                                </label>
                            </div>
                            <input type="file" id="profile_upload" name="profile_image" accept="image/*" class="hidden">
                            <p class="text-xs font-bold text-apple_muted uppercase tracking-wider">Click image to upload new</p>
                        </div>
                        
                        <div class="bg-white/40 p-8 rounded-2xl border border-white/60 shadow-inner">
                            <h3 class="text-lg font-bold text-apple_text mb-6 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-user-edit text-primary mr-3"></i> Profile Details
                            </h3>
                            <!-- Username -->
                            <div>
                                <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Username *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-apple_muted opacity-70"></i>
                                    </div>
                                    <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required class="w-full rounded-xl pl-12 pr-4 py-3.5 border transition-all text-[15px]">
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/40 p-8 rounded-2xl border border-white/60 shadow-inner">
                            <h3 class="text-lg font-bold text-apple_text mb-4 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-shield-alt text-primary mr-3"></i> Change Password
                            </h3>
                            <p class="text-xs font-semibold text-apple_muted mb-6 bg-white/60 inline-block px-3 py-1.5 rounded-lg border border-black/5">Leave blank if you don't want to change your password.</p>
                            
                            <div class="space-y-5">
                                <!-- New Password -->
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">New Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-apple_muted opacity-70"></i>
                                        </div>
                                        <input type="password" name="new_password" class="w-full rounded-xl pl-12 pr-4 py-3.5 border transition-all text-[15px]" placeholder="Enter new password">
                                    </div>
                                </div>
                                
                                <!-- Confirm Password -->
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Confirm New Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-apple_muted opacity-70"></i>
                                        </div>
                                        <input type="password" name="confirm_password" class="w-full rounded-xl pl-12 pr-4 py-3.5 border transition-all text-[15px]" placeholder="Confirm new password">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="bg-primary hover:bg-blue-600 text-white font-bold py-4 px-10 rounded-xl transition-all transform hover:-translate-y-0.5 shadow-md hover:shadow-lg w-full md:w-auto text-[15px] flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Save Profile Changes
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('profile_upload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profile_preview');
                    const icon = document.getElementById('profile_icon');
                    
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (icon) {
                        icon.classList.add('hidden');
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
