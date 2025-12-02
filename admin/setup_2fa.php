<?php
require_once 'auth.php';
require_once 'GoogleAuthenticator.php';
require_once '../config/db.php';

// Initialize Google Authenticator
$ga = new GoogleAuthenticator();

// Get current admin info
$adminId = $_SESSION['admin_id'];

try {
    // Get admin details
    $stmt = $pdo->prepare("SELECT username, google_auth_secret, google_auth_enabled FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        header('Location: index.php');
        exit;
    }
    
    // Generate new secret if not exists
    if (empty($admin['google_auth_secret'])) {
        $secret = $ga->createSecret();
        // Save secret to database
        $updateStmt = $pdo->prepare("UPDATE admins SET google_auth_secret = ? WHERE id = ?");
        $updateStmt->execute([$secret, $adminId]);
        $admin['google_auth_secret'] = $secret;
    }
    
    // Generate QR code URL
    $qrCodeUrl = $ga->getQRCodeUrl('Hypecrews Admin', $admin['google_auth_secret']);
    
    // Handle form submission
    $error = '';
    $success = '';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['enable_2fa'])) {
            $code = $_POST['code'];
            
            // Verify the code
            if ($ga->verifyCode($admin['google_auth_secret'], $code)) {
                // Enable 2FA (cannot be disabled once enabled)
                $updateStmt = $pdo->prepare("UPDATE admins SET google_auth_enabled = 1 WHERE id = ?");
                $updateStmt->execute([$adminId]);
                $success = 'Two-factor authentication has been enabled successfully!';
                $admin['google_auth_enabled'] = 1;
            } else {
                $error = 'Invalid authentication code. Please try again.';
            }
        }
        // Note: Removed disable 2FA option as it's now mandatory
    }
    
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - Hypecrews Admin</title>
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
    <style>
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.9));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        
        .setup-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
        }
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Navigation -->
    <nav class="bg-light/80 backdrop-blur-sm border-b border-gray-800">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <i class="fas fa-shield-alt text-primary text-2xl mr-3"></i>
                    <h1 class="text-xl font-bold">2FA Setup</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="index.php" class="text-gray-300 hover:text-white flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                    <span class="text-gray-300">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
                    <a href="index.php?logout=1" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold mb-2">Two-Factor Authentication</h1>
                <p class="text-gray-400">Secure your admin account with Google Authenticator</p>
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
            
            <div class="setup-container rounded-2xl shadow-xl p-8">
                <?php if ($admin['google_auth_enabled']): ?>
                <!-- 2FA Enabled -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-green-900/50 flex items-center justify-center mb-6">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Two-Factor Authentication Active</h2>
                    <p class="text-gray-400 mb-8">Your account is secured with mandatory two-factor authentication.</p>
                    <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-4 mb-6">
                        <p class="text-yellow-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            Two-factor authentication is mandatory for all admin accounts and cannot be disabled.
                        </p>
                    </div>
                    <a href="index.php" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </a>
                </div>
                <?php else: ?>
                <!-- 2FA Setup -->
                <div class="text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-blue-900/50 flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-blue-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">Set Up Two-Factor Authentication</h2>
                    <p class="text-gray-400 mb-8">Scan the QR code with Google Authenticator app and enter the code below.</p>
                    
                    <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-4 mb-6">
                        <p class="text-yellow-300">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Two-factor authentication is mandatory for all admin accounts.
                        </p>
                    </div>
                    
                    <div class="flex flex-col items-center mb-8">
                        <div class="mb-6">
                            <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code" class="rounded-lg border-2 border-gray-700">
                        </div>
                        
                        <div class="bg-dark/50 rounded-lg p-4 mb-6 w-full max-w-md">
                            <p class="text-sm text-gray-400 mb-2">Secret Key (manual entry):</p>
                            <p class="font-mono text-lg break-all"><?php echo htmlspecialchars($admin['google_auth_secret']); ?></p>
                        </div>
                        
                        <form method="POST" class="w-full max-w-md">
                            <div class="mb-6">
                                <label for="code" class="block text-gray-300 font-medium mb-2 text-left">Authentication Code</label>
                                <input type="text" id="code" name="code" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm text-center text-xl tracking-widest" placeholder="000000" maxlength="6" required>
                                <p class="mt-2 text-sm text-gray-500">Enter the 6-digit code from your authenticator app</p>
                            </div>
                            
                            <button type="submit" name="enable_2fa" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                                Enable 2FA
                            </button>
                        </form>
                    </div>
                    
                    <div class="text-sm text-gray-500">
                        <p>Don't have Google Authenticator? Download it from your app store:</p>
                        <div class="flex justify-center space-x-4 mt-2">
                            <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="text-blue-400 hover:text-blue-300">
                                <i class="fab fa-apple mr-1"></i> App Store
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="text-blue-400 hover:text-blue-300">
                                <i class="fab fa-google-play mr-1"></i> Google Play
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>