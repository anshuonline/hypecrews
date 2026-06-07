<?php
// Ensure $user and $current_user_page variables are available
$current_user_page = isset($current_user_page) ? $current_user_page : 'profile';
?>

<div class="glass-card rounded-3xl p-6 relative overflow-hidden group h-full flex flex-col">
    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full blur-[40px] group-hover:bg-primary/20 transition-all"></div>
    
    <!-- User Avatar & Info -->
    <div class="relative z-10 text-center mb-6 border-b border-white/5 pb-6">
        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500/20 to-purple-500/20 p-1 mx-auto mb-4 relative shadow-[0_0_30px_rgba(99,102,241,0.2)]">
            <div class="w-full h-full rounded-full bg-[#0B0F19] flex items-center justify-center border border-white/5">
                <i class="fas fa-user-astronaut text-3xl text-transparent bg-clip-text bg-gradient-to-br from-indigo-400 to-purple-400"></i>
            </div>
            <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-[#0B0F19] rounded-full"></div>
        </div>
        <h2 class="font-heading text-xl font-bold text-white"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
        <p class="text-primary font-medium tracking-wide text-xs mt-1">@<?php echo htmlspecialchars($user['username']); ?></p>
    </div>
    
    <!-- Navigation Menu -->
    <div class="relative z-10 flex-1 space-y-2 mb-6">
        <div class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3 px-2">Menu</div>
        
        <a href="profile.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 <?php echo $current_user_page === 'profile' ? 'bg-primary/20 text-white border border-primary/30 shadow-[0_0_15px_rgba(99,102,241,0.15)]' : 'text-gray-400 hover:bg-white/5 hover:text-white border border-transparent'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_user_page === 'profile' ? 'bg-primary text-white shadow-sm' : 'bg-black/20 text-gray-400 group-hover:bg-black/30'; ?>">
                <i class="fas fa-id-card"></i>
            </div>
            <span class="font-semibold text-sm">Profile Overview</span>
        </a>
        
        <a href="support_chat.php" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 <?php echo $current_user_page === 'support_chat' ? 'bg-secondary/20 text-white border border-secondary/30 shadow-[0_0_15px_rgba(139,92,246,0.15)]' : 'text-gray-400 hover:bg-white/5 hover:text-white border border-transparent'; ?>">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center <?php echo $current_user_page === 'support_chat' ? 'bg-secondary text-white shadow-sm' : 'bg-black/20 text-gray-400 group-hover:bg-black/30'; ?>">
                <i class="fas fa-comments"></i>
            </div>
            <span class="font-semibold text-sm">Support Chat</span>
        </a>

        <!-- Placeholder for future options -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 text-gray-400 hover:bg-white/5 hover:text-white border border-transparent opacity-50 cursor-not-allowed" title="Coming Soon">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-black/20">
                <i class="fas fa-box"></i>
            </div>
            <span class="font-semibold text-sm">My Orders <span class="text-[9px] bg-white/10 px-1.5 py-0.5 rounded text-white ml-1">SOON</span></span>
        </a>
    </div>

    <!-- Action Menu -->
    <div class="relative z-10 mt-auto pt-6 border-t border-white/5">
        <a href="?logout=1" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 text-red-400 hover:bg-red-500/10 hover:text-red-300 border border-transparent hover:border-red-500/20 group">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-500/10 text-red-400 group-hover:bg-red-500/20 group-hover:text-red-300 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <span class="font-semibold text-sm">Logout</span>
        </a>
    </div>
</div>
