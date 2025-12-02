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
    <section class="pt-24 pb-12 bg-gradient-to-r from-dark to-[#0f172a] text-white">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">About <span class="text-yellow-300">Hypecrews</span></h1>
            <p class="text-xl max-w-3xl mx-auto">We're a passionate team of digital experts dedicated to helping businesses thrive in the online world.</p>
        </div>
    </section>

    <!-- Company Story -->
    <section class="py-16 bg-light">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row items-center gap-12">
                <div class="lg:w-1/2">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" alt="Our Team" class="rounded-xl w-full">
                </div>
                <div class="lg:w-1/2">
                    <h2 class="text-3xl font-bold mb-6">Our <span class="text-primary">Story</span></h2>
                    <p class="text-gray-400 mb-6">Founded in 2015, Hypecrews began with a simple mission: to bridge the gap between creative vision and digital execution. What started as a small team of passionate developers and designers has grown into a full-service digital agency.</p>
                    <p class="text-gray-400 mb-6">Over the years, we've had the privilege of working with startups, established enterprises, and everything in between. Each project has taught us something new and helped refine our approach to digital solutions.</p>
                    <p class="text-gray-400 mb-8">Today, we continue to push boundaries and explore new technologies while staying true to our core values of innovation, integrity, and excellence.</p>
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                                <i class="fas fa-project-diagram text-primary"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold">250+</p>
                                <p class="text-gray-400">Projects Completed</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div>
                                <p class="text-2xl font-bold">50+</p>
                                <p class="text-gray-400">Happy Clients</p>
                            </div>
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
                <!-- Team Member 1 -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Alex Johnson</h3>
                    <p class="text-primary mb-3">CEO & Founder</p>
                    <p class="text-gray-400 mb-4">Visionary leader with 15+ years in digital transformation</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Team Member 2 -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Sarah Williams</h3>
                    <p class="text-primary mb-3">Creative Director</p>
                    <p class="text-gray-400 mb-4">Award-winning designer with expertise in brand strategy</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Team Member 3 -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Michael Chen</h3>
                    <p class="text-primary mb-3">CTO</p>
                    <p class="text-gray-400 mb-4">Tech innovator specializing in scalable web solutions</p>
                    <div class="flex justify-center space-x-3">
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Team Member 4 -->
                <div class="team-member text-center">
                    <div class="overflow-hidden rounded-xl mb-6">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=774&q=80" alt="Team Member" class="w-full h-80 object-cover transition duration-500">
                    </div>
                    <h3 class="text-xl font-bold">Emily Rodriguez</h3>
                    <p class="text-primary mb-3">Marketing Director</p>
                    <p class="text-gray-400 mb-4">Data-driven marketer with expertise in digital campaigns</p>
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