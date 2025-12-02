<?php
// Footer component
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<footer class="bg-[#0f172a] text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            <div>
                <h3 class="text-2xl font-bold mb-6">HYPE<span class="text-primary">CREWS</span></h3>
                <p class="text-gray-400 mb-6">Empowering businesses with innovative digital solutions to thrive in the modern marketplace.</p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-bold mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="index.php" class="text-gray-400 hover:text-white transition">Home</a></li>
                    <li><a href="index.php#services" class="text-gray-400 hover:text-white transition">Services</a></li>
                    <li><a href="about.php" class="text-gray-400 hover:text-white transition">About Us</a></li>
                    <li><a href="contact.php" class="text-gray-400 hover:text-white transition">Contact</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-bold mb-6">Our Services</h4>
                <ul class="space-y-3">
                    <li><a href="services.php#copyright" class="text-gray-400 hover:text-white transition">Copyright Protection</a></li>
                    <li><a href="services.php#social" class="text-gray-400 hover:text-white transition">Social Media Management</a></li>
                    <li><a href="services.php#digital" class="text-gray-400 hover:text-white transition">Digital Marketing</a></li>
                    <li><a href="services.php#web" class="text-gray-400 hover:text-white transition">Web Development</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-bold mb-6">Newsletter</h4>
                <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest updates and offers.</p>
                <form class="flex">
                    <input type="email" placeholder="Your email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none text-dark">
                    <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-r-lg">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 text-center">
            <p class="text-gray-400">&copy; <?php echo date("Y"); ?> Hypecrews. All rights reserved.</p>
        </div>
    </div>
</footer>