<?php
session_start();
$pageTitle = "Our Services — Hypecrews";
$pageDescription = "Explore Hypecrews' premium digital services: copyright protection, social media management, digital marketing, web development, video production, SEO, artist management and more.";
$pageKeywords = "copyright protection, social media management, digital marketing, web development, video production, SEO services, artist management, media distribution, Hypecrews services";
$canonical = "https://hypecrews.com/services.php";
$jsonLd = '{
  "@context": "https://schema.org",
  "@type": "Service",
  "serviceType": "Digital Services",
  "provider": { "@type": "Organization", "name": "Hypecrews", "url": "https://hypecrews.com", "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png", "address": { "@type": "PostalAddress", "addressLocality": "Golaghat", "addressRegion": "Assam", "addressCountry": "IN" } },
  "url": "https://hypecrews.com/services.php",
  "name": "Hypecrews Digital Services",
  "description": "Premium digital services including copyright protection, social media management, digital marketing, web development, video production, SEO, artist management and media distribution."
}';

$services = [
    ['id' => 'copyright', 'icon' => 'fa-copyright', 'title' => 'Copyright Protection', 'query' => 'Copyright Protection',
     'desc' => 'We protect your intellectual property across every digital platform — proactive monitoring for unauthorised use of your music, video, images and text, swift DMCA takedowns, and persistent follow-up to ensure permanent removal.',
     'tags' => ['DMCA takedowns', 'Asset monitoring', 'Platform removal']],
    ['id' => 'social', 'icon' => 'fa-hashtag', 'title' => 'Social Media Management', 'query' => 'Social Media Management',
     'desc' => 'Strategic content planning, daily publishing, audience engagement and performance analysis. We build a strong brand voice and turn followers into a loyal community.',
     'tags' => ['Content calendar', 'Community', 'Reporting']],
    ['id' => 'digital', 'icon' => 'fa-bullhorn', 'title' => 'Digital Marketing', 'query' => 'Digital Marketing',
     'desc' => 'A data-driven approach across SEO, PPC, content and email. We analyse market trends to craft targeted, high-converting strategies that increase leads and ROI.',
     'tags' => ['Paid media', 'Funnels', 'Analytics']],
    ['id' => 'web', 'icon' => 'fa-laptop-code', 'title' => 'Web & Development', 'query' => 'Web & Development',
     'desc' => 'From informational websites to complex e-commerce platforms. We deliver visually stunning, responsive and secure digital products tailored to your business.',
     'tags' => ['Websites', 'E-commerce', 'Apps & APIs']],
    ['id' => 'recovery', 'icon' => 'fa-arrows-rotate', 'title' => 'Recovery & Support', 'query' => 'Recovery & Support',
     'desc' => 'Digital crisis assistance for compromised accounts, platform suspensions and reputation damage. Our team implements strategic recovery protocols to restore your assets.',
     'tags' => ['Account recovery', 'Reputation', 'Crisis response']],
    ['id' => 'video', 'icon' => 'fa-clapperboard', 'title' => 'Video Production', 'query' => 'Video Production',
     'desc' => 'The full creative process — concept development, scriptwriting, filming, post-production and final delivery — tailored to captivate your audience.',
     'tags' => ['Brand films', 'Social video', 'Post-production']],
    ['id' => 'marketing', 'icon' => 'fa-chart-line', 'title' => 'Marketing & SEO', 'query' => 'Marketing & SEO',
     'desc' => 'Drive organic growth with proven white-hat SEO. We optimise your presence to rank higher, attract targeted traffic and convert visitors into loyal customers.',
     'tags' => ['Technical SEO', 'Content', 'Link building']],
    ['id' => 'creative', 'icon' => 'fa-pen-nib', 'title' => 'Creative & Production', 'query' => 'Creative & Production',
     'desc' => 'Premium design, branding and visual storytelling. From logo design and UI/UX to complete identity guidelines, we make your brand feel as good as it is.',
     'tags' => ['Brand identity', 'UI/UX', 'Design systems']],
    ['id' => 'business-support', 'icon' => 'fa-briefcase', 'title' => 'Business Support', 'query' => 'Business Support',
     'desc' => 'Administrative, financial and strategic support — virtual assistants, documentation management and process optimisation for your back office.',
     'tags' => ['Virtual assistants', 'Operations', 'Process design']],
    ['id' => 'company-registration', 'icon' => 'fa-file-signature', 'title' => 'Company Registration', 'query' => 'Company Registration',
     'desc' => 'End-to-end guidance through choosing a legal structure, filing documentation with authorities and securing the licences your venture needs.',
     'tags' => ['Incorporation', 'Compliance', 'Licensing']],
    ['id' => 'movie-pr', 'icon' => 'fa-film', 'title' => 'Movie PR', 'query' => 'Movie PR',
     'desc' => 'Strategic public relations for films — media outreach, press release distribution, red-carpet events and crisis communication that builds real buzz.',
     'tags' => ['Press & media', 'Launch events', 'Crisis PR']],
    ['id' => 'media-distribution', 'icon' => 'fa-broadcast-tower', 'title' => 'Media Distribution', 'query' => 'Media Distribution', 'external' => 'https://music.hypecrews.com',
     'desc' => 'Efficient delivery of your music and video to a global audience. We manage formatting, delivery to major streaming platforms, metadata and royalty collection.',
     'tags' => ['Spotify & Apple', 'YouTube', 'Royalties']],
    ['id' => 'artist-management', 'icon' => 'fa-microphone-lines', 'title' => 'Artist Management', 'query' => 'Artist Management',
     'desc' => 'End-to-end management for emerging and established artists — bookings, contract negotiations, brand partnerships, tour logistics and career strategy.',
     'tags' => ['Bookings', 'Contracts', 'Career strategy']],
];

