<?php
$pageTitle = "Contact Us - Hypecrews";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0f172a',
                        light: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>

    <!-- Contact Hero Section -->
    <section class="pt-24 pb-12 bg-gradient-to-r from-dark to-[#0f172a] text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Get In <span class="text-yellow-300">Touch</span></h1>
            <p class="text-xl max-w-3xl mx-auto">Have questions or want to discuss a project? We'd love to hear from you. Reach out and let's start a conversation.</p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-12">
                <!-- Contact Form -->
                <div class="lg:w-1/2">
                    <div class="bg-dark rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold mb-6">Send Us a Message</h2>
                        <form id="contactForm" class="space-y-6">
                            <div>
                                <label for="name" class="block text-gray-300 font-medium mb-2">Full Name</label>
                                <input type="text" id="name" name="name" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="John Doe" required>
                            </div>
                            <div>
                                <label for="email" class="block text-gray-300 font-medium mb-2">Email Address</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="john@example.com" required>
                            </div>
                            <div>
                                <label for="phone" class="block text-gray-300 font-medium mb-2">Phone Number</label>
                                <input type="text" id="phone" name="phone" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="9876543210" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15">
                            </div>
                            <div>
                                <label for="subject" class="block text-gray-300 font-medium mb-2">Subject</label>
                                <input type="text" id="subject" name="subject" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="How can we help?" required>
                            </div>
                            <div>
                                <label for="service" class="block text-gray-300 font-medium mb-2">Service Interest</label>
                                <select id="service" name="service" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                                    <option value="" class="bg-dark">Select a service</option>
                                    <option value="copyright" class="bg-dark">Copyright Protection & Removal</option>
                                    <option value="social" class="bg-dark">Social Media Management</option>
                                    <option value="marketing" class="bg-dark">Digital Marketing</option>
                                    <option value="web" class="bg-dark">Web & Development</option>
                                    <option value="recovery" class="bg-dark">Recovery & Support</option>
                                    <option value="video" class="bg-dark">Video Production</option>
                                    <option value="seo" class="bg-dark">Marketing & SEO</option>
                                    <option value="creative" class="bg-dark">Creative & Production</option>
                                    <option value="other" class="bg-dark">Other</option>
                                </select>
                            </div>
                            <div>
                                <label for="message" class="block text-gray-300 font-medium mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="Tell us about your project..." required></textarea>
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">Send Message</button>
                            </div>
                        </form>
                        <div id="formMessage" class="mt-6 hidden"></div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="lg:w-1/2">
                    <div class="space-y-8">
                        <!-- Office Location -->
                        <div class="contact-info-card bg-dark rounded-xl shadow-lg p-8 transition duration-300">
                            <div class="flex items-start">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                    <i class="fas fa-map-marker-alt text-primary text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Our Office</h3>
                                    <p class="text-gray-400">Golaghat, Assam, 785621, India</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Numbers -->
                        <div class="contact-info-card bg-dark rounded-xl shadow-lg p-8 transition duration-300">
                            <div class="flex items-start">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                    <i class="fas fa-phone-alt text-primary text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Call Us</h3>
                                    <p class="text-gray-400">Main: +913613243276</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email Addresses -->
                        <div class="contact-info-card bg-dark rounded-xl shadow-lg p-8 transition duration-300">
                            <div class="flex items-start">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                    <i class="fas fa-envelope text-primary text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Email Us</h3>
                                    <p class="text-gray-400">General Inquiries: info@hypecrews.com<br>Support: support@hypecrews.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Working Hours -->
                        <div class="contact-info-card bg-dark rounded-xl shadow-lg p-8 transition duration-300">
                            <div class="flex items-start">
                                <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                    <i class="fas fa-clock text-primary text-2xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold mb-2">Working Hours</h3>
                                    <p class="text-gray-400">Monday To Sunday 24x7</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Frequently Asked <span class="text-primary">Questions</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Find answers to common questions about our services and processes</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- FAQ Item 1 -->
                <div class="bg-light rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-3">How long does a typical project take to complete?</h3>
                    <p class="text-gray-400">Project timelines vary depending on scope and complexity. Simple websites can be completed in 2-4 weeks, while larger projects may take 2-6 months. We provide detailed timelines during our initial consultation.</p>
                </div>
                
                <!-- FAQ Item 2 -->
                <div class="bg-light rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-3">Do you offer ongoing support after project completion?</h3>
                    <p class="text-gray-400">Yes, we offer various maintenance and support packages to ensure your digital assets continue to perform optimally. Our team is always available for updates, improvements, and troubleshooting.</p>
                </div>
                
                <!-- FAQ Item 3 -->
                <div class="bg-light rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-3">What information should I prepare before contacting you?</h3>
                    <p class="text-gray-400">To help us understand your needs better, please prepare information about your business goals, target audience, timeline, and budget. Examples of websites or designs you like can also be helpful.</p>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="bg-light rounded-xl p-6">
                    <h3 class="text-xl font-bold mb-3">How do you ensure the security of my data and intellectual property?</h3>
                    <p class="text-gray-400">We take data security seriously and employ industry-standard encryption, secure development practices, and confidentiality agreements. All client data is stored securely, and we never share your information without explicit permission.</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        // Phone number validation - Allow only digits
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            
            phoneInput.addEventListener('keypress', function(e) {
                // Allow only digits (0-9)
                if (e.which < 48 || e.which > 57) {
                    e.preventDefault();
                }
            });
            
            // Also prevent pasting non-numeric content
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = paste.replace(/[^0-9]/g, '');
                this.value = numericValue;
            });
        });
    </script>
</body>
</html>