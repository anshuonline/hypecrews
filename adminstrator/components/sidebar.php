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
?>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-40 hidden md:hidden backdrop-blur-sm" onclick="toggleSidebar()"></div>

<div id="admin-sidebar" class="sidebar w-64 flex-shrink-0 flex flex-col absolute md:relative z-50 h-full bg-[#0f172a] transform -translate-x-full md:translate-x-0 transition-transform duration-300">
    <div class="p-6 border-b border-gray-800">
        <img src="../graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews Admin" class="h-10 w-auto">
    </div>
    
    <nav class="flex-1 py-6">
        <a href="index.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home mr-3"></i>
            <span>Dashboard</span>
        </a>
        <a href="orders.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'orders') ? 'active' : ''; ?>">
            <i class="fas fa-box mr-3"></i>
            <span>Orders</span>
        </a>
        <a href="reviews.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'reviews') ? 'active' : ''; ?>">
            <i class="fas fa-star mr-3"></i>
            <span>Reviews</span>
        </a>
        <a href="queries.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'queries') ? 'active' : ''; ?>">
            <i class="fas fa-question-circle mr-3"></i>
            <span>Queries</span>
        </a>
        <a href="newsletter.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'newsletter') ? 'active' : ''; ?>">
            <i class="fas fa-envelope mr-3"></i>
            <span>Newsletter</span>
        </a>
        <a href="users.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'users') ? 'active' : ''; ?>">
            <i class="fas fa-users mr-3"></i>
            <span>Users</span>
        </a>
        <div class="px-6 py-2 mt-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Careers</div>
        <a href="jobs.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'jobs') ? 'active' : ''; ?>">
            <i class="fas fa-briefcase mr-3"></i>
            <span>Manage Jobs</span>
        </a>
        <a href="job_applications.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white <?php echo ($current_page == 'job_applications') ? 'active' : ''; ?>">
            <i class="fas fa-file-alt mr-3"></i>
            <span>Applications</span>
        </a>
    </nav>
    
    <div class="p-6 border-t border-gray-800">
        <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center mr-3">
                <i class="fas fa-user"></i>
            </div>
            <div>
                <p class="font-medium"><?php echo htmlspecialchars($admin_username); ?></p>
                <p class="text-sm text-gray-400">Administrator</p>
            </div>
        </div>
        <a href="logout.php" class="mt-4 flex items-center text-gray-400 hover:text-white">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamically inject hamburger menu into the main content header
    const header = document.querySelector('header');
    if (header) {
        const headerFlex = header.querySelector('.flex.justify-between.items-center') || header.querySelector('.flex') || header;
        if (headerFlex) {
            const titleElement = headerFlex.querySelector('h2');
            
            const hamburgerBtn = document.createElement('button');
            hamburgerBtn.className = 'md:hidden text-white mr-4 focus:outline-none hover:text-primary transition-colors';
            hamburgerBtn.innerHTML = '<i class="fas fa-bars text-xl"></i>';
            hamburgerBtn.onclick = toggleSidebar;
            
            if (titleElement) {
                // Group hamburger and title
                const titleWrapper = document.createElement('div');
                titleWrapper.className = 'flex items-center';
                headerFlex.insertBefore(titleWrapper, titleElement);
                titleWrapper.appendChild(hamburgerBtn);
                titleWrapper.appendChild(titleElement);
            } else {
                headerFlex.insertBefore(hamburgerBtn, headerFlex.firstChild);
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