$faqs = [
    ['How quickly can Hypecrews start?', 'Most engagements begin within 5–7 business days of the kick-off call. Urgent copyright takedowns and account recovery cases can be prioritised within 24–48 hours.'],
    ['Do you work with clients outside India?', 'Yes. We work with brands, labels and creators across India, the UK, US, UAE and Southeast Asia. All communication and delivery is fully remote-friendly.'],
    ['Can I combine multiple services?', 'Absolutely. Most clients combine brand, web and growth services. We build a single roadmap and one accountable team rather than selling disconnected deliverables.'],
    ['How is pricing decided?', 'After a discovery call we scope the work and quote a fixed project fee or a monthly retainer. You always get a clear breakdown before anything starts.'],
];
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="page-hero">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 300px; height: 300px; top: 2%; right: -6%;"></div>
    <div class="orb orb-2" style="width: 220px; height: 220px; bottom: -4%; left: 4%;"></div>

    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-[1.12fr_.88fr] gap-14 items-center">
            <div>
                <div class="chip" data-aos="fade-up"><i class="fas fa-layer-group" style="color: var(--blue);"></i> 13 specialised capabilities</div>
                <h1 class="display-1 text-balance" style="margin-top: 22px;" data-aos="fade-up" data-aos-delay="60">
                    Premium services for<br><span class="gradient-text">digital excellence.</span>
                </h1>
                <p class="lead" style="margin-top: 20px;" data-aos="fade-up" data-aos-delay="120">
                    One senior team covering brand, product, protection and growth. Explore how we help ambitious businesses build a presence that performs as well as it looks.
                </p>
                <div class="flex flex-wrap gap-3" style="margin-top: 30px;" data-aos="fade-up" data-aos-delay="180">
                    <a href="#capabilities" class="btn btn-primary btn-lg">Explore services <i class="fas fa-arrow-down"></i></a>
                    <a href="/contact.php" class="btn btn-outline btn-lg">Book a strategy call</a>
                </div>
                <div class="flex flex-wrap items-center gap-8" style="margin-top: 38px;" data-aos="fade-up" data-aos-delay="240">
                    <div class="hero-stat"><strong>1,000+</strong><small>Projects delivered</small></div>
                    <div class="hero-stat"><strong>98%</strong><small>Client success rate</small></div>
                    <div class="hero-stat"><strong>4.9/5</strong><small>Average rating</small></div>
                </div>
            </div>

            <div data-aos="fade-left" data-aos-delay="120">
                <div class="card-dark" style="border-radius: 28px; padding: 30px; position: relative; overflow: hidden;">
                    <div class="arc-lines"></div>
                    <div class="relative z-1">
                        <div class="flex items-center justify-between" style="margin-bottom: 22px;">
                            <div class="flex items-center gap-3">
                                <div class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.14); border-color: rgba(96,193,229,.3); color: var(--sky);"><i class="fas fa-compass"></i></div>
                                <div><p style="font-size: 12px; font-weight: 800; color: #fff;">Service snapshot</p><p style="font-size: 10px; color: rgba(255,255,255,.5);">What clients ask for most</p></div>
                            </div>
                            <span class="badge" style="background: rgba(96,193,229,.16); color: var(--ice); border-color: rgba(96,193,229,.3);">2026</span>
                        </div>

                        <?php $bars = [['Brand & identity', '88%'], ['Social & content', '76%'], ['Web & product', '82%'], ['Protection & recovery', '94%']];
                        foreach ($bars as $b): ?>
                        <div style="margin-bottom: 15px;">
                            <div class="flex items-center justify-between" style="margin-bottom: 6px;"><span style="font-size: 11px; font-weight: 700; color: rgba(255,255,255,.72);"><?php echo $b[0]; ?></span><span style="font-size: 10px; font-weight: 800; color: var(--ice);"><?php echo $b[1]; ?></span></div>
                            <div style="height: 7px; border-radius: 99px; background: rgba(255,255,255,.1); overflow: hidden;"><div style="width: <?php echo $b[1]; ?>; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--sky), var(--ice));"></div></div>
                        </div>
                        <?php endforeach; ?>

                        <div style="margin-top: 22px; padding-top: 18px; border-top: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <p style="font-size: 10.5px; color: rgba(255,255,255,.55); max-width: 180px;">Not sure what you need? We’ll map it in 30 minutes.</p>
                            <a href="/contact.php" class="btn btn-sky btn-sm">Talk to us</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================= QUICK NAV -->
