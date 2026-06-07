<?php
session_start();
$pageTitle = "Hypecrews - Peak Digital Excellence";
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <!-- Google Search Console Verification -->
    <meta name="google-site-verification" content="_JU3E8DA157M7_ibIQi_qx6wtqnbnuei76ShghWBGJ4" />
    <!-- SEO Meta Tags -->
    <meta name="description" content="Hypecrews is a leading digital marketing agency in India offering web development, copyright protection, social media management, and creative digital solutions.">
    <meta name="keywords" content="digital marketing agency india, web development, copyright protection, social media management, hypecrews">
    <link rel="canonical" href="https://hypecrews.com/">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Hypecrews - Peak Digital Excellence">
    <meta property="og:description" content="Elevate your brand with award-winning design, flawless development, and strategic marketing that dominates the digital landscape.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Hypecrews - Peak Digital Excellence">
    <meta name="twitter:description" content="Elevate your brand with award-winning design, flawless development, and strategic marketing that dominates the digital landscape.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <!-- JSON-LD Structured Data: Organization -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Hypecrews",
        "legalName": "Hypecrews Software Private Limited",
        "url": "https://hypecrews.com",
        "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png",
        "description": "Leading digital marketing agency in India offering web development, copyright protection, social media management, and creative digital solutions.",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Golaghat",
            "addressRegion": "Assam",
            "addressCountry": "IN"
        },
        "sameAs": [],
        "serviceArea": {
            "@type": "Country",
            "name": "India"
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4',
                        dark: '#0B0F19',
                        surface: '#151b2b'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    },
                    animation: {
                        'blob': 'blob 7s infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'float-delayed': 'float 6s ease-in-out 3s infinite',
                        'spin-slow': 'spin 15s linear infinite',
                        'pulse-glow': 'pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'marquee': 'marquee 25s linear infinite',
                    },
                    keyframes: {
                        blob: {
                            '0%': { transform: 'translate(0px, 0px) scale(1)' },
                            '33%': { transform: 'translate(30px, -50px) scale(1.1)' },
                            '66%': { transform: 'translate(-20px, 20px) scale(0.9)' },
                            '100%': { transform: 'translate(0px, 0px) scale(1)' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-20px)' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { opacity: '1', transform: 'scale(1)' },
                            '50%': { opacity: '.7', transform: 'scale(1.05)' },
                        },
                        marquee: {
                            '0%': { transform: 'translateX(0%)' },
                            '100%': { transform: 'translateX(-100%)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B0F19;
            color: #f8fafc;
            overflow-x: hidden;
        }

        /* Ambient Background */
        .ambient-bg {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: -1;
            background: 
                radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.08), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.08), transparent 25%);
            pointer-events: none;
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(21, 27, 43, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        }

        .glass-card:hover {
            background: rgba(30, 41, 59, 0.7);
            border: 1px solid rgba(99, 102, 241, 0.4);
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        }

        /* Text Gradients */
        .text-gradient {
            background: linear-gradient(to right, #818cf8, #c084fc, #2dd4bf);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Scroll Reveal Animations */
        .reveal-up { opacity: 0; transform: translateY(50px); transition: all 1s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-down { opacity: 0; transform: translateY(-50px); transition: all 1s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-left { opacity: 0; transform: translateX(-50px); transition: all 1s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-right { opacity: 0; transform: translateX(50px); transition: all 1s cubic-bezier(0.5, 0, 0, 1); }
        .scale-in { opacity: 0; transform: scale(0.9); transition: all 1s cubic-bezier(0.5, 0, 0, 1); }
        
        .reveal-up.active, .reveal-down.active, .reveal-left.active, .reveal-right.active, .scale-in.active {
            opacity: 1; transform: translate(0) scale(1);
        }

        /* Staggered delays */
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }
        .delay-500 { transition-delay: 500ms; }

        /* Custom Buttons */
        .btn-glow {
            position: relative;
        }
        .btn-glow::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px; right: -2px; bottom: -2px;
            background: linear-gradient(45deg, #6366f1, #8b5cf6, #06b6d4, #6366f1);
            z-index: -1;
            border-radius: inherit;
            background-size: 400%;
            animation: glow-spin 20s linear infinite;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        .btn-glow:hover::before { opacity: 1; }
        
        @keyframes glow-spin {
            0% { background-position: 0 0; }
            50% { background-position: 400% 0; }
            100% { background-position: 0 0; }
        }

        /* Typing cursor */
        .typing-cursor::after {
            content: '|';
            animation: blink 1s step-start infinite;
            color: #6366f1;
        }
        @keyframes blink { 50% { opacity: 0; } }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>

    <?php include 'components/nav.php'; ?>

    <!-- HERO SECTION: The visual masterpiece -->
    <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
        <!-- Animated Blob Backgrounds -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob"></div>
        <div class="absolute top-1/3 right-1/4 w-96 h-96 bg-secondary/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-accent/20 rounded-full mix-blend-screen filter blur-[100px] animate-blob animation-delay-4000"></div>

        <div class="container mx-auto px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center gap-16">
            <!-- Left: Typography & CTA -->
            <div class="w-full lg:w-1/2 text-center lg:text-left">
                <div class="reveal-up">
                    <div class="inline-flex items-center justify-center lg:justify-start px-4 py-2 rounded-full border border-white/10 bg-white/5 backdrop-blur-md mb-8">
                        <span class="w-2 h-2 rounded-full bg-primary animate-pulse mr-3 shadow-[0_0_10px_#6366f1]"></span>
                        <span class="text-sm font-medium tracking-widest uppercase text-gray-300">The Future of Digital</span>
                    </div>
                </div>

                <h1 class="font-heading text-5xl md:text-7xl lg:text-[80px] font-black leading-[1.1] mb-6 tracking-tight reveal-up delay-100">
                    We Build <br/>
                    <span class="text-gradient">Experiences</span><br/>
                    <span id="typing-text" class="text-white typing-cursor"></span>
                </h1>

                <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto lg:mx-0 font-light leading-relaxed reveal-up delay-200">
                    Elevate your brand with award-winning design, flawless development, and strategic marketing that dominates the digital landscape.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-5 reveal-up delay-300">
                    <a href="#services" class="btn-glow px-8 py-4 bg-white text-dark rounded-full font-bold tracking-wide transition-transform hover:scale-105 w-full sm:w-auto text-center shadow-[0_0_30px_rgba(255,255,255,0.2)]">
                        Explore Offerings
                    </a>
                    <a href="contact.php" class="px-8 py-4 rounded-full border border-white/20 text-white font-semibold hover:bg-white/10 transition-all w-full sm:w-auto text-center flex items-center justify-center group">
                        Let's Talk
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Floating Stats -->
                <div class="grid grid-cols-3 gap-4 mt-16 reveal-up delay-400 border-t border-white/10 pt-8 max-w-lg mx-auto lg:mx-0">
                    <div>
                        <div class="text-3xl font-heading font-bold text-white mb-1"><span class="counter" data-target="1000">0</span>+</div>
                        <div class="text-sm text-gray-500 uppercase tracking-wider">Projects</div>
                    </div>
                    <div>
                        <div class="text-3xl font-heading font-bold text-white mb-1"><span class="counter" data-target="750">0</span>+</div>
                        <div class="text-sm text-gray-500 uppercase tracking-wider">Clients</div>
                    </div>
                    <div>
                        <div class="text-3xl font-heading font-bold text-white mb-1"><span class="counter" data-target="98">0</span>%</div>
                        <div class="text-sm text-gray-500 uppercase tracking-wider">Success</div>
                    </div>
                </div>
            </div>

            <!-- Right: 3D Visual/Abstract Element -->
            <div class="w-full lg:w-1/2 relative h-[500px] lg:h-[700px] reveal-left delay-300 hidden md:block">
                <!-- Main Floating Card -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-96 glass-panel rounded-3xl animate-float z-20 border border-white/20 flex flex-col justify-between p-6 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-transparent"></div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-primary to-secondary flex items-center justify-center shadow-lg">
                            <i class="fas fa-bolt text-white"></i>
                        </div>
                        <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-semibold backdrop-blur-md">Live Status</span>
                    </div>
                    <div class="relative z-10">
                        <div class="h-2 w-full bg-white/10 rounded-full overflow-hidden mb-4">
                            <div class="h-full bg-gradient-to-r from-primary to-accent w-3/4 animate-pulse"></div>
                        </div>
                        <h3 class="text-xl font-heading font-bold text-white">Optimization Active</h3>
                        <p class="text-sm text-gray-400 mt-1">System running at peak performance.</p>
                    </div>
                </div>

                <!-- Secondary Floating Card -->
                <div class="absolute top-1/4 right-10 w-48 h-48 glass-panel rounded-2xl animate-float-delayed z-30 p-5 flex flex-col items-center justify-center border border-secondary/30 shadow-[0_0_30px_rgba(139,92,246,0.15)]">
                    <div class="w-16 h-16 rounded-full border-4 border-secondary/50 border-t-secondary animate-spin-slow mb-3 flex items-center justify-center">
                        <i class="fas fa-chart-line text-secondary"></i>
                    </div>
                    <span class="font-bold text-lg">+342%</span>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Growth</span>
                </div>

                <!-- Ring Decoration -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] border border-white/5 rounded-full animate-[spin_30s_linear_infinite] z-10">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-4 h-4 bg-primary rounded-full shadow-[0_0_15px_#6366f1]"></div>
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-3 h-3 bg-accent rounded-full shadow-[0_0_15px_#06b6d4]"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Infinite Client Marquee -->
    <section class="py-10 border-y border-white/5 bg-black/20 backdrop-blur-md relative z-10 overflow-hidden">
        <div class="flex overflow-hidden">
            <div class="flex animate-marquee whitespace-nowrap items-center opacity-50">
                <!-- Logos -->
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-gem text-primary"></i> Premium Brands</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-bolt text-accent"></i> Fast Execution</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-shield-alt text-secondary"></i> Secure Assets</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-globe text-primary"></i> Global Reach</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-chart-pie text-accent"></i> Data Driven</span>
                <!-- Repeat for infinite loop -->
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-gem text-primary"></i> Premium Brands</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-bolt text-accent"></i> Fast Execution</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-shield-alt text-secondary"></i> Secure Assets</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-globe text-primary"></i> Global Reach</span>
                <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-chart-pie text-accent"></i> Data Driven</span>
            </div>
        </div>
    </section>

    <!-- Premium Services Grid -->
    <section id="services" class="py-32 relative z-10">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-6xl font-bold mb-6">Our <span class="text-gradient">Expertise</span></h2>
                <p class="text-lg text-gray-400 font-light">We deliver state-of-the-art solutions across a diverse range of digital disciplines to ensure your absolute market dominance.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service 1 -->
                <div class="glass-card rounded-3xl p-8 reveal-up group">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center mb-6 border border-indigo-500/20 group-hover:bg-indigo-500/20 transition-colors">
                        <i class="fas fa-copyright text-indigo-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-heading font-bold mb-3">Copyright Protection</h3>
                    <p class="text-gray-400 mb-6 font-light leading-relaxed">Advanced tracking and aggressive removal of unauthorized content to protect your intellectual property.</p>
                    <a href="services.php#copyright" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-indigo-400 group-hover:text-indigo-300">
                        Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Service 2 -->
                <div class="glass-card rounded-3xl p-8 reveal-up delay-100 group">
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center mb-6 border border-purple-500/20 group-hover:bg-purple-500/20 transition-colors">
                        <i class="fas fa-hashtag text-purple-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-heading font-bold mb-3">Social Mastery</h3>
                    <p class="text-gray-400 mb-6 font-light leading-relaxed">Strategic social media management that builds viral momentum and deeply engages your core audience.</p>
                    <a href="services.php#social" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-purple-400 group-hover:text-purple-300">
                        Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Service 3 -->
                <div class="glass-card rounded-3xl p-8 reveal-up delay-200 group">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 flex items-center justify-center mb-6 border border-cyan-500/20 group-hover:bg-cyan-500/20 transition-colors">
                        <i class="fas fa-laptop-code text-cyan-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-heading font-bold mb-3">Web Architecture</h3>
                    <p class="text-gray-400 mb-6 font-light leading-relaxed">High-performance, bespoke web applications and websites engineered with the latest frontend stacks.</p>
                    <a href="services.php#web" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-cyan-400 group-hover:text-cyan-300">
                        Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Service 4 -->
                <div class="glass-card rounded-3xl p-8 reveal-up group">
                    <div class="w-14 h-14 rounded-2xl bg-rose-500/10 flex items-center justify-center mb-6 border border-rose-500/20 group-hover:bg-rose-500/20 transition-colors">
                        <i class="fas fa-video text-rose-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-heading font-bold mb-3">Cinematic Video</h3>
                    <p class="text-gray-400 mb-6 font-light leading-relaxed">Stunning visual productions from concept to final cut, designed to captivate and convert.</p>
                    <a href="services.php#video" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-rose-400 group-hover:text-rose-300">
                        Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Service 5 -->
                <div class="glass-card rounded-3xl p-8 reveal-up delay-100 group">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-6 border border-emerald-500/20 group-hover:bg-emerald-500/20 transition-colors">
                        <i class="fas fa-shield-alt text-emerald-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-heading font-bold mb-3">Digital Recovery</h3>
                    <p class="text-gray-400 mb-6 font-light leading-relaxed">Expert recovery protocols for hacked accounts and aggressive reputation management solutions.</p>
                    <a href="services.php#recovery" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-emerald-400 group-hover:text-emerald-300">
                        Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Service 6 -->
                <div class="glass-card rounded-3xl p-8 reveal-up delay-200 group bg-gradient-to-br from-surface to-primary/5">
                    <div class="flex flex-col items-center text-center h-full justify-center">
                        <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center mb-4 shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                            <i class="fas fa-th-large text-dark text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-heading font-bold mb-2">View All 13+ Services</h3>
                        <p class="text-sm text-gray-400 mb-6">Marketing, PR, Distribution & More.</p>
                        <a href="services.php" class="px-6 py-3 rounded-full border border-white/20 hover:bg-white hover:text-dark transition-all font-semibold w-full">
                            Complete Catalog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Advanced Feature Split Section -->
    <section class="py-24 relative z-10 overflow-hidden bg-black/40 border-y border-white/5">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="w-full lg:w-1/2 reveal-left">
                    <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6 leading-tight">Data-Driven.<br/>Results-Oriented.</h2>
                    <p class="text-gray-400 font-light text-lg mb-8">We don't just guess. Every campaign, line of code, and design choice is backed by analytics and deep market research to ensure maximum ROI.</p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center shrink-0 mr-4 border border-accent/20">
                                <i class="fas fa-crosshairs text-accent"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-1">Precision Targeting</h4>
                                <p class="text-sm text-gray-500">Reaching exactly who matters most to your business.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 mr-4 border border-primary/20">
                                <i class="fas fa-tachometer-alt text-primary"></i>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold mb-1">High Velocity</h4>
                                <p class="text-sm text-gray-500">Rapid deployment and scaling of digital assets.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-1/2 relative reveal-right">
                    <!-- Dashboard Mockup Element -->
                    <div class="glass-panel rounded-2xl p-4 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] transform lg:-rotate-2 hover:rotate-0 transition-transform duration-700">
                        <div class="flex items-center gap-2 mb-4 px-2 border-b border-white/5 pb-4">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                <div class="text-xs text-gray-400 mb-1 uppercase tracking-wider">Traffic</div>
                                <div class="text-2xl font-bold text-accent">+124.5%</div>
                            </div>
                            <div class="bg-white/5 rounded-xl p-4 border border-white/5">
                                <div class="text-xs text-gray-400 mb-1 uppercase tracking-wider">Conversion</div>
                                <div class="text-2xl font-bold text-primary">4.8%</div>
                            </div>
                        </div>
                        <div class="h-32 bg-gradient-to-t from-primary/20 to-transparent rounded-xl border-b-2 border-primary relative flex items-end px-2">
                            <!-- Faux chart bars -->
                            <div class="w-1/6 h-1/4 bg-white/20 mx-1 rounded-t-sm"></div>
                            <div class="w-1/6 h-2/4 bg-white/20 mx-1 rounded-t-sm"></div>
                            <div class="w-1/6 h-1/3 bg-white/20 mx-1 rounded-t-sm"></div>
                            <div class="w-1/6 h-3/4 bg-primary/50 mx-1 rounded-t-sm"></div>
                            <div class="w-1/6 h-full bg-primary mx-1 rounded-t-sm shadow-[0_0_15px_#6366f1]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Formal Call to Action -->
    <section class="py-32 relative z-10 text-center px-4">
        <div class="glass-panel max-w-4xl mx-auto rounded-[3rem] p-12 md:p-20 relative overflow-hidden border border-white/10 reveal-up">
            <div class="absolute inset-0 bg-gradient-to-b from-primary/10 to-transparent pointer-events-none"></div>
            
            <h2 class="font-heading text-4xl md:text-6xl font-black mb-6">Ready to <span class="text-gradient">Dominate?</span></h2>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto font-light">Join hundreds of successful brands that have transformed their digital presence with our elite team.</p>
            
            <a href="contact.php" class="inline-flex items-center justify-center px-10 py-5 bg-white text-dark text-lg font-bold rounded-full transition-all duration-300 hover:scale-105 shadow-[0_0_40px_rgba(255,255,255,0.3)]">
                Start Your Project <i class="fas fa-rocket ml-3"></i>
            </a>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <script>
        // Typing Effect Logic
        const words = ["that Convert.", "that Inspire.", "that Dominate."];
        let i = 0;
        let timer;

        function typingEffect() {
            let word = words[i].split("");
            var loopTyping = function() {
                if (word.length > 0) {
                    document.getElementById('typing-text').innerHTML += word.shift();
                } else {
                    setTimeout(deletingEffect, 2000);
                    return false;
                };
                timer = setTimeout(loopTyping, 100);
            };
            loopTyping();
        }

        function deletingEffect() {
            let word = words[i].split("");
            var loopDeleting = function() {
                if (word.length > 0) {
                    word.pop();
                    document.getElementById('typing-text').innerHTML = word.join("");
                } else {
                    if (words.length > (i + 1)) {
                        i++;
                    } else {
                        i = 0;
                    };
                    setTimeout(typingEffect, 500);
                    return false;
                };
                timer = setTimeout(loopDeleting, 50);
            };
            loopDeleting();
        }

        // Counter Animation Logic
        function animateCounters() {
            const counters = document.querySelectorAll('.counter');
            counters.forEach(counter => {
                counter.innerText = '0';
                const updateCounter = () => {
                    const target = +counter.getAttribute('data-target');
                    const c = +counter.innerText;
                    const increment = target / 50; // Speed adjustment

                    if (c < target) {
                        counter.innerText = `${Math.ceil(c + increment)}`;
                        setTimeout(updateCounter, 30);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCounter();
            });
        }

        // Intersection Observer for Reveal Animations & Counters
        document.addEventListener('DOMContentLoaded', () => {
            typingEffect();

            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.1
            };

            let countersAnimated = false;

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                        
                        // Check if it's the stats container to run counters
                        if(entry.target.classList.contains('delay-400') && entry.target.querySelector('.counter') && !countersAnimated) {
                            animateCounters();
                            countersAnimated = true;
                        }
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal-up, .reveal-down, .reveal-left, .reveal-right, .scale-in');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
    <?php include 'components/guest_chat.php'; ?>
</body>
</html>