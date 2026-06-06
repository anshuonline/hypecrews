<?php
// This file should be included in admin pages
// Make sure $current_page is set before including this file
if (!isset($current_page)) {
    $current_page = '';
}

// Ensure $admin_username is set
if (!isset($admin_username)) {
    $admin_username = 'Administrator';
}

// Fetch profile image if possible
$admin_profile_image = null;
$unread_chat_count = 0;
if (isset($pdo) && isset($_SESSION['admin_id'])) {
    try {
        $admin_id = $_SESSION['admin_id'];
        
        // Update last active time for the current admin
        $pdo->exec("UPDATE administrators SET last_active = CURRENT_TIMESTAMP WHERE id = $admin_id");
        
        $stmt_sidebar = $pdo->prepare("SELECT profile_image FROM administrators WHERE id = ?");
        $stmt_sidebar->execute([$admin_id]);
        $row = $stmt_sidebar->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['profile_image'])) {
            $admin_profile_image = $row['profile_image'];
        }
        
        $stmt_unread = $pdo->prepare("SELECT COUNT(*) FROM admin_chats WHERE sender_id != ? AND (created_at > (SELECT last_chat_read FROM administrators WHERE id = ?) OR (SELECT last_chat_read FROM administrators WHERE id = ?) IS NULL)");
        $stmt_unread->execute([$_SESSION['admin_id'], $_SESSION['admin_id'], $_SESSION['admin_id']]);
        $unread_chat_count = $stmt_unread->fetchColumn();
    } catch (Exception $e) {
        // silently ignore error in sidebar
    }
}
?>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden md:hidden backdrop-blur-md transition-opacity" onclick="toggleSidebar()"></div>

