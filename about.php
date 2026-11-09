<?php
session_start();
$pageTitle = "About Us — Hypecrews";
$pageDescription = "Meet Hypecrews, a passionate digital brand company from Assam, India. Since 2022 we have delivered web development, marketing, creative and copyright protection solutions.";
$pageKeywords = "about hypecrews, digital agency team, assam india agency, digital marketing company, web development agency, Hypecrews Software Private Limited";
$canonical = "https://hypecrews.com/about.php";
$jsonLd = '{
  "@context": "https://schema.org", "@type": "AboutPage", "name": "About Hypecrews",
  "url": "https://hypecrews.com/about.php",
  "mainEntity": { "@type": "Organization", "name": "Hypecrews", "legalName": "Hypecrews Software Private Limited", "url": "https://hypecrews.com", "logo": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png", "foundingDate": "2022", "founder": { "@type": "Person", "name": "Dheeraj Jyoti Saikia", "jobTitle": "CEO & Founder" }, "address": { "@type": "PostalAddress", "addressLocality": "Golaghat", "addressRegion": "Assam", "addressCountry": "IN" }, "numberOfEmployees": { "@type": "QuantitativeValue", "minValue": 10 } }
}';
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="page-hero">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 300px; height: 300px; top: 0; left: -8%;"></div>
    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div>
                <div class="chip" data-aos="fade-up"><i class="fas fa-location-dot" style="color: var(--blue);"></i> Golaghat, Assam · Since 2022</div>
                <h1 class="display-1 text-balance" style="margin-top: 22px;" data-aos="fade-up" data-aos-delay="60">
                    We create, develop<br>and <span class="gradient-text">protect</span> brands.
                </h1>
                <p class="lead" style="margin-top: 20px;" data-aos="fade-up" data-aos-delay="120">
                    Hypecrews is a premium digital brand company built at the intersection of entertainment and technology. From identity to deployment, we help ambitious businesses look sharp and grow with confidence.
                </p>
                <div class="flex flex-wrap gap-3" style="margin-top: 30px;" data-aos="fade-up" data-aos-delay="180">
                    <a href="/contact.php" class="btn btn-primary btn-lg">Work with us <i class="fas fa-arrow-right"></i></a>
                    <a href="/careers.php" class="btn btn-outline btn-lg">Join the team</a>
                </div>
            </div>
            <div data-aos="fade-left" data-aos-delay="120">
                <div style="border-radius: 28px; overflow: hidden; border: 1px solid var(--line); box-shadow: var(--shadow-md); position: relative;">
                    <img src="/graphics/516as4d8a16s5d64as.jpg" alt="The Hypecrews team at work" style="width: 100%; height: 430px; object-fit: cover;">
                    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(11,28,47,.8));"></div>
                    <div style="position: absolute; left: 26px; right: 26px; bottom: 24px; color: #fff;">
                        <p style="font-size: 10px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: var(--ice);">One senior team</p>
                        <p style="font-family: var(--font-head); font-size: 21px; font-weight: 700; color: #fff; margin-top: 5px;">Brand, product, growth and protection.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================== STORY -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-[.9fr_1.1fr] gap-14 items-center">
            <div data-aos="fade-right">
                <span class="eyebrow">Our story</span>
                <h2 class="display-2" style="margin-top: 16px;">From Guwahati to<br><span class="gradient-text">pan-India.</span></h2>
                <div class="grid grid-cols-2 gap-4" style="margin-top: 28px;">
                    <div class="card card-pad" style="padding: 22px;">
                        <strong style="display:block;font-family:var(--font-head);font-size:30px;color:var(--navy);">1000+</strong>
                        <p style="font-size: 11.5px; font-weight: 700; color: var(--muted);">Projects completed</p>
                    </div>
                    <div class="card card-pad" style="padding: 22px;">
                        <strong style="display:block;font-family:var(--font-head);font-size:30px;color:var(--navy);">700+</strong>
                        <p style="font-size: 11.5px; font-weight: 700; color: var(--muted);">Happy clients</p>
                    </div>
                </div>
            </div>
            <div data-aos="fade-left">
                <p class="lead">Established in 2022 in Guwahati, Assam, we have quickly evolved into a comprehensive digital solutions provider serving clients across the country.</p>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.85; margin-top: 16px;">We believe in bridging the gap between creativity, technology and security. Whether you are an artist looking for a spotlight, a business seeking a digital footprint, or a brand needing protection, our expert team delivers excellence with a personal touch.</p>
                <div class="flex flex-wrap gap-4" style="margin-top: 26px;">
                    <div class="flex items-center gap-3"><div class="icon-tile icon-tile-sm"><i class="fas fa-layer-group"></i></div><span style="font-size: 13px; font-weight: 700; color: var(--navy);">360° digital partner</span></div>
                    <div class="flex items-center gap-3"><div class="icon-tile icon-tile-sm"><i class="fas fa-shield-halved"></i></div><span style="font-size: 13px; font-weight: 700; color: var(--navy);">Protection built in</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================================================== WHAT WE DO -->
