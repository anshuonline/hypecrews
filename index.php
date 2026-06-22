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
        
        /* Ultra Modern Animations */
        @keyframes shimmer { 100% { transform: translateX(100%); } }
        /* Spotlight Effect */
        @keyframes spotlight { 
            0% { opacity: 0; transform: translate(-72%, -62%) scale(0.5); }
            100% { opacity: 1; transform: translate(-50%,-40%) scale(1); }
        }
        .spotlight {
            position: absolute;
            top: 0; left: 0;
            width: 138%; height: 138%;
            background: radial-gradient(ellipse 50% 50% at 50% 50%, rgba(255,255,255,0.15) 0%, transparent 100%);
            animation: spotlight 2s ease forwards;
            pointer-events: none;
            z-index: 10;
        }

        /* 3D Animated Grid Floor */
        .grid-3d-floor {
            position: absolute;
            bottom: -50%; left: -50%; width: 200%; height: 100%;
            background-image: 
                linear-gradient(to right, rgba(99, 102, 241, 0.2) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(99, 102, 241, 0.2) 1px, transparent 1px);
            background-size: 50px 50px;
            transform: perspective(500px) rotateX(60deg) translateY(100px) translateZ(-200px);
            animation: grid-move 10s linear infinite;
            mask-image: linear-gradient(to top, black 40%, transparent 100%);
            -webkit-mask-image: linear-gradient(to top, black 40%, transparent 100%);
            z-index: 0;
        }
        @keyframes grid-move { 0% { transform: perspective(500px) rotateX(60deg) translateY(0) translateZ(-200px); } 100% { transform: perspective(500px) rotateX(60deg) translateY(50px) translateZ(-200px); } }

        /* Double Marquee */
        @keyframes marquee-reverse { 0% { transform: translateX(-100%); } 100% { transform: translateX(0%); } }
        .animate-marquee-reverse { animation: marquee-reverse 25s linear infinite; }

        /* Lamp Effect */
        .lamp-glow-top {
            position: absolute;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 400px; height: 150px;
            background: rgba(99, 102, 241, 0.5);
            filter: blur(80px);
            border-radius: 50%;
        }
        .lamp-cone {
            position: absolute;
            top: 0; left: 50%; transform: translateX(-50%);
            width: 600px; height: 100%;
            background: conic-gradient(from 180deg at 50% -20%, transparent 120deg, rgba(99, 102, 241, 0.3) 180deg, transparent 240deg);
            filter: blur(20px);
            mask-image: linear-gradient(to bottom, black 0%, transparent 100%);
            -webkit-mask-image: linear-gradient(to bottom, black 0%, transparent 100%);
        }

        .blur-reveal { opacity: 0; filter: blur(15px); transform: translateY(30px); transition: all 1.2s cubic-bezier(0.25, 1, 0.5, 1); }
        .blur-reveal.active { opacity: 1; filter: blur(0); transform: translateY(0); }

        .bento-card {
            background: linear-gradient(145deg, rgba(30, 41, 59, 0.4) 0%, rgba(15, 23, 42, 0.6) 100%);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .bento-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(99, 102, 241, 0.15);
        }

        .bento-card::after {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(600px circle at var(--mouse-x, 0) var(--mouse-y, 0), rgba(255, 255, 255, 0.08), transparent 40%);
            z-index: 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .bento-card:hover::after { opacity: 1; }
        .bento-content { position: relative; z-index: 1; }

        .magic-btn-wrapper {
            position: relative;
            display: inline-flex;
            overflow: hidden;
            border-radius: 9999px;
            padding: 1px;
            transition: transform 0.3s;
        }
        .magic-btn-wrapper:hover {
            transform: scale(1.05);
        }
        .magic-border {
            position: absolute;
            inset: -1000%;
            animation: spin-slow 4s linear infinite;
            background: conic-gradient(from 90deg at 50% 50%, #E2CBFF 0%, #393BB2 50%, #E2CBFF 100%);
        }
        
        .glowing-sphere {
            width: 320px; height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.9), rgba(99,102,241,0.6) 40%, rgba(11,15,25,1) 80%);
            box-shadow: 
                0 0 80px rgba(99, 102, 241, 0.5),
                inset -20px -20px 60px rgba(0,0,0,0.9),
                inset 20px 20px 40px rgba(255,255,255,0.3);
            animation: float 6s ease-in-out infinite, auroraflow 30s linear infinite;
            position: relative;
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>

    <?php include 'components/nav.php'; ?>

    <!-- HERO SECTION: The visual masterpiece -->
    <section class="relative min-h-screen flex items-center justify-center pt-20 overflow-hidden">
        <!-- Spotlight & 3D Grid Background -->
        <div class="spotlight"></div>
        <div class="grid-3d-floor"></div>

        <div class="container mx-auto px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center gap-16">
            <!-- Left: Typography & CTA -->
            <div class="w-full lg:w-1/2 text-center lg:text-left">
                <div class="blur-reveal delay-100">
                    <a href="#" class="inline-flex items-center justify-center mb-8 px-4 py-1.5 text-sm font-medium transition-all rounded-full bg-white/5 border border-white/10 hover:bg-white/10 hover:border-white/20 group relative overflow-hidden backdrop-blur-md">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full animate-[shimmer_2s_infinite]"></div>
                        <span class="w-2 h-2 rounded-full bg-primary mr-2 animate-pulse shadow-[0_0_10px_#6366f1]"></span>
                        <span class="text-gray-300 tracking-wider uppercase text-xs group-hover:text-white transition-colors">The Future of Digital</span>
                    </a>
                </div>

                <h1 class="font-heading text-5xl md:text-7xl lg:text-[80px] font-black leading-[1.1] mb-6 tracking-tight blur-reveal delay-200">
                    We Build <br/>
                    <span class="text-gradient">Experiences</span><br/>
                    <span id="typing-text" class="text-white typing-cursor"></span>
                </h1>

                <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto lg:mx-0 font-light leading-relaxed blur-reveal delay-300">
                    Elevate your brand with award-winning design, flawless development, and strategic marketing that dominates the digital landscape.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-5 blur-reveal delay-400">
                    <a href="#services" class="magic-btn-wrapper">
                        <span class="magic-border"></span>
                        <span class="inline-flex h-full w-full items-center justify-center rounded-full bg-slate-950 px-8 py-4 text-sm font-bold text-white backdrop-blur-3xl transition-colors hover:bg-slate-900">
                            Explore Offerings
                        </span>
                    </a>
                    <a href="contact.php" class="px-8 py-4 rounded-full border border-white/20 text-white font-semibold hover:bg-white/10 transition-all w-full sm:w-auto text-center flex items-center justify-center group">
                        Let's Talk
                        <i class="fas fa-arrow-right ml-2 group-hover:translate-x-2 transition-transform"></i>
                    </a>
                </div>

                <!-- Floating Stats -->
                <div class="grid grid-cols-3 gap-4 mt-16 blur-reveal delay-500 border-t border-white/10 pt-8 max-w-lg mx-auto lg:mx-0">
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
            <div class="w-full lg:w-1/2 relative h-[500px] lg:h-[700px] blur-reveal delay-400 hidden md:flex items-center justify-center">
                <!-- Glowing 3D Sphere -->
                <div class="glowing-sphere z-20"></div>

                <!-- Orbital Rings -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[450px] h-[450px] border border-white/10 rounded-full animate-[spin_40s_linear_infinite] z-10">
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3 h-3 bg-primary rounded-full shadow-[0_0_15px_#6366f1]"></div>
                </div>
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] border border-white/5 rounded-full animate-[spin_50s_linear_infinite_reverse] z-10">
                    <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-4 h-4 bg-accent rounded-full shadow-[0_0_20px_#06b6d4]"></div>
                </div>
                
                <!-- Floating Glass Card -->
                <div class="absolute top-1/4 right-0 w-48 h-48 glass-panel rounded-2xl animate-float-delayed z-30 p-5 flex flex-col items-center justify-center border border-secondary/30 shadow-[0_0_30px_rgba(139,92,246,0.15)] backdrop-blur-xl">
                    <div class="w-16 h-16 rounded-full border-4 border-secondary/50 border-t-secondary animate-[spin_4s_linear_infinite] mb-3 flex items-center justify-center">
                        <i class="fas fa-bolt text-secondary"></i>
                    </div>
                    <span class="font-bold text-lg text-white">Peak</span>
                    <span class="text-xs text-gray-400 uppercase tracking-wider">Performance</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Dual 3D Marquee -->
    <section class="py-24 relative z-10 overflow-hidden bg-black/50 [mask-image:linear-gradient(to_bottom,transparent,black_20%,black_80%,transparent)]">
        <div class="flex flex-col gap-8 transform -rotate-3 scale-110 shadow-2xl">
            <!-- Row 1 Left -->
            <div class="flex overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
                <div class="flex animate-marquee whitespace-nowrap items-center opacity-60">
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-gem text-primary"></i> Premium Brands</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-bolt text-accent"></i> Fast Execution</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-shield-alt text-secondary"></i> Secure Assets</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-globe text-primary"></i> Global Reach</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-chart-pie text-accent"></i> Data Driven</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-gem text-primary"></i> Premium Brands</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-bolt text-accent"></i> Fast Execution</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-shield-alt text-secondary"></i> Secure Assets</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-globe text-primary"></i> Global Reach</span>
                    <span class="mx-12 text-2xl font-heading font-bold tracking-widest uppercase text-gray-400 flex items-center gap-3"><i class="fas fa-chart-pie text-accent"></i> Data Driven</span>
                </div>
            </div>
            <!-- Row 2 Right -->
            <div class="flex overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]">
                <div class="flex animate-marquee-reverse whitespace-nowrap items-center opacity-40 text-xl">
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Web Architecture</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Social Mastery</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Cinematic Video</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Digital Recovery</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Copyright Protection</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Web Architecture</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Social Mastery</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Cinematic Video</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Digital Recovery</span>
                    <span class="mx-12 font-heading font-bold tracking-widest uppercase text-gray-500 flex items-center gap-3">Copyright Protection</span>
                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="bento-services-container">
                <!-- Service 1 (Spans 2 columns) -->
                <div class="bento-card md:col-span-2 p-10 blur-reveal group">
                    <div class="bento-content h-full flex flex-col justify-center">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20 group-hover:bg-indigo-500/20 transition-colors">
                                <i class="fas fa-copyright text-indigo-400 text-2xl"></i>
                            </div>
                            <a href="services.php#copyright" class="w-10 h-10 rounded-full border border-white/10 flex items-center justify-center bg-white/5 hover:bg-white/20 transition-all">
                                <i class="fas fa-arrow-right text-white -rotate-45 group-hover:rotate-0 transition-transform"></i>
                            </a>
                        </div>
                        <h3 class="text-3xl font-heading font-bold mb-3">Copyright Protection</h3>
                        <p class="text-gray-400 font-light leading-relaxed max-w-lg">Advanced tracking and aggressive removal of unauthorized content to comprehensively protect your intellectual property worldwide.</p>
                    </div>
                </div>

                <!-- Service 2 -->
                <div class="bento-card p-8 blur-reveal delay-100 group">
                    <div class="bento-content">
                        <div class="w-14 h-14 rounded-2xl bg-purple-500/10 flex items-center justify-center mb-6 border border-purple-500/20 group-hover:bg-purple-500/20 transition-colors">
                            <i class="fas fa-hashtag text-purple-400 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-heading font-bold mb-3">Social Mastery</h3>
                        <p class="text-gray-400 mb-6 font-light leading-relaxed">Strategic social media management that builds viral momentum and engagement.</p>
                        <a href="services.php#social" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-purple-400 group-hover:text-purple-300">
                            Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Service 3 -->
                <div class="bento-card p-8 blur-reveal delay-200 group">
                    <div class="bento-content">
                        <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 flex items-center justify-center mb-6 border border-cyan-500/20 group-hover:bg-cyan-500/20 transition-colors">
                            <i class="fas fa-laptop-code text-cyan-400 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-heading font-bold mb-3">Web Architecture</h3>
                        <p class="text-gray-400 mb-6 font-light leading-relaxed">High-performance, bespoke web applications engineered with modern stacks.</p>
                        <a href="services.php#web" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-cyan-400 group-hover:text-cyan-300">
                            Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Service 4 -->
                <div class="bento-card p-8 blur-reveal delay-300 group">
                    <div class="bento-content">
                        <div class="w-14 h-14 rounded-2xl bg-rose-500/10 flex items-center justify-center mb-6 border border-rose-500/20 group-hover:bg-rose-500/20 transition-colors">
                            <i class="fas fa-video text-rose-400 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-heading font-bold mb-3">Cinematic Video</h3>
                        <p class="text-gray-400 mb-6 font-light leading-relaxed">Stunning visual productions from concept to final cut to convert audiences.</p>
                        <a href="services.php#video" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-rose-400 group-hover:text-rose-300">
                            Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>

                <!-- Service 5 -->
                <div class="bento-card p-8 blur-reveal delay-400 group">
                    <div class="bento-content">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 flex items-center justify-center mb-6 border border-emerald-500/20 group-hover:bg-emerald-500/20 transition-colors">
                            <i class="fas fa-shield-alt text-emerald-400 text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-heading font-bold mb-3">Digital Recovery</h3>
                        <p class="text-gray-400 mb-6 font-light leading-relaxed">Expert recovery protocols for hacked accounts and aggressive reputation management.</p>
                        <a href="services.php#recovery" class="inline-flex items-center text-sm font-bold uppercase tracking-widest text-emerald-400 group-hover:text-emerald-300">
                            Explore <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sticky Scroll Section -->
    <section class="relative z-10 bg-black/40 border-y border-white/5 py-24">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-16 relative items-start">
                <!-- Sticky Text Left -->
                <div class="w-full lg:w-1/2 lg:sticky lg:top-32 space-y-0 lg:space-y-32 py-10" id="sticky-scroll-text">
                    <div class="scroll-item transition-all duration-500 transform lg:translate-x-0 lg:opacity-100 bg-white/5 lg:bg-transparent p-8 lg:p-0 rounded-3xl border border-white/10 lg:border-transparent mb-6 lg:mb-0 relative overflow-hidden reveal-up">
                        <div class="lg:hidden absolute -right-6 -top-6 opacity-[0.05] text-[150px] pointer-events-none">
                           <i class="fas fa-copyright text-indigo-500"></i>
                        </div>
                        <h2 class="font-heading text-3xl md:text-5xl font-bold mb-4 lg:mb-6 leading-tight relative z-10">Copyright Protection.</h2>
                        <p class="text-gray-400 font-light text-base lg:text-lg relative z-10">Advanced tracking and aggressive removal of unauthorized content to comprehensively protect your intellectual property worldwide.</p>
                    </div>
                    <div class="scroll-item transition-all duration-500 transform lg:-translate-x-4 lg:opacity-30 bg-white/5 lg:bg-transparent p-8 lg:p-0 rounded-3xl border border-white/10 lg:border-transparent mb-6 lg:mb-0 relative overflow-hidden reveal-up">
                        <div class="lg:hidden absolute -right-6 -top-6 opacity-[0.05] text-[150px] pointer-events-none">
                           <i class="fas fa-hashtag text-purple-500"></i>
                        </div>
                        <h2 class="font-heading text-3xl md:text-5xl font-bold mb-4 lg:mb-6 leading-tight relative z-10">Social Mastery.</h2>
                        <p class="text-gray-400 font-light text-base lg:text-lg relative z-10">Strategic social media management that builds viral momentum and engagement.</p>
                    </div>
                    <div class="scroll-item transition-all duration-500 transform lg:-translate-x-4 lg:opacity-30 bg-white/5 lg:bg-transparent p-8 lg:p-0 rounded-3xl border border-white/10 lg:border-transparent mb-6 lg:mb-0 relative overflow-hidden reveal-up">
                        <div class="lg:hidden absolute -right-6 -top-6 opacity-[0.05] text-[150px] pointer-events-none">
                           <i class="fas fa-laptop-code text-cyan-500"></i>
                        </div>
                        <h2 class="font-heading text-3xl md:text-5xl font-bold mb-4 lg:mb-6 leading-tight relative z-10">Web Architecture.</h2>
                        <p class="text-gray-400 font-light text-base lg:text-lg relative z-10">High-performance, bespoke web applications engineered with modern stacks.</p>
                    </div>
                    <div class="scroll-item transition-all duration-500 transform lg:-translate-x-4 lg:opacity-30 bg-white/5 lg:bg-transparent p-8 lg:p-0 rounded-3xl border border-white/10 lg:border-transparent mb-6 lg:mb-0 relative overflow-hidden reveal-up">
                        <div class="lg:hidden absolute -right-6 -top-6 opacity-[0.05] text-[150px] pointer-events-none">
                           <i class="fas fa-video text-rose-500"></i>
                        </div>
                        <h2 class="font-heading text-3xl md:text-5xl font-bold mb-4 lg:mb-6 leading-tight relative z-10">Cinematic Video.</h2>
                        <p class="text-gray-400 font-light text-base lg:text-lg relative z-10">Stunning visual productions from concept to final cut to convert audiences.</p>
                    </div>
                    <div class="scroll-item transition-all duration-500 transform lg:-translate-x-4 lg:opacity-30 bg-white/5 lg:bg-transparent p-8 lg:p-0 rounded-3xl border border-white/10 lg:border-transparent mb-6 lg:mb-0 relative overflow-hidden reveal-up">
                        <div class="lg:hidden absolute -right-6 -top-6 opacity-[0.05] text-[150px] pointer-events-none">
                           <i class="fas fa-shield-alt text-emerald-500"></i>
                        </div>
                        <h2 class="font-heading text-3xl md:text-5xl font-bold mb-4 lg:mb-6 leading-tight relative z-10">Digital Recovery.</h2>
                        <p class="text-gray-400 font-light text-base lg:text-lg relative z-10">Expert recovery protocols for hacked accounts and aggressive reputation management.</p>
                    </div>
                </div>

                <!-- Scrolling Visuals Right -->
                <div class="w-full lg:w-1/2 space-y-24 py-10 hidden lg:block" id="sticky-scroll-visuals">
                    <div class="h-[400px] glass-panel rounded-2xl p-6 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] flex items-center justify-center bg-gradient-to-br from-indigo-900/40 to-transparent">
                        <i class="fas fa-copyright text-9xl text-indigo-500/50"></i>
                    </div>
                    <div class="h-[400px] glass-panel rounded-2xl p-6 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] flex items-center justify-center bg-gradient-to-br from-purple-900/40 to-transparent">
                        <i class="fas fa-hashtag text-9xl text-purple-500/50"></i>
                    </div>
                    <div class="h-[400px] glass-panel rounded-2xl p-6 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] flex items-center justify-center bg-gradient-to-br from-cyan-900/40 to-transparent">
                        <i class="fas fa-laptop-code text-9xl text-cyan-500/50"></i>
                    </div>
                    <div class="h-[400px] glass-panel rounded-2xl p-6 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] flex items-center justify-center bg-gradient-to-br from-rose-900/40 to-transparent">
                        <i class="fas fa-video text-9xl text-rose-500/50"></i>
                    </div>
                    <div class="h-[400px] glass-panel rounded-2xl p-6 border border-white/10 shadow-[0_30px_60px_rgba(0,0,0,0.6)] flex items-center justify-center bg-gradient-to-br from-emerald-900/40 to-transparent">
                        <i class="fas fa-shield-alt text-9xl text-emerald-500/50"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Lamp Component Call to Action -->
    <section class="pt-40 pb-32 relative z-10 text-center px-4 overflow-hidden bg-dark">
        <div class="lamp-glow-top"></div>
        <div class="lamp-cone"></div>
        
        <div class="relative z-10 reveal-up mt-20">
            <h2 class="font-heading text-5xl md:text-7xl font-black mb-6 text-white text-shadow-glow">Ready to <span class="text-gradient">Dominate?</span></h2>
            <p class="text-xl text-gray-400 mb-10 max-w-2xl mx-auto font-light">Join hundreds of successful brands that have transformed their digital presence with our elite team.</p>
            
            <a href="contact.php" class="magic-btn-wrapper mt-4">
                <span class="magic-border"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-full bg-slate-950 px-10 py-5 text-lg font-bold text-white backdrop-blur-3xl transition-colors hover:bg-slate-900">
                    Start Your Project <i class="fas fa-rocket ml-3"></i>
                </span>
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
                        if(entry.target.classList.contains('delay-500') && entry.target.querySelector('.counter') && !countersAnimated) {
                            animateCounters();
                            countersAnimated = true;
                        }
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal-up, .reveal-down, .reveal-left, .reveal-right, .scale-in, .blur-reveal');
            revealElements.forEach(el => observer.observe(el));

            // Spotlight effect for bento services grid
            const bentoContainer = document.getElementById("bento-services-container");
            if (bentoContainer) {
                bentoContainer.addEventListener("mousemove", e => {
                    for(const card of document.querySelectorAll("#bento-services-container .bento-card")) {
                        const rect = card.getBoundingClientRect(),
                              x = e.clientX - rect.left,
                              y = e.clientY - rect.top;
                        card.style.setProperty("--mouse-x", `${x}px`);
                        card.style.setProperty("--mouse-y", `${y}px`);
                    }
                });
            }

            // Sticky Scroll Logic
            const stickyTexts = document.querySelectorAll('#sticky-scroll-text .scroll-item');
            const stickyVisuals = document.querySelectorAll('#sticky-scroll-visuals .glass-panel');
            
            if (stickyTexts.length > 0 && stickyVisuals.length > 0) {
                window.addEventListener('scroll', () => {
                    if (window.innerWidth < 1024) return; // Disable sticky opacity logic on mobile

                    let index = 0;
                    stickyVisuals.forEach((visual, i) => {
                        const rect = visual.getBoundingClientRect();
                        if (rect.top <= window.innerHeight / 2) {
                            index = i;
                        }
                    });
                    stickyTexts.forEach((text, i) => {
                        if (i === index) {
                            text.classList.remove('lg:opacity-30', 'lg:-translate-x-4');
                            text.classList.add('lg:opacity-100', 'lg:translate-x-0');
                        } else {
                            text.classList.add('lg:opacity-30', 'lg:-translate-x-4');
                            text.classList.remove('lg:opacity-100', 'lg:translate-x-0');
                        }
                    });
                });
            }
        });
    </script>
    <?php include 'components/guest_chat.php'; ?>
</body>
</html>