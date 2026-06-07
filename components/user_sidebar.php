<?php
// Ensure $user and $current_user_page variables are available
$current_user_page = isset($current_user_page) ? $current_user_page : 'profile';

// Calculate unread support messages for user
$unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM support_chats c JOIN support_sessions s ON c.session_id = s.id WHERE s.user_id = ? AND c.sender_type = 'admin' AND c.is_read = 0");
$unread_stmt->execute([$_SESSION['user_id']]);
$support_unread = $unread_stmt->fetchColumn();
?>

<div class="bg-white/5 border border-white/10 rounded-3xl p-5 relative overflow-hidden flex flex-col h-full shadow-2xl backdrop-blur-md">
    <!-- User Avatar & Info -->
    <div class="relative z-10 flex items-center mb-8 pb-6 border-b border-white/10">
        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 p-[2px] mr-4 shrink-0 shadow-lg">
            <div class="w-full h-full rounded-full bg-[#0f172a] flex items-center justify-center overflow-hidden">
                <span class="text-xl font-bold text-white"><?php echo substr(htmlspecialchars($user['first_name']), 0, 1); ?></span>
            </div>
        </div>
        <div class="overflow-hidden">
            <h2 class="font-heading text-lg font-bold text-white truncate"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
            <p class="text-gray-400 font-medium text-xs truncate">@<?php echo htmlspecialchars($user['username']); ?></p>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <div class="relative z-10 flex-1 space-y-1.5 mb-6">
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-3 px-3">Main Menu</div>
        
        <a href="profile.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_user_page === 'profile' ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white font-medium'; ?>">
            <i class="fas fa-user-circle w-5 text-center <?php echo $current_user_page === 'profile' ? 'text-indigo-400' : ''; ?>"></i>
            <span class="text-sm">Profile Settings</span>
        </a>
        
        <a href="track_orders.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_user_page === 'orders' ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white font-medium'; ?>">
            <i class="fas fa-box w-5 text-center <?php echo $current_user_page === 'orders' ? 'text-blue-400' : ''; ?>"></i>
            <span class="text-sm">My Orders</span>
        </a>

        <a href="change_password.php" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_user_page === 'change_password' ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white font-medium'; ?>">
            <i class="fas fa-lock w-5 text-center <?php echo $current_user_page === 'change_password' ? 'text-rose-400' : ''; ?>"></i>
            <span class="text-sm">Security & Password</span>
        </a>
        
        <a href="support_chat.php" class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-all duration-200 <?php echo $current_user_page === 'support_chat' ? 'bg-white/10 text-white font-semibold' : 'text-gray-400 hover:bg-white/5 hover:text-white font-medium'; ?>">
            <div class="flex items-center gap-3">
                <i class="fas fa-comments w-5 text-center <?php echo $current_user_page === 'support_chat' ? 'text-purple-400' : ''; ?>"></i>
                <span class="text-sm">Support Chat</span>
            </div>
            <?php if($support_unread > 0): ?>
                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg border border-red-400 animate-pulse"><?php echo $support_unread; ?></span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Action Menu -->
    <div class="relative z-10 mt-auto pt-4 border-t border-white/10">
        <a href="?logout=1" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-200 text-red-400 hover:bg-red-500/10 hover:text-red-300 font-medium">
            <i class="fas fa-sign-out-alt w-5 text-center"></i>
            <span class="text-sm">Logout</span>
        </a>
    </div>
</div>
