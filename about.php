<?php
$pageTitle = "About Us - Hypecrews";
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

    <!-- About Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-dark via-[#0f172a] to-secondary/20 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMCIgY3k9IjEwIiByPSIxMCIgZmlsbD0icmdiYSgyNTUsMjU1LDI1NSwwLjAzKSIgLz48L3N2Zz4=')] opacity-20"></div>
        <div class="container mx-auto px-4 text-center relative z-10">
            <div class="inline-block mb-6 px-4 py-2 bg-primary/10 rounded-full border border-primary/30">
                <span class="text-primary font-medium">Welcome to Hypecrews</span>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold mb-6 leading-tight">
                Transforming Ideas Into <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-primary">Digital Reality</span>
            </h1>
            <p class="text-xl md:text-2xl max-w-3xl mx-auto text-gray-300 mb-10">
                We're a passionate team of digital innovators, creators, and strategists dedicated to helping businesses thrive in the ever-evolving online landscape.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="#story" class="px-8 py-4 bg-primary hover:bg-primary/90 text-white font-bold rounded-lg transition-all duration-300 transform hover:-translate-y-1 shadow-lg hover:shadow-primary/30">
                    Discover Our Story
                </a>
                <a href="services.php" class="px-8 py-4 bg-transparent border-2 border-white hover:bg-white hover:text-dark text-white font-bold rounded-lg transition-all duration-300 transform hover:-translate-y-1">
                    Explore Services
                </a>
            </div>
        </div>
    </section>

    <!-- Company Story -->
    <section class="py-16 bg-gradient-to-br from-light to-dark/90" id="story">
        <div class="container mx-auto px-4">
            <!-- Image moved to top -->
            <div class="relative mb-16">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl max-w-4xl mx-auto">
                    <img src="graphics/516as4d8a16s5d64as.jpg" alt="Our Team" class="w-full h-auto object-cover transition-transform duration-700 hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-dark/80 to-transparent"></div>
                    <div class="absolute bottom-6 left-6 text-white">
                        <h3 class="text-2xl font-bold">Our Journey</h3>
                        <p class="text-gray-200">From Local Roots to National Impact</p>
                    </div>
                </div>
                <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-primary/20 rounded-full blur-xl"></div>
            </div>
            
            <!-- Content below image -->
            <div class="max-w-4xl mx-auto">
                <div class="mb-12 text-center">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary rounded-full text-sm font-medium mb-4">OUR STORY</span>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6">From <span class="text-yellow-300">Guwahati</span> to Pan-India: Your Complete <span class="text-primary">Digital Partner</span></h2>
                    <p class="text-gray-300 mb-6 text-lg max-w-3xl mx-auto">Established in 2022 in the vibrant city of Guwahati, Assam, we have quickly evolved into a comprehensive digital solutions provider serving clients across the entire nation. We believe in bridging the gap between creativity, technology, and security.</p>
                    <p class="text-gray-300 mb-8 text-lg max-w-3xl mx-auto">Whether you are an artist looking for a spotlight, a business seeking a digital footprint, or a brand needing protection, our expert team is dedicated to delivering excellence with a personal touch.</p>
                </div>
                
                <div class="space-y-12">
                    <div class="border-l-4 border-primary pl-6 py-2">
                        <h3 class="text-xl font-bold text-white mb-3">What We Do</h3>
                        <p class="text-gray-300 mb-4">We operate at the intersection of entertainment and technology. Our 360-degree approach includes:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <p class="text-gray-300"><strong>Digital Growth:</strong> Expert Digital Marketing and Social Media Management</p>
                            </div>
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <p class="text-gray-300"><strong>Creative Services:</strong> Music & Video Production, PR & Artist Management</p>
                            </div>
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                                <p class="text-gray-300"><strong>Development:</strong> Custom Web & App Development</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="border-l-4 border-yellow-300 pl-6 py-2">
                        <h3 class="text-xl font-bold text-white mb-3">Security & Protection</h3>
                        <p class="text-gray-300 mb-4">In the digital age, your assets are vulnerable. We stand apart by offering specialized security services:</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-yellow-300">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <p class="text-gray-300"><strong>Content Protection:</strong> Robust Anti-Piracy measures</p>
                            </div>
                            <div class="flex items-start">
                                <div class="mt-1 mr-3 text-yellow-300">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <p class="text-gray-300"><strong>Account Recovery:</strong> Restore your digital identity</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 p-8 bg-gradient-to-r from-dark/80 to-primary/5 rounded-2xl border border-primary/30 shadow-xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-full -translate-y-16 translate-x-16"></div>
                    <div class="absolute bottom-0 left-0 w-24 h-24 bg-yellow-300/10 rounded-full translate-y-12 -translate-x-12"></div>
                    <div class="relative z-10 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/10 rounded-full mb-6">
                            <i class="fas fa-crown text-primary text-2xl"></i>
                        </div>
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-4">Our Core Philosophy</h3>
                        <div class="space-y-3">
                            <p class="text-2xl md:text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 via-primary to-secondary">
                                We are Hypecrews IT Solutions Private Limited
                            </p>
                            <div class="flex flex-col sm:flex-row justify-center gap-2 mt-6">
                                <span class="px-6 py-3 bg-dark/80 backdrop-blur-sm border border-primary/30 rounded-full text-lg font-bold text-primary shadow-lg">
                                    <i class="fas fa-paint-brush mr-2"></i>We Create
                                </span>
                                <span class="px-6 py-3 bg-dark/80 backdrop-blur-sm border border-primary/30 rounded-full text-lg font-bold text-yellow-300 shadow-lg">
                                    <i class="fas fa-code mr-2"></i>We Develop
                                </span>
                                <span class="px-6 py-3 bg-dark/80 backdrop-blur-sm border border-primary/30 rounded-full text-lg font-bold text-secondary shadow-lg">
                                    <i class="fas fa-shield-alt mr-2"></i>We Protect
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-6 justify-center mt-12">
                    <div class="flex items-center bg-dark/50 p-4 rounded-xl border border-primary/20">
                        <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                            <i class="fas fa-project-diagram text-primary text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">1000+</p>
                            <p class="text-gray-400">Projects Completed</p>
                        </div>
                    </div>
                    <div class="flex items-center bg-dark/50 p-4 rounded-xl border border-primary/20">
                        <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                            <i class="fas fa-users text-primary text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-white">754</p>
                            <p class="text-gray-400">Happy Clients</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-16 bg-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our <span class="text-primary">Mission</span> & Vision</h2>
                <p class="text-gray-400 max-w-2xl mx-auto">Driving digital transformation through innovative solutions and exceptional service</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <div class="bg-light rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                        <i class="fas fa-bullseye text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Our Mission</h3>
                    <p class="text-gray-400 mb-4">To empower businesses of all sizes with cutting-edge digital strategies that drive growth, enhance customer engagement, and deliver measurable results in an ever-evolving digital landscape.</p>
                    <p class="text-gray-400">We believe in building long-term partnerships based on trust, transparency, and exceptional service that exceeds expectations.</p>
                </div>
                
                <div class="bg-light rounded-xl p-8 shadow-lg">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6">
                        <i class="fas fa-eye text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Our Vision</h3>
                    <p class="text-gray-400 mb-4">To be the leading digital agency that transforms businesses through innovation, creativity, and technology, setting new standards for excellence in the industry.</p>
                    <p class="text-gray-400">We envision a future where every business, regardless of size, has access to world-class digital solutions that enable them to compete globally.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Our Core <span class="text-primary">Values</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">The principles that guide everything we do</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-lightbulb text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Innovation</h3>
                    <p class="text-gray-400">We constantly explore new ideas and technologies to deliver groundbreaking solutions that set our clients apart.</p>
                </div>
                
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-handshake text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Integrity</h3>
                    <p class="text-gray-400">We conduct business with honesty and transparency, building trust through consistent actions and ethical practices.</p>
                </div>
                
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-medal text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Excellence</h3>
                    <p class="text-gray-400">We strive for perfection in everything we do, delivering high-quality work that exceeds expectations every time.</p>
                </div>
                
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Collaboration</h3>
                    <p class="text-gray-400">We believe in the power of teamwork, both internally and with our clients, to achieve extraordinary results.</p>
                </div>
                
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-heart text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Passion</h3>
                    <p class="text-gray-400">We're driven by a genuine love for what we do and a commitment to making a positive impact on our clients' success.</p>
                </div>
                
                <div class="bg-dark rounded-xl p-8 shadow-lg text-center">
                    <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-chart-line text-primary text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Results</h3>
                    <p class="text-gray-400">We focus on delivering measurable outcomes that contribute to our clients' growth and long-term success.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16 bg-dark">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Meet Our <span class="text-primary">Team</span></h2>
                <p class="text-gray-400 max-w-2xl mx-auto">The talented professionals behind our success</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Dheeraj Jyoti Saikia - CEO & Founder -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/co-founder.png" alt="Dheeraj Jyoti Saikia" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Dheeraj Jyoti Saikia</h3>
                    <p class="text-primary mb-3">CEO & Founder</p>
                    <p class="text-gray-400 mb-4">Visionary leader driving innovation and growth</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Jitu Moni Hazarika - Co Founder -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/co-founder.png" alt="Jitu Moni Hazarika" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Jitu Moni Hazarika</h3>
                    <p class="text-primary mb-3">Co Founder</p>
                    <p class="text-gray-400 mb-4">Strategic partner in business development and operations</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Rajdeep Pandit - COO & CTO -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/co-founder.png" alt="Rajdeep Pandit" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Rajdeep Pandit</h3>
                    <p class="text-primary mb-3">COO & CTO</p>
                    <p class="text-gray-400 mb-4">Chief Operating Officer and Chief Technology Officer</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Aruhan Gogoi - Head of Production -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/co-founder.png" alt="Aruhan Gogoi" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Aruhan Gogoi</h3>
                    <p class="text-primary mb-3">Head of Production</p>
                    <p class="text-gray-400 mb-4">Leadership in production operations and quality assurance</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Shreya Kumar - Marketing Director -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/female2.png" alt="Shreya Kumar" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Shreya Kumar</h3>
                    <p class="text-primary mb-3">Marketing Director</p>
                    <p class="text-gray-400 mb-4">Strategic marketer with expertise in brand growth</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Raghu Agarwal - Manager -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/male.png" alt="Raghu Agarwal" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Raghu Agarwal</h3>
                    <p class="text-primary mb-3">Manager</p>
                    <p class="text-gray-400 mb-4">Operations manager ensuring smooth business execution</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Harshit - Executor -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/male.png" alt="Harshit" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Harshit</h3>
                    <p class="text-primary mb-3">Executor</p>
                    <p class="text-gray-400 mb-4">Efficient executor driving project implementation</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Kavya Singh - HR Executive -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/female1.png" alt="Kavya Singh" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Kavya Singh</h3>
                    <p class="text-primary mb-3">HR Executive</p>
                    <p class="text-gray-400 mb-4">Human resources specialist fostering team growth</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Aadhya Patel - Sales Manager -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="graphics/our-team/female2.png" alt="Aadhya Patel" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Aadhya Patel</h3>
                    <p class="text-primary mb-3">Sales Manager</p>
                    <p class="text-gray-400 mb-4">Sales leader driving business growth and client relations</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Work With Us?</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto">Join the many businesses that have transformed their digital presence with Hypecrews.</p>
            <a href="index.php#contact" class="inline-block bg-white text-primary font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">Get In Touch</a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>