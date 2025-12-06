<?php
// Footer component
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<footer class="bg-[#0f172a] text-white pt-16 pb-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-12 mb-12">
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
                <h4 class="text-lg font-bold mb-6">Legal</h4>
                <ul class="space-y-3">
                    <li><a href="refund-policy.php" class="text-gray-400 hover:text-white transition">Refund Policy</a></li>
                    <li><a href="terms-conditions.php" class="text-gray-400 hover:text-white transition">Terms & Conditions</a></li>
                    <li><a href="privacy-policy.php" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="text-lg font-bold mb-6">Newsletter</h4>
                <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest updates and offers.</p>
                <form id="newsletterForm" class="flex">
                    <input type="email" id="newsletterEmail" placeholder="Your email" class="px-4 py-2 w-full rounded-l-lg focus:outline-none bg-dark text-white border border-gray-700" required>
                    <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-r-lg text-white">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <div id="newsletterMessage" class="mt-2 text-sm hidden"></div>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 text-center">
            <p class="text-gray-400">&copy; <?php echo date("Y"); ?> Hypecrews. All rights reserved.</p>
        </div>
    </div>
    
    <!-- Newsletter Thank You Popup -->
    <div id="newsletterPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 text-center">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
            <h3 class="text-2xl font-bold mb-2">Thank You!</h3>
            <p class="text-gray-600 mb-6" id="popupMessage">Thank you for subscribing to our newsletter!</p>
            <button id="closePopup" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Close</button>
        </div>
    </div>
    
    <script>
        // Newsletter form submission
        document.addEventListener('DOMContentLoaded', function() {
            const newsletterForm = document.getElementById('newsletterForm');
            const newsletterEmail = document.getElementById('newsletterEmail');
            const newsletterMessage = document.getElementById('newsletterMessage');
            const newsletterPopup = document.getElementById('newsletterPopup');
            const popupMessage = document.getElementById('popupMessage');
            const closePopup = document.getElementById('closePopup');
            
            if (newsletterForm) {
                newsletterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const email = newsletterEmail.value.trim();
                    
                    // Basic validation
                    if (!email) {
                        showMessage('Please enter your email address.', 'error');
                        return;
                    }
                    
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        showMessage('Please enter a valid email address.', 'error');
                        return;
                    }
                    
                    // Send AJAX request
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '/newsletter_subscribe.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4) {
                            if (xhr.status === 200) {
                                try {
                                    const response = JSON.parse(xhr.responseText);
                                    
                                    if (response.status === 'success') {
                                        // Show thank you popup
                                        popupMessage.textContent = response.message;
                                        newsletterPopup.classList.remove('hidden');
                                        
                                        // Hide form
                                        newsletterForm.classList.add('hidden');
                                    } else {
                                        showMessage(response.message, 'error');
                                    }
                                } catch (e) {
                                    showMessage('An error occurred. Please try again. (Parse Error)', 'error');
                                    console.error('Parse error:', e);
                                    console.error('Response text:', xhr.responseText);
                                }
                            } else {
                                showMessage('An error occurred. Please try again. (Server Error)', 'error');
                                console.error('Server error:', xhr.status, xhr.statusText);
                                console.error('Response text:', xhr.responseText);
                            }
                        }
                    };
                    
                    xhr.onerror = function() {
                        showMessage('Network error. Please check your connection and try again.', 'error');
                        console.error('Network error');
                    };
                    
                    xhr.send('email=' + encodeURIComponent(email));
                });
            }
            
            // Close popup
            if (closePopup) {
                closePopup.addEventListener('click', function() {
                    newsletterPopup.classList.add('hidden');
                });
            }
            
            // Close popup when clicking outside
            if (newsletterPopup) {
                newsletterPopup.addEventListener('click', function(e) {
                    if (e.target === newsletterPopup) {
                        newsletterPopup.classList.add('hidden');
                    }
                });
            }
            
            function showMessage(text, type) {
                if (newsletterMessage) {
                    const alertClass = type === 'error' ? 
                        'text-red-500' : 
                        'text-green-500';
                    
                    newsletterMessage.textContent = text;
                    newsletterMessage.className = 'mt-2 text-sm ' + alertClass;
                    newsletterMessage.classList.remove('hidden');
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        newsletterMessage.classList.add('hidden');
                    }, 5000);
                }
            }
        });
    </script>
</footer>