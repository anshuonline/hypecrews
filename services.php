<?php
session_start();
$pageTitle = "Our Services - Hypecrews";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="Explore Hypecrews' premium digital services: copyright protection, social media management, digital marketing, web development, video production, SEO & more.">
    <meta name="keywords" content="copyright protection, social media management, digital marketing, web development, video production, SEO services, artist management, media distribution, hypecrews services">
    <link rel="canonical" href="https://hypecrews.com/services.php">

    <!-- Open Graph Tags -->
    <meta property="og:title" content="Our Services - Hypecrews | Premium Digital Solutions">
    <meta property="og:description" content="Explore Hypecrews' premium digital services: copyright protection, social media management, digital marketing, web development, video production, SEO & more.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/services.php">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">

    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Our Services - Hypecrews | Premium Digital Solutions">
    <meta name="twitter:description" content="Explore Hypecrews' premium digital services: copyright protection, social media management, digital marketing, web development, video production, SEO & more.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "serviceType": "Digital Services",
        "provider": {
            "@type": "Organization",
            "name": "Hypecrews",
            "url": "https://hypecrews.com",
            "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Golaghat",
                "addressRegion": "Assam",
                "addressCountry": "IN"
            }
        },
        "url": "https://hypecrews.com/services.php",
        "name": "Hypecrews Digital Services",
        "description": "Premium digital services including copyright protection, social media management, digital marketing, web development, video production, SEO, artist management, and media distribution.",
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Hypecrews Services",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Copyright Protection",
                        "description": "Proactive monitoring and DMCA takedown services to protect your intellectual property across all digital platforms."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Social Media Management",
                        "description": "Strategic content planning, daily posting, audience engagement, and performance analysis to build your brand presence."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Digital Marketing",
                        "description": "Data-driven SEO, PPC advertising, content marketing, and email campaigns to maximize online visibility and ROI."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Web & Development",
                        "description": "Comprehensive web and application development from informational websites to complex e-commerce platforms."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Recovery & Support",
                        "description": "Digital crisis assistance for compromised accounts, platform suspensions, and reputation damage recovery."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Video Production",
                        "description": "Full creative process from concept development and scriptwriting to filming, post-production, and final delivery."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Marketing & SEO",
                        "description": "Specialized strategies to rank higher on search engines, drive targeted traffic, and convert visitors into customers."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Creative & Production",
                        "description": "Premium design, branding, logo design, UI/UX, and comprehensive brand identity guidelines."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Business Support",
                        "description": "Administrative, financial, and strategic support including virtual assistant services and process optimization."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Company Registration",
                        "description": "Complete guidance through business registration, legal structure, documentation filing, and licensing."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Movie PR",
                        "description": "Strategic public relations campaigns for films including media outreach, press releases, and crisis communication."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Media Distribution",
                        "description": "Music and video content delivery to major streaming platforms with metadata management and royalty collection."
                    }
                },
                {
                    "@type": "Offer",
                    "itemOffered": {
                        "@type": "Service",
                        "name": "Artist Management",
                        "description": "End-to-end management for artists including bookings, contract negotiations, brand partnerships, and career strategy."
                    }
                }
            ]
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
                        dark: '#0B0F19', // Deep formal dark
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
        
        .service-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            font-weight: 600;
            color: #cbd5e1;
            transition: color 0.3s ease;
        }
        
        .service-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: -2px;
            left: 0;
            background-color: #6366f1;
            transition: width 0.3s ease;
        }
        
        .service-link:hover {
            color: #fff;
        }
        
        .service-link:hover::after {
            width: 100%;
        }

        .service-link i {
            transition: transform 0.3s ease;
        }

        .service-link:hover i {
            transform: translateX(5px);
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white antialiased selection:bg-primary selection:text-white">
    <?php include 'components/nav.php'; ?>

    <!-- Services Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <!-- Elegant subtle animated background -->
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[120px] animate-[pulse_8s_infinite_alternate]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-900/20 rounded-full blur-[120px] animate-[pulse_10s_infinite_alternate-reverse]"></div>
        </div>
        
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10 reveal-up">
            <div class="inline-flex items-center justify-center mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-primary mr-2 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wider uppercase text-gray-300">Expert Solutions</span>
            </div>
            
            <h1 class="font-heading text-5xl md:text-7xl font-bold mb-8 tracking-tight leading-tight">
                Premium Services for <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Digital Excellence</span>
            </h1>
            
            <p class="text-xl max-w-3xl mx-auto text-slate-400 mb-12 font-light leading-relaxed">
                Discover how our specialized team can help elevate your brand, protect your assets, and accelerate your business growth through meticulous digital execution.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-6">
                <a href="#services" class="px-8 py-4 bg-white text-dark font-semibold rounded-full transition-all duration-300 hover:bg-gray-200 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                    Explore Offerings
                </a>
                <a href="index.php#contact" class="px-8 py-4 bg-transparent border border-white/20 text-white font-semibold rounded-full transition-all duration-300 hover:bg-white/10">
                    Consult With Us
                </a>
            </div>
        </div>
    </section>

    <!-- Services Details Section -->
    <section id="services" class="py-24 relative z-10">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">Core <span class="text-gray-400">Capabilities</span></h2>
                <div class="w-16 h-1 bg-gradient-to-r from-primary to-secondary mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Copyright Protection & Removal -->
                <div id="copyright" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-copyright text-indigo-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Copyright Protection</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            We specialize in protecting your intellectual property across all digital platforms. Our service includes proactive monitoring for unauthorized use of your copyrighted content (music, videos, images, text), swift issuance of DMCA takedown notices, and persistent follow-up to ensure permanent removal from websites, social media, and file-sharing platforms.
                        </p>
                        <a href="service_query.php?service=Copyright Protection" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Social Media Management -->
                <div id="social" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 100ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-hashtag text-purple-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Social Media Management</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Our expert team handles all aspects of your social media presence, from strategic content planning and creation to daily posting, audience engagement, and performance analysis. We focus on building a strong brand voice, increasing follower count, driving traffic, and fostering a loyal community across platforms.
                        </p>
                        <a href="service_query.php?service=Social Media Management" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Digital Marketing -->
                <div id="digital" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 200ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-bullhorn text-blue-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Digital Marketing</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Designed to maximize your online visibility and drive measurable results. We employ a data-driven approach encompassing SEO, PPC advertising, content marketing, and email campaigns. We analyze market trends to craft targeted, high-converting strategies that increase leads and ROI.
                        </p>
                        <a href="service_query.php?service=Digital Marketing" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Web & Development -->
                <div id="web" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 300ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-laptop-code text-cyan-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Web & Development</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Comprehensive web and application development services, covering everything from simple informational websites to complex e-commerce platforms. Our developers utilize the latest technologies to deliver visually stunning, responsive, and highly secure digital solutions tailored to your business needs.
                        </p>
                        <a href="service_query.php?service=Web & Development" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Recovery & Support -->
                <div id="recovery" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-sync-alt text-emerald-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Recovery & Support</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Essential assistance for digital crises, focusing on regaining control of compromised accounts and repairing reputation damage. Whether dealing with platform suspensions, hacked accounts, or negative press, our dedicated team implements strategic recovery protocols to restore your digital assets.
                        </p>
                        <a href="service_query.php?service=Recovery & Support" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Video Production -->
                <div id="video" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 100ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-video text-rose-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Video Production</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Covering the entire creative process, from initial concept development and scriptwriting to filming, post-production editing, and final delivery. We specialize in producing high-quality, engaging visual content tailored to captivate your audience and effectively convey your message.
                        </p>
                        <a href="service_query.php?service=Video Production" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Marketing & SEO -->
                <div id="marketing" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 200ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-chart-line text-teal-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Marketing & SEO</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Drive organic growth and scale your business with our specialized Marketing and SEO strategies. We optimize your digital presence to rank higher on search engines, drive targeted traffic, and convert visitors into long-term loyal customers using proven white-hat methodologies.
                        </p>
                        <a href="service_query.php?service=Marketing & SEO" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Creative & Production -->
                <div id="creative" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 300ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-paint-brush text-pink-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Creative & Production</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Our creative studio delivers premium design, branding, and visual storytelling that resonates with your target demographic. From logo design and UI/UX to comprehensive brand identity guidelines, we ensure your aesthetic reflects the high quality of your brand.
                        </p>
                        <a href="service_query.php?service=Creative & Production" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Extended Services Section -->
    <section class="py-24 relative z-10 border-t border-white/5">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center mb-20 reveal-up">
                <h2 class="font-heading text-4xl md:text-5xl font-bold mb-6">Extended <span class="text-gray-400">Offerings</span></h2>
                <div class="w-16 h-1 bg-gradient-to-r from-purple-400 to-indigo-400 mx-auto rounded-full"></div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Business Support -->
                <div id="business-support" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-briefcase text-yellow-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Business Support</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Designed to streamline your operations and foster sustainable growth. We offer specialized administrative, financial, and strategic support, including virtual assistant services, documentation management, and process optimization to handle your essential back-office functions.
                        </p>
                        <a href="service_query.php?service=Business Support" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Company Registration -->
                <div id="company-registration" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 100ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-file-signature text-orange-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Company Registration</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            We guide you through the entire registration process, including choosing the appropriate legal structure, preparing and filing required documentation with relevant government authorities, and securing necessary licenses to ensure a seamless setup for your venture.
                        </p>
                        <a href="service_query.php?service=Company Registration" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Movie PR -->
                <div id="movie-pr" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 200ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-ticket-alt text-red-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Movie PR</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Crafting and executing strategic Public Relations campaigns specifically for films. We handle media outreach, press release distribution, red carpet events, and crisis communication to generate positive buzz and secure high-impact reviews across platforms.
                        </p>
                        <a href="service_query.php?service=Movie PR" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Media Distribution -->
                <div id="media-distribution" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32" style="transition-delay: 300ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-broadcast-tower text-green-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Media Distribution</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            Efficiently delivering your music and video content to a global audience. We manage the entire pipeline, ensuring proper formatting and delivery to major streaming platforms (Spotify, Apple Music, YouTube) while handling metadata and royalty collection.
                        </p>
                        <a href="https://music.hypecrews.com" target="_blank" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Explore Platform <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                        </a>
                    </div>
                </div>

                <!-- Artist Management -->
                <div id="artist-management" class="glass-card rounded-3xl p-8 lg:p-10 reveal-up group scroll-mt-32 lg:col-span-2 mx-auto max-w-4xl w-full" style="transition-delay: 400ms;">
                    <div class="flex flex-col h-full">
                        <div class="flex items-center mb-8">
                            <div class="icon-box w-14 h-14 rounded-2xl flex items-center justify-center border border-white/10 mr-6 shadow-inner">
                                <i class="fas fa-microphone-alt text-sky-400 text-2xl"></i>
                            </div>
                            <h3 class="font-heading text-2xl font-bold tracking-tight">Artist Management</h3>
                        </div>
                        <p class="text-slate-400 mb-8 flex-grow leading-relaxed font-light">
                            End-to-end management services for emerging and established artists. We handle bookings, contract negotiations, brand partnerships, tour logistics, and overall career strategy so you can focus entirely on your craft and performance.
                        </p>
                        <a href="service_query.php?service=Artist Management" class="service-link text-sm uppercase tracking-widest mt-auto">
                            Learn More <i class="fas fa-arrow-right ml-2 text-xs"></i>
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
            <h2 class="font-heading text-3xl md:text-5xl font-bold mb-6 tracking-tight">Initiate Your Transformation</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto text-slate-400 font-light">Consult with our specialists to construct a tailored strategy for your digital objectives.</p>
            <a href="index.php#contact" class="inline-flex items-center justify-center px-10 py-4 bg-white text-dark font-semibold rounded-full transition-all duration-300 hover:bg-gray-200 hover:shadow-[0_0_20px_rgba(255,255,255,0.3)]">
                Schedule Consultation
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