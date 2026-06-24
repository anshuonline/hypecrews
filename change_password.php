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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Peak Experience</title>
    <!-- SEO Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Change your Hypecrews account password and manage your security credentials.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        body { background-color: #0B0F19; color: #f8fafc; overflow-x: hidden; }
        .ambient-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            background: radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.05), transparent 25%),
                        radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.05), transparent 25%);
            pointer-events: none;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            background: rgba(0, 0, 0, 0.2);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
            outline: none;
        }
        .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-left { opacity: 0; transform: translateX(-30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-right { opacity: 0; transform: translateX(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-up.active, .reveal-left.active, .reveal-right.active { opacity: 1; transform: translate(0); }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
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
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-32 pb-20 min-h-screen relative z-10">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
            <div class="flex justify-between items-end mb-10 reveal-up">
                <div>
                    <h1 class="font-heading text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Security</h1>
                    <p class="text-gray-400 font-light">Manage your security credentials.</p>
                </div>
            </div>
            
            <?php if ($error): ?>
            <div class="mb-8 p-4 rounded-2xl bg-red-900/20 border border-red-500/30 backdrop-blur-md reveal-up flex items-center shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-4 animate-pulse"></i>
                <p class="text-red-200 font-medium"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="mb-8 p-4 rounded-2xl bg-emerald-900/20 border border-emerald-500/30 backdrop-blur-md reveal-up flex items-center shadow-[0_0_20px_rgba(16,185,129,0.1)]">
                <i class="fas fa-check-circle text-emerald-400 text-xl mr-4"></i>
                <p class="text-emerald-200 font-medium"><?php echo htmlspecialchars($success); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Sidebar Menu -->
                <div class="w-full lg:w-1/4 xl:w-1/5 reveal-left delay-100 flex flex-col h-full">
                    <?php 
                    $current_user_page = 'change_password';
                    include 'components/user_sidebar.php'; 
                    ?>
                </div>
                
                <!-- Forms -->
                <div class="w-full lg:w-3/4 xl:w-4/5 space-y-8">
                    <!-- Security -->
                    <div class="glass-card rounded-3xl p-8 reveal-right delay-200 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-rose-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                        <h3 class="font-heading text-2xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-shield-alt text-rose-400"></i> Security Credentials
                        </h3>
                        
                        <form method="POST">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-6 backdrop-blur-sm shadow-lg">
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-rose-400 transition-colors mb-2 sm:mb-0">Current Key</label>
                                    <input type="password" name="current_password" required placeholder="••••••••" class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-rose-400 transition-colors mb-2 sm:mb-0">New Key</label>
                                    <input type="password" name="new_password" required minlength="6" placeholder="••••••••" class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-rose-400 transition-colors mb-2 sm:mb-0">Verify Key</label>
                                    <input type="password" name="confirm_password" required minlength="6" placeholder="••••••••" class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                            </div>
                            
                            <div class="flex justify-end">
                                <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-full bg-rose-500/10 border border-rose-500/50 text-rose-400 font-bold hover:bg-rose-500 hover:text-white transition-all shadow-[0_0_15px_rgba(244,63,94,0.1)] flex items-center justify-center gap-2">
                                    Update Authorization <i class="fas fa-lock text-sm"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>

