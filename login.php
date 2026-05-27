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
            
            // Use MD5 to verify password
            if ($user && $user['password'] === md5($password)) {
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
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Log in to your Hypecrews account. Access your dashboard, manage projects, and connect with our digital agency services.">
    <meta name="keywords" content="hypecrews login, hypecrews account, hypecrews sign in, hypecrews dashboard">
    <link rel="canonical" href="https://hypecrews.com/login.php">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Login - Hypecrews">
    <meta property="og:description" content="Log in to your Hypecrews account. Access your dashboard and manage your projects.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/login.php">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Login - Hypecrews">
    <meta name="twitter:description" content="Log in to your Hypecrews account. Access your dashboard and manage your projects.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Login - Hypecrews",
        "description": "Log in to your Hypecrews account to access your dashboard and manage projects.",
        "url": "https://hypecrews.com/login.php",
        "publisher": {
            "@type": "Organization",
            "name": "Hypecrews",
            "logo": {
                "@type": "ImageObject",
                "url": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png"
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4',
                        dark: '#0B0F19',
                        surface: '#151b2b'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B0F19;
            color: #f8fafc;
        }
        
        .glass-panel {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .glass-input {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
            outline: none;
        }
        
        .reveal-up {
            opacity: 0;
            transform: translateY(30px);
            animation: slideUpFade 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        
        @keyframes slideUpFade {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        .abstract-blob {
            position: absolute;
            filter: blur(80px);
            z-index: 0;
            border-radius: 50%;
            animation: pulse-slow 8s infinite alternate;
        }

        @keyframes pulse-slow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.3; }
            100% { transform: scale(1.1) translate(20px, -20px); opacity: 0.5; }
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white min-h-screen flex flex-col">
    <?php include 'components/nav.php'; ?>
    
    <div class="flex-grow flex items-center justify-center relative overflow-hidden pt-24 pb-12 px-4">
        <!-- Abstract Background -->
        <div class="abstract-blob bg-primary w-96 h-96 top-[-10%] left-[-10%] pointer-events-none"></div>
        <div class="abstract-blob bg-secondary w-80 h-80 bottom-[-10%] right-[-10%] pointer-events-none" style="animation-direction: alternate-reverse; animation-delay: 2s;"></div>
        <div class="abstract-blob bg-accent/30 w-72 h-72 top-[40%] left-[40%] pointer-events-none"></div>
        
        <div class="relative z-10 w-full max-w-5xl mx-auto flex flex-col lg:flex-row rounded-3xl overflow-hidden glass-panel reveal-up shadow-2xl">
            <!-- Left Side - Form -->
            <div class="w-full lg:w-1/2 p-8 md:p-12 lg:p-16 flex flex-col justify-center bg-dark/60">
                <div class="mb-10 reveal-up delay-100">
                    <a href="index.php" class="inline-block mb-6 text-gray-400 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Home
                    </a>
                    <h1 class="font-heading text-4xl font-bold text-white mb-2 tracking-tight">Welcome <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Back</span></h1>
                    <p class="text-gray-400 font-light">Access your dashboard to continue.</p>
                </div>
                
                <?php if ($error): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 reveal-up delay-100 flex items-start">
                    <i class="fas fa-exclamation-circle text-red-400 mt-0.5 mr-3"></i>
                    <p class="text-red-300 text-sm"><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 reveal-up delay-100 flex items-start">
                    <i class="fas fa-check-circle text-emerald-400 mt-0.5 mr-3"></i>
                    <p class="text-emerald-300 text-sm"><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6 reveal-up delay-200">
                    <input type="hidden" name="login" value="1">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Username or Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-500"></i>
                            </div>
                            <input 
                                type="text" 
                                name="username" 
                                required 
                                class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500"
                                placeholder="Enter your username">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-sm font-semibold text-gray-300">Password</label>
                            <a href="#" class="text-xs font-medium text-primary hover:text-indigo-300 transition-colors">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-500"></i>
                            </div>
                            <input 
                                type="password" 
                                name="password" 
                                required 
                                class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500"
                                placeholder="Enter your password">
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember-me" 
                            type="checkbox" 
                            class="h-4 w-4 text-primary focus:ring-primary border-gray-600 rounded bg-dark/50 cursor-pointer">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-400 cursor-pointer">
                            Keep me logged in
                        </label>
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-500 hover:to-purple-600 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] transform hover:-translate-y-1">
                        Sign In <i class="fas fa-sign-in-alt ml-2"></i>
                    </button>
                </form>
                
                <div class="mt-8 text-center reveal-up delay-300">
                    <p class="text-gray-400 text-sm">
                        Don't have an account? 
                        <a href="register.php" class="font-bold text-white hover:text-primary transition-colors border-b border-transparent hover:border-primary pb-0.5">
                            Create one now
                        </a>
                    </p>
                </div>
            </div>
            
            <!-- Right Side - Visuals -->
            <div class="w-full lg:w-1/2 hidden lg:flex flex-col justify-between p-12 bg-gradient-to-br from-indigo-900/20 to-purple-900/10 border-l border-white/5 relative">
                <div class="absolute inset-0 bg-[url('graphics/grid.svg')] opacity-20 mix-blend-overlay"></div>
                
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center border border-white/10 backdrop-blur-md mb-6 shadow-xl">
                        <i class="fas fa-shield-alt text-2xl text-primary drop-shadow-[0_0_10px_rgba(99,102,241,0.8)]"></i>
                    </div>
                    <h2 class="font-heading text-3xl font-bold text-white mb-4">Enterprise Grade <br/>Security</h2>
                    <p class="text-gray-400 font-light leading-relaxed max-w-sm">
                        Your account is protected by industry-leading encryption and advanced telemetry algorithms.
                    </p>
                </div>
                
                <div class="relative z-10 space-y-4">
                    <div class="glass-panel p-4 rounded-2xl flex items-center border border-white/5 bg-white/5">
                        <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-check text-emerald-400"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">SSL Encrypted Connection</h4>
                            <p class="text-xs text-gray-400">Data transmitted securely over HTTPS.</p>
                        </div>
                    </div>
                    
                    <div class="glass-panel p-4 rounded-2xl flex items-center border border-white/5 bg-white/5">
                        <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center mr-4 shrink-0">
                            <i class="fas fa-fingerprint text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-white">Identity Verification</h4>
                            <p class="text-xs text-gray-400">Multi-layered authentication protocols.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>