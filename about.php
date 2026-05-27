<?php
session_start();
$pageTitle = "About Us - Hypecrews";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Learn about Hypecrews, a passionate digital agency team from Assam, India. We deliver innovative web development, marketing, and copyright protection solutions.">
    <meta name="keywords" content="about hypecrews, digital agency team, assam india agency, digital marketing company, web development agency">
    <link rel="canonical" href="https://hypecrews.com/about">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="About Us - Hypecrews">
    <meta property="og:description" content="Meet the passionate team behind Hypecrews. From Assam to pan-India, we transform ideas into digital reality with innovation and excellence.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/about">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="About Us - Hypecrews">
    <meta name="twitter:description" content="Meet the passionate team behind Hypecrews. From Assam to pan-India, we transform ideas into digital reality with innovation and excellence.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <!-- JSON-LD Structured Data: AboutPage -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "AboutPage",
        "name": "About Hypecrews",
        "description": "Learn about Hypecrews Software Private Limited, a passionate digital agency team from Golaghat, Assam, India delivering innovative digital solutions.",
        "url": "https://hypecrews.com/about",
        "mainEntity": {
            "@type": "Organization",
            "name": "Hypecrews",
            "legalName": "Hypecrews Software Private Limited",
            "url": "https://hypecrews.com",
            "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png",
            "foundingDate": "2022",
            "founders": [
                {
                    "@type": "Person",
                    "name": "Dheeraj Jyoti Saikia",
                    "jobTitle": "CEO & Founder"
                },
                {
                    "@type": "Person",
                    "name": "Jitu Moni Hazarika",
                    "jobTitle": "Co Founder"
                }
            ],
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Golaghat",
                "addressRegion": "Assam",
                "addressCountry": "IN"
            },
            "numberOfEmployees": {
                "@type": "QuantitativeValue",
                "minValue": 10
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
            transform: translateY(-8px);
        }

        .icon-box {
            position: relative;
            z-index: 1;
            overflow: hidden;
        }
        
        .icon-box::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(99,102,241,0.2) 0%, rgba(139,92,246,0.2) 100%);
            z-index: -1;
            transition: opacity 0.5s ease;
            opacity: 0.5;
        }

        .glass-card:hover .icon-box::before {
            opacity: 1;
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
        
        .timeline-line {
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(to bottom, transparent, rgba(99, 102, 241, 0.3), transparent);
            transform: translateX(-50%);
        }
        
        @media (max-width: 768px) {
            .timeline-line {
                left: 20px;
                transform: none;
            }
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white antialiased selection:bg-primary selection:text-white">
    <?php include 'components/nav.php'; ?>

    <!-- About Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <!-- Elegant subtle animated background -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[120px] animate-[pulse_8s_infinite_alternate]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-900/20 rounded-full blur-[120px] animate-[pulse_10s_infinite_alternate-reverse]"></div>
        </div>
        
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10 reveal-up">
            <div class="inline-flex items-center justify-center mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-primary mr-2 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wider uppercase text-gray-300">Welcome to Hypecrews</span>
            </div>
            
            <h1 class="font-heading text-5xl md:text-7xl font-bold mb-8 tracking-tight leading-tight">
                Transforming Ideas Into <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-primary">Digital Reality</span>
            </h1>
            
            <p class="text-xl max-w-3xl mx-auto text-slate-400 mb-12 font-light leading-relaxed">
                We're a passionate team of digital innovators, creators, and strategists dedicated to helping businesses thrive in the ever-evolving online landscape.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#story" class="px-8 py-4 bg-white text-dark font-semibold rounded-full transition-all duration-300 hover:bg-gray-200 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                    Discover Our Story
                </a>
                <a href="services.php" class="px-8 py-4 bg-transparent border border-white/20 text-white font-semibold rounded-full transition-all duration-300 hover:bg-white/10">
                    Explore Services
                </a>
            </div>
        </div>
    </section>

    <!-- Company Story & Philosophy -->
    <section class="py-24 relative z-10" id="story">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
                <div class="reveal-up relative">
                    <div class="relative rounded-3xl overflow-hidden border border-white/10 shadow-[0_0_40px_rgba(99,102,241,0.15)] group">
                        <div class="absolute inset-0 bg-gradient-to-t from-dark to-transparent z-10 opacity-60"></div>
                        <img src="graphics/516as4d8a16s5d64as.jpg" alt="Our Team" class="w-full h-auto object-cover transition-transform duration-700 group-hover:scale-105">
                        <div class="absolute bottom-8 left-8 z-20">
                            <h3 class="font-heading text-3xl font-bold text-white mb-2">Our Journey</h3>
                            <p class="text-indigo-300 font-light tracking-wide">From Local Roots to National Impact</p>
                        </div>
                    </div>
                    <div class="absolute -z-10 -bottom-10 -right-10 w-64 h-64 bg-primary/20 rounded-full blur-[80px]"></div>
                </div>
                
                <div class="reveal-up" style="transition-delay: 200ms;">
                    <h2 class="font-heading text-3xl md:text-4xl font-bold mb-6 leading-tight">From <span class="text-yellow-300">Guwahati</span> to Pan-India:<br> Your Complete <span class="text-primary">Digital Partner</span></h2>
                    <p class="text-slate-400 mb-6 text-lg font-light leading-relaxed">
                        Established in 2022 in the vibrant city of Guwahati, Assam, we have quickly evolved into a comprehensive digital solutions provider serving clients across the entire nation. We believe in bridging the gap between creativity, technology, and security.
                    </p>
                    <p class="text-slate-400 mb-10 text-lg font-light leading-relaxed">
                        Whether you are an artist looking for a spotlight, a business seeking a digital footprint, or a brand needing protection, our expert team is dedicated to delivering excellence with a personal touch.
                    </p>
                    
                    <div class="flex flex-wrap gap-4">
                        <div class="flex items-center glass-card px-6 py-4 rounded-2xl w-full sm:w-auto">
                            <i class="fas fa-project-diagram text-primary text-2xl mr-4"></i>
                            <div>
                                <p class="text-2xl font-bold text-white">1000+</p>
                                <p class="text-slate-400 text-sm">Projects Completed</p>
                            </div>
                        </div>
                        <div class="flex items-center glass-card px-6 py-4 rounded-2xl w-full sm:w-auto">
                            <i class="fas fa-users text-secondary text-2xl mr-4"></i>
                            <div>
                                <p class="text-2xl font-bold text-white">700+</p>
                                <p class="text-slate-400 text-sm">Happy Clients</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- What We Do Matrix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-24">
                <div class="glass-card p-10 rounded-3xl reveal-up">
                    <div class="flex items-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center mr-6 border border-white/5">
                            <i class="fas fa-layer-group text-indigo-400 text-2xl"></i>
                        </div>
                        <h3 class="font-heading text-2xl font-bold">What We Do</h3>
                    </div>
                    <p class="text-slate-400 mb-8 font-light">We operate at the intersection of entertainment and technology. Our 360-degree approach includes:</p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-indigo-400 mt-1 mr-4"></i>
                            <p class="text-gray-300 font-light"><strong class="text-white">Digital Growth:</strong> Expert Digital Marketing & Social Media</p>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-indigo-400 mt-1 mr-4"></i>
                            <p class="text-gray-300 font-light"><strong class="text-white">Creative Services:</strong> Music, Video Production & Artist PR</p>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-arrow-right text-indigo-400 mt-1 mr-4"></i>
                            <p class="text-gray-300 font-light"><strong class="text-white">Development:</strong> Custom Web & App Development</p>
                        </li>
                    </ul>
                </div>

                <div class="glass-card p-10 rounded-3xl reveal-up" style="transition-delay: 200ms;">
                    <div class="flex items-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-yellow-500/10 flex items-center justify-center mr-6 border border-white/5">
                            <i class="fas fa-shield-alt text-yellow-400 text-2xl"></i>
                        </div>
                        <h3 class="font-heading text-2xl font-bold">Security & Protection</h3>
                    </div>
                    <p class="text-slate-400 mb-8 font-light">In the digital age, your assets are vulnerable. We stand apart by offering specialized security services:</p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <i class="fas fa-check text-yellow-400 mt-1 mr-4"></i>
                            <p class="text-gray-300 font-light"><strong class="text-white">Content Protection:</strong> Robust Anti-Piracy & Copyright Takedowns</p>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-yellow-400 mt-1 mr-4"></i>
                            <p class="text-gray-300 font-light"><strong class="text-white">Account Recovery:</strong> Restore compromised digital identities</p>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Identity Banner -->
            <div class="glass-card p-10 md:p-16 rounded-3xl relative overflow-hidden text-center reveal-up border border-primary/20">
                <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full -translate-y-32 translate-x-32 blur-[40px]"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-secondary/10 rounded-full translate-y-32 -translate-x-32 blur-[40px]"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white/5 rounded-2xl mb-8 border border-white/10 shadow-inner">
                        <i class="fas fa-crown text-yellow-300 text-3xl drop-shadow-[0_0_10px_rgba(253,224,71,0.5)]"></i>
                    </div>
                    <p class="text-3xl md:text-5xl font-heading font-bold mb-4 tracking-tight text-white">We are Hypecrews Software Private Limited</p>
                    <p class="text-indigo-300 mb-10 text-xl font-mono tracking-widest">CIN: U73100AS2026PTC029838</p>
                    
                    <div class="flex flex-col sm:flex-row justify-center gap-6">
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 px-8 py-4 rounded-xl flex items-center shadow-lg transition-transform hover:-translate-y-2">
                            <i class="fas fa-paint-brush text-indigo-400 text-xl mr-3"></i>
                            <span class="font-bold text-white tracking-wide">We Create</span>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 px-8 py-4 rounded-xl flex items-center shadow-lg transition-transform hover:-translate-y-2" style="transition-delay: 100ms;">
                            <i class="fas fa-code text-yellow-300 text-xl mr-3"></i>
                            <span class="font-bold text-white tracking-wide">We Develop</span>
                        </div>
                        <div class="bg-white/5 backdrop-blur-md border border-white/10 px-8 py-4 rounded-xl flex items-center shadow-lg transition-transform hover:-translate-y-2" style="transition-delay: 200ms;">
                            <i class="fas fa-shield-alt text-emerald-400 text-xl mr-3"></i>
                            <span class="font-bold text-white tracking-wide">We Protect</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-24 relative z-10 border-t border-white/5">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">Our <span class="text-gray-400">Direction</span></h2>
                <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="glass-card p-10 md:p-12 rounded-3xl reveal-up group">
                    <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center mb-8 border border-white/5 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-bullseye text-primary text-3xl"></i>
                    </div>
                    <h3 class="font-heading text-3xl font-bold mb-6">The Mission</h3>
                    <p class="text-slate-400 font-light leading-relaxed mb-6">
                        To empower businesses of all sizes with cutting-edge digital strategies that drive growth, enhance customer engagement, and deliver measurable results in an ever-evolving digital landscape.
                    </p>
                    <p class="text-slate-400 font-light leading-relaxed">
                        We believe in building long-term partnerships based on trust, transparency, and exceptional service that exceeds expectations.
                    </p>
                </div>
                
                <div class="glass-card p-10 md:p-12 rounded-3xl reveal-up group" style="transition-delay: 200ms;">
                    <div class="w-16 h-16 rounded-2xl bg-secondary/10 flex items-center justify-center mb-8 border border-white/5 group-hover:scale-110 transition-transform duration-500">
                        <i class="fas fa-eye text-secondary text-3xl"></i>
                    </div>
                    <h3 class="font-heading text-3xl font-bold mb-6">The Vision</h3>
                    <p class="text-slate-400 font-light leading-relaxed mb-6">
                        To be the leading digital agency that transforms businesses through innovation, creativity, and technology, setting new standards for excellence in the industry.
                    </p>
                    <p class="text-slate-400 font-light leading-relaxed">
                        We envision a future where every business, regardless of size, has access to world-class digital solutions that enable them to compete globally.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-24 relative z-10 border-t border-white/5 bg-[#0a0f18]">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">Core <span class="text-gray-400">Values</span></h2>
                <p class="text-slate-400 max-w-2xl mx-auto font-light">The foundational principles that guide every decision and action at Hypecrews.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Value Cards -->
                <?php
                $values = [
                    ['icon' => 'fa-lightbulb', 'color' => 'text-yellow-400', 'bg' => 'bg-yellow-500/10', 'title' => 'Innovation', 'desc' => 'We constantly explore new ideas and technologies to deliver groundbreaking solutions.'],
                    ['icon' => 'fa-handshake', 'color' => 'text-blue-400', 'bg' => 'bg-blue-500/10', 'title' => 'Integrity', 'desc' => 'We conduct business with honesty and transparency, building trust.'],
                    ['icon' => 'fa-medal', 'color' => 'text-amber-400', 'bg' => 'bg-amber-500/10', 'title' => 'Excellence', 'desc' => 'We strive for perfection in everything we do, exceeding expectations.'],
                    ['icon' => 'fa-users', 'color' => 'text-indigo-400', 'bg' => 'bg-indigo-500/10', 'title' => 'Collaboration', 'desc' => 'We believe in the power of teamwork to achieve extraordinary results.'],
                    ['icon' => 'fa-heart', 'color' => 'text-rose-400', 'bg' => 'bg-rose-500/10', 'title' => 'Passion', 'desc' => 'We are driven by a genuine love for making a positive impact.'],
                    ['icon' => 'fa-chart-line', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-500/10', 'title' => 'Results', 'desc' => 'We focus on delivering measurable outcomes for our clients.']
                ];
                $delay = 0;
                foreach ($values as $value) {
                    echo '
                    <div class="glass-card p-8 rounded-3xl text-center reveal-up group" style="transition-delay: '.$delay.'ms;">
                        <div class="w-16 h-16 rounded-2xl '.$value['bg'].' flex items-center justify-center mx-auto mb-6 border border-white/5 transition-transform duration-500 group-hover:-translate-y-2 group-hover:shadow-[0_10px_20px_rgba(0,0,0,0.3)]">
                            <i class="fas '.$value['icon'].' '.$value['color'].' text-2xl"></i>
                        </div>
                        <h3 class="font-heading text-xl font-bold mb-4">'.$value['title'].'</h3>
                        <p class="text-slate-400 font-light text-sm">'.$value['desc'].'</p>
                    </div>';
                    $delay += 100;
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-24 relative z-10 border-t border-white/5">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">The <span class="text-gray-400">Leadership</span></h2>
                <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 max-w-4xl mx-auto">
                <!-- Dheeraj Jyoti Saikia - CEO & Founder -->
                <div class="glass-card rounded-3xl p-6 text-center reveal-up group">
                    <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-6 border-4 border-white/10 shadow-2xl group-hover:border-primary/50 transition-colors duration-500">
                        <img src="graphics/our-team/co-founder.png" alt="Dheeraj Jyoti Saikia" class="w-full h-full object-cover transition duration-700 group-hover:scale-110 grayscale group-hover:grayscale-0">
                    </div>
                    <h3 class="font-heading text-2xl font-bold mb-1">Dheeraj Jyoti Saikia</h3>
                    <p class="text-primary font-semibold mb-4 tracking-wider text-sm uppercase">CEO & Founder</p>
                    <p class="text-slate-400 font-light mb-6">Visionary leader driving innovation and growth</p>
                    <div class="flex justify-center space-x-4">
                        <a href="https://www.linkedin.com/in/dheerajjyotisaikia/" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-300 hover:bg-blue-600 hover:text-white transition-all duration-300 border border-white/10" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-300 hover:bg-sky-500 hover:text-white transition-all duration-300 border border-white/10">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Jitu Moni Hazarika - Co Founder -->
                <div class="glass-card rounded-3xl p-6 text-center reveal-up group" style="transition-delay: 200ms;">
                    <div class="relative w-48 h-48 mx-auto rounded-full overflow-hidden mb-6 border-4 border-white/10 shadow-2xl group-hover:border-secondary/50 transition-colors duration-500">
                        <img src="graphics/our-team/co-founder.png" alt="Jitu Moni Hazarika" class="w-full h-full object-cover transition duration-700 group-hover:scale-110 grayscale group-hover:grayscale-0">
                    </div>
                    <h3 class="font-heading text-2xl font-bold mb-1">Jitu Moni Hazarika</h3>
                    <p class="text-secondary font-semibold mb-4 tracking-wider text-sm uppercase">Co Founder</p>
                    <p class="text-slate-400 font-light mb-6">Strategic partner in business development and operations</p>
                    <div class="flex justify-center space-x-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-300 hover:bg-blue-600 hover:text-white transition-all duration-300 border border-white/10">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-300 hover:bg-sky-500 hover:text-white transition-all duration-300 border border-white/10">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formal CTA Section -->
    <section class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-indigo-900/10 pointer-events-none"></div>
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10 reveal-up">
            <h2 class="font-heading text-3xl md:text-5xl font-bold mb-6 tracking-tight">Ready to Work With Us?</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto text-slate-400 font-light">Join the many businesses that have transformed their digital presence with Hypecrews.</p>
            <a href="index.php#contact" class="inline-flex items-center justify-center px-10 py-4 bg-white text-dark font-semibold rounded-full transition-all duration-300 hover:bg-gray-200 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                Get In Touch
            </a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    
    <!-- Intersection Observer for smooth reveal animations -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        // Optional: stop observing once animated
                        // observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal-up');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>