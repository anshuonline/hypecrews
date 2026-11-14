<?php
session_start();

// Machine & Agent Readiness Headers (RFC 8288 & Content-Signal)
header('Content-Signal: search=yes, ai-train=no');
header('Link: </llms.txt>; rel="describedby"; type="text/markdown", </?format=md>; rel="alternate"; type="text/markdown", </.well-known/mcp/server-card.json>; rel="service-desc"; type="application/json"', false);

// Serve Markdown on Demand
$acceptHeader = $_SERVER['HTTP_ACCEPT'] ?? '';
$formatQuery = $_GET['format'] ?? '';
$isMarkdownRequested = ($formatQuery === 'md' || $formatQuery === 'markdown') ||
    (stripos($acceptHeader, 'text/markdown') !== false || stripos($acceptHeader, 'text/x-markdown') !== false);

if ($isMarkdownRequested) {
    header('Content-Type: text/markdown; charset=utf-8');
    echo <<<MARKDOWN
# Hypecrews — Peak Digital Excellence

> Hypecrews is a premium digital brand company delivering web development, copyright protection, social media management, and creative growth solutions.

We combine identity, product, and growth into one sharp system — built to make ambitious businesses impossible to ignore. Trusted by 750+ brands across India and beyond.

## Contact Information
- **Legal Name**: Hypecrews Software Private Limited
- **Phone**: [+91 361 324 3276](tel:+913613243276)
- **Email**: [info@hypecrews.com](mailto:info@hypecrews.com)
- **Support**: [support@hypecrews.com](mailto:support@hypecrews.com)
- **Headquarters**: Golaghat, Assam 785621, India
- **Website**: https://hypecrews.com
- **Hours**: Monday – Friday, 09:00 – 18:00 IST

## Core Services
1. **Web Development & Custom Software**: Modern responsive websites, web applications, SaaS dashboards, e-commerce, and high-performance API integrations.
2. **Copyright Protection & Rights Defense**: 24/7 web-scale anti-piracy monitoring, DMCA enforcement, rights management, and social media asset reclamation.
3. **Social Media Management**: Strategic content direction, high-retention video production, organic audience expansion, and community operations.
4. **Brand Identity & Creative Systems**: Typography, color theory, comprehensive logo systems, design tokens, and corporate design language.
5. **Digital Growth & Performance**: Search engine optimization (SEO), conversion rate optimization (CRO), paid advertising funnels, and data analytics.

## Official Verified Profiles
- LinkedIn: https://www.linkedin.com/company/hypecrews
- Instagram: https://www.instagram.com/hypecrews
- X: https://x.com/hypecrews
- Facebook: https://www.facebook.com/hypecrews
- YouTube: https://www.youtube.com/@hypecrews
- GitHub: https://github.com/anshuonline/hypecrews

## Agent & Machine-Readable Interfaces
- LLM Briefing: https://hypecrews.com/llms.txt
- LLM Full Reference: https://hypecrews.com/llms-full.txt
- MCP Server Card: https://hypecrews.com/.well-known/mcp/server-card.json
- Track Order Status: https://hypecrews.com/track_orders.php
- Start a Project: https://hypecrews.com/contact.php
MARKDOWN;
    exit;
}

$pageTitle = "Hypecrews — Peak Digital Excellence";
$pageDescription = "Hypecrews is a premium digital brand company delivering web development, copyright protection, social media management and creative growth solutions.";
$pageKeywords = "digital brand company, web development, copyright protection, social media management, creative agency, Hypecrews";
$canonical = "https://hypecrews.com/";

