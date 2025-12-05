<?php
$pageTitle = "Hypecrews - Professional Digital Services";
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

    <!-- Hero Section -->
    <section id="home" class="pt-20 pb-16 bg-gradient-to-r from-dark to-[#0f172a] text-white relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary/10 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center relative z-10">
            <div class="md:w-1/2 mb-10 md:mb-0 animate-on-scroll">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    <span class="ml-3 text-primary font-bold">INNOVATION DRIVEN</span>
                </div>
                <h1 class="text-4xl md:text-6xl font-bold mb-6 leading-tight">
                    Elevate Your <span class="block bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Digital Presence</span>
                </h1>
                <p class="text-xl mb-8 text-gray-300 max-w-lg">Professional services tailored to grow your business in the digital world with cutting-edge solutions.</p>
                <div class="flex flex-wrap gap-4">
                    <a href="#services" class="bg-gradient-to-r from-primary to-secondary text-white font-bold py-4 px-8 rounded-xl hover:from-indigo-700 hover:to-purple-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">Explore Services</a>
                    <a href="#contact" class="border-2 border-white text-white font-bold py-4 px-8 rounded-xl hover:bg-white hover:text-dark transition-all duration-300 transform hover:-translate-y-1">Get In Touch</a>
                </div>
                
                <!-- Stats bar -->
                <div class="mt-12 flex flex-wrap gap-8">
                    <div>
                        <p class="text-3xl font-bold text-primary">250+</p>
                        <p class="text-gray-400">Projects</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary">50+</p>
                        <p class="text-gray-400">Clients</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary">98%</p>
                        <p class="text-gray-400">Satisfaction</p>
                    </div>
                </div>
            </div>
            
            <div class="md:w-1/2 flex justify-center animate-on-scroll">
                <div class="relative">
                    <!-- Main hero image -->
                    <div class="relative z-10 transform rotate-3 transition-all duration-500 hover:rotate-6">
                        <div class="bg-gradient-to-br from-primary/20 to-secondary/20 backdrop-blur-sm rounded-3xl p-6 shadow-2xl border border-white/10">
                            <img src="graphics/516as4d8a16s5d64as.jpg" alt="Digital Marketing" class="rounded-2xl w-full shadow-lg">
                        </div>
                    </div>
                    
                    <!-- Floating elements -->
                    <div class="absolute -top-6 -right-6 bg-gradient-to-r from-primary to-secondary rounded-2xl shadow-xl p-4 z-20 transform transition-all duration-300 hover:scale-110">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center">
                                <i class="fas fa-award text-primary text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-bold text-white">Award</p>
                                <p class="text-white/80 text-sm">Winning</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-6 -left-6 bg-white text-dark rounded-2xl shadow-xl p-4 z-20 transform transition-all duration-300 hover:scale-110">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center">
                                <i class="fas fa-lightbulb text-white text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <p class="font-bold">Creative</p>
                                <p class="text-gray-600 text-sm">Solutions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our <span class="gradient-text">Services</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Comprehensive digital solutions to boost your brand and business growth</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Service 1 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-copyright text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Copyright Protection & Removal</h3>
                    <p class="text-gray-400 mb-4">Safeguard your intellectual property and handle copyright infringement issues effectively.</p>
                    <a href="services.php#copyright" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 2 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-hashtag text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Social Media Management</h3>
                    <p class="text-gray-400 mb-4">Strategic management of your social platforms to engage audiences and build brand presence.</p>
                    <a href="services.php#social" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 3 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-bullhorn text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Digital Marketing</h3>
                    <p class="text-gray-400 mb-4">Data-driven marketing campaigns to reach your target audience and drive conversions.</p>
                    <a href="services.php#digital" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 4 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-laptop-code text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Web & Development</h3>
                    <p class="text-gray-400 mb-4">Custom websites and web applications designed to meet your specific business needs.</p>
                    <a href="services.php#web" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 5 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-sync-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Recovery & Support</h3>
                    <p class="text-gray-400 mb-4">Technical support and recovery services to keep your digital assets secure and functional.</p>
                    <a href="services.php#recovery" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 6 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-video text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Video Production</h3>
                    <p class="text-gray-400 mb-4">Professional video content creation for marketing, training, and brand storytelling.</p>
                    <a href="services.php#video" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 7 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-chart-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Marketing & SEO</h3>
                    <p class="text-gray-400 mb-4">Optimize your online visibility and drive organic traffic through strategic marketing and SEO.</p>
                    <a href="services.php#marketing" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 8 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll md:col-span-2 lg:col-span-1 lg:col-start-2">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-paint-brush text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Creative & Production</h3>
                    <p class="text-gray-400 mb-4">Innovative creative solutions including graphic design, branding, and multimedia production.</p>
                    <a href="services.php#creative" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 9 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-briefcase text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Business Support Services</h3>
                    <p class="text-gray-400 mb-4">Comprehensive business assistance to streamline operations and enhance productivity.</p>
                    <a href="services.php#business-support" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 10 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-file-contract text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Company Registration</h3>
                    <p class="text-gray-400 mb-4">Seamless business incorporation services with legal compliance guidance.</p>
                    <a href="services.php#company-registration" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 11 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-film text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Movie PR</h3>
                    <p class="text-gray-400 mb-4">Strategic public relations services for film promotion and media coverage.</p>
                    <a href="services.php#movie-pr" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 12 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-music text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Music/Video Distribution</h3>
                    <p class="text-gray-400 mb-4">Global distribution services for music and video content across major platforms.</p>
                    <a href="services.php#distribution" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
                
                <!-- Service 13 -->
                <div class="service-card bg-dark rounded-2xl p-6 shadow-lg transition duration-300 hover:shadow-xl animate-on-scroll">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-6 transition-all duration-300">
                        <i class="fas fa-microphone text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Artist Management</h3>
                    <p class="text-gray-400 mb-4">Professional representation and career development for creative talents.</p>
                    <a href="services.php#artist-management" class="text-primary font-medium flex items-center group">
                        Learn more
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-gradient-to-br from-dark to-light">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2 animate-on-scroll">
                    <div class="relative">
                        <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl w-full h-full absolute -top-4 -left-4 z-0"></div>
                        <div class="relative z-10 rounded-2xl overflow-hidden border-8 border-dark shadow-xl">
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="About Hypecrews" class="w-full h-auto object-cover">
                        </div>
                        <div class="absolute -bottom-6 -right-6 bg-dark rounded-xl shadow-lg p-4 z-20 border border-gray-800">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                                    <i class="fas fa-award text-primary"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="font-bold text-lg">250+</p>
                                    <p class="text-gray-400 text-sm">Projects</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 animate-on-scroll">
                    <div class="bg-dark rounded-2xl shadow-lg p-8 border border-gray-800">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                            <span class="ml-3 text-primary font-medium">ABOUT US</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold mb-6">About <span class="gradient-text">Hypecrews</span></h2>
                        <p class="text-gray-400 mb-6 leading-relaxed">We are a team of digital experts dedicated to helping businesses thrive in the online world. With years of experience across various industries, we bring innovative solutions tailored to your unique needs.</p>
                        <p class="text-gray-400 mb-8 leading-relaxed">Our mission is to empower brands with cutting-edge digital strategies that drive growth, engagement, and success in an ever-evolving digital landscape.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-lightbulb text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Innovation</h4>
                                    <p class="text-gray-400 text-sm">Creative solutions for modern challenges</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-rocket text-primary"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Excellence</h4>
                                    <p class="text-gray-400 text-sm">Delivering results that exceed expectations</p>
                                </div>
                            </div>
                        </div>
                        
                        <a href="about.php" class="inline-flex items-center bg-gradient-to-r from-primary to-secondary text-white font-bold py-3 px-6 rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-300 shadow-md hover:shadow-lg group">
                            Discover More
                            <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-16 bg-gradient-to-br from-light to-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="flex items-center justify-center mb-4">
                    <div class="w-10 h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                    <span class="mx-3 text-primary font-medium">CONTACT US</span>
                    <div class="w-10 h-1 bg-gradient-to-r from-primary to-secondary rounded-full"></div>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Get In <span class="gradient-text">Touch</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Have a project in mind? Reach out to us and let's discuss how we can help your business grow.</p>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="lg:w-1/2 animate-on-scroll">
                    <div class="bg-dark rounded-2xl shadow-lg p-8 border border-gray-800">
                        <h3 class="text-2xl font-bold mb-6">Send Us a Message</h3>
                        <form class="space-y-6">
                            <div>
                                <label for="name" class="block text-gray-300 font-medium mb-2">Full Name</label>
                                <input type="text" id="name" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-white" placeholder="John Doe">
                            </div>
                            <div>
                                <label for="email" class="block text-gray-300 font-medium mb-2">Email Address</label>
                                <input type="email" id="email" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-white" placeholder="john@example.com">
                            </div>
                            <div>
                                <label for="subject" class="block text-gray-300 font-medium mb-2">Subject</label>
                                <input type="text" id="subject" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-white" placeholder="How can we help?">
                            </div>
                            <div>
                                <label for="message" class="block text-gray-300 font-medium mb-2">Message</label>
                                <textarea id="message" rows="5" class="w-full px-4 py-3 bg-light border border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-300 text-white" placeholder="Tell us about your project..."></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-md hover:shadow-lg">Send Message</button>
                        </form>
                    </div>
                </div>
                
                <div class="lg:w-1/2 animate-on-scroll">
                    <div class="bg-dark rounded-2xl shadow-lg p-8 h-full border border-gray-800">
                        <h3 class="text-2xl font-bold mb-6">Contact Information</h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start p-4 rounded-xl hover:bg-light transition-all duration-300">
                                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-primary text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Our Location</h4>
                                    <p class="text-gray-400">Golaghat, Assam, 785621, India</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start p-4 rounded-xl hover:bg-light transition-all duration-300">
                                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone-alt text-primary text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Phone Number</h4>
                                    <p class="text-gray-400">+913613243276</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start p-4 rounded-xl hover:bg-light transition-all duration-300">
                                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-primary text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Email Address</h4>
                                    <p class="text-gray-400">info@hypecrews.com</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start p-4 rounded-xl hover:bg-light transition-all duration-300">
                                <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-primary text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-bold text-lg mb-1">Working Hours</h4>
                                    <p class="text-gray-400">Monday To Sunday 24x7</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-10">
                            <h4 class="font-bold text-lg mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-white hover:bg-indigo-700 transition-all duration-300 shadow-md hover:shadow-lg">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Clients Section -->
    <section class="py-16 bg-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 animate-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our <span class="gradient-text">Clients</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">We've worked with some of the biggest brands in the industry</p>
            </div>
            
            <div class="animate-slide">
                <!-- Axis Bank -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/axis-bank.png" alt="Axis Bank" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Axis Bank</span>
                    </div>
                </div>
                
                <!-- Amazon -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/amazon.png" alt="Amazon" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Amazon</span>
                    </div>
                </div>
                
                <!-- Decathlon -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/decathlon.png" alt="Decathlon" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Decathlon</span>
                    </div>
                </div>
                
                <!-- Samsung -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/samsung.png" alt="Samsung" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Samsung</span>
                    </div>
                </div>
                
                <!-- Infosys -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/infosys.png" alt="Infosys" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Infosys</span>
                    </div>
                </div>
                
                <!-- Duplicate for seamless loop -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/axis-bank.png" alt="Axis Bank" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Axis Bank</span>
                    </div>
                </div>
                
                <!-- Amazon -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/amazon.png" alt="Amazon" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Amazon</span>
                    </div>
                </div>
                
                <!-- Decathlon -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/decathlon.png" alt="Decathlon" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Decathlon</span>
                    </div>
                </div>
                
                <!-- Samsung -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/samsung.png" alt="Samsung" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Samsung</span>
                    </div>
                </div>
                
                <!-- Infosys -->
                <div class="flex-shrink-0 flex items-center justify-center p-8 bg-light rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 w-64 inline-flex mx-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mr-4">
                            <img src="graphics/logos/infosys.png" alt="Infosys" class="w-8 h-8 object-contain">
                        </div>
                        <span class="text-2xl font-bold text-white">Infosys</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <?php include 'components/cursor_highlight.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>