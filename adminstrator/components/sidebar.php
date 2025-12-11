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

<div class="sidebar w-64 flex-shrink-0 flex flex-col">
    <div class="p-6 border-b border-gray-800">
        <h1 class="text-2xl font-bold">Hypecrews <span class="text-primary">Admin</span></h1>
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