$jsonLd = json_encode([
    "@context" => "https://schema.org",
    "@graph" => [
        [
            "@type" => ["Organization", "LocalBusiness", "ProfessionalService"],
            "@id" => "https://hypecrews.com/#organization",
            "name" => "Hypecrews",
            "legalName" => "Hypecrews Software Private Limited",
            "url" => "https://hypecrews.com",
            "logo" => [
                "@type" => "ImageObject",
                "url" => "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png"
            ],
            "image" => "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png",
            "description" => "Premium digital brand company delivering web development, copyright protection, social media management and creative growth solutions.",
            "telephone" => "+91 361 324 3276",
            "email" => "info@hypecrews.com",
            "priceRange" => "$$",
            "currenciesAccepted" => "INR, USD, EUR",
            "paymentAccepted" => "Cash, Credit Card, UPI, Bank Transfer",
            "foundingDate" => "2024",
            "address" => [
                "@type" => "PostalAddress",
                "streetAddress" => "Golaghat",
                "addressLocality" => "Golaghat",
                "addressRegion" => "Assam",
                "postalCode" => "785621",
                "addressCountry" => "IN"
            ],
            "contactPoint" => [
                [
                    "@type" => "ContactPoint",
                    "telephone" => "+91 361 324 3276",
                    "contactType" => "customer service",
                    "email" => "info@hypecrews.com",
                    "areaServed" => ["IN", "US", "GB", "AE"],
                    "availableLanguage" => ["English", "Hindi", "Assamese"]
                ],
                [
                    "@type" => "ContactPoint",
                    "telephone" => "+91 361 324 3276",
                    "contactType" => "technical support",
                    "email" => "support@hypecrews.com",
                    "areaServed" => "Worldwide",
                    "availableLanguage" => ["English", "Hindi"]
                ]
            ],
            "openingHoursSpecification" => [
                "@type" => "OpeningHoursSpecification",
                "dayOfWeek" => ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
                "opens" => "09:00",
                "closes" => "18:00"
            ],
            "sameAs" => [
                "https://www.linkedin.com/company/hypecrews",
                "https://www.instagram.com/hypecrews",
                "https://x.com/hypecrews",
                "https://www.facebook.com/hypecrews",
                "https://www.youtube.com/@hypecrews",
                "https://github.com/anshuonline/hypecrews"
            ]
        ],
        [
            "@type" => "WebSite",
            "@id" => "https://hypecrews.com/#website",
            "url" => "https://hypecrews.com",
            "name" => "Hypecrews",
            "publisher" => ["@id" => "https://hypecrews.com/#organization"],
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => "https://hypecrews.com/services.php?q={search_term_string}",
                "query-input" => "required name=search_term_string"
            ]
        ],
        [
            "@type" => "WebPage",
            "@id" => "https://hypecrews.com/#webpage",
            "url" => "https://hypecrews.com",
            "name" => "Hypecrews — Peak Digital Excellence",
            "isPartOf" => ["@id" => "https://hypecrews.com/#website"],
            "about" => ["@id" => "https://hypecrews.com/#organization"],
            "description" => "Hypecrews is a premium digital brand company delivering web development, copyright protection, social media management and creative growth solutions."
        ],
        [
            "@type" => "Service",
            "@id" => "https://hypecrews.com/services.php#web",
            "name" => "Web Development & Custom Software",
            "provider" => ["@id" => "https://hypecrews.com/#organization"],
            "serviceType" => "Web Development",
            "description" => "Custom high-performance web applications, portals, and cloud solutions engineered for brand authority."
        ],
        [
            "@type" => "Service",
            "@id" => "https://hypecrews.com/services.php#copyright",
            "name" => "Copyright Protection & Enforcement",
            "provider" => ["@id" => "https://hypecrews.com/#organization"],
            "serviceType" => "Copyright Protection",
            "description" => "Anti-piracy monitoring, DMCA takedown enforcement, and brand asset legal defense."
        ],
        [
            "@type" => "Service",
            "@id" => "https://hypecrews.com/services.php#social",
            "name" => "Social Media Management & Strategy",
            "provider" => ["@id" => "https://hypecrews.com/#organization"],
            "serviceType" => "Social Media Management",
            "description" => "End-to-end social media operations, content creation, audience scaling, and brand positioning."
        ],
        [
            "@type" => "Service",
            "@id" => "https://hypecrews.com/services.php#creative",
            "name" => "Brand Identity & Creative Systems",
            "provider" => ["@id" => "https://hypecrews.com/#organization"],
            "serviceType" => "Brand Design",
            "description" => "Comprehensive visual systems, logo design, design guidelines, and creative assets."
        ],
        [
            "@type" => "FAQPage",
            "@id" => "https://hypecrews.com/#faq",
            "mainEntity" => [
                [
                    "@type" => "Question",
                    "name" => "What services does Hypecrews provide?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Hypecrews specializes in custom web development, copyright protection and DMCA enforcement, social media management, brand identity design, and digital performance growth."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "Where is Hypecrews located and how can I contact the team?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Hypecrews is headquartered in Golaghat, Assam 785621, India. You can contact us by phone at +91 361 324 3276, by email at info@hypecrews.com, or through our website contact form."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "How does Hypecrews protect digital assets and copyrights?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "We provide continuous automated scanning across major video and social platforms, swift DMCA takedowns, rights claim dispute handling, and proactive digital rights management."
                    ]
                ],
                [
                    "@type" => "Question",
                    "name" => "Can clients track the status of active projects and orders?",
                    "acceptedAnswer" => [
                        "@type" => "Answer",
                        "text" => "Yes, active clients can track real-time delivery status using our Track Orders portal at https://hypecrews.com/track_orders.php."
                    ]
                ]
            ]
        ]
    ]
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="hero-home">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 320px; height: 320px; top: 4%; left: -8%;"></div>
    <div class="orb orb-2" style="width: 240px; height: 240px; bottom: 0; right: 6%;"></div>

    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <div class="chip" data-aos="fade-up">
                    <span style="width: 7px; height: 7px; border-radius: 99px; background: #1a9f6e; box-shadow: 0 0 0 4px rgba(26,159,110,.13); display: inline-block;"></span>
                    Available for Q1 2027 projects
                </div>

                <h1 class="display-1 text-balance" style="margin-top: 24px;" data-aos="fade-up" data-aos-delay="60">
                    We build brands<br>
                    <span class="gradient-text">worth following.</span>
                </h1>

                <p class="lead" style="margin-top: 22px;" data-aos="fade-up" data-aos-delay="120">
                    Hypecrews is a premium digital brand company. We combine identity, product and growth into one sharp system — built to make ambitious businesses impossible to ignore.
                </p>

                <div class="flex flex-wrap gap-3" style="margin-top: 32px;" data-aos="fade-up" data-aos-delay="180">
                    <a href="/contact.php" class="btn btn-primary btn-lg">Start a Project <i class="fas fa-arrow-right"></i></a>
                    <a href="/services.php" class="btn btn-outline btn-lg">Explore Services</a>
                </div>

                <div class="flex flex-wrap items-center gap-8" style="margin-top: 42px;" data-aos="fade-up" data-aos-delay="240">
                    <div class="flex items-center" style="gap: 0;">
                        <span class="nav-avatar" style="width: 36px; height: 36px; border: 2px solid #fff; margin-right: -8px; background: linear-gradient(140deg, #A0E0EF, #60C1E5);">A</span>
                        <span class="nav-avatar" style="width: 36px; height: 36px; border: 2px solid #fff; margin-right: -8px; background: linear-gradient(140deg, #60C1E5, #005A9C); color: #fff;">R</span>
                        <span class="nav-avatar" style="width: 36px; height: 36px; border: 2px solid #fff; margin-right: -8px; background: linear-gradient(140deg, #1B365D, #005A9C); color: #fff;">M</span>
                        <span class="nav-avatar" style="width: 36px; height: 36px; border: 2px solid #fff; background: #fff; color: #1B365D; font-size: 10px;">+</span>
                    </div>
                    <div>
                        <div style="color: #F0A92E; font-size: 12px; letter-spacing: 2px;"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <p style="font-size: 12px; color: var(--muted); font-weight: 700;">Trusted by 750+ brands across India</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-6" style="margin-top: 24px; font-size: 13px; color: var(--navy); font-weight: 600;" itemscope itemtype="https://schema.org/LocalBusiness" data-aos="fade-up" data-aos-delay="280">
                    <meta itemprop="name" content="Hypecrews Software Private Limited">
                    <a href="tel:+913613243276" class="inline-flex items-center gap-2 text-inherit hover:opacity-80 transition" itemprop="telephone">
                        <i class="fas fa-phone-alt" style="color: var(--blue);"></i>
                        <span>+91 361 324 3276</span>
                    </a>
                    <span style="color: var(--line);">|</span>
                    <a href="mailto:info@hypecrews.com" class="inline-flex items-center gap-2 text-inherit hover:opacity-80 transition" itemprop="email">
                        <i class="fas fa-envelope" style="color: var(--blue);"></i>
                        <span>info@hypecrews.com</span>
                    </a>
                </div>
            </div>

            <!-- Brand intelligence mockup -->
            <div class="relative" data-aos="fade-left" data-aos-delay="120">
                <div class="mockup">
                    <div class="mockup-bar"><span></span><span></span><span></span><div></div></div>
                    <div class="mockup-body">
                        <div class="flex items-center justify-between" style="margin-bottom: 14px;">
                            <div class="flex items-center gap-3">
                                <div class="icon-tile icon-tile-sm"><i class="fas fa-wave-square"></i></div>
                                <div>
                                    <p style="font-size: 11px; font-weight: 800; color: var(--navy);">Brand Command Center</p>
                                    <p style="font-size: 9px; color: var(--muted-2);">Live growth overview</p>
                                </div>
                            </div>
                            <span class="badge badge-green">Live</span>
                        </div>
                        <div class="mockup-kpis">
                            <div class="mockup-kpi"><p>Reach</p><strong>2.4M</strong><span class="up"><i class="fas fa-arrow-trend-up"></i> 38%</span></div>
                            <div class="mockup-kpi"><p>Leads</p><strong>8.9k</strong><span class="up"><i class="fas fa-arrow-trend-up"></i> 24%</span></div>
                            <div class="mockup-kpi"><p>ROAS</p><strong>6.2x</strong><span class="up"><i class="fas fa-arrow-trend-up"></i> 17%</span></div>
                        </div>
                        <div class="mockup-chart">
                            <div class="mockup-chart-head"><p>Campaign performance</p><span>Last 90 days</span></div>
                            <div class="mockup-bars">
                                <i style="height: 34%"></i><i style="height: 52%"></i><i style="height: 42%"></i><i style="height: 66%"></i><i style="height: 58%"></i><i style="height: 78%"></i><i style="height: 70%"></i><i style="height: 92%"></i><i style="height: 82%"></i><i style="height: 100%"></i>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2" style="margin-top: 12px;">
                            <div style="border: 1px solid var(--line); border-radius: 11px; padding: 10px; background: #fbfdff;"><p style="font-size: 8px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Content</p><p style="font-size: 12px; font-weight: 800; color: var(--navy); margin-top: 3px;">128 assets</p></div>
                            <div style="border: 1px solid var(--line); border-radius: 11px; padding: 10px; background: #fbfdff;"><p style="font-size: 8px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Assets</p><p style="font-size: 12px; font-weight: 800; color: var(--navy); margin-top: 3px;">340 protected</p></div>
                            <div style="border: 1px solid var(--line); border-radius: 11px; padding: 10px; background: #fbfdff;"><p style="font-size: 8px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Sentiment</p><p style="font-size: 12px; font-weight: 800; color: #15805a; margin-top: 3px;">98% positive</p></div>
                        </div>
                    </div>
                </div>
                <div class="mockup-float mockup-float-1">
                    <div class="icon-tile"><i class="fas fa-arrow-trend-up"></i></div>
                    <div><strong>+212% growth</strong><small>Organic reach this quarter</small></div>
                </div>
                <div class="mockup-float mockup-float-2">
                    <div class="icon-tile"><i class="fas fa-shield-halved"></i></div>
                    <div><strong>340 assets secured</strong><small>Copyright protection live</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================== TRUSTED BY -->
