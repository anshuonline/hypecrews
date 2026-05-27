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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Peak Experience</title>
    <!-- SEO Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Manage your Hypecrews profile, update personal information, and change your security credentials.">
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
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-32 pb-20 min-h-screen relative z-10">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
            <div class="flex justify-between items-end mb-10 reveal-up">
                <div>
                    <h1 class="font-heading text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Command Center</h1>
                    <p class="text-gray-400 font-light">Manage your digital identity and security settings.</p>
                </div>
                <a href="?logout=1" class="px-5 py-2.5 rounded-full border border-red-500/30 text-red-400 hover:bg-red-500/10 hover:border-red-500 transition-all flex items-center gap-2 group">
                    <i class="fas fa-sign-out-alt group-hover:-translate-x-1 transition-transform"></i> <span class="hidden sm:inline">Logout</span>
                </a>
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
                <!-- Sidebar Profile Card -->
                <div class="w-full lg:w-1/3 reveal-left delay-100">
                    <div class="glass-card rounded-3xl p-8 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-[40px] group-hover:bg-primary/20 transition-all"></div>
                        
                        <div class="relative z-10 text-center mb-8">
                            <div class="w-28 h-28 rounded-full bg-gradient-to-br from-indigo-500/20 to-purple-500/20 p-1 mx-auto mb-4 relative shadow-[0_0_30px_rgba(99,102,241,0.2)]">
                                <div class="w-full h-full rounded-full bg-[#0B0F19] flex items-center justify-center border border-white/5">
                                    <i class="fas fa-user-astronaut text-4xl text-transparent bg-clip-text bg-gradient-to-br from-indigo-400 to-purple-400"></i>
                                </div>
                                <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-500 border-2 border-[#0B0F19] rounded-full"></div>
                            </div>
                            <h2 class="font-heading text-2xl font-bold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                            <p class="text-primary font-medium tracking-wide text-sm mt-1">@<?php echo htmlspecialchars($user['username']); ?></p>
                        </div>
                        
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-calendar-alt w-4 text-indigo-400"></i> Member</span>
                                <span class="text-sm font-medium"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-envelope w-4 text-purple-400"></i> Email</span>
                                <span class="text-sm font-medium truncate max-w-[150px]" title="<?php echo htmlspecialchars($user['email']); ?>"><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-phone w-4 text-cyan-400"></i> Mobile</span>
                                <span class="text-sm font-medium"><?php echo htmlspecialchars($user['mobile_number']); ?></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-globe-americas w-4 text-emerald-400"></i> Location</span>
                                <span class="text-sm font-medium"><?php echo htmlspecialchars($user['country']); ?></span>
                            </div>
                            
                            <?php if (!empty($user['company_name'])): ?>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-building w-4 text-amber-400"></i> Corp</span>
                                <span class="text-sm font-medium truncate max-w-[150px]"><?php echo htmlspecialchars($user['company_name']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Forms -->
                <div class="w-full lg:w-2/3 space-y-8">
                    <!-- Profile Update -->
                    <div class="glass-card rounded-3xl p-8 reveal-right delay-200 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full blur-[60px] pointer-events-none"></div>
                        <h3 class="font-heading text-2xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-id-card text-secondary"></i> Identity Parameters
                        </h3>
                        
                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">First Name</label>
                                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Last Name</label>
                                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Primary Email</label>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full input-glass rounded-xl px-4 py-3">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Mobile Directive</label>
                                    <input type="tel" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Age / Country</label>
                                    <div class="flex gap-3">
                                        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required min="13" max="120" class="w-1/3 input-glass rounded-xl px-4 py-3">
                                        <input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" required class="w-2/3 input-glass rounded-xl px-4 py-3">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Enterprise Name (Opt)</label>
                                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($user['company_name']); ?>" class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Enterprise URL (Opt)</label>
                                    <input type="url" name="company_website" value="<?php echo htmlspecialchars($user['company_website']); ?>" placeholder="https://" class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-white text-dark font-bold hover:scale-105 transition-transform shadow-[0_0_20px_rgba(255,255,255,0.2)]">
                                Sync Data <i class="fas fa-sync-alt ml-2"></i>
                            </button>
                        </form>
                    </div>
                    
                    <!-- Security -->
                    <div class="glass-card rounded-3xl p-8 reveal-right delay-300 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-rose-500/5 rounded-full blur-[60px] pointer-events-none"></div>
                        <h3 class="font-heading text-2xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-shield-alt text-rose-400"></i> Security Credentials
                        </h3>
                        
                        <form method="POST">
                            <input type="hidden" name="change_password" value="1">
                            <div class="mb-5">
                                <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Current Key</label>
                                <input type="password" name="current_password" required class="w-full input-glass rounded-xl px-4 py-3">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">New Key</label>
                                    <input type="password" name="new_password" required minlength="6" class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold tracking-wider text-gray-400 uppercase mb-2">Verify Key</label>
                                    <input type="password" name="confirm_password" required minlength="6" class="w-full input-glass rounded-xl px-4 py-3">
                                </div>
                            </div>
                            
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-gradient-to-r from-rose-500 to-pink-500 text-white font-bold hover:scale-105 hover:shadow-[0_0_20px_rgba(244,63,94,0.4)] transition-all">
                                Update Authorization <i class="fas fa-lock ml-2"></i>
                            </button>
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