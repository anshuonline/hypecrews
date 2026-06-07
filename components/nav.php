<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
?>

<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-X5LSWLN0Q6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-X5LSWLN0Q6');
</script>

<!-- Page Preloader -->
<div id="page-loader" style="position:fixed;inset:0;z-index:9999;background:#0B0F19;display:flex;justify-content:center;align-items:center;transition:opacity 0.5s;">
    <img src="graphics/loading.gif" alt="Loading..." style="width:48px;height:48px;object-fit:contain;">
</div>
<script>
    function hideLoader(){
        var loader = document.getElementById('page-loader');
        if(loader && loader.style.display !== 'none'){
            loader.style.opacity = '0';
            loader.style.pointerEvents = 'none';
            setTimeout(function(){ loader.style.display = 'none'; }, 500);
        }
    }
    window.addEventListener('load', hideLoader);
    setTimeout(hideLoader, 3000);
</script>

<header class="fixed w-full bg-dark/90 backdrop-blur-sm shadow-sm z-50 transition-all duration-300">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex items-center">
            <a href="index.php" class="text-2xl font-bold">
                <img src="graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews" class="h-10 w-auto">
            </a>
        </div>
        <nav id="mobile-menu" class="hidden md:flex items-center space-x-1">
            <a href="index.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Home</a>
            <div class="relative group">
                <button class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 flex items-center">
                    Services <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                </button>
                <div class="absolute left-1/2 -translate-x-1/2 mt-4 w-[700px] bg-[#0f172a]/95 backdrop-blur-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] rounded-2xl p-6 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-400 transform group-hover:translate-y-0 translate-y-4 border border-white/10 dropdown-menu grid grid-cols-2 gap-x-6 gap-y-2">
                    <a href="services.php#copyright" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 flex items-center justify-center mr-4 group-hover/item:bg-indigo-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-copyright text-indigo-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-indigo-400 transition-colors">Copyright Protection</div>
                            <div class="text-xs text-gray-400 mt-0.5">Protect & remove content</div>
                        </div>
                    </a>
                    <a href="services.php#social" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center mr-4 group-hover/item:bg-purple-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-hashtag text-purple-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-purple-400 transition-colors">Social Media</div>
                            <div class="text-xs text-gray-400 mt-0.5">Management & strategy</div>
                        </div>
                    </a>
                    <a href="services.php#digital" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center mr-4 group-hover/item:bg-blue-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-bullhorn text-blue-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-blue-400 transition-colors">Digital Marketing</div>
                            <div class="text-xs text-gray-400 mt-0.5">PPC, leads & campaigns</div>
                        </div>
                    </a>
                    <a href="services.php#web" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center mr-4 group-hover/item:bg-cyan-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-laptop-code text-cyan-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-cyan-400 transition-colors">Web & Development</div>
                            <div class="text-xs text-gray-400 mt-0.5">Custom websites & apps</div>
                        </div>
                    </a>
                    <a href="services.php#recovery" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center mr-4 group-hover/item:bg-emerald-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-sync-alt text-emerald-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-emerald-400 transition-colors">Recovery & Support</div>
                            <div class="text-xs text-gray-400 mt-0.5">Account & reputation fixes</div>
                        </div>
                    </a>
                    <a href="services.php#video" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-rose-500/10 flex items-center justify-center mr-4 group-hover/item:bg-rose-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-video text-rose-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-rose-400 transition-colors">Video Production</div>
                            <div class="text-xs text-gray-400 mt-0.5">Cinematic visual content</div>
                        </div>
                    </a>
                    <a href="services.php#marketing" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-teal-500/10 flex items-center justify-center mr-4 group-hover/item:bg-teal-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-chart-line text-teal-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-teal-400 transition-colors">Marketing & SEO</div>
                            <div class="text-xs text-gray-400 mt-0.5">Organic growth & traffic</div>
                        </div>
                    </a>
                    <a href="services.php#creative" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center mr-4 group-hover/item:bg-pink-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-paint-brush text-pink-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-pink-400 transition-colors">Creative & Production</div>
                            <div class="text-xs text-gray-400 mt-0.5">Design & branding</div>
                        </div>
                    </a>
                    <a href="services.php#business-support" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center mr-4 group-hover/item:bg-yellow-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-briefcase text-yellow-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-yellow-400 transition-colors">Business Support</div>
                            <div class="text-xs text-gray-400 mt-0.5">Admin & operations</div>
                        </div>
                    </a>
                    <a href="services.php#company-registration" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center mr-4 group-hover/item:bg-orange-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-file-signature text-orange-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-orange-400 transition-colors">Company Registration</div>
                            <div class="text-xs text-gray-400 mt-0.5">Legal & setup</div>
                        </div>
                    </a>
                    <a href="services.php#movie-pr" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-red-500/10 flex items-center justify-center mr-4 group-hover/item:bg-red-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-ticket-alt text-red-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-red-400 transition-colors">Movie PR</div>
                            <div class="text-xs text-gray-400 mt-0.5">Film public relations</div>
                        </div>
                    </a>
                    <a href="https://music.hypecrews.com" target="_blank" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item">
                        <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center mr-4 group-hover/item:bg-green-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-broadcast-tower text-green-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-green-400 transition-colors">Media Distribution</div>
                            <div class="text-xs text-gray-400 mt-0.5">Music & video publishing</div>
                        </div>
                    </a>
                    <a href="services.php#artist-management" class="flex items-center p-3 rounded-xl hover:bg-white/5 transition-all duration-300 group/item col-span-1">
                        <div class="w-10 h-10 rounded-lg bg-sky-500/10 flex items-center justify-center mr-4 group-hover/item:bg-sky-500/20 group-hover/item:scale-110 transition-all duration-300 shrink-0 border border-white/5">
                            <i class="fas fa-microphone-alt text-sky-400"></i>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-white group-hover/item:text-sky-400 transition-colors">Artist Management</div>
                            <div class="text-xs text-gray-400 mt-0.5">Career & booking support</div>
                        </div>
                    </a>
                </div>
            </div>
            <a href="softwares.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Softwares</a>
            <a href="about.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">About</a>
            <a href="careers.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Careers</a>
            <a href="contact.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Contact</a>

            
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="px-4 py-2 rounded-xl font-bold text-white hover:bg-white/10 transition-all duration-300 flex items-center border border-transparent hover:border-white/10 backdrop-blur-sm shadow-[0_0_15px_rgba(255,255,255,0.05)]">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-primary to-secondary flex items-center justify-center mr-2 shadow-inner">
                        <span class="text-xs font-black"><?php echo substr(htmlspecialchars($firstName), 0, 1); ?></span>
                    </div>
                    <?php echo htmlspecialchars($firstName); ?> 
                </a>
            <?php else: ?>
                <a href="login.php" class="ml-2 bg-gradient-to-r from-primary to-secondary text-white px-5 py-2 rounded-lg font-medium hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg">Login</a>
            <?php endif; ?>
        </nav>
        <button type="button" id="mobile-menu-button" class="md:hidden text-2xl text-white" onclick="document.getElementById('mobile-menu-dropdown').classList.toggle('hidden');">
            <i class="fas fa-bars pointer-events-none"></i>
        </button>
    </div>
    <!-- Mobile Menu -->
    <div id="mobile-menu-dropdown" class="md:hidden hidden bg-dark shadow-lg" style="position:absolute;top:100%;left:0;width:100%;z-index:9998;background:#0B0F19;">
        <div class="container mx-auto px-4 py-3 flex flex-col space-y-3">
            <a href="index.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Home</a>
            <div class="relative">
                <button type="button" id="mobile-services-toggle" class="w-full text-left px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300 flex items-center justify-between" onclick="document.getElementById('mobile-services-menu').classList.toggle('hidden'); var icon = this.querySelector('i'); if(icon) icon.classList.toggle('rotate-180');">
                    Services <i class="fas fa-chevron-down ml-2 text-xs transition-transform duration-300 pointer-events-none"></i>
                </button>
                <div id="mobile-services-menu" class="mt-2 bg-light/5 border border-white/5 rounded-xl py-2 hidden overflow-hidden transition-all duration-300">
                    <a href="services.php#copyright" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-indigo-400 transition-all duration-300">
                        <i class="fas fa-copyright w-6 text-indigo-400"></i> Copyright Protection
                    </a>
                    <a href="services.php#social" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-purple-400 transition-all duration-300">
                        <i class="fas fa-hashtag w-6 text-purple-400"></i> Social Media
                    </a>
                    <a href="services.php#digital" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-blue-400 transition-all duration-300">
                        <i class="fas fa-bullhorn w-6 text-blue-400"></i> Digital Marketing
                    </a>
                    <a href="services.php#web" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-cyan-400 transition-all duration-300">
                        <i class="fas fa-laptop-code w-6 text-cyan-400"></i> Web & Development
                    </a>
                    <a href="services.php#recovery" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-emerald-400 transition-all duration-300">
                        <i class="fas fa-sync-alt w-6 text-emerald-400"></i> Recovery & Support
                    </a>
                    <a href="services.php#video" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-rose-400 transition-all duration-300">
                        <i class="fas fa-video w-6 text-rose-400"></i> Video Production
                    </a>
                    <a href="services.php#marketing" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-teal-400 transition-all duration-300">
                        <i class="fas fa-chart-line w-6 text-teal-400"></i> Marketing & SEO
                    </a>
                    <a href="services.php#creative" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-pink-400 transition-all duration-300">
                        <i class="fas fa-paint-brush w-6 text-pink-400"></i> Creative & Production
                    </a>
                    <a href="services.php#business-support" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-yellow-400 transition-all duration-300">
                        <i class="fas fa-briefcase w-6 text-yellow-400"></i> Business Support
                    </a>
                    <a href="services.php#company-registration" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-orange-400 transition-all duration-300">
                        <i class="fas fa-file-signature w-6 text-orange-400"></i> Company Registration
                    </a>
                    <a href="services.php#movie-pr" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-red-400 transition-all duration-300">
                        <i class="fas fa-ticket-alt w-6 text-red-400"></i> Movie PR
                    </a>
                    <a href="https://music.hypecrews.com" target="_blank" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-green-400 transition-all duration-300">
                        <i class="fas fa-broadcast-tower w-6 text-green-400"></i> Media Distribution
                    </a>
                    <a href="services.php#artist-management" class="block px-6 py-3 text-sm text-gray-300 hover:bg-white/5 hover:text-sky-400 transition-all duration-300">
                        <i class="fas fa-microphone-alt w-6 text-sky-400"></i> Artist Management
                    </a>
                </div>
            </div>
            <a href="softwares.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Softwares</a>
            <a href="about.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">About</a>
            <a href="careers.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Careers</a>
            <a href="contact.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">Contact</a>

            
            <?php if ($isLoggedIn): ?>
                <a href="track_orders.php" class="px-4 py-2 rounded-lg font-medium text-white hover:bg-light/10 transition-all duration-300">
                    <i class="fas fa-truck mr-2"></i> Track Orders
                </a>
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

<script>
    (function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenuDropdown = document.getElementById('mobile-menu-dropdown');
        
        if (mobileMenuButton && mobileMenuDropdown) {
            // Close menu when clicking outside
            document.addEventListener('click', function(event) {
                const isClickInsideMenu = mobileMenuButton.contains(event.target) || mobileMenuDropdown.contains(event.target);
                if (!isClickInsideMenu && !mobileMenuDropdown.classList.contains('hidden')) {
                    mobileMenuDropdown.classList.add('hidden');
                }
            });
        }
    })();
</script>