<section style="padding: 34px 0 38px; background: #fff; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line);">
    <div class="shell shell-wide">
        <p class="text-center" style="font-size: 10px; font-weight: 800; letter-spacing: .22em; text-transform: uppercase; color: var(--muted-2); margin-bottom: 24px;">Brands that trust Hypecrews</p>
        <div class="marquee" style="--bg: #fff;">
            <div class="marquee-track">
                <div class="logo-cloud">
                    <span class="logo-text">AMAZON</span>
                    <img src="/graphics/logos/axis-bank.png" alt="Axis Bank">
                    <img src="/graphics/logos/decathlon.png" alt="Decathlon">
                    <span class="logo-text">INFOSYS</span>
                    <img src="/graphics/logos/samsung.png" alt="Samsung">
                    <span class="logo-text">SONY MUSIC</span>
                    <img src="/graphics/logos/amazon.png" alt="Amazon">
                    <span class="logo-text">ZOMATO</span>
                    <img src="/graphics/logos/infosys.png" alt="Infosys">
                </div>
                <div class="logo-cloud" aria-hidden="true">
                    <span class="logo-text">AMAZON</span>
                    <img src="/graphics/logos/axis-bank.png" alt="">
                    <img src="/graphics/logos/decathlon.png" alt="">
                    <span class="logo-text">INFOSYS</span>
                    <img src="/graphics/logos/samsung.png" alt="">
                    <span class="logo-text">SONY MUSIC</span>
                    <img src="/graphics/logos/amazon.png" alt="">
                    <span class="logo-text">ZOMATO</span>
                    <img src="/graphics/logos/infosys.png" alt="">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================== STATS BAND -->
