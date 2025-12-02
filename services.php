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
    <section class="pt-24 pb-12 bg-gradient-to-r from-dark to-[#0f172a] text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">Our Comprehensive <span class="text-yellow-300">Services</span></h1>
            <p class="text-xl max-w-3xl mx-auto">Discover how our expert team can help elevate your brand and accelerate your business growth through our specialized digital services.</p>
        </div>
    </section>

    <!-- Services Details Section -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
                <!-- Copyright Protection & Removal -->
                <div id="copyright" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-copyright text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Copyright Protection & Removal</h3>
                                <p class="text-gray-400 mt-2">Safeguard your intellectual property</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Protect your creative works and brand assets with our comprehensive copyright protection services. Our experts help you register copyrights, monitor for infringements, and take legal action when necessary.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Copyright registration assistance</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Infringement monitoring</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Takedown notices and legal support</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Licensing agreement guidance</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Social Media Management -->
                <div id="social" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-hashtag text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Social Media Management</h3>
                                <p class="text-gray-400 mt-2">Build your brand presence online</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Engage your audience and grow your community with our strategic social media management services. We create compelling content and manage your social profiles to maximize engagement.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Content creation and scheduling</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Community management</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Analytics and reporting</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Paid social advertising</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Digital Marketing -->
                <div id="digital" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-bullhorn text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Digital Marketing</h3>
                                <p class="text-gray-400 mt-2">Drive targeted traffic and conversions</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Reach your ideal customers with our data-driven digital marketing strategies. We combine SEO, PPC, content marketing, and analytics to deliver measurable results for your business.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Search engine marketing (SEM)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Email marketing campaigns</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Conversion rate optimization</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Marketing automation</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Web & Development -->
                <div id="web" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-laptop-code text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Web & Development</h3>
                                <p class="text-gray-400 mt-2">Create stunning digital experiences</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Build powerful, responsive websites and web applications that engage users and drive business results. Our development team creates custom solutions tailored to your specific needs.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Custom website development</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>E-commerce solutions</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Mobile-responsive design</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Ongoing maintenance and support</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Recovery & Support -->
                <div id="recovery" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-sync-alt text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Recovery & Support</h3>
                                <p class="text-gray-400 mt-2">Keep your systems running smoothly</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Ensure your digital assets remain secure and operational with our comprehensive recovery and support services. We provide rapid response solutions for technical issues and data recovery.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Data backup and recovery</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Technical troubleshooting</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>System maintenance</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>24/7 support options</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Video Production -->
                <div id="video" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-video text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Video Production</h3>
                                <p class="text-gray-400 mt-2">Tell your story visually</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Create compelling video content that captures attention and communicates your message effectively. From concept to delivery, we produce high-quality videos for all platforms.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Concept development and scripting</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Professional filming and editing</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Animation and motion graphics</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Video marketing strategy</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Marketing & SEO -->
                <div id="marketing" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-chart-line text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Marketing & SEO</h3>
                                <p class="text-gray-400 mt-2">Boost your online visibility</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Improve your search engine rankings and drive organic traffic with our comprehensive SEO and marketing services. We optimize your digital presence to attract more qualified visitors.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Keyword research and optimization</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>On-page and technical SEO</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Link building strategies</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Performance analytics</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Creative & Production -->
                <div id="creative" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-paint-brush text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Creative & Production</h3>
                                <p class="text-gray-400 mt-2">Design that makes an impact</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Transform your brand vision into stunning visual assets with our creative production services. Our designers craft memorable experiences that resonate with your audience.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Branding and identity design</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Print and digital graphics</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>User interface design</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Creative consulting</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Services Section -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Additional <span class="text-yellow-300">Services</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Specialized offerings to support your business growth and creative projects</p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Professional Business Support Services -->
                <div id="business-support" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-briefcase text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Professional Business Support Services</h3>
                                <p class="text-gray-400 mt-2">Comprehensive business assistance solutions</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Our professional business support services provide comprehensive assistance to help streamline your operations, enhance productivity, and achieve your business objectives efficiently.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Administrative support and organization</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Business process optimization</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Financial and accounting assistance</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Strategic planning consultation</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Company Registration Assistance -->
                <div id="company-registration" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-file-contract text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Company Registration Assistance</h3>
                                <p class="text-gray-400 mt-2">Seamless business incorporation services</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">We simplify the company registration process by guiding you through all legal requirements, documentation, and compliance procedures to establish your business entity efficiently.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Business structure selection advice</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Registration documentation preparation</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Legal compliance guidance</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Ongoing corporate compliance support</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- PR for Movie -->
                <div id="movie-pr" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-film text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">PR for Movie</h3>
                                <p class="text-gray-400 mt-2">Strategic movie promotion and publicity</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">Our specialized public relations services for movies help generate buzz, secure media coverage, and build anticipation for your film release through targeted campaigns.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Media outreach and press kit development</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Film festival submission strategy</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Celebrity and influencer engagement</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Social media campaign management</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
                    </div>
                </div>

                <!-- Music/Video Distribution Service -->
                <div id="distribution" class="service-detail-card bg-dark rounded-xl shadow-lg overflow-hidden transition duration-300">
                    <div class="p-8">
                        <div class="flex items-start mb-6">
                            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mr-6">
                                <i class="fas fa-music text-primary text-3xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold">Music/Video Distribution Service</h3>
                                <p class="text-gray-400 mt-2">Global distribution for your creative content</p>
                            </div>
                        </div>
                        <p class="text-gray-400 mb-6">We help artists and creators distribute their music and video content across major platforms worldwide, ensuring maximum reach and revenue generation for your creative works.</p>
                        <ul class="space-y-3 mb-6">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Multi-platform distribution (Spotify, Apple Music, YouTube, etc.)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Royalty collection and management</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Digital rights management</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                <span>Analytics and performance reporting</span>
                            </li>
                        </ul>
                        <a href="index.php#contact" class="inline-block bg-primary hover:bg-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition">Learn More</a>
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
    
    <script src="js/main.js"></script>
</body>
</html>