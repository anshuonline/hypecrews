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
        
        /* Aurora Flow Background */
        @keyframes auroraflow { 0% { transform: rotate(0deg) scale(1); } 50% { transform: rotate(180deg) scale(1.2); } 100% { transform: rotate(360deg) scale(1); } }
        .ambient-bg {
            position: fixed; top: -50%; left: -50%; width: 200%; height: 200%; z-index: -1;
            background-image: 
                radial-gradient(ellipse at 50% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.15) 0%, transparent 40%);
            animation: auroraflow 25s linear infinite;
            filter: blur(80px);
            pointer-events: none;
        }
        
        /* Magic Button */
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .magic-btn-wrapper { position: relative; display: inline-flex; overflow: hidden; border-radius: 9999px; padding: 2px; }
        .magic-border { position: absolute; inset: -1000%; animation: spin 2s linear infinite; background: conic-gradient(from 90deg at 50% 50%, #e2cbff 0%, #393bb2 50%, #e2cbff 100%); }
        
        /* Bento Card Hover Spotlight */
        .bento-card { position: relative; border-radius: 1.5rem; border: 1px solid rgba(255,255,255,0.1); overflow: hidden; background: rgba(15,23,42,0.4); backdrop-filter: blur(20px); }
        .bento-card::before {
            content: ''; position: absolute; inset: 0; opacity: 0; transition: opacity 0.3s;
            background: radial-gradient(800px circle at var(--mouse-x) var(--mouse-y), rgba(255,255,255,0.06), transparent 40%); z-index: 1; pointer-events: none;
        }
        .bento-card:hover::before { opacity: 1; }
        
        .input-glass { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.1); color: white; transition: all 0.3s ease; }
        .input-glass:focus { background: rgba(0, 0, 0, 0.2); border-color: #6366f1; box-shadow: 0 0 15px rgba(99, 102, 241, 0.3); outline: none; }
        
        /* Reveal Animations */
        .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-left { opacity: 0; transform: translateX(-30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-right { opacity: 0; transform: translateX(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-right.active { opacity: 1; transform: translate(0); }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }

        /* AI Chat Component Styles */
        .ai-chat-widget { position: fixed; bottom: 2rem; right: 2rem; z-index: 50; display: flex; flex-direction: column; align-items: flex-end; }
        .ai-chat-btn { width: 3.5rem; height: 3.5rem; border-radius: 9999px; background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); display: flex; justify-content: center; align-items: center; cursor: pointer; box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.5); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); border: 2px solid rgba(255,255,255,0.1); }
        .ai-chat-btn:hover { transform: scale(1.05); }
        .ai-chat-btn:active { transform: scale(0.95); }
        .ai-chat-window { width: 380px; height: 600px; max-height: calc(100vh - 120px); background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(24px); border: 1px solid rgba(255,255,255,0.1); border-radius: 24px; margin-bottom: 1rem; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); transform-origin: bottom right; transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); opacity: 0; transform: scale(0.95) translateY(20px); pointer-events: none; }
        .ai-chat-window.open { opacity: 1; transform: scale(1) translateY(0); pointer-events: auto; }
        
        .ai-chat-header { padding: 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: space-between; background: rgba(255,255,255,0.02); }
        .ai-status-dot { width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; box-shadow: 0 0 10px #10b981; animation: pulse 2s infinite; }
        
        .ai-chat-messages { flex: 1; overflow-y: auto; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; scrollbar-width: none; }
        .ai-chat-messages::-webkit-scrollbar { display: none; }
        
        .message-bubble { max-width: 85%; padding: 0.875rem 1.25rem; border-radius: 18px; font-size: 0.95rem; line-height: 1.5; animation: slideUpPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(10px); }
        .message-ai { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.05); color: #e2e8f0; border-bottom-left-radius: 4px; align-self: flex-start; }
        .message-user { background: linear-gradient(135deg, rgba(99, 102, 241, 0.8) 0%, rgba(139, 92, 246, 0.8) 100%); border: 1px solid rgba(255,255,255,0.1); color: white; border-bottom-right-radius: 4px; align-self: flex-end; }
        
        .ai-chat-input-area { padding: 1rem; border-top: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.2); }
        .ai-chat-input-wrapper { position: relative; display: flex; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 9999px; padding: 0.25rem; transition: all 0.3s ease; }
        .ai-chat-input-wrapper:focus-within { background: rgba(0,0,0,0.3); border-color: rgba(99, 102, 241, 0.5); box-shadow: 0 0 20px rgba(99, 102, 241, 0.2); }
        .ai-chat-input { flex: 1; background: transparent; border: none; outline: none; padding: 0.75rem 1rem; color: white; font-size: 0.95rem; }
        .ai-chat-input::placeholder { color: #64748b; }
        .ai-chat-submit { width: 2.5rem; height: 2.5rem; border-radius: 50%; background: white; color: black; display: flex; justify-content: center; align-items: center; border: none; cursor: pointer; transition: transform 0.2s; }
        .ai-chat-submit:hover { transform: scale(1.05); background: #e2e8f0; }
        
        @keyframes slideUpPop { to { opacity: 1; transform: translateY(0); } }
        
        /* Mobile fixes */
        @media (max-width: 640px) {
            .ai-chat-window { width: calc(100vw - 2rem); height: 500px; position: fixed; bottom: 5rem; right: 1rem; left: 1rem; z-index: 40; margin-bottom: 0; }
            .ai-chat-btn { bottom: 1rem; right: 1rem; position: fixed; }
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>
    <div class="fixed inset-0 bg-[linear-gradient(to_right,#4f4f4f1a_1px,transparent_1px),linear-gradient(to_bottom,#4f4f4f1a_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none z-[-1]"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-32 pb-20 min-h-screen relative z-10">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
            <div class="flex justify-between items-end mb-10 reveal-up">
                <div>
                    <h1 class="font-heading text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Command Center</h1>
                    <p class="text-gray-400 font-light">Manage your digital identity and security settings.</p>
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
                    $current_user_page = 'profile';
                    include 'components/user_sidebar.php'; 
                    ?>
                </div>
                
                <!-- Forms -->
                <div class="w-full lg:w-3/4 xl:w-4/5 space-y-8">
                    <!-- Profile Update -->
                    <div class="bento-card p-8 reveal-right delay-200 relative" id="profile-bento">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/5 rounded-full blur-[60px] pointer-events-none"></div>
                        <h3 class="font-heading text-2xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-id-card text-secondary"></i> Identity Parameters
                        </h3>
                        
                        <form method="POST">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="bg-white/5 border border-white/10 rounded-2xl overflow-hidden mb-8 backdrop-blur-sm shadow-lg">
                                <!-- Row 1 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">First Name</label>
                                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <!-- Row 2 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Last Name</label>
                                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <!-- Row 3 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Primary Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <!-- Row 4 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Mobile Directive</label>
                                    <input type="tel" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <!-- Row 5 (Age/Country) -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Age / Country</label>
                                    <div class="w-full sm:w-2/3 flex gap-4">
                                        <input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required min="13" max="120" class="w-16 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium border-r border-white/10 pr-3">
                                        <input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" required class="flex-1 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium pl-1">
                                    </div>
                                </div>
                                <!-- Row 6 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 border-b border-white/5 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Enterprise Name <span class="text-[10px] text-gray-500 font-normal ml-1">(Opt)</span></label>
                                    <input type="text" name="company_name" value="<?php echo htmlspecialchars($user['company_name']); ?>" placeholder="Leave blank if none" class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                                <!-- Row 7 -->
                                <div class="flex flex-col sm:flex-row sm:items-center px-5 py-4 group transition-colors hover:bg-white/[0.02]">
                                    <label class="w-full sm:w-1/3 text-sm font-semibold text-gray-300 group-focus-within:text-primary transition-colors mb-2 sm:mb-0">Enterprise URL <span class="text-[10px] text-gray-500 font-normal ml-1">(Opt)</span></label>
                                    <input type="url" name="company_website" value="<?php echo htmlspecialchars($user['company_website']); ?>" placeholder="https://" class="w-full sm:w-2/3 bg-transparent text-white text-base focus:outline-none placeholder-gray-600 font-medium">
                                </div>
                            </div>
                            
                            <div class="flex justify-end relative z-10 mt-6">
                                <button type="submit" class="magic-btn-wrapper w-full sm:w-auto hover:scale-105 transition-transform group">
                                    <span class="magic-border"></span>
                                    <span class="inline-flex h-full w-full items-center justify-center rounded-full bg-slate-950 px-8 py-3 text-sm font-bold text-white backdrop-blur-3xl transition-colors group-hover:bg-slate-900 gap-2">
                                        Sync Data <i class="fas fa-sync-alt"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                    

                </div>
            </div>
        </div>
    </div>
    
    <!-- Animated AI Chat Component -->
    <div class="ai-chat-widget">
        <!-- Chat Window -->
        <div class="ai-chat-window" id="aiChatWindow">
            <!-- Header -->
            <div class="ai-chat-header">
                <div class="flex items-center gap-3">
                    <div class="relative flex h-10 w-10 shrink-0 overflow-hidden rounded-full border border-white/10 bg-black/50 items-center justify-center">
                        <i class="fas fa-robot text-primary text-xl"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-white tracking-wide">Nexus AI</span>
                        <div class="flex items-center gap-1.5">
                            <div class="ai-status-dot"></div>
                            <span class="text-[10px] text-emerald-400 font-medium uppercase tracking-wider">Online</span>
                        </div>
                    </div>
                </div>
                <button id="closeAiChatBtn" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Messages -->
            <div class="ai-chat-messages" id="aiChatMessages">
                <!-- Initial AI Message -->
                <div class="message-bubble message-ai" style="animation-delay: 0.1s">
                    Hi <?php echo htmlspecialchars($user['first_name'] ?? 'there'); ?>! I'm your dedicated Peak Experience assistant. How can I help you dominate the digital space today?
                </div>
                <!-- Smart command chips -->
                <div class="flex flex-wrap gap-2 mt-2 opacity-0 transform translate-y-2 animate-[slideUpPop_0.4s_ease_forwards]" style="animation-delay: 0.6s">
                    <button class="chat-chip px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs text-gray-300 hover:bg-primary/20 hover:text-primary transition-colors hover:border-primary/30">Analyze Profile</button>
                    <button class="chat-chip px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs text-gray-300 hover:bg-primary/20 hover:text-primary transition-colors hover:border-primary/30">Boost Security</button>
                </div>
            </div>
            
            <!-- Input Area -->
            <div class="ai-chat-input-area">
                <div class="ai-chat-input-wrapper">
                    <input type="text" id="aiChatInput" class="ai-chat-input" placeholder="Ask Nexus anything..." autocomplete="off">
                    <button id="aiChatSubmit" class="ai-chat-submit">
                        <i class="fas fa-arrow-up"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Floating Action Button -->
        <button id="toggleAiChatBtn" class="ai-chat-btn">
            <i class="fas fa-sparkles text-white text-xl"></i>
        </button>
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

            // Spotlight effect for profile bento card
            const profileCard = document.getElementById("profile-bento");
            if (profileCard) {
                profileCard.addEventListener("mousemove", e => {
                    const rect = profileCard.getBoundingClientRect(),
                          x = e.clientX - rect.left,
                          y = e.clientY - rect.top;
                    profileCard.style.setProperty("--mouse-x", `${x}px`);
                    profileCard.style.setProperty("--mouse-y", `${y}px`);
                });
            }

            // AI Chat Logic
            const chatWidget = document.querySelector('.ai-chat-widget');
            if (chatWidget) {
                const toggleBtn = document.getElementById('toggleAiChatBtn');
                const closeBtn = document.getElementById('closeAiChatBtn');
                const chatWindow = document.getElementById('aiChatWindow');
                const chatMessages = document.getElementById('aiChatMessages');
                const chatInput = document.getElementById('aiChatInput');
                const chatSubmit = document.getElementById('aiChatSubmit');
                
                let isChatOpen = false;

                const toggleChat = () => {
                    isChatOpen = !isChatOpen;
                    if (isChatOpen) {
                        chatWindow.classList.add('open');
                        toggleBtn.innerHTML = '<i class="fas fa-times text-white text-xl"></i>';
                        setTimeout(() => chatInput.focus(), 300);
                    } else {
                        chatWindow.classList.remove('open');
                        toggleBtn.innerHTML = '<i class="fas fa-sparkles text-white text-xl"></i>';
                    }
                };

                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', () => { isChatOpen = true; toggleChat(); });

                const appendMessage = (text, sender) => {
                    const msgDiv = document.createElement('div');
                    msgDiv.className = `message-bubble message-${sender}`;
                    msgDiv.textContent = text;
                    chatMessages.appendChild(msgDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                };

                const simulateAIResponse = (userText) => {
                    // Typing indicator
                    const typingDiv = document.createElement('div');
                    typingDiv.className = 'message-bubble message-ai typing-indicator flex gap-1';
                    typingDiv.innerHTML = '<span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce"></span><span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span><span class="w-1.5 h-1.5 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>';
                    chatMessages.appendChild(typingDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    setTimeout(() => {
                        typingDiv.remove();
                        const responses = [
                            "I've analyzed your request. Updating your security parameters immediately.",
                            "That's a great strategy. I'll prepare a comprehensive report for you.",
                            "Access granted. Your command center is fully operational.",
                            "I can integrate that API seamlessly into your current architecture."
                        ];
                        const reply = responses[Math.floor(Math.random() * responses.length)];
                        appendMessage(reply, 'ai');
                    }, 1500);
                };

                const handleSend = () => {
                    const text = chatInput.value.trim();
                    if (!text) return;
                    
                    appendMessage(text, 'user');
                    chatInput.value = '';
                    
                    // Remove chips if they exist
                    const chips = document.querySelector('.chat-chip')?.parentElement;
                    if (chips) chips.remove();

                    simulateAIResponse(text);
                };

                chatSubmit.addEventListener('click', handleSend);
                chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') handleSend();
                });

                // Chip interactions
                document.querySelectorAll('.chat-chip').forEach(chip => {
                    chip.addEventListener('click', (e) => {
                        chatInput.value = e.target.textContent;
                        handleSend();
                    });
                });
            }
        });
    </script>
</body>
</html>