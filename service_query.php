<?php
session_start();
require_once 'config/db.php';

// Get service name from URL parameter
$service_name = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : 'Service Inquiry';

$error = '';
$success = '';

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$name = '';
$email = '';
$phone = '';
$company = '';

// If user is logged in, pre-fill their information
if ($user_logged_in) {
    try {
        $stmt = $pdo->prepare("SELECT first_name, last_name, email, mobile_number, company_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $name = $user['first_name'] . ' ' . $user['last_name'];
            $email = $user['email'];
            $phone = $user['mobile_number'];
            $company = $user['company_name'];
        }
    } catch (PDOException $e) {
        $error = "Error fetching user information: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = isset($_POST['service_name']) ? htmlspecialchars($_POST['service_name']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $company = isset($_POST['company']) ? trim($_POST['company']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validation
    if (empty($name)) {
        $error = 'Name is required';
    } elseif (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (empty($message)) {
        $error = 'Message is required';
    } else {
        try {
            // Insert the query into database
            $stmt = $pdo->prepare("INSERT INTO service_queries (service_name, name, email, phone, company, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$service_name, $name, $email, $phone, $company, $message]);
            
            $success = 'Your query has been submitted successfully! We will get back to you soon.';
            
            // Clear form fields after successful submission
            $name = '';
            $email = '';
            $phone = '';
            $company = '';
            $message = '';
        } catch (PDOException $e) {
            $error = "Error submitting query: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Query - Hypecrews</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Submit a service inquiry to Hypecrews for digital marketing, web development, or creative solutions. We'll get back to you promptly.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="https://hypecrews.com/service_query.php">
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
                        accent: '#ec4899',
                        dark: '#0B0F19',
                        light: '#f8fafc'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
    <style>
        .abstract-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            animation: float 10s infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.1); }
        }
    </style>
</head>
<body class="antialiased selection:bg-primary selection:text-white min-h-screen flex flex-col relative bg-dark">
    <?php include 'components/nav.php'; ?>

    <div class="flex-grow flex items-center justify-center relative overflow-hidden pt-32 pb-20 px-4">
        <!-- Abstract Background -->
        <div class="abstract-blob bg-primary w-96 h-96 top-[-10%] left-[-10%] pointer-events-none"></div>
        <div class="abstract-blob bg-secondary w-80 h-80 bottom-[-10%] right-[-10%] pointer-events-none" style="animation-direction: alternate-reverse; animation-delay: 2s;"></div>
        
        <div class="relative z-10 w-full max-w-2xl mx-auto rounded-3xl overflow-hidden bg-[#0f172a]/80 backdrop-blur-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-white/10 p-8 md:p-12">
            
            <a href="services.php" class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-white transition-colors mb-8 group">
                <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center mr-3 group-hover:bg-white/10 transition-colors">
                    <i class="fas fa-arrow-left text-xs group-hover:-translate-x-1 transition-transform"></i>
                </div>
                Back to Services
            </a>

            <div class="mb-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 border border-white/10 mb-6 shadow-inner">
                    <i class="fas fa-paper-plane text-2xl bg-clip-text text-transparent bg-gradient-to-r from-primary to-secondary"></i>
                </div>
                <h1 class="text-3xl md:text-4xl font-heading font-bold text-white mb-3">Service Inquiry</h1>
                <p class="text-gray-400">Interested in <span class="text-white font-medium">"<?php echo $service_name; ?>"</span>? Fill out the details below.</p>
            </div>
            
            <?php if ($error): ?>
            <div class="mb-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm animate-[fadeIn_0.3s_ease-out]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-red-500/20 flex items-center justify-center mr-4 shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <p class="text-red-300 text-sm font-medium"><?php echo $error; ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="mb-8 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 backdrop-blur-sm animate-[fadeIn_0.3s_ease-out]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center mr-4 shrink-0">
                        <i class="fas fa-check-circle text-emerald-400"></i>
                    </div>
                    <p class="text-emerald-300 text-sm font-medium"><?php echo $success; ?></p>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-5">
                <input type="hidden" name="service_name" value="<?php echo $service_name; ?>">
                
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-cube text-gray-500 group-focus-within:text-primary transition-colors"></i>
                    </div>
                    <input 
                        type="text" 
                        class="w-full pl-12 pr-4 py-4 bg-dark/50 border border-white/10 rounded-xl text-gray-400 cursor-not-allowed"
                        value="<?php echo $service_name; ?>" 
                        readonly>
                </div>
                
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-500 group-focus-within:text-primary transition-colors"></i>
                    </div>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        required
                        class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-300"
                        placeholder="Your Full Name *"
                        value="<?php echo htmlspecialchars($name); ?>">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-500 group-focus-within:text-primary transition-colors"></i>
                        </div>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-300"
                            placeholder="Email Address *"
                            value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-500 group-focus-within:text-primary transition-colors"></i>
                        </div>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-300"
                            placeholder="Phone Number"
                            maxlength="15"
                            value="<?php echo htmlspecialchars($phone); ?>">
                    </div>
                </div>
                
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-building text-gray-500 group-focus-within:text-primary transition-colors"></i>
                    </div>
                    <input 
                        type="text" 
                        id="company" 
                        name="company" 
                        class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-300"
                        placeholder="Company Name (Optional)"
                        value="<?php echo htmlspecialchars($company); ?>">
                </div>
                
                <div class="relative group">
                    <div class="absolute top-4 left-4 flex items-start pointer-events-none">
                        <i class="fas fa-comment-alt text-gray-500 group-focus-within:text-primary transition-colors"></i>
                    </div>
                    <textarea 
                        id="message" 
                        name="message" 
                        rows="4"
                        required
                        class="w-full pl-12 pr-4 py-4 bg-white/5 border border-white/10 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all duration-300 resize-none"
                        placeholder="Tell us about your requirements... *"><?php echo htmlspecialchars(isset($_POST['message']) ? $_POST['message'] : ''); ?></textarea>
                </div>
                
                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 shadow-lg hover:shadow-primary/25 transform hover:-translate-y-1 flex items-center justify-center space-x-2">
                        <span>Send Inquiry</span>
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('keypress', function(e) {
                if (e.which < 48 || e.which > 57) e.preventDefault();
            });
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                this.value = paste.replace(/[^0-9]/g, '');
            });
        });
    </script>
</body>
</html>