<section class="section-dark stat-band" style="padding: 64px 0;">
    <div class="arc-lines"></div>
    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-4 gap-10 items-end">
            <div data-aos="fade-right">
                <span class="eyebrow eyebrow-light">By the numbers</span>
                <h2 style="font-size: clamp(1.7rem, 2.6vw, 2.3rem); margin-top: 12px;">Quiet confidence,<br><span class="gradient-text-light">measured results.</span></h2>
            </div>
            <div class="lg:col-span-3" data-aos="fade-left">
                <div class="stat-grid">
                    <div class="stat-cell"><div class="num"><span data-count="1000" data-suffix="+">0+</span></div><p>Projects delivered</p></div>
                    <div class="stat-cell"><div class="num"><span data-count="750" data-suffix="+">0+</span></div><p>Brands served</p></div>
                    <div class="stat-cell"><div class="num"><span data-count="98" data-suffix="%">0%</span></div><p>Success rate</p></div>
                    <div class="stat-cell"><div class="num"><span data-count="4.9" data-decimals="1" data-suffix="/5">0/5</span></div><p>Client rating</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================= SERVICES -->
<section class="section" id="services" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-8 section-head" style="max-width: none;">
            <div style="max-width: 640px;">
                <span class="eyebrow" data-aos="fade-up">What we do</span>
                <h2 class="display-2" style="margin-top: 16px;" data-aos="fade-up" data-aos-delay="50">One partner for brand,<br><span class="gradient-text">product and growth.</span></h2>
            </div>
            <a href="/services.php" class="link-arrow" data-aos="fade-up" data-aos-delay="100">View all 13 services <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- Featured -->
            <article class="card card-hover card-pad md:col-span-2" style="background: linear-gradient(135deg, #fff 25%, #eef8fd);" data-aos="fade-up">
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div style="max-width: 470px;">
                        <div class="icon-tile icon-tile-lg"><i class="fas fa-copyright"></i></div>
                        <h3 style="font-size: 26px; margin-top: 22px;">Copyright Protection</h3>
                        <p style="font-size: 14px; color: var(--muted); line-height: 1.8; margin-top: 10px;">Proactive monitoring, fast DMCA takedowns and persistent follow-up to keep your creative work safe across every platform.</p>
                        <a href="/services.php#copyright" class="link-arrow" style="margin-top: 24px;">Protect your work <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <div class="hidden md:block" style="flex: 0 0 220px;">
                        <div style="border-radius: 18px; background: #fff; border: 1px solid var(--line); padding: 16px; box-shadow: var(--shadow-sm);">
                            <div class="flex items-center justify-between" style="margin-bottom: 14px;"><span class="badge badge-sky">Live scan</span><i class="fas fa-shield-halved" style="color: var(--sky);"></i></div>
                            <div style="height: 8px; border-radius: 99px; background: #e9f3f9; overflow: hidden; margin-bottom: 8px;"><div style="width: 86%; height: 100%; background: linear-gradient(90deg, var(--blue), var(--sky)); border-radius: 99px;"></div></div>
                            <p style="font-size: 10px; color: var(--muted-2); font-weight: 700;">340 assets monitored · 98% cleared</p>
                            <div class="flex items-center gap-2" style="margin-top: 16px;">
                                <span style="width: 7px; height: 7px; border-radius: 99px; background: #1a9f6e;"></span>
                                <span style="font-size: 10px; font-weight: 800; color: #15805a;">All systems protected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="60">
                <div class="icon-tile"><i class="fas fa-hashtag"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Social Media</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;">Strategy, content and community that turns followers into a real audience.</p>
                <a href="/services.php#social" class="link-arrow" style="margin-top: 20px;">Explore <i class="fas fa-arrow-right"></i></a>
            </article>

            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="90">
                <div class="icon-tile"><i class="fas fa-laptop-code"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Web & Product</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;">High-performance websites and applications designed around real business outcomes.</p>
                <a href="/services.php#web" class="link-arrow" style="margin-top: 20px;">Explore <i class="fas fa-arrow-right"></i></a>
            </article>

            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="120">
                <div class="icon-tile"><i class="fas fa-bullhorn"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Digital Marketing</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;">Paid media, SEO and lifecycle campaigns engineered for efficient growth.</p>
                <a href="/services.php#digital" class="link-arrow" style="margin-top: 20px;">Explore <i class="fas fa-arrow-right"></i></a>
            </article>

            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="150">
                <div class="icon-tile"><i class="fas fa-clapperboard"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Video & Content</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;">Cinematic brand films, social content and campaigns that people remember.</p>
                <a href="/services.php#video" class="link-arrow" style="margin-top: 20px;">Explore <i class="fas fa-arrow-right"></i></a>
            </article>

            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="180">
                <div class="icon-tile"><i class="fas fa-pen-nib"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Creative & Brand</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;">Identity systems, UI/UX and design language with a premium point of view.</p>
                <a href="/services.php#creative" class="link-arrow" style="margin-top: 20px;">Explore <i class="fas fa-arrow-right"></i></a>
            </article>
        </div>
    </div>