<section style="background: #fff; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); padding: 18px 0;">
    <div class="shell shell-wide">
        <div class="flex items-center gap-2" style="overflow-x: auto; scrollbar-width: none;" data-aos="fade-up">
            <span style="font-size: 10px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: var(--muted-2); white-space: nowrap; margin-right: 10px;">Jump to</span>
            <?php foreach ($services as $svc): ?>
            <a href="#<?php echo $svc['id']; ?>" class="chip" style="white-space: nowrap; font-size: 11px;"><?php echo $svc['title']; ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================================================== CAPABILITIES -->
<section class="section" id="capabilities" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Core capabilities</span>
            <h2 class="display-2">Everything your brand needs, under one roof.</h2>
            <p class="lead">Thirteen focused services, one accountable team. Pick a single engagement or combine them into a complete brand and growth system.</p>
        </div>

        <!-- Featured -->
        <article id="copyright" class="card" style="padding: clamp(26px, 4vw, 46px); background: linear-gradient(135deg, #fff 30%, #eef8fd); margin-bottom: 22px;" data-aos="fade-up">
            <div class="grid lg:grid-cols-[1.25fr_.75fr] gap-10 items-center">
                <div>
                    <div class="flex items-center gap-4">
                        <div class="icon-tile icon-tile-lg"><i class="fas fa-copyright"></i></div>
                        <div><span class="badge badge-sky">Most requested</span><h3 style="font-size: clamp(24px, 3vw, 34px); margin-top: 8px;">Copyright Protection</h3></div>
                    </div>
                    <p style="font-size: 14.5px; color: var(--muted); line-height: 1.85; margin-top: 18px; max-width: 62ch;"><?php echo $services[0]['desc']; ?></p>
                    <div class="flex flex-wrap gap-2" style="margin-top: 18px;">
                        <?php foreach ($services[0]['tags'] as $tag): ?><span class="chip" style="font-size: 11px;"><i class="fas fa-check" style="color: #15805a; font-size: 9px;"></i><?php echo $tag; ?></span><?php endforeach; ?>
                    </div>
                    <a href="service_query.php?service=<?php echo urlencode($services[0]['query']); ?>" class="btn btn-primary" style="margin-top: 24px;">Protect your work <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="card" style="padding: 22px; border-radius: 18px; box-shadow: var(--shadow-sm);">
                    <div class="flex items-center justify-between" style="margin-bottom: 16px;"><span class="badge badge-sky"><span style="width:6px;height:6px;border-radius:99px;background:#15805a;display:inline-block;"></span> Live scan</span><i class="fas fa-shield-halved" style="color: var(--sky);"></i></div>
                    <div style="height: 9px; border-radius: 99px; background: #e9f3f9; overflow: hidden; margin-bottom: 9px;"><div style="width: 86%; height: 100%; border-radius: 99px; background: linear-gradient(90deg, var(--blue), var(--sky));"></div></div>
                    <p style="font-size: 10.5px; color: var(--muted-2); font-weight: 700;">340 assets monitored · 98% cleared</p>
                    <div style="height: 1px; background: var(--line); margin: 16px 0;"></div>
                    <div class="flex items-center justify-between" style="font-size: 10.5px; color: var(--muted);"><span>Takedowns issued</span><strong style="color: var(--navy);">1,240+</strong></div>
                    <div class="flex items-center justify-between" style="font-size: 10.5px; color: var(--muted); margin-top: 8px;"><span>Avg. removal time</span><strong style="color: var(--navy);">48 hours</strong></div>
                    <div class="flex items-center gap-2" style="margin-top: 16px;"><span style="width: 7px; height: 7px; border-radius: 99px; background: #1a9f6e;"></span><span style="font-size: 10px; font-weight: 800; color: #15805a;">All systems protected</span></div>
                </div>
            </div>
        </article>

        <!-- Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php foreach (array_slice($services, 1) as $i => $svc): ?>
            <article id="<?php echo $svc['id']; ?>" class="card card-hover card-pad" style="display: flex; flex-direction: column;" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 60; ?>">
                <div class="flex items-start justify-between">
                    <div class="icon-tile"><i class="fas <?php echo $svc['icon']; ?>"></i></div>
                    <span style="font-family: var(--font-head); font-size: 12px; font-weight: 800; color: var(--muted-2);"><?php echo str_pad($i + 2, 2, '0', STR_PAD_LEFT); ?></span>
                </div>
                <h3 style="font-size: 19px; margin-top: 20px;"><?php echo $svc['title']; ?></h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 9px; flex-grow: 1;"><?php echo $svc['desc']; ?></p>
                <div class="flex flex-wrap gap-2" style="margin-top: 16px;">
                    <?php foreach ($svc['tags'] as $tag): ?><span style="font-size: 10px; font-weight: 700; color: var(--blue); background: #eef7fd; border: 1px solid #d9edf8; border-radius: 99px; padding: 4px 9px;"><?php echo $tag; ?></span><?php endforeach; ?>
                </div>
                <?php if (!empty($svc['external'])): ?>
                <a href="<?php echo $svc['external']; ?>" target="_blank" rel="noopener" class="link-arrow" style="margin-top: 20px; font-size: 11px;">Explore platform <i class="fas fa-arrow-up-right-from-square"></i></a>
                <?php else: ?>
                <a href="service_query.php?service=<?php echo urlencode($svc['query']); ?>" class="link-arrow" style="margin-top: 20px; font-size: 11px;">Learn more <i class="fas fa-arrow-right"></i></a>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ======================================================== STATS -->
