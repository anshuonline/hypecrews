<?php
// Admin Navigation Component
// This file should be included in all admin pages

// Make sure we have access to session variables
if (!isset($_SESSION)) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Get 2FA status if available
$is2FAEnabled = isset($is2FAEnabled) ? $is2FAEnabled : 0;

// Get admin username if available
$adminUsername = isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : 'Admin';

// Get page title if available, otherwise default to 'Admin Dashboard'
$pageTitle = isset($pageTitle) ? $pageTitle : 'Admin Dashboard';
?>

<!-- Admin Navigation -->
<nav class="bg-light/80 backdrop-blur-sm border-b border-gray-800" role="navigation" aria-label="Main navigation">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <i class="fas fa-user-shield text-primary text-2xl mr-3" aria-hidden="true"></i>
                <h1 class="text-xl font-bold"><?php echo htmlspecialchars($pageTitle); ?></h1>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-4">
                <a href="setup_2fa.php" class="text-gray-300 hover:text-white flex items-center px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-<?php echo $is2FAEnabled ? 'lock' : 'unlock'; ?> mr-2" aria-hidden="true"></i>
                    <?php echo $is2FAEnabled ? '2FA Enabled' : 'Setup 2FA'; ?>
                </a>
                <a href="index.php" class="text-gray-300 hover:text-white flex items-center px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i> Dashboard
                </a>
                <a href="selected.php" class="text-gray-300 hover:text-white flex items-center px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-star mr-2" aria-hidden="true"></i> Selected
                </a>
                <span class="text-gray-300 px-3 py-2">Welcome, <?php echo htmlspecialchars($adminUsername); ?>!</span>
                <a href="?logout=1" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                </a>
            </div>
            
            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="text-gray-300 hover:text-white focus:outline-none focus:text-white transition-colors duration-200" aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4 transition-all duration-300 ease-in-out overflow-hidden">
            <div class="pt-2 space-y-1">
                <a href="setup_2fa.php" class="block text-gray-300 hover:text-white hover:bg-light/50 px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-<?php echo $is2FAEnabled ? 'lock' : 'unlock'; ?> mr-2" aria-hidden="true"></i>
                    <?php echo $is2FAEnabled ? '2FA Enabled' : 'Setup 2FA'; ?>
                </a>
                <a href="index.php" class="block text-gray-300 hover:text-white hover:bg-light/50 px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-tachometer-alt mr-2" aria-hidden="true"></i> Dashboard
                </a>
                <a href="selected.php" class="block text-gray-300 hover:text-white hover:bg-light/50 px-3 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-star mr-2" aria-hidden="true"></i> Selected
                </a>
                <div class="border-t border-gray-700 pt-2 mt-2">
                    <span class="block px-3 py-2 text-gray-300">Welcome, <?php echo htmlspecialchars($adminUsername); ?>!</span>
                    <a href="?logout=1" class="block bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-md mt-2 transition-colors duration-200">
                        <i class="fas fa-sign-out-alt mr-2" aria-hidden="true"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
    /* Smooth transition for mobile menu */
    #mobile-menu {
        max-height: 0;
        opacity: 0;
    }
    
    #mobile-menu:not(.hidden) {
        max-height: 500px;
        opacity: 1;
    }
    
    /* Improve focus styles for accessibility */
    button:focus, a:focus {
        outline: 2px solid #6366f1;
        outline-offset: 2px;
    }
</style>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                
                // Toggle menu visibility
                mobileMenu.classList.toggle('hidden');
                
                // Update aria-expanded attribute
                mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                
                // Change icon based on state
                const icon = mobileMenuButton.querySelector('i');
                if (icon) {
                    if (isExpanded) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    } else {
                        icon.classList.remove('fa-bars');
                        icon.classList.add('fa-times');
                    }
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideNav = document.querySelector('nav').contains(event.target);
                if (!isClickInsideNav && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    
                    // Reset icon
                    const icon = mobileMenuButton.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            });
            
            // Close mobile menu when pressing Escape key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    mobileMenuButton.focus();
                    
                    // Reset icon
                    const icon = mobileMenuButton.querySelector('i');
                    if (icon) {
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            });
        }
    });
</script>