<?php
session_start();
require_once '../config/db.php';
require_once 'GoogleAuthenticator.php';

// Initialize all variables to prevent undefined variable warnings
$error = '';
$show2FA = false;
$require2FASetup = false;
$username = '';
$postPassword = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store POST data for form persistence
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $postPassword = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Initialize Google Authenticator
    $ga = new GoogleAuthenticator();
    
    if (isset($_POST['step']) && $_POST['step'] == '2fa_setup') {
        // Step 2: Set up 2FA for the first time
        try {
            // Check credentials against database
            $stmt = $pdo->prepare("SELECT id, username, google_auth_secret, google_auth_enabled FROM admins WHERE username = ? AND password = ?");
            $stmt->execute([$username, md5($postPassword)]);
            $admin = $stmt->fetch();
            
            if ($admin) {
                // Generate and save secret if not exists
                if (empty($admin['google_auth_secret'])) {
                    $secret = $ga->createSecret();
                    $updateStmt = $pdo->prepare("UPDATE admins SET google_auth_secret = ? WHERE id = ?");
                    $updateStmt->execute([$secret, $admin['id']]);
                    $admin['google_auth_secret'] = $secret;
                }
                
                // Verify the 2FA code
                $code = isset($_POST['code']) ? $_POST['code'] : '';
                if ($ga->verifyCode($admin['google_auth_secret'], $code)) {
                    // Enable 2FA permanently
                    $updateStmt = $pdo->prepare("UPDATE admins SET google_auth_enabled = 1 WHERE id = ?");
                    $updateStmt->execute([$admin['id']]);
                    
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_id'] = $admin['id'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid authentication code. Please try again.';
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == '2fa') {
        // Step 2: Verify 2FA code for existing users
        $code = isset($_POST['code']) ? $_POST['code'] : '';
        
        try {
            // Check credentials against database
            $stmt = $pdo->prepare("SELECT id, username, google_auth_secret, google_auth_enabled FROM admins WHERE username = ? AND password = ?");
            $stmt->execute([$username, md5($postPassword)]);
            $admin = $stmt->fetch();
            
            if ($admin && $admin['google_auth_enabled'] == 1) {
                // Verify the 2FA code
                if ($ga->verifyCode($admin['google_auth_secret'], $code)) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_id'] = $admin['id'];
                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid authentication code';
                }
            } else {
                $error = 'Two-factor authentication is required for this account';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    } else {
        // Step 1: Verify username/password
        try {
            // Check credentials against database
            $stmt = $pdo->prepare("SELECT id, username, google_auth_secret, google_auth_enabled FROM admins WHERE username = ? AND password = ?");
            $stmt->execute([$username, md5($postPassword)]);
            $admin = $stmt->fetch();
            
            if ($admin) {
                // Check if 2FA is required
                if ($admin['google_auth_enabled'] == 1) {
                    // Show 2FA form for existing users
                    $show2FA = true;
                } elseif (is_null($admin['google_auth_secret']) || empty($admin['google_auth_secret'])) {
                    // First time login - require 2FA setup
                    $require2FASetup = true;
                } else {
                    // 2FA setup in progress
                    $require2FASetup = true;
                }
            } else {
                $error = 'Invalid username or password';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Hypecrews</title>
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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .login-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
        }
        
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
        
        .btn-primary {
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(99, 102, 241, 0.4);
        }
        
        .logo-icon {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .form-step {
            transition: opacity 0.3s ease;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto">
            <div class="login-container rounded-2xl shadow-xl p-8">
                <div class="text-center mb-8">
                    <div class="mx-auto bg-gradient-to-br from-primary to-secondary rounded-xl w-16 h-16 flex items-center justify-center mb-4 logo-icon">
                        <i class="fas fa-user-shield text-white text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold">
                        <?php 
                        if (!empty($show2FA) && $show2FA) {
                            echo 'Two-Factor Authentication';
                        } elseif (!empty($require2FASetup) && $require2FASetup) {
                            echo 'Setup Two-Factor Authentication';
                        } else {
                            echo 'Admin Login';
                        }
                        ?>
                    </h1>
                    <p class="text-gray-400 mt-2">
                        <?php 
                        if (!empty($show2FA) && $show2FA) {
                            echo 'Enter the code from your authenticator app';
                        } elseif (!empty($require2FASetup) && $require2FASetup) {
                            echo 'Scan the QR code to set up two-factor authentication';
                        } else {
                            echo 'Access the audition management panel';
                        }
                        ?>
                    </p>
                </div>
                
                <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm animate-pulse">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-300 font-medium"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($show2FA) && $show2FA): ?>
                <!-- 2FA Verification Form -->
                <form method="POST" class="space-y-6 form-step">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($postPassword ?? ''); ?>">
                    <input type="hidden" name="step" value="2fa">
                    
                    <div>
                        <label for="code" class="block text-gray-300 font-medium mb-2">Authentication Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-500"></i>
                            </div>
                            <input type="text" id="code" name="code" class="w-full pl-10 pr-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm text-center text-xl tracking-widest input-field" placeholder="000000" maxlength="6" required autofocus>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Enter the 6-digit code from your Google Authenticator app</p>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                        Verify Code
                    </button>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-gray-400 hover:text-gray-300 text-sm transition-colors">
                            <i class="fas fa-arrow-left mr-1"></i> Back to login
                        </a>
                    </div>
                </form>
                <?php elseif (!empty($require2FASetup) && $require2FASetup): ?>
                <!-- 2FA Setup Form -->
                <?php
                // Generate QR code for setup
                $ga = new GoogleAuthenticator();
                $qrCodeUrl = '';
                $secret = '';
                
                try {
                    $stmt = $pdo->prepare("SELECT google_auth_secret FROM admins WHERE username = ?");
                    $stmt->execute([$username]);
                    $admin = $stmt->fetch();
                    
                    // Generate secret if not exists
                    if (empty($admin['google_auth_secret'])) {
                        $secret = $ga->createSecret();
                        // We won't save it here, it will be saved during verification
                    } else {
                        $secret = $admin['google_auth_secret'];
                    }
                    
                    $qrCodeUrl = $ga->getQRCodeUrl('Hypecrews Admin', $secret);
                } catch (Exception $e) {
                    // Handle exception silently
                }
                ?>
                <form method="POST" class="space-y-6 form-step">
                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($postPassword ?? ''); ?>">
                    <input type="hidden" name="step" value="2fa_setup">
                    
                    <div class="text-center mb-6">
                        <div class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 rounded-xl p-6 mb-6 border border-gray-700">
                            <p class="text-gray-300 mb-4">Two-factor authentication is required for all admin accounts. Please scan the QR code with Google Authenticator and enter the code below.</p>
                            
                            <?php if (!empty($qrCodeUrl)): ?>
                            <div class="flex justify-center mb-4">
                                <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code" class="rounded-lg border-2 border-gray-600 bg-white p-2">
                            </div>
                            
                            <div class="bg-dark/50 rounded-lg p-3">
                                <p class="text-sm text-gray-400 mb-1">Secret Key:</p>
                                <p class="font-mono text-sm break-all text-gray-300"><?php echo htmlspecialchars($secret); ?></p>
                            </div>
                            <?php else: ?>
                            <div class="bg-red-900/50 border border-red-700 rounded-lg p-4">
                                <p class="text-red-300">Unable to generate QR code. Please contact administrator.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div>
                        <label for="code" class="block text-gray-300 font-medium mb-2">Authentication Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-500"></i>
                            </div>
                            <input type="text" id="code" name="code" class="w-full pl-10 pr-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm text-center text-xl tracking-widest input-field" placeholder="000000" maxlength="6" required autofocus>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Enter the 6-digit code from your Google Authenticator app</p>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                        Complete Setup
                    </button>
                </form>
                
                <div class="mt-6 text-center text-sm text-gray-500">
                    <p class="mb-2">Don't have Google Authenticator?</p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fab fa-apple mr-1"></i> App Store
                        </a>
                        <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fab fa-google-play mr-1"></i> Google Play
                        </a>
                    </div>
                </div>
                <?php else: ?>
                <!-- Login Form -->
                <form method="POST" class="space-y-6 form-step">
                    <div>
                        <label for="username" class="block text-gray-300 font-medium mb-2">Username</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <input type="text" id="username" name="username" class="w-full pl-10 pr-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm input-field" placeholder="Enter your username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-300 font-medium mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input type="password" id="password" name="password" class="w-full pl-10 pr-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm input-field" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full btn-primary bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                        Sign In
                    </button>
                </form>
                
                <div class="mt-8 text-center text-sm text-gray-500">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>
                        <p>Two-factor authentication is required for all admin accounts</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-6 text-xs text-gray-600">
                <p>© 2023 Hypecrews Admin Panel. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>