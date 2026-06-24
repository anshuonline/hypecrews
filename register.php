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
        }
    }

    // Register user if no errors
    if (empty($errors)) {
        try {
            // Use MD5 for password hashing
            $hashed_password = md5($password);
            
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
            // Log the actual error for debugging
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "Registration failed: " . $e->getMessage();
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
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Create your Hypecrews account. Sign up to access premium digital services, project management, and our creative agency dashboard.">
    <meta name="keywords" content="hypecrews register, hypecrews sign up, create hypecrews account, hypecrews membership">
    <link rel="canonical" href="https://hypecrews.com/register.php">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Register - Hypecrews | Create Your Account">
    <meta property="og:description" content="Create your Hypecrews account. Sign up to access premium digital services and our creative agency dashboard.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/register.php">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Register - Hypecrews | Create Your Account">
    <meta name="twitter:description" content="Create your Hypecrews account. Sign up to access premium digital services and our creative agency dashboard.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Register - Hypecrews",
        "description": "Create your Hypecrews account to access premium digital services and project management tools.",
        "url": "https://hypecrews.com/register.php",
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

        .abstract-blob {
            position: fixed; /* Fixed so they stay in background on long scroll */
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
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YCMZ1CPN6G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YCMZ1CPN6G');
</script>
</head>
<body class="antialiased selection:bg-primary selection:text-white min-h-screen flex flex-col relative">
    <!-- Abstract Background -->
    <div class="abstract-blob bg-primary w-[500px] h-[500px] top-[-10%] right-[-10%] pointer-events-none"></div>
    <div class="abstract-blob bg-secondary w-96 h-96 bottom-[10%] left-[-10%] pointer-events-none" style="animation-direction: alternate-reverse; animation-delay: 2s;"></div>
    
    <?php include 'components/nav.php'; ?>
    
    <div class="flex-grow flex items-center justify-center pt-32 pb-20 px-4 relative z-10">
        <div class="w-full max-w-4xl mx-auto glass-panel rounded-[2.5rem] overflow-hidden shadow-2xl reveal-up border border-white/10 p-8 md:p-12 lg:p-16">
            
            <div class="text-center mb-10 reveal-up delay-100">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/5 border border-white/10 mb-6 shadow-lg">
                    <i class="fas fa-user-astronaut text-2xl text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent"></i>
                </div>
                <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4 tracking-tight">Join the <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-secondary">Crew</span></h1>
                <p class="text-gray-400 font-light max-w-lg mx-auto">Create your account to access our premium digital services, telemetry dashboard, and command center.</p>
            </div>
            
            <?php if (!empty($errors)): ?>
            <div class="mb-8 p-6 rounded-2xl bg-red-500/10 border border-red-500/20 reveal-up delay-100 flex items-start">
                <i class="fas fa-exclamation-circle text-red-400 mt-1 mr-4 text-lg"></i>
                <div>
                    <h3 class="text-red-400 font-bold mb-2">Registration Failed</h3>
                    <ul class="list-disc pl-5 space-y-1 text-red-300/80 text-sm">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-6 reveal-up delay-200">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">First Name <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-id-card"></i></div>
                            <input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="John">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Last Name <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-id-card"></i></div>
                            <input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="Doe">
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Username <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-user"></i></div>
                            <input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="johndoe123">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Email Address <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-envelope"></i></div>
                            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="john@example.com">
                        </div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Mobile Number <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-phone"></i></div>
                            <input type="tel" name="mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="+1 234 567 8900">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Country <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-globe"></i></div>
                            <input type="text" name="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="United States">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Age <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-birthday-cake"></i></div>
                            <input type="number" name="age" min="1" max="120" value="<?php echo isset($_POST['age']) ? intval($_POST['age']) : ''; ?>" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="25">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Company Name <span class="text-gray-500 font-normal text-xs">(Optional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-building"></i></div>
                            <input type="text" name="company_name" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="Tech Innovations Inc.">
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Company Website <span class="text-gray-500 font-normal text-xs">(Optional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-link"></i></div>
                            <input type="url" name="company_website" value="<?php echo isset($_POST['company_website']) ? htmlspecialchars($_POST['company_website']) : ''; ?>" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="https://example.com">
                        </div>
                    </div>
                    
                    <!-- Security -->
                    <div class="md:col-span-2 my-2">
                        <div class="h-px bg-white/10 w-full"></div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-lock"></i></div>
                            <input type="password" name="password" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="Minimum 6 characters">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm Password <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500"><i class="fas fa-lock"></i></div>
                            <input type="password" name="confirm_password" required class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-gray-500" placeholder="Confirm your password">
                        </div>
                    </div>
                </div>
                
                <div class="flex items-start mt-6">
                    <input type="checkbox" id="terms" required class="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-600 rounded bg-dark/50 cursor-pointer shrink-0">
                    <label for="terms" class="ml-3 block text-sm text-gray-400 cursor-pointer">
                        I agree to the <a href="#" class="text-primary hover:text-white transition-colors">Terms of Service</a> and <a href="#" class="text-primary hover:text-white transition-colors">Privacy Policy</a>.
                    </label>
                </div>
                
                <div class="mt-8 pt-4">
                    <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-500 hover:to-purple-600 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] transform hover:-translate-y-1">
                        Create Account <i class="fas fa-user-plus ml-2"></i>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 text-center border-t border-white/5 pt-8">
                <p class="text-gray-400 text-sm">
                    Already have an account? 
                    <a href="login.php" class="font-bold text-white hover:text-primary transition-colors border-b border-transparent hover:border-primary pb-0.5">
                        Sign In instead
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
