<?php
session_start();
$pageTitle = "Contact Us - Hypecrews";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Contact Hypecrews for digital marketing, web development, copyright protection & more. Reach us at info@hypecrews.com or call +913613243276. Golaghat, Assam.">
    <meta name="keywords" content="contact hypecrews, digital agency contact, get in touch, hypecrews support, hypecrews email, hypecrews phone, digital services inquiry">
    <link rel="canonical" href="https://hypecrews.com/contact.php">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Contact Us - Hypecrews | Get In Touch Today">
    <meta property="og:description" content="Contact Hypecrews for digital marketing, web development, copyright protection & more. Reach us at info@hypecrews.com or call +913613243276. Golaghat, Assam.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/contact.php">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Contact Us - Hypecrews | Get In Touch Today">
    <meta name="twitter:description" content="Contact Hypecrews for digital marketing, web development, copyright protection & more. Reach us at info@hypecrews.com or call +913613243276. Golaghat, Assam.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ContactPage",
        "name": "Contact Hypecrews",
        "description": "Get in touch with Hypecrews for digital marketing, web development, copyright protection, and other premium digital services.",
        "url": "https://hypecrews.com/contact.php",
        "mainEntity": {
            "@type": "Organization",
            "name": "Hypecrews",
            "url": "https://hypecrews.com",
            "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png",
            "email": "info@hypecrews.com",
            "telephone": "+913613243276",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Golaghat",
                "addressRegion": "Assam",
                "postalCode": "785621",
                "addressCountry": "IN"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+913613243276",
                "email": "info@hypecrews.com",
                "contactType": "customer service",
                "availableLanguage": ["English", "Hindi"],
                "hoursAvailable": {
                    "@type": "OpeningHoursSpecification",
                    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                    "opens": "00:00",
                    "closes": "23:59"
                }
            }
        }
    }
    </script>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0B0F19',
                        light: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B0F19;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.05) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.05) 0, transparent 50%);
            color: #f8fafc;
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.15);
            transform: translateY(-4px);
        }

        .reveal-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }

        .reveal-up.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Interactive form elements */
        .glass-input {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(30, 41, 59, 0.8);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
            outline: none;
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white antialiased selection:bg-primary selection:text-white">
    <?php include 'components/nav.php'; ?>

    <!-- Contact Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[10%] left-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[120px] animate-[pulse_8s_infinite_alternate]"></div>
            <div class="absolute bottom-[-10%] right-[10%] w-[40%] h-[40%] bg-purple-900/20 rounded-full blur-[120px] animate-[pulse_10s_infinite_alternate-reverse]"></div>
        </div>
        
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10 reveal-up">
            <div class="inline-flex items-center justify-center mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wider uppercase text-gray-300">We Are Online</span>
            </div>
            
            <h1 class="font-heading text-5xl md:text-7xl font-bold mb-8 tracking-tight leading-tight">
                Let's Build Something <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-primary">Incredible Together</span>
            </h1>
            
            <p class="text-xl max-w-3xl mx-auto text-slate-400 mb-12 font-light leading-relaxed">
                Have questions or want to discuss a project? We'd love to hear from you. Reach out and let's start a conversation that transforms your digital presence.
            </p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-16 relative z-10">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12 max-w-7xl mx-auto">
                <!-- Contact Form -->
                <div class="lg:w-[55%] reveal-up">
                    <div class="glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden h-full">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full blur-[60px] pointer-events-none"></div>
                        
                        <h2 class="font-heading text-3xl font-bold mb-8 relative z-10">Send Us a <span class="text-primary">Message</span></h2>
                        
                        <form id="contactForm" class="space-y-6 relative z-10">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-slate-300 font-semibold text-sm mb-2">Full Name <span class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-slate-500"></i>
                                        </div>
                                        <input type="text" id="name" name="name" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-slate-500" placeholder="John Doe" required>
                                    </div>
                                </div>
                                <div>
                                    <label for="email" class="block text-slate-300 font-semibold text-sm mb-2">Email Address <span class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-slate-500"></i>
                                        </div>
                                        <input type="email" id="email" name="email" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-slate-500" placeholder="john@example.com" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="phone" class="block text-slate-300 font-semibold text-sm mb-2">Phone Number</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-phone text-slate-500"></i>
                                        </div>
                                        <input type="text" id="phone" name="phone" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-slate-500" placeholder="9876543210" maxlength="15">
                                    </div>
                                </div>
                                <div>
                                    <label for="subject" class="block text-slate-300 font-semibold text-sm mb-2">Subject <span class="text-red-400">*</span></label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fas fa-tag text-slate-500"></i>
                                        </div>
                                        <input type="text" id="subject" name="subject" class="glass-input w-full pl-11 pr-4 py-3.5 rounded-xl text-white placeholder-slate-500" placeholder="How can we help?" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="service" class="block text-slate-300 font-semibold text-sm mb-2">Service Interest <span class="text-red-400">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fas fa-layer-group text-slate-500"></i>
                                    </div>
                                    <select id="service" name="service" class="glass-input w-full pl-11 pr-10 py-3.5 rounded-xl text-white appearance-none cursor-pointer" required>
                                        <option value="" class="bg-[#0B0F19] text-gray-300" disabled selected>Select a service</option>
                                        <option value="copyright" class="bg-[#0B0F19] text-white">Copyright Protection & Removal</option>
                                        <option value="social" class="bg-[#0B0F19] text-white">Social Media Management</option>
                                        <option value="marketing" class="bg-[#0B0F19] text-white">Digital Marketing</option>
                                        <option value="web" class="bg-[#0B0F19] text-white">Web & Development</option>
                                        <option value="recovery" class="bg-[#0B0F19] text-white">Recovery & Support</option>
                                        <option value="video" class="bg-[#0B0F19] text-white">Video Production</option>
                                        <option value="seo" class="bg-[#0B0F19] text-white">Marketing & SEO</option>
                                        <option value="creative" class="bg-[#0B0F19] text-white">Creative & Production</option>
                                        <option value="partner" class="bg-[#0B0F19] text-white font-bold text-primary">Become a Partner</option>
                                        <option value="other" class="bg-[#0B0F19] text-white">Other</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label for="message" class="block text-slate-300 font-semibold text-sm mb-2">Message <span class="text-red-400">*</span></label>
                                <textarea id="message" name="message" rows="5" class="glass-input w-full px-4 py-3.5 rounded-xl text-white placeholder-slate-500 resize-y" placeholder="Tell us about your project..." required></textarea>
                            </div>
                            
                            <div class="pt-2">
                                <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] transform hover:-translate-y-1 flex items-center justify-center">
                                    Send Message <i class="fas fa-paper-plane ml-2"></i>
                                </button>
                            </div>
                        </form>
                        <div id="formMessage" class="mt-6 hidden"></div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="lg:w-[45%] flex flex-col space-y-6">
                    <div class="reveal-up" style="transition-delay: 100ms;">
                        <!-- Office Location -->
                        <div class="glass-card rounded-3xl p-8 flex items-start group">
                            <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mr-6 border border-white/5 shrink-0 group-hover:bg-primary/20 transition-colors">
                                <i class="fas fa-map-marker-alt text-primary text-3xl drop-shadow-[0_0_8px_rgba(99,102,241,0.5)]"></i>
                            </div>
                            <div>
                                <h3 class="font-heading text-xl font-bold mb-2 text-white group-hover:text-primary transition-colors">Our Headquarters</h3>
                                <p class="text-slate-400 font-light leading-relaxed">Golaghat, Assam, 785621<br>India</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reveal-up" style="transition-delay: 200ms;">
                        <!-- Contact Numbers -->
                        <div class="glass-card rounded-3xl p-8 flex items-start group">
                            <div class="w-16 h-16 rounded-2xl bg-secondary/10 flex items-center justify-center mr-6 border border-white/5 shrink-0 group-hover:bg-secondary/20 transition-colors">
                                <i class="fas fa-phone-alt text-secondary text-3xl drop-shadow-[0_0_8px_rgba(139,92,246,0.5)]"></i>
                            </div>
                            <div>
                                <h3 class="font-heading text-xl font-bold mb-2 text-white group-hover:text-secondary transition-colors">Call Us Directly</h3>
                                <p class="text-slate-400 font-light leading-relaxed">
                                    <span class="block">Main: <a href="tel:+913613243276" class="hover:text-white transition-colors">+91 361 324 3276</a></span>
                                    <span class="block mt-1 text-xs uppercase tracking-wider text-primary font-semibold">Available 24/7</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reveal-up" style="transition-delay: 300ms;">
                        <!-- Email Addresses -->
                        <div class="glass-card rounded-3xl p-8 flex items-start group">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500/10 flex items-center justify-center mr-6 border border-white/5 shrink-0 group-hover:bg-emerald-500/20 transition-colors">
                                <i class="fas fa-envelope text-emerald-400 text-3xl drop-shadow-[0_0_8px_rgba(52,211,153,0.5)]"></i>
                            </div>
                            <div>
                                <h3 class="font-heading text-xl font-bold mb-2 text-white group-hover:text-emerald-400 transition-colors">Email Support</h3>
                                <p class="text-slate-400 font-light leading-relaxed">
                                    <span class="block mb-1">Inquiries: <a href="mailto:info@hypecrews.com" class="hover:text-white transition-colors">info@hypecrews.com</a></span>
                                    <span class="block">Support: <a href="mailto:support@hypecrews.com" class="hover:text-white transition-colors">support@hypecrews.com</a></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="reveal-up" style="transition-delay: 400ms;">
                        <!-- Social Media -->
                        <div class="glass-card rounded-3xl p-8 text-center group">
                            <h3 class="font-heading text-xl font-bold mb-6 text-white">Connect With Us</h3>
                            <div class="flex justify-center space-x-4">
                                <a href="#" class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:bg-[#1DA1F2] hover:text-white hover:border-[#1DA1F2] transition-all duration-300 shadow-lg">
                                    <i class="fab fa-twitter text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:bg-[#E1306C] hover:text-white hover:border-[#E1306C] transition-all duration-300 shadow-lg">
                                    <i class="fab fa-instagram text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:bg-[#0077B5] hover:text-white hover:border-[#0077B5] transition-all duration-300 shadow-lg">
                                    <i class="fab fa-linkedin-in text-xl"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-slate-300 hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2] transition-all duration-300 shadow-lg">
                                    <i class="fab fa-facebook-f text-xl"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-24 relative z-10 border-t border-white/5 bg-[#0a0f18]">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-16 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">Frequently Asked <span class="text-gray-400">Questions</span></h2>
                <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full mb-6"></div>
                <p class="text-slate-400 max-w-2xl mx-auto font-light">Find answers to common questions about our services and processes.</p>
            </div>
            
            <div class="max-w-3xl mx-auto space-y-6">
                <!-- FAQ Items -->
                <?php
                $faqs = [
                    [
                        'q' => 'How long does a typical project take to complete?',
                        'a' => 'Project timelines vary depending on scope and complexity. Simple websites can be completed in 2-4 weeks, while larger projects may take 2-6 months. We provide detailed timelines during our initial consultation.'
                    ],
                    [
                        'q' => 'Do you offer ongoing support after project completion?',
                        'a' => 'Yes, we offer various maintenance and support packages to ensure your digital assets continue to perform optimally. Our team is always available for updates, improvements, and troubleshooting.'
                    ],
                    [
                        'q' => 'What information should I prepare before contacting you?',
                        'a' => 'To help us understand your needs better, please prepare information about your business goals, target audience, timeline, and budget. Examples of websites or designs you like can also be helpful.'
                    ],
                    [
                        'q' => 'How do you ensure the security of my data and intellectual property?',
                        'a' => 'We take data security seriously and employ industry-standard encryption, secure development practices, and confidentiality agreements. All client data is stored securely, and we never share your information without explicit permission.'
                    ]
                ];

                $delay = 0;
                foreach ($faqs as $faq) {
                    echo '
                    <div class="glass-card rounded-2xl p-8 reveal-up group hover:border-primary/30" style="transition-delay: '.$delay.'ms;">
                        <h3 class="font-heading text-xl font-bold mb-4 flex items-start text-white">
                            <i class="fas fa-question-circle text-primary mt-1 mr-4 opacity-80 group-hover:opacity-100 transition-opacity"></i>
                            '.$faq['q'].'
                        </h3>
                        <p class="text-slate-400 font-light leading-relaxed pl-9">'.$faq['a'].'</p>
                    </div>';
                    $delay += 100;
                }
                ?>
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
            
            // Intersection Observer for animations
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal-up');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>