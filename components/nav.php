<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<header class="fixed w-full bg-dark/90 backdrop-blur-sm shadow-sm z-50 transition-all duration-300">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">HYPE<span class="text-white">CREWS</span></h1>
        </div>
        <nav id="mobile-menu" class="hidden md:flex items-center space-x-1">
            <a href="index.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Home</a>
            <div class="relative group">
                <button class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 flex items-center">
                    Services <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                </button>
                <div class="absolute left-0 mt-2 w-72 bg-dark shadow-xl rounded-xl py-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 border-t-4 border-primary dropdown-menu">
                    <a href="services.php#copyright" class="dropdown-item">
                        <i class="fas fa-copyright text-primary"></i>
                        <span>Copyright Protection & Removal</span>
                    </a>
                    <a href="services.php#social" class="dropdown-item">
                        <i class="fas fa-hashtag text-primary"></i>
                        <span>Social Media Management</span>
                    </a>
                    <a href="services.php#digital" class="dropdown-item">
                        <i class="fas fa-bullhorn text-primary"></i>
                        <span>Digital Marketing</span>
                    </a>
                    <a href="services.php#web" class="dropdown-item">
                        <i class="fas fa-laptop-code text-primary"></i>
                        <span>Web & Development</span>
                    </a>
                    <a href="services.php#recovery" class="dropdown-item">
                        <i class="fas fa-sync-alt text-primary"></i>
                        <span>Recovery & Support</span>
                    </a>
                    <a href="services.php#video" class="dropdown-item">
                        <i class="fas fa-video text-primary"></i>
                        <span>Video Production</span>
                    </a>
                    <a href="services.php#marketing" class="dropdown-item">
                        <i class="fas fa-chart-line text-primary"></i>
                        <span>Marketing & SEO</span>
                    </a>
                    <a href="services.php#creative" class="dropdown-item">
                        <i class="fas fa-paint-brush text-primary"></i>
                        <span>Creative & Production</span>
                    </a>
                    <a href="services.php#business-support" class="dropdown-item">
                        <i class="fas fa-briefcase text-primary"></i>
                        <span>Business Support Services</span>
                    </a>
                    <a href="services.php#company-registration" class="dropdown-item">
                        <i class="fas fa-file-contract text-primary"></i>
                        <span>Company Registration</span>
                    </a>
                    <a href="services.php#movie-pr" class="dropdown-item">
                        <i class="fas fa-film text-primary"></i>
                        <span>Movie PR</span>
                    </a>
                    <a href="services.php#distribution" class="dropdown-item">
                        <i class="fas fa-music text-primary"></i>
                        <span>Music/Video Distribution</span>
                    </a>
                </div>
            </div>
            <a href="about.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">About</a>
            <a href="contact.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Contact</a>
            <a href="form.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Audition</a>
            
            <?php if ($isLoggedIn): ?>
                <div class="relative group">
                    <button class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 flex items-center">
                        <i class="fas fa-user mr-2"></i> <?php echo htmlspecialchars($firstName); ?> 
                        <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-dark shadow-xl rounded-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 border-t-4 border-primary">
                        <a href="profile.php" class="block px-4 py-2 text-white hover:bg-light/10 hover:text-primary transition-all duration-300">
                            <i class="fas fa-user-circle mr-2"></i> My Profile
                        </a>
                        <a href="logout.php" class="block px-4 py-2 text-white hover:bg-light/10 hover:text-red-500 transition-all duration-300">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="ml-2 bg-gradient-to-r from-primary to-secondary text-white px-5 py-2 rounded-lg font-medium hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg">Login</a>
            <?php endif; ?>
        </nav>
        <button id="mobile-menu-button" class="md:hidden text-2xl text-white">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu-dropdown" class="md:hidden hidden bg-dark shadow-lg">
        <div class="container mx-auto px-4 py-3 flex flex-col space-y-3">
            <a href="index.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Home</a>
            <div class="relative">
                <button id="mobile-services-toggle" class="w-full text-left px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 flex items-center justify-between">
                    Services <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300"></i>
                </button>
                <div id="mobile-services-menu" class="mt-2 bg-light/10 rounded-xl py-2 hidden">
                    <a href="services.php#copyright" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-copyright text-primary mr-2"></i>Copyright Protection & Removal
                    </a>
                    <a href="services.php#social" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-hashtag text-primary mr-2"></i>Social Media Management
                    </a>
                    <a href="services.php#digital" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-bullhorn text-primary mr-2"></i>Digital Marketing
                    </a>
                    <a href="services.php#web" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-laptop-code text-primary mr-2"></i>Web & Development
                    </a>
                    <a href="services.php#recovery" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-sync-alt text-primary mr-2"></i>Recovery & Support
                    </a>
                    <a href="services.php#video" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-video text-primary mr-2"></i>Video Production
                    </a>
                    <a href="services.php#marketing" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-chart-line text-primary mr-2"></i>Marketing & SEO
                    </a>
                    <a href="services.php#creative" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-paint-brush text-primary mr-2"></i>Creative & Production
                    </a>
                    <a href="services.php#business-support" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-briefcase text-primary mr-2"></i>Business Support Services
                    </a>
                    <a href="services.php#company-registration" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-file-contract text-primary mr-2"></i>Company Registration
                    </a>
                    <a href="services.php#movie-pr" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-film text-primary mr-2"></i>Movie PR
                    </a>
                    <a href="services.php#distribution" class="block px-6 py-3 text-white hover:bg-light/10 hover:text-primary transition-all duration-300 rounded-lg">
                        <i class="fas fa-music text-primary mr-2"></i>Music/Video Distribution
                    </a>
                </div>
            </div>
            <a href="about.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">About</a>
            <a href="contact.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Contact</a>
            <a href="form.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Audition</a>
            
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">
                    <i class="fas fa-user-circle mr-2"></i> My Profile
                </a>
                <a href="logout.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 text-red-500">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="bg-gradient-to-r from-primary to-secondary text-white px-5 py-2 rounded-lg font-medium hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg text-center">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Spacer div to account for fixed navbar height -->
<div class="h-16"></div>