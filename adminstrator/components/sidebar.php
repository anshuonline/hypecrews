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

<style>
/* Inline Sidebar CSS to prevent caching issues */
.admin-sidebar-wrapper {
    background: #000000 !important; /* True AMOLED Black */
    border-right: 1px solid rgba(255, 255, 255, 0.08);
    transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-x: hidden;
}

.admin-nav-link {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    margin: 4px 16px;
    border-radius: 12px; /* Apple-style rounded selection */
    padding: 10px 16px;
    font-weight: 500;
    color: #a1a1aa !important; /* zinc-400 */
    position: relative;
    display: flex;
    align-items: center;
    text-decoration: none;
}

.admin-nav-link:hover {
    background: rgba(255, 255, 255, 0.1); /* Frosted glass hover */
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #ffffff !important;
}

.admin-nav-link.active {
    background: #0066cc; /* Apple Blue for active */
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
}

.admin-nav-link i {
    color: rgba(255, 255, 255, 0.5);
    transition: color 0.2s;
    width: 24px;
    text-align: center;
    display: inline-block;
}

.admin-nav-link:hover i, .admin-nav-link.active i {
    color: #ffffff;
}

.pulse-animation {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* Hide scrollbar for nav */
.admin-sidebar-nav::-webkit-scrollbar {
    display: none;
}
.admin-sidebar-nav {
    scrollbar-width: none;
}

/* Sidebar Collapsed State (Desktop Only) */
@media (min-width: 768px) {
    .admin-sidebar-wrapper.collapsed {
        width: 80px !important;
    }

    .admin-sidebar-wrapper.collapsed .admin-nav-link span:not(.chat-badge),
    .admin-sidebar-wrapper.collapsed .nav-section-title,
    .admin-sidebar-wrapper.collapsed .profile-text,
    .admin-sidebar-wrapper.collapsed .logo-img {
        display: none !important;
    }

    .admin-sidebar-wrapper.collapsed .logo-icon-only {
        display: flex !important;
    }

    .admin-sidebar-wrapper.collapsed .admin-nav-link {
        margin: 4px 12px;
        padding: 12px 0;
        justify-content: center;
    }

    .admin-sidebar-wrapper.collapsed .admin-nav-link i {
        margin-right: 0 !important;
        font-size: 1.25rem;
    }
    
    .admin-sidebar-wrapper.collapsed .chat-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        padding: 2px 5px;
        font-size: 9px;
    }
    
    .admin-sidebar-wrapper.collapsed .profile-container {
        padding: 10px;
        justify-content: center;
    }
    
    .admin-sidebar-wrapper.collapsed .profile-avatar {
        margin-right: 0 !important;
    }
    
    /* Tooltip for collapsed state */
    .admin-sidebar-wrapper.collapsed .admin-nav-link:hover::after {
        content: attr(data-title);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        margin-left: 10px;
        background: rgba(30, 41, 59, 0.95);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        white-space: nowrap;
        z-index: 100;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        pointer-events: none;
    }
}
</style>

<script>
    // Apply collapsed state immediately to prevent FOUC (Flash of Unstyled Content)
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        document.write('<style>#admin-sidebar { width: 80px !important; } #admin-sidebar .admin-nav-link span:not(.chat-badge), #admin-sidebar .nav-section-title, #admin-sidebar .profile-text, #admin-sidebar .logo-img { display: none !important; } #admin-sidebar .logo-icon-only { display: flex !important; } #admin-sidebar .admin-nav-link { margin: 4px 12px; padding: 12px 0; justify-content: center; } #admin-sidebar .admin-nav-link i { margin-right: 0 !important; font-size: 1.25rem; } #admin-sidebar .profile-container { padding: 10px; justify-content: center; } #admin-sidebar .profile-avatar { margin-right: 0 !important; } </style>');
    }
</script>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/80 z-40 hidden md:hidden backdrop-blur-md transition-opacity" onclick="toggleSidebar()"></div>