</section>

<!-- ====================================================== WHY US -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative" data-aos="fade-right">
                <div style="border-radius: 30px; overflow: hidden; border: 1px solid var(--line); box-shadow: var(--shadow-md); position: relative;">
                    <img src="/graphics/516as4d8a16s5d64as.jpg" alt="Hypecrews team at work" style="width: 100%; height: 520px; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 45%, rgba(11,28,47,.72));"></div>
                    <div style="position: absolute; left: 26px; right: 26px; bottom: 24px; color: #fff;">
                        <p style="font-size: 10px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: var(--ice);">Since 2022</p>
                        <p style="font-family: var(--font-head); font-size: 20px; font-weight: 700; color: #fff; margin-top: 4px;">A senior team, obsessed with the details.</p>
                    </div>
                </div>
                <div class="card" style="position: absolute; right: -16px; top: 34px; padding: 16px 18px; border-radius: 16px; box-shadow: var(--shadow-md); display: flex; align-items: center; gap: 12px; max-width: 210px;" data-aos="zoom-in" data-aos-delay="200">
                    <div class="icon-tile icon-tile-sm"><i class="fas fa-award"></i></div>
                    <div><strong style="display: block; font-size: 12px; color: var(--navy);">Top-rated agency</strong><small style="font-size: 10px; color: var(--muted-2);">4.9/5 across 300+ reviews</small></div>
                </div>
            </div>

            <div data-aos="fade-left">
                <span class="eyebrow">Why Hypecrews</span>
                <h2 class="display-2" style="margin-top: 16px;">Built like a partner.<br><span class="gradient-text">Not a vendor.</span></h2>
                <p class="lead" style="margin-top: 18px;">We work like an extension of your team — senior strategists, designers and engineers in one room, accountable for the outcome, not just the deliverable.</p>

                <div class="grid sm:grid-cols-2 gap-5" style="margin-top: 32px;">
                    <div class="flex items-start gap-3">
                        <div class="icon-tile icon-tile-sm"><i class="fas fa-user-tie"></i></div>
                        <div><h3 style="font-size: 14.5px;">Senior by default</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.65; margin-top: 4px;">No hand-offs to juniors after the pitch.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="icon-tile icon-tile-sm"><i class="fas fa-compass-drafting"></i></div>
                        <div><h3 style="font-size: 14.5px;">Design with intent</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.65; margin-top: 4px;">Every screen maps to a business goal.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="icon-tile icon-tile-sm"><i class="fas fa-bolt"></i></div>
                        <div><h3 style="font-size: 14.5px;">Momentum weekly</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.65; margin-top: 4px;">Visible progress, every single week.</p></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="icon-tile icon-tile-sm"><i class="fas fa-shield-halved"></i></div>
                        <div><h3 style="font-size: 14.5px;">Protection included</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.65; margin-top: 4px;">Your assets stay yours. Always.</p></div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4" style="margin-top: 34px;">
                    <a href="/about.php" class="btn btn-primary">Meet the team <i class="fas fa-arrow-right"></i></a>
                    <a href="/careers.php" class="btn btn-ghost">We’re hiring <i class="fas fa-arrow-up-right-from-square" style="font-size: 11px;"></i></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================== CASE STUDIES -->