<section class="section-dark stat-band" style="padding: 58px 0;">
    <div class="arc-lines"></div>
    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-4 gap-8 items-end">
            <div data-aos="fade-right">
                <span class="eyebrow eyebrow-light">Measured results</span>
                <h2 style="font-size: clamp(1.6rem, 2.5vw, 2.2rem); margin-top: 12px;">Quiet confidence.<br><span class="gradient-text-light">Real outcomes.</span></h2>
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

<!-- ======================================================= PROCESS -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">How engagements work</span>
            <h2 class="display-2">Simple to start. Clear to run.</h2>
            <p class="lead">Whether it’s a single takedown or a full rebrand, the same calm, predictable process applies.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 steps">
            <div class="step" data-aos="fade-up"><div class="step-num">01</div><h3>Discover</h3><p>We audit your brand, assets and goals, then agree on the sharpest opportunity to chase.</p></div>
            <div class="step" data-aos="fade-up" data-aos-delay="80"><div class="step-num">02</div><h3>Scope</h3><p>You receive a clear roadmap, fixed quote and timeline — no vague retainers.</p></div>
            <div class="step" data-aos="fade-up" data-aos-delay="160"><div class="step-num">03</div><h3>Deliver</h3><p>Our senior team executes in focused sprints with weekly visibility and review points.</p></div>
            <div class="step" data-aos="fade-up" data-aos-delay="240"><div class="step-num">04</div><h3>Compound</h3><p>Measure, optimise and protect. The work keeps paying back long after launch.</p></div>
        </div>
    </div>