<div id="admin-sidebar" class="admin-sidebar-wrapper w-64 flex-shrink-0 flex flex-col absolute md:relative z-50 h-full transform -translate-x-full md:translate-x-0">
    
    <!-- Header with Logo and Collapse Toggle -->
    <div class="p-6 border-b border-white/10 flex items-center justify-between min-h-[80px]">
        <div class="flex items-center justify-center flex-1">
            <!-- Full Logo -->
            <img src="../graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews Admin" class="logo-img h-9 w-auto hover:opacity-80 transition-opacity">
            <!-- Icon Only Logo (shown when collapsed) -->
            <div class="logo-icon-only hidden w-8 h-8 bg-primary text-white rounded-xl items-center justify-center font-bold text-xl shadow-lg">H</div>
        </div>
        <!-- Desktop Toggle Button -->
        <button onclick="toggleDesktopSidebar()" class="hidden md:flex text-white/50 hover:text-white transition-colors focus:outline-none w-8 h-8 rounded-full hover:bg-white/10 items-center justify-center -mr-2 flex-shrink-0">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    
    <nav class="admin-sidebar-nav flex-1 py-4 overflow-y-auto">
        <!-- General Section -->
        <div class="nav-section-title px-6 py-2 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em] truncate">General</div>
        
        <a href="index.php" class="admin-nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" data-title="Dashboard">
            <i class="fas fa-home mr-3 text-lg"></i>
            <span>Dashboard</span>
        </a>
        <a href="orders.php" class="admin-nav-link <?php echo ($current_page == 'orders') ? 'active' : ''; ?>" data-title="Orders">
            <i class="fas fa-box mr-3 text-lg"></i>
            <span>Orders</span>
        </a>
        <a href="reviews.php" class="admin-nav-link <?php echo ($current_page == 'reviews') ? 'active' : ''; ?>" data-title="Reviews">
            <i class="fas fa-star mr-3 text-lg"></i>
            <span>Reviews</span>
        </a>
        <a href="queries.php" class="admin-nav-link <?php echo ($current_page == 'queries') ? 'active' : ''; ?>" data-title="Queries">
            <i class="fas fa-question-circle mr-3 text-lg"></i>
            <span>Queries</span>
        </a>
        
        <!-- Community Section -->
        <div class="nav-section-title px-6 py-2 mt-4 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em] truncate">Community</div>
        
        <a href="users.php" class="admin-nav-link <?php echo ($current_page == 'users') ? 'active' : ''; ?>" data-title="Users">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span>Users</span>
        </a>
        <a href="newsletter.php" class="admin-nav-link <?php echo ($current_page == 'newsletter') ? 'active' : ''; ?>" data-title="Newsletter">
            <i class="fas fa-envelope mr-3 text-lg"></i>
            <span>Newsletter</span>
        </a>
        <a href="team_chat.php" class="admin-nav-link justify-between <?php echo ($current_page == 'team_chat') ? 'active' : ''; ?>" data-title="Team Chat">
            <div class="flex items-center">
                <i class="fas fa-comments mr-3 text-lg"></i>
                <span>Team Chat</span>
            </div>
            <?php if ($unread_chat_count > 0): ?>
            <span class="chat-badge bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-lg pulse-animation"><?php echo $unread_chat_count; ?></span>
            <?php endif; ?>
        </a>
        <a href="activity_logs.php" class="admin-nav-link <?php echo ($current_page == 'activity_logs') ? 'active' : ''; ?>" data-title="Activity Logs">
            <i class="fas fa-clipboard-list mr-3 text-lg"></i>
            <span>Activity Logs</span>
        </a>
        
        <!-- App Content Section -->
        <div class="nav-section-title px-6 py-2 mt-4 mb-1 text-[10px] font-bold text-white/30 uppercase tracking-[0.15em] truncate">Content</div>
        
        <a href="softwares.php" class="admin-nav-link <?php echo ($current_page == 'softwares') ? 'active' : ''; ?>" data-title="Softwares">
            <i class="fas fa-laptop-code mr-3 text-lg"></i>
            <span>Softwares</span>
        </a>
        <a href="jobs.php" class="admin-nav-link <?php echo ($current_page == 'jobs') ? 'active' : ''; ?>" data-title="Jobs">
            <i class="fas fa-briefcase mr-3 text-lg"></i>
            <span>Jobs</span>
        </a>
        <a href="job_applications.php" class="admin-nav-link <?php echo ($current_page == 'job_applications') ? 'active' : ''; ?>" data-title="Applications">
            <i class="fas fa-file-alt mr-3 text-lg"></i>
            <span>Applications</span>
        </a>
    </nav>
    
    <div class="p-4 border-t border-white/10 mt-auto bg-black/50 backdrop-blur-md profile-container flex flex-col">
        <a href="profile.php" class="flex items-center p-2.5 rounded-[14px] hover:bg-white/10 transition-colors mb-2 border border-transparent hover:border-white/5 cursor-pointer" data-title="Profile">
            <?php if ($admin_profile_image): ?>
                <div class="profile-avatar w-11 h-11 rounded-full mr-3 overflow-hidden border border-white/20 bg-black shrink-0 shadow-sm transition-all">
                    <img src="../<?php echo htmlspecialchars($admin_profile_image); ?>" class="w-full h-full object-cover">
                </div>
            <?php else: ?>
                <div class="profile-avatar w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3 shadow-md shrink-0 text-white font-bold text-lg border border-white/10 transition-all">
                    <?php echo substr(htmlspecialchars($admin_username), 0, 1); ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-text overflow-hidden">
                <p class="font-bold text-[13px] text-white truncate"><?php echo htmlspecialchars($admin_username); ?></p>
                <p class="text-[11px] text-white/50 font-medium mt-0.5 uppercase tracking-wide truncate">Edit Profile</p>
            </div>
        </a>
        <a href="logout.php" class="admin-nav-link !mx-0 !bg-transparent hover:!bg-red-500/10 text-white/50 hover:text-red-400 p-2.5 rounded-[12px] group" data-title="Logout">
            <i class="fas fa-sign-out-alt mr-3 ml-1 group-hover:-translate-x-0.5 transition-transform text-lg w-auto text-center"></i>
            <span class="text-xs font-bold uppercase tracking-wider">Logout</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Set initial active states for dynamic styling
    const currentPath = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.admin-nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href === currentPath) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // Apply collapsed state class to sidebar if needed
    const sidebar = document.getElementById('admin-sidebar');
    if (localStorage.getItem('sidebar_collapsed') === 'true') {
        sidebar.classList.add('collapsed');
    }

    // Dynamically inject mobile hamburger menu into the main content header
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

// Mobile Sidebar Toggle
function toggleSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    // If we're opening on mobile, remove collapsed state
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }
}

// Desktop Sidebar Collapse Toggle
function toggleDesktopSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
        localStorage.setItem('sidebar_collapsed', 'false');
    } else {
        sidebar.classList.add('collapsed');
        localStorage.setItem('sidebar_collapsed', 'true');
    }
}
</script>