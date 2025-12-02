<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Login logic
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                header("Location: profile.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Login error. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.5s ease-out'
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        
        .glass-effect {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        .gradient-border {
            position: relative;
            overflow: hidden;
        }
        
        .gradient-border::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #6366f1, #8b5cf6, #ec4899, #6366f1);
            background-size: 300% 300%;
            z-index: -1;
            border-radius: 16px;
            animation: gradientShift 3s ease infinite;
            padding: 2px;
        }
        
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body class="bg-dark text-white min-h-screen p-4">
    <?php include 'components/nav.php'; ?>
    
    <div class="relative z-10 w-full max-w-6xl mx-auto">
        <div class="flex flex-col lg:flex-row gap-12 items-center">
            <!-- Left Column - Login Form -->
            <div class="w-full lg:w-1/2">
                <div class="mb-10">
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-4">Welcome Back</h1>
                    <p class="text-gray-400 text-lg">Sign in to continue your journey</p>
                </div>
                
                <div class="gradient-border rounded-xl animate-slide-up">
                    <div class="glass-effect rounded-xl p-8">
                        <?php if ($error): ?>
                        <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm animate-fade-in">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-red-300"><?php echo htmlspecialchars($error); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm animate-fade-in">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                                <div>
                                    <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="login" value="1">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Username or Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        name="username" 
                                        required 
                                        class="w-full bg-dark/50 border border-gray-700 rounded-lg pl-10 pr-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                        placeholder="Enter your username or email">
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-300">Password</label>
                                    <a href="#" class="text-sm text-primary hover:text-indigo-300 transition-colors">
                                        Forgot password?
                                    </a>
                                </div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-500"></i>
                                    </div>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        required 
                                        class="w-full bg-dark/50 border border-gray-700 rounded-lg pl-10 pr-4 py-3.5 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300"
                                        placeholder="Enter your password">
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <input 
                                    id="remember-me" 
                                    name="remember-me" 
                                    type="checkbox" 
                                    class="h-4 w-4 text-primary focus:ring-primary border-gray-600 rounded bg-dark/50">
                                <label for="remember-me" class="ml-2 block text-sm text-gray-300">
                                    Remember me
                                </label>
                            </div>
                            
                            <button 
                                type="submit" 
                                class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-medium py-3.5 px-4 rounded-lg transition-all duration-300 transform hover:-translate-y-0.5 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark focus:ring-primary">
                                Sign In
                            </button>
                        </form>
                        
                        <div class="mt-8 pt-6 border-t border-gray-700/50">
                            <p class="text-center text-gray-400">
                                Don't have an account? 
                                <a href="register.php" class="font-medium text-primary hover:text-indigo-300 transition-colors">
                                    Register now
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Graphic -->
            <div class="w-full lg:w-1/2 hidden lg:block">
                <div class="relative h-full min-h-[500px] rounded-2xl overflow-hidden">
                    <!-- Background gradient elements -->
                    <div class="absolute inset-0">
                        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary rounded-full mix-blend-soft-light filter blur-3xl opacity-30 floating-animation"></div>
                        <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-secondary rounded-full mix-blend-soft-light filter blur-3xl opacity-30 floating-animation" style="animation-delay: 1s;"></div>
                    </div>
                    
                    <!-- Main graphic content -->
                    <div class="relative z-10 h-full flex flex-col items-center justify-center p-8 text-center">
                        <div class="mb-8">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center mx-auto mb-6 floating-animation">
                                <i class="fas fa-user-lock text-white text-5xl"></i>
                            </div>
                            <h2 class="text-3xl font-bold text-white mb-4">Secure Access</h2>
                            <p class="text-gray-300 text-lg max-w-md">
                                Your digital journey begins here. Secure authentication with industry-leading protection.
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6 mt-8">
                            <div class="bg-dark/30 backdrop-blur-sm rounded-xl p-4 border border-gray-700 transition-all duration-300 hover:border-primary/50 hover:shadow-lg">
                                <i class="fas fa-shield-alt text-primary text-2xl mb-3"></i>
                                <h3 class="font-semibold mb-1">Secure</h3>
                                <p class="text-gray-400 text-sm">End-to-end encryption</p>
                            </div>
                            <div class="bg-dark/30 backdrop-blur-sm rounded-xl p-4 border border-gray-700 transition-all duration-300 hover:border-primary/50 hover:shadow-lg">
                                <i class="fas fa-bolt text-primary text-2xl mb-3"></i>
                                <h3 class="font-semibold mb-1">Fast</h3>
                                <p class="text-gray-400 text-sm">Lightning quick access</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center lg:hidden">
            <p class="text-gray-500 text-sm">
                © 2023 Hypecrews. All rights reserved.
            </p>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    
    <script>
        // Add subtle animations to form elements
        document.addEventListener('DOMContentLoaded', function() {
            const formElements = document.querySelectorAll('input, button');
            formElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
</body>
</html>