<section class="section" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Selected work</span>
            <h2 class="display-2">Proof, not promises.</h2>
            <p class="lead">A snapshot of recent brand, product and growth engagements. Demo case studies shown for preview.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <article class="case-card" data-aos="fade-up">
                <div class="case-visual" style="background: linear-gradient(145deg, #1B365D, #005A9C);">
                    <div class="case-window">
                        <div class="row w40"></div><div class="row w85"></div><div class="row w70"></div>
                        <div class="window-grid"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="case-body">
                    <div class="tags"><span class="tag">Brand identity</span><span class="tag">Web</span></div>
                    <h3>Northline Capital</h3>
                    <p>A complete rebrand and investor platform for a fast-growing financial advisory firm.</p>
                    <div class="flex items-center justify-between" style="margin-top: 18px;">
                        <span style="font-size: 11px; font-weight: 800; color: #15805a;"><i class="fas fa-arrow-trend-up"></i> +212% qualified leads</span>
                        <a href="/contact.php" class="link-arrow" style="font-size: 10px;">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>

            <article class="case-card" data-aos="fade-up" data-aos-delay="80">
                <div class="case-visual" style="background: linear-gradient(145deg, #005A9C, #60C1E5);">
                    <div class="case-window">
                        <div class="row w40"></div><div class="row w55"></div><div class="row w85"></div>
                        <div class="window-grid"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="case-body">
                    <div class="tags"><span class="tag">Social</span><span class="tag">Content</span></div>
                    <h3>Verve Studios</h3>
                    <p>Always-on social and creator strategy that took a boutique studio to national attention.</p>
                    <div class="flex items-center justify-between" style="margin-top: 18px;">
                        <span style="font-size: 11px; font-weight: 800; color: #15805a;"><i class="fas fa-arrow-trend-up"></i> 2.4M monthly reach</span>
                        <a href="/contact.php" class="link-arrow" style="font-size: 10px;">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>

            <article class="case-card" data-aos="fade-up" data-aos-delay="160">
                <div class="case-visual" style="background: linear-gradient(145deg, #12263F, #1B365D);">
                    <div class="case-window">
                        <div class="row w40"></div><div class="row w70"></div><div class="row w55"></div>
                        <div class="window-grid"><span></span><span></span><span></span></div>
                    </div>
                </div>
                <div class="case-body">
                    <div class="tags"><span class="tag">Product</span><span class="tag">Development</span></div>
                    <h3>Aurora Labs</h3>
                    <p>A product design system and web app that cut onboarding time by more than half.</p>
                    <div class="flex items-center justify-between" style="margin-top: 18px;">
                        <span style="font-size: 11px; font-weight: 800; color: #15805a;"><i class="fas fa-arrow-trend-up"></i> -58% onboarding time</span>
                        <a href="/contact.php" class="link-arrow" style="font-size: 10px;">View <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ======================================================== PROCESS -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">How we work</span>
            <h2 class="display-2">A calm, predictable process.</h2>
            <p class="lead">Clear stages, clear owners, no surprises. You always know what’s happening and why.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 steps">
            <div class="step" data-aos="fade-up">
                <div class="step-num">01</div>
                <h3>Discover</h3>
                <p>We audit your brand, market and goals, then agree on the sharpest opportunity to chase.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="80">
                <div class="step-num">02</div>
                <h3>Define</h3>
                <p>Strategy, positioning and scope are locked into a clear roadmap with measurable milestones.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="160">
                <div class="step-num">03</div>
                <h3>Design</h3>
                <p>We build the identity, product and content system — reviewed in fast, focused iterations.</p>
            </div>
            <div class="step" data-aos="fade-up" data-aos-delay="240">
                <div class="step-num">04</div>
                <h3>Drive</h3>
                <p>Launch, measure and compound. Ongoing optimisation turns a project into a growth engine.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================== TESTIMONIALS -->