</section>

<!-- ========================================================== FAQ -->
<section class="section" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-[.85fr_1.15fr] gap-14 items-start">
            <div data-aos="fade-right">
                <span class="eyebrow">Questions</span>
                <h2 class="display-2" style="margin-top: 16px;">Good to know<br><span class="gradient-text">before we start.</span></h2>
                <p class="lead" style="margin-top: 18px;">Still unsure which service fits? Send us a note and we’ll point you in the right direction — even if it’s not us.</p>
                <a href="/contact.php" class="btn btn-primary" style="margin-top: 24px;">Ask a question <i class="fas fa-arrow-right"></i></a>
            </div>
            <div data-aos="fade-left">
                <?php foreach ($faqs as $i => $faq): ?>
                <div class="accordion" data-open="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                    <button type="button" class="accordion-trigger"><?php echo $faq[0]; ?><i class="fas fa-plus"></i></button>
                    <div class="accordion-panel"><div class="accordion-panel-inner"><?php echo $faq[1]; ?></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===================================================== FINAL CTA -->
<section class="section-dark" style="padding: 92px 0; position: relative; overflow: hidden;">
    <div class="hero-mesh-dark"></div>
    <div class="orb orb-1" style="width: 260px; height: 260px; top: -10%; right: 8%; opacity: .18;"></div>
    <div class="shell shell-wide relative z-1 text-center" data-aos="zoom-in">
        <span class="eyebrow eyebrow-light eyebrow-center">Your move</span>
        <h2 class="display-2" style="margin-top: 16px; color: #fff;">Let’s build the brand<br><span class="gradient-text-light">you keep imagining.</span></h2>
        <p class="lead lead-light" style="margin: 20px auto 32px; text-align: center;">Book a free strategy call. We’ll review your current presence and show you the three highest-impact moves for the next quarter.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/contact.php" class="btn btn-sky btn-lg">Book a strategy call <i class="fas fa-arrow-right"></i></a>
            <a href="/softwares.php" class="btn btn-outline-light btn-lg">Explore softwares</a>
        </div>
        <p style="margin-top: 22px; font-size: 11.5px; color: rgba(255,255,255,.45);"><i class="fas fa-lock" style="margin-right: 6px;"></i>No pressure, no obligation. Just useful clarity.</p>
    </div>
</section>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