<section class="section" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">What we do</span>
            <h2 class="display-2">A 360-degree approach.</h2>
            <p class="lead">We operate at the intersection of entertainment and technology — covering everything from brand identity to full-scale digital deployment.</p>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            <article class="card card-hover card-pad md:col-span-2" style="background: linear-gradient(135deg, #fff 30%, #eef8fd);" data-aos="fade-up">
                <div class="icon-tile icon-tile-lg"><i class="fas fa-layer-group"></i></div>
                <h3 style="font-size: 25px; margin-top: 20px;">Creative, development and growth</h3>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.8; margin-top: 10px; max-width: 56ch;">One connected team across strategy, design, engineering and marketing — so nothing gets lost between agencies.</p>
                <div class="grid sm:grid-cols-3 gap-3" style="margin-top: 22px;">
                    <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;"><p style="font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);">Digital growth</p><p style="font-size:12.5px;font-weight:700;color:var(--navy);margin-top:4px;">Marketing & social</p></div>
                    <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;"><p style="font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);">Creative</p><p style="font-size:12.5px;font-weight:700;color:var(--navy);margin-top:4px;">Video, PR & design</p></div>
                    <div style="background:#fff;border:1px solid var(--line);border-radius:14px;padding:14px;"><p style="font-size:10px;font-weight:800;letter-spacing:.1em;text-transform:uppercase;color:var(--muted-2);">Development</p><p style="font-size:12.5px;font-weight:700;color:var(--navy);margin-top:4px;">Web & app solutions</p></div>
                </div>
            </article>
            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="80">
                <div class="icon-tile"><i class="fas fa-shield-halved"></i></div>
                <h3 style="font-size: 20px; margin-top: 20px;">Security</h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 9px;">In the digital age, your assets are vulnerable. We stand apart by offering specialised protection alongside creation.</p>
                <ul style="margin-top: 16px; display: grid; gap: 9px;">
                    <li class="flex items-center gap-2" style="font-size: 12.5px; color: var(--muted);"><i class="fas fa-check" style="color:#15805a;font-size:10px;"></i> Content protection</li>
                    <li class="flex items-center gap-2" style="font-size: 12.5px; color: var(--muted);"><i class="fas fa-check" style="color:#15805a;font-size:10px;"></i> Account recovery</li>
                    <li class="flex items-center gap-2" style="font-size: 12.5px; color: var(--muted);"><i class="fas fa-check" style="color:#15805a;font-size:10px;"></i> Reputation defence</li>
                </ul>
            </article>
        </div>
    </div>
</section>

<!-- ================================================== IDENTITY BAND -->
<section class="section-dark" style="padding: 76px 0; position: relative; overflow: hidden;">
    <div class="hero-mesh-dark"></div>
    <div class="arc-lines"></div>
    <div class="shell shell-wide relative z-1 text-center" data-aos="zoom-in">
        <div class="icon-tile icon-tile-lg" style="margin: 0 auto 20px; background: rgba(96,193,229,.12); border-color: rgba(96,193,229,.28); color: var(--sky);"><i class="fas fa-crown"></i></div>
        <p style="font-family: var(--font-head); font-size: clamp(1.5rem, 3.2vw, 2.6rem); font-weight: 700; color: #fff; line-height: 1.2;">We are Hypecrews Software Private Limited</p>
        <p style="font-size: 13px; letter-spacing: .18em; color: var(--ice); margin-top: 12px;">CIN: U73100AS2026PTC029838</p>
        <div class="flex flex-wrap justify-center gap-3" style="margin-top: 28px;">
            <span class="chip chip-light"><i class="fas fa-paint-brush" style="color: var(--sky);"></i> We create</span>
            <span class="chip chip-light"><i class="fas fa-code" style="color: var(--sky);"></i> We develop</span>
            <span class="chip chip-light"><i class="fas fa-shield-halved" style="color: var(--sky);"></i> We protect</span>
        </div>
    </div>
</section>