<section class="section" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-6 section-head" style="max-width: none;">
            <div>
                <span class="eyebrow" data-aos="fade-up">Client voices</span>
                <h2 class="display-2" style="margin-top: 16px;" data-aos="fade-up" data-aos-delay="50">The work speaks.<br><span class="gradient-text">So do our clients.</span></h2>
            </div>
            <div class="flex gap-2" data-aos="fade-up">
                <div class="swiper-button-prev" style="position: static; margin: 0;"></div>
                <div class="swiper-button-next" style="position: static; margin: 0;"></div>
            </div>
        </div>

        <div class="swiper" data-swiper="testimonials" data-aos="fade-up">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“Hypecrews rebuilt our entire digital presence in ten weeks. The clarity of thinking was as impressive as the design — our qualified leads doubled.”</blockquote>
                        <div class="who"><span class="avatar">SJ</span><div><strong>Sarah Jenkins</strong><small>CMO, Northline Capital</small></div></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“Their copyright team removed 400+ unauthorised copies of our catalogue in 48 hours. That level of protection is rare — and genuinely valuable.”</blockquote>
                        <div class="who"><span class="avatar">ER</span><div><strong>Elena Rostova</strong><small>Founder, Verve Studios</small></div></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“The precision and speed are unmatched. They shipped a product design system our in-house team still uses every day.”</blockquote>
                        <div class="who"><span class="avatar">MT</span><div><strong>Marcus Thorne</strong><small>VP Product, Aurora Labs</small></div></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“A genuinely senior team. They challenged our assumptions, then delivered work that outperformed every benchmark we set.”</blockquote>
                        <div class="who"><span class="avatar">AK</span><div><strong>Aarav Kapoor</strong><small>Director, Meridian Group</small></div></div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div class="stars"><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i></div>
                        <blockquote>“From the first workshop to launch, everything felt considered. Hypecrews is the rare agency that treats your budget like their own.”</blockquote>
                        <div class="who"><span class="avatar">LM</span><div><strong>Leila Mendez</strong><small>Head of Brand, Decathlon India</small></div></div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<!-- ===================================================== FINAL CTA -->
<section class="section-dark" style="padding: 92px 0; position: relative; overflow: hidden;">
    <div class="hero-mesh-dark"></div>
    <div class="orb orb-1" style="width: 260px; height: 260px; top: -10%; right: 8%; opacity: .18;"></div>
    <div class="shell shell-wide relative z-1 text-center" data-aos="zoom-in">
        <span class="eyebrow eyebrow-light eyebrow-center">Your move</span>
        <h2 class="display-2" style="margin-top: 16px; color: #fff;">Let’s make your brand<br><span class="gradient-text-light">impossible to ignore.</span></h2>
        <p class="lead lead-light" style="margin: 20px auto 32px; text-align: center;">Book a free strategy call. We’ll review your current presence and show you the three highest-impact moves for the next quarter.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/contact.php" class="btn btn-sky btn-lg">Book a strategy call <i class="fas fa-arrow-right"></i></a>
            <a href="/services.php" class="btn btn-outline-light btn-lg">See what we do</a>
        </div>
        <p style="margin-top: 22px; font-size: 11.5px; color: rgba(255,255,255,.45);"><i class="fas fa-lock" style="margin-right: 6px;"></i>No pressure, no obligation. Just useful clarity.</p>
    </div>
</section>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