<div id="admin-sidebar" class="sidebar w-64 flex-shrink-0 flex flex-col absolute md:relative z-50 h-full transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="p-6 border-b border-white/10 flex items-center justify-center">
        <!-- Kept original logo but centered it, could also use a custom version if needed -->
        <img src="../graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews Admin" class="h-9 w-auto hover:opacity-80 transition-opacity">
    </div>
    
    <nav class="flex-1 py-4 overflow-y-auto overflow-x-hidden" style="scrollbar-width: none;">
        <!-- General Section -->
        <div class="px-6 py-2 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em]">General</div>
        
        <a href="index.php" class="nav-link flex items-center <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home mr-3 text-lg"></i>
            <span>Dashboard</span>
        </a>
        <a href="orders.php" class="nav-link flex items-center <?php echo ($current_page == 'orders') ? 'active' : ''; ?>">
            <i class="fas fa-box mr-3 text-lg"></i>
            <span>Orders</span>
        </a>
        <a href="reviews.php" class="nav-link flex items-center <?php echo ($current_page == 'reviews') ? 'active' : ''; ?>">
            <i class="fas fa-star mr-3 text-lg"></i>
            <span>Reviews</span>
        </a>
        <a href="queries.php" class="nav-link flex items-center <?php echo ($current_page == 'queries') ? 'active' : ''; ?>">
            <i class="fas fa-question-circle mr-3 text-lg"></i>
            <span>Queries</span>
        </a>
        
        <!-- Community Section -->
        <div class="px-6 py-2 mt-4 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em]">Community</div>
        
        <a href="users.php" class="nav-link flex items-center <?php echo ($current_page == 'users') ? 'active' : ''; ?>">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span>Users</span>
        </a>
        <a href="newsletter.php" class="nav-link flex items-center <?php echo ($current_page == 'newsletter') ? 'active' : ''; ?>">
            <i class="fas fa-envelope mr-3 text-lg"></i>
            <span>Newsletter</span>
        </a>
        <a href="team_chat.php" class="nav-link flex items-center justify-between <?php echo ($current_page == 'team_chat') ? 'active' : ''; ?>">
            <div class="flex items-center">
                <i class="fas fa-comments mr-3 text-lg"></i>
                <span>Team Chat</span>
            </div>
            <?php if ($unread_chat_count > 0): ?>
            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg pulse-animation"><?php echo $unread_chat_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="activity_logs.php" class="nav-link flex items-center <?php echo ($current_page == 'activity_logs') ? 'active' : ''; ?>">
            <i class="fas fa-clipboard-list mr-3 text-lg"></i>
            <span>Activity Logs</span>
        </a>
        
        <!-- App Content Section -->
        <div class="px-6 py-2 mt-4 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em]">Content</div>
        
        <a href="softwares.php" class="nav-link flex items-center <?php echo ($current_page == 'softwares') ? 'active' : ''; ?>">
            <i class="fas fa-laptop-code mr-3 text-lg"></i>
            <span>Softwares</span>
        </a>
        <a href="jobs.php" class="nav-link flex items-center <?php echo ($current_page == 'jobs') ? 'active' : ''; ?>">
            <i class="fas fa-briefcase mr-3 text-lg"></i>
            <span>Jobs</span>
        </a>
        <a href="job_applications.php" class="nav-link flex items-center <?php echo ($current_page == 'job_applications') ? 'active' : ''; ?>">
            <i class="fas fa-file-alt mr-3 text-lg"></i>
            <span>Applications</span>
        </a>
    </nav>
    
    <div class="p-4 border-t border-white/10 mt-auto bg-black/50 backdrop-blur-md">
        <a href="profile.php" class="flex items-center p-2.5 rounded-[14px] hover:bg-white/10 transition-colors mb-2 border border-transparent hover:border-white/5 cursor-pointer">
            <?php if ($admin_profile_image): ?>
                <div class="w-11 h-11 rounded-full mr-3 overflow-hidden border border-white/20 bg-black shrink-0 shadow-sm">
                    <img src="../<?php echo htmlspecialchars($admin_profile_image); ?>" class="w-full h-full object-cover">
                </div>
            <?php else: ?>
                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3 shadow-md shrink-0 text-white font-bold text-lg border border-white/10">
                    <?php echo substr(htmlspecialchars($admin_username), 0, 1); ?>
                </div>
            <?php endif; ?>
            
            <div class="overflow-hidden">
                <p class="font-bold text-[13px] text-white truncate"><?php echo htmlspecialchars($admin_username); ?></p>
                <p class="text-[11px] text-white/50 font-medium mt-0.5 uppercase tracking-wide">Edit Profile</p>
            </div>
        </a>
        <a href="logout.php" class="flex items-center text-white/50 hover:text-red-400 hover:bg-red-500/10 transition-all p-2.5 rounded-[12px] group">
            <i class="fas fa-sign-out-alt mr-3 ml-1 group-hover:-translate-x-0.5 transition-transform"></i>
            <span class="text-xs font-bold uppercase tracking-wider">Logout</span>
        </a>
    </div>
</div>

<style>
/* Hide scrollbar for nav */
nav::-webkit-scrollbar {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamically inject hamburger menu into the main content header
    const header = document.querySelector('header');
    if (header) {
        const titleElement = header.querySelector('h1, h2, h3');
        
        const hamburgerBtn = document.createElement('button');
        // Use text-gray-800 for Apple UI light theme header (since the header is light)
        hamburgerBtn.className = 'md:hidden text-gray-800 hover:text-primary transition-colors mr-4 focus:outline-none z-50 relative flex-shrink-0';
        hamburgerBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
        hamburgerBtn.onclick = toggleSidebar;
        
        if (titleElement) {
            const titleParent = titleElement.parentElement;
            
            // Ensure the parent is a flex container to align the button and title horizontally
            if (!titleParent.classList.contains('flex')) {
                titleParent.classList.add('flex', 'items-center');
            }
            
            titleParent.insertBefore(hamburgerBtn, titleElement);
        } else {
            // Fallback if no heading found
            const firstChild = header.firstChild;
            if (firstChild) {
                header.insertBefore(hamburgerBtn, firstChild);
            } else {
                header.appendChild(hamburgerBtn);
            }
        }
    }
});

function toggleSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}
</script>