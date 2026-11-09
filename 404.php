<?php
$pageTitle = "Page Not Found — Hypecrews";
$pageDescription = "Page not found — The page you're looking for doesn't exist or has been moved. Return to Hypecrews homepage to explore our digital services.";
$robots = "noindex, nofollow";
http_response_code(404);
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<section style="position: relative; min-height: 100vh; display: flex; align-items: center; padding: 150px 0 90px; background: linear-gradient(150deg, var(--navy-deep), var(--navy) 58%, #0a2c4e 100%); overflow: hidden;">
    <div class="arc-lines"></div>
    <div style="position: absolute; inset: 0; background: radial-gradient(circle at 78% 22%, rgba(96,193,229,.16), transparent 42%), radial-gradient(circle at 16% 82%, rgba(0,90,156,.34), transparent 46%); pointer-events: none;"></div>
    <div class="shell shell-wide relative z-1">
        <div style="max-width: 880px; margin: 0 auto; text-align: center;">
            <div style="position: relative; display: inline-block; margin-bottom: 8px;" data-aos="zoom-in">
                <span style="font-family: var(--font-head); font-weight: 800; font-size: clamp(6rem, 18vw, 13rem); line-height: .9; letter-spacing: -.05em; background: linear-gradient(160deg, #ffffff 8%, var(--ice) 42%, var(--sky) 68%, var(--blue) 100%); -webkit-background-clip: text; background-clip: text; color: transparent; display: block;">404</span>
                <span style="position: absolute; inset: 0; font-family: var(--font-head); font-weight: 800; font-size: clamp(6rem, 18vw, 13rem); line-height: .9; letter-spacing: -.05em; color: rgba(96,193,229,.16); filter: blur(28px); z-index: -1;">404</span>
            </div>

            <div data-aos="fade-up">
                <span class="chip chip-light" style="font-size: 11px; margin-bottom: 20px;"><i class="fas fa-compass"></i> Off the map</span>
                <h1 style="font-size: clamp(1.9rem, 4vw, 3rem); color: #fff; margin-top: 18px;">This page took a wrong turn.</h1>
                <p style="font-size: 14.5px; color: rgba(255,255,255,.62); line-height: 1.85; margin-top: 16px; max-width: 540px; margin-inline: auto;">The link may be broken, the page may have moved, or it never existed. Let’s get you back to something beautiful.</p>

                <div class="flex flex-wrap items-center justify-center gap-3" style="margin-top: 30px;">
                    <a href="/index.php" class="btn btn-sky btn-lg">Back to home <i class="fas fa-arrow-right"></i></a>
                    <a href="/contact.php" class="btn btn-ghost-light btn-lg"><i class="fas fa-envelope"></i> Contact us</a>
                </div>
            </div>

            <div style="margin-top: clamp(38px, 6vw, 64px);" data-aos="fade-up" data-aos-delay="120">
                <p style="font-size: 10px; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 18px;">Popular destinations</p>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    <a href="/services.php" class="card" style="padding: 20px; text-align: left; background: rgba(255,255,255,.045); border-color: rgba(255,255,255,.1);">
                        <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.13); border-color: rgba(96,193,229,.26); color: var(--sky); margin-bottom: 12px;"><i class="fas fa-layer-group"></i></span>
                        <strong style="display: block; font-size: 13px; color: #fff;">Our services</strong>
                        <small style="display: block; font-size: 11px; color: rgba(255,255,255,.5); margin-top: 3px;">13 capabilities, one team</small>
                    </a>
                    <a href="/about.php" class="card" style="padding: 20px; text-align: left; background: rgba(255,255,255,.045); border-color: rgba(255,255,255,.1);">
                        <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.13); border-color: rgba(96,193,229,.26); color: var(--sky); margin-bottom: 12px;"><i class="fas fa-users"></i></span>
                        <strong style="display: block; font-size: 13px; color: #fff;">About us</strong>
                        <small style="display: block; font-size: 11px; color: rgba(255,255,255,.5); margin-top: 3px;">The story behind the brand</small>
                    </a>
                    <a href="/softwares.php" class="card" style="padding: 20px; text-align: left; background: rgba(255,255,255,.045); border-color: rgba(255,255,255,.1);">
                        <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.13); border-color: rgba(96,193,229,.26); color: var(--sky); margin-bottom: 12px;"><i class="fas fa-desktop"></i></span>
                        <strong style="display: block; font-size: 13px; color: #fff;">Softwares</strong>
                        <small style="display: block; font-size: 11px; color: rgba(255,255,255,.5); margin-top: 3px;">Tools we’ve built</small>
                    </a>
                    <a href="/contact.php" class="card" style="padding: 20px; text-align: left; background: rgba(255,255,255,.045); border-color: rgba(255,255,255,.1);">
                        <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.13); border-color: rgba(96,193,229,.26); color: var(--sky); margin-bottom: 12px;"><i class="fas fa-headset"></i></span>
                        <strong style="display: block; font-size: 13px; color: #fff;">Contact</strong>
                        <small style="display: block; font-size: 11px; color: rgba(255,255,255,.5); margin-top: 3px;">We reply within hours</small>
                    </a>
                </div>
            </div>

            <p style="font-size: 11px; color: rgba(255,255,255,.35); margin-top: 36px;">Typed the URL manually? Double-check the spelling, or <a href="/index.php" style="color: var(--ice); font-weight: 700;">start from the homepage</a>.</p>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
</body>
</html>