<!-- ================================================= MISSION/VISION -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="grid md:grid-cols-2 gap-6">
            <article class="card card-hover card-pad" style="padding: clamp(26px, 4vw, 40px);" data-aos="fade-up">
                <div class="icon-tile icon-tile-lg"><i class="fas fa-bullseye"></i></div>
                <h3 style="font-size: 27px; margin-top: 20px;">The Mission</h3>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.85; margin-top: 12px;">To empower businesses of all sizes with cutting-edge digital strategies that drive growth, enhance customer engagement and deliver measurable results in an ever-evolving digital landscape.</p>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.85; margin-top: 12px;">We believe in building long-term partnerships based on trust, transparency and exceptional service that exceeds expectations.</p>
            </article>
            <article class="card card-hover card-pad" style="padding: clamp(26px, 4vw, 40px);" data-aos="fade-up" data-aos-delay="80">
                <div class="icon-tile icon-tile-lg"><i class="fas fa-eye"></i></div>
                <h3 style="font-size: 27px; margin-top: 20px;">The Vision</h3>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.85; margin-top: 12px;">To be the leading digital agency that transforms businesses through innovation, creativity and technology, setting new standards for excellence in the industry.</p>
                <p style="font-size: 14px; color: var(--muted); line-height: 1.85; margin-top: 12px;">We envision a future where every business, regardless of size, has access to world-class digital solutions that let them compete globally.</p>
            </article>
        </div>
    </div>
</section>

<!-- ==================================================== CORE VALUES -->
<section class="section" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Core values</span>
            <h2 class="display-2">The principles behind every decision.</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <?php
            $values = [
                ['icon' => 'fa-lightbulb', 'title' => 'Innovation', 'desc' => 'We constantly explore new ideas and technologies to deliver groundbreaking solutions.'],
                ['icon' => 'fa-handshake', 'title' => 'Integrity', 'desc' => 'We conduct business with honesty and transparency, building trust.'],
                ['icon' => 'fa-medal', 'title' => 'Excellence', 'desc' => 'We strive for perfection in everything we do, exceeding expectations.'],
                ['icon' => 'fa-users', 'title' => 'Collaboration', 'desc' => 'We believe in the power of teamwork to achieve extraordinary results.'],
                ['icon' => 'fa-heart', 'title' => 'Passion', 'desc' => 'We are driven by a genuine love for making a positive impact.'],
                ['icon' => 'fa-chart-line', 'title' => 'Results', 'desc' => 'We focus on delivering measurable outcomes for our clients.'],
            ];
            foreach ($values as $i => $value): ?>
            <article class="card card-hover card-pad" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 60; ?>">
                <div class="icon-tile"><i class="fas <?php echo $value['icon']; ?>"></i></div>
                <h3 style="font-size: 19px; margin-top: 20px;"><?php echo $value['title']; ?></h3>
                <p style="font-size: 13px; color: var(--muted); line-height: 1.75; margin-top: 8px;"><?php echo $value['desc']; ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===================================================== LEADERSHIP -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Leadership</span>
            <h2 class="display-2">The people behind the work.</h2>
        </div>
        <div class="card" style="max-width: 760px; margin-inline: auto; padding: clamp(24px, 4vw, 38px);" data-aos="zoom-in">
            <div class="grid sm:grid-cols-[180px_1fr] gap-8 items-center">
                <div style="width: 180px; height: 180px; border-radius: 24px; overflow: hidden; border: 1px solid var(--line); box-shadow: var(--shadow-sm); margin-inline: auto;">
                    <img src="/graphics/our-team/co-founder.png" alt="Dheeraj Jyoti Saikia" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div>
                    <span class="badge badge-sky">Founder</span>
                    <h3 style="font-size: 26px; margin-top: 10px;">Dheeraj Jyoti Saikia</h3>
                    <p style="font-size: 11px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--blue); margin-top: 4px;">CEO &amp; Founder</p>
                    <p style="font-size: 13.5px; color: var(--muted); line-height: 1.8; margin-top: 12px;">Visionary leader driving innovation and growth across every Hypecrews engagement.</p>
                    <div class="flex gap-2" style="margin-top: 16px;">
                        <a href="https://www.linkedin.com/in/dheerajjyotisaikia/" target="_blank" rel="noopener" class="icon-tile icon-tile-sm" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="icon-tile icon-tile-sm" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    </div>
                </div>
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
        <h2 class="display-2" style="margin-top: 16px; color: #fff;">Ready to work<br><span class="gradient-text-light">with us?</span></h2>
        <p class="lead lead-light" style="margin: 20px auto 32px; text-align: center;">Join the many businesses that have transformed their digital presence with Hypecrews.</p>
        <div class="flex flex-wrap justify-center gap-3">
            <a href="/contact.php" class="btn btn-sky btn-lg">Start a conversation <i class="fas fa-arrow-right"></i></a>
            <a href="/services.php" class="btn btn-outline-light btn-lg">Explore services</a>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
