<?php
$pageTitle = "Our Services - Hypecrews";
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

    <!-- Services Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-dark via-[#0f172a] to-secondary/20 text-white relative overflow-hidden">
        <!-- Animated background elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary/10 rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-primary/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="inline-flex items-center justify-center mb-6 w-16 h-16 bg-primary/10 rounded-2xl mx-auto border border-primary/20">
                <i class="fas fa-concierge-bell text-primary text-2xl"></i>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">Our Comprehensive <span class="bg-gradient-to-r from-yellow-300 to-primary bg-clip-text text-transparent">Services</span></h1>
            <p class="text-xl max-w-3xl mx-auto text-gray-300 mb-10">Discover how our expert team can help elevate your brand and accelerate your business growth through our specialized digital services.</p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#services" class="px-8 py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-xl transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30">
                    Explore Services
                </a>
                <a href="index.php#contact" class="px-8 py-4 bg-transparent border-2 border-white hover:bg-white hover:text-dark text-white font-bold rounded-xl transition-all duration-300 transform hover:-translate-y-1">
                    Get In Touch
                </a>
            </div>
        </div>
    </section>

    <!-- Services Details Section -->
    <section id="services" class="py-20 bg-gradient-to-br from-light to-dark relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="inline-flex items-center justify-center mb-6 w-16 h-16 bg-primary/10 rounded-2xl mx-auto border border-primary/20">
                    <i class="fas fa-toolbox text-primary text-2xl"></i>
                </div>
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Our <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Premium Services</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto text-lg">Comprehensive digital solutions to boost your brand and business growth with cutting-edge innovation</p>
                
                <!-- Animated underline -->
                <div class="mt-6 flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-primary to-secondary rounded-full animate-pulse"></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                <!-- Copyright Protection & Removal -->
                <div id="copyright" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-copyright text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Copyright Protection & Removal</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">We specialize in protecting your intellectual property across all digital platforms. Our service includes proactive monitoring for unauthorized use of your copyrighted content (music, videos, images, text), swift issuance of DMCA takedown notices, and persistent follow-up to ensure permanent removal from websites, social media, and file-sharing platforms. We safeguard your work and revenue stream.</p>
                        <a href="service_query.php?service=Copyright Protection & Removal" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Social Media Management -->
                <div id="social" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-hashtag text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Social Media Management</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our expert team handles all aspects of your social media presence, from strategic content planning and creation to daily posting, audience engagement, and performance analysis. We focus on building a strong brand voice, increasing follower count, driving traffic, and fostering a loyal community across platforms like Instagram, Facebook, Twitter, and TikTok, ensuring your brand stays relevant and connected.</p>
                        <a href="service_query.php?service=Social Media Management" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Digital Marketing -->
                <div id="digital" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-bullhorn text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Digital Marketing</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our Digital Marketing services are designed to maximize your online visibility and drive measurable results. We employ a data-driven approach encompassing search engine optimization (SEO), pay-per-click (PPC) advertising, content marketing, and email campaigns. We analyze market trends and audience behavior to craft targeted, high-converting strategies that increase leads, sales, and overall ROI.</p>
                        <a href="service_query.php?service=Digital Marketing" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Web & Development -->
                <div id="web" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-laptop-code text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Web & Development</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">We offer comprehensive web and application development services, covering everything from simple informational websites to complex e-commerce platforms and custom web applications. Our team of skilled developers utilizes the latest technologies and best practices to deliver visually stunning, highly functional, responsive, and secure digital solutions tailored to your specific business needs and user experience goals.</p>
                        <a href="service_query.php?service=Web & Development" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Recovery & Support -->
                <div id="recovery" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-sync-alt text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Recovery & Support</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our Recovery & Support service provides essential assistance for digital crises, focusing on regaining control of compromised accounts and repairing online reputation damage. Whether dealing with platform suspensions, hacked accounts, content removal disputes, or negative press, our dedicated team works swiftly to assess the situation, implement strategic recovery protocols, and provide ongoing support to restore your digital assets and professional standing.</p>
                        <a href="service_query.php?service=Recovery & Support" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Video Production -->
                <div id="video" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-video text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Video Production</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our Video Production service covers the entire creative process, from initial concept development and scriptwriting to filming, post-production editing, and final delivery. We specialize in producing high-quality, engaging visual content, including promotional videos, music videos, short films, corporate explainers, and social media clips, tailored to captivate your audience and effectively convey your brand's message.</p>
                        <a href="service_query.php?service=Video Production" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Marketing & SEO -->
                <div id="marketing" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-chart-line text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Marketing & SEO</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our Marketing & SEO services are designed to boost your online visibility and drive targeted traffic to your website. We combine proven search engine optimization techniques with strategic digital marketing campaigns to improve your search rankings, increase organic traffic, and generate qualified leads. Our data-driven approach ensures measurable results that contribute to your business growth and revenue objectives.</p>
                        <a href="service_query.php?service=Marketing & SEO" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Creative & Production -->
                <div id="creative" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-paint-brush text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Creative & Production</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our Creative & Production service is the hub for turning ideas into stunning realities. We provide end-to-end creative solutions, including graphic design (logos, branding assets, digital media), high-quality photography, and visual content strategy. Our focus is on delivering compelling, aesthetically refined content that aligns with your brand identity and resonates powerfully with your target audience across all digital and print mediums.</p>
                        <a href="service_query.php?service=Creative & Production" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Services Section -->
    <section class="py-20 bg-gradient-to-br from-light to-dark relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary/5 rounded-full blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-16 animate-on-scroll">
                <div class="inline-flex items-center justify-center mb-6 w-16 h-16 bg-primary/10 rounded-2xl mx-auto border border-primary/20">
                    <i class="fas fa-plus-circle text-primary text-2xl"></i>
                </div>
                <h2 class="text-3xl md:text-5xl font-bold mb-6">Extended <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">Service Offerings</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto text-lg">Additional specialized services to meet your diverse business needs</p>
                
                <!-- Animated underline -->
                <div class="mt-6 flex justify-center">
                    <div class="w-24 h-1 bg-gradient-to-r from-primary to-secondary rounded-full animate-pulse"></div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Professional Business Support Services -->
                <div id="business-support" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-briefcase text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Professional Business Support Services</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">Our comprehensive suite of Professional Business Support Services is designed to streamline your operations and foster sustainable growth. We offer specialized administrative, financial, and strategic support, including virtual assistant services, documentation and compliance management, human resources consulting, and process optimization. Our goal is to handle your essential back-office functions efficiently, allowing you to focus your time and resources on core business activities and strategic expansion.</p>
                        <a href="service_query.php?service=Professional Business Support Services" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Company Registration Assistance -->
                <div id="company-registration" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-file-contract text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Company Registration Assistance</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">This service assists entrepreneurs and businesses with the necessary steps to formally establish their company. We guide you through the entire registration process, including choosing the appropriate legal structure (e.g., sole proprietorship, LLC, corporation), preparing and filing required documentation with relevant government authorities, securing necessary licenses and permits, and advising on initial compliance requirements. Our goal is to ensure a seamless, legally sound, and efficient setup for your new venture.</p>
                        <a href="service_query.php?service=Company Registration Assistance" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- PR for Movie -->
                <div id="movie-pr" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-film text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">PR for Movie</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">This service focuses on crafting and executing strategic Public Relations campaigns specifically for films and cinematic projects. We handle media outreach, press release distribution, red carpet event coordination, junket scheduling, and crisis communication. Our goal is to generate positive buzz, secure high-impact reviews and interviews, and increase visibility across traditional media, film festivals, and digital platforms to drive audience attendance and critical acclaim.</p>
                        <a href="service_query.php?service=PR for Movie" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>

                <!-- Music/Video Distribution Service -->
                <div id="distribution" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-music text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Music/Video Distribution Service</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">This service is dedicated to efficiently delivering your music and video content to a global audience. We manage the entire distribution pipeline, ensuring your work is properly formatted and delivered to major streaming platforms (e.g., Spotify, Apple Music, YouTube, Amazon Prime Video), digital stores, and international networks. We handle metadata, ISRC/UPC registration, royalty collection, and detailed performance analytics, maximizing your content's reach and revenue potential while maintaining full ownership and control.</p>
                        <a href="service_query.php?service=Music/Video Distribution Service" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>
                <!-- Artist Management -->
                <div id="artist-management" class="service-detail-card bg-dark rounded-2xl shadow-lg overflow-hidden transition-all duration-500 hover:shadow-2xl transform hover:-translate-y-3 border border-gray-800 hover:border-primary/50 animate-on-scroll group">
                    <div class="p-8 relative overflow-hidden">
                        <!-- Animated background element -->
                        <div class="absolute -top-20 -right-20 w-40 h-40 bg-primary/5 rounded-full blur-2xl transition-all duration-700 group-hover:scale-150"></div>
                        
                        <div class="flex items-start mb-6 relative z-10">
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center mr-6 transition-all duration-500 group-hover:from-primary/30 group-hover:to-secondary/30 border border-primary/20 group-hover:border-primary/40 transform group-hover:rotate-12">
                                <i class="fas fa-microphone text-primary text-3xl transition-all duration-300 group-hover:scale-110"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold mb-2 group-hover:text-primary transition-colors duration-300">Artist Management</h3>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6 relative z-10">We provide dedicated, 360-degree career guidance and logistical support for artists, musicians, and performers. Our service encompasses contract negotiation, booking management (gigs, tours, appearances), brand development, strategic career planning, financial oversight, and day-to-day administrative tasks. We act as your primary liaison, ensuring your professional endeavors are optimized for maximum success, creative fulfillment, and long-term sustainability while allowing you to focus purely on your artistry.</p>
                        <a href="service_query.php?service=Artist Management" class="inline-block bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white font-medium py-3 px-6 rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30 relative z-10 group-hover:shadow-lg group-hover:scale-105">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Transform Your Business?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Let's discuss how our services can help you achieve your business goals and drive growth.</p>
            <a href="index.php#contact" class="inline-block bg-white text-primary font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">Get Started Today</a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <?php include 'components/cursor_highlight.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>