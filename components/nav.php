<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$firstName = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$firstInitial = $firstName ? strtoupper(substr($firstName, 0, 1)) : 'U';

$navServices = [
    ['href' => '/services.php#copyright', 'icon' => 'fa-copyright', 'title' => 'Copyright Protection', 'desc' => 'Takedowns & asset defence'],
    ['href' => '/services.php#social', 'icon' => 'fa-hashtag', 'title' => 'Social Media', 'desc' => 'Content, community & growth'],
    ['href' => '/services.php#digital', 'icon' => 'fa-bullhorn', 'title' => 'Digital Marketing', 'desc' => 'Paid media & performance'],
    ['href' => '/services.php#web', 'icon' => 'fa-laptop-code', 'title' => 'Web & Development', 'desc' => 'Sites, apps & platforms'],
    ['href' => '/services.php#recovery', 'icon' => 'fa-arrows-rotate', 'title' => 'Recovery & Support', 'desc' => 'Accounts, reputation, crisis'],
    ['href' => '/services.php#video', 'icon' => 'fa-clapperboard', 'title' => 'Video Production', 'desc' => 'Cinematic brand content'],
    ['href' => '/services.php#marketing', 'icon' => 'fa-chart-line', 'title' => 'Marketing & SEO', 'desc' => 'Organic visibility engine'],
    ['href' => '/services.php#creative', 'icon' => 'fa-pen-nib', 'title' => 'Creative & Brand', 'desc' => 'Identity, design & UI/UX'],
    ['href' => '/services.php#business-support', 'icon' => 'fa-briefcase', 'title' => 'Business Support', 'desc' => 'Operations & back office'],
    ['href' => '/services.php#company-registration', 'icon' => 'fa-file-signature', 'title' => 'Company Registration', 'desc' => 'Legal setup, end to end'],
    ['href' => '/services.php#movie-pr', 'icon' => 'fa-film', 'title' => 'Movie PR', 'desc' => 'Film publicity & press'],
    ['href' => '/services.php#artist-management', 'icon' => 'fa-microphone-lines', 'title' => 'Artist Management', 'desc' => 'Careers, bookings, deals'],
];
?>

<header class="site-header" id="siteHeader">
    <div class="shell shell-wide nav-inner">
        <a href="/index.php" class="brand-logo" aria-label="Hypecrews home">
            <img src="/graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews">
        </a>

        <nav class="nav-desktop" aria-label="Primary">
            <a href="/index.php" class="nav-link" data-nav-page="index.php,">Home</a>

            <div class="nav-dropdown">
                <button type="button" class="nav-link" aria-haspopup="true" aria-expanded="false">
                    Services <i class="fas fa-chevron-down"></i>
                </button>
                <div class="mega-menu">
                    <div class="mega-grid">
                        <?php foreach ($navServices as $svc): ?>
                        <a href="<?php echo $svc['href']; ?>" class="mega-item">
                            <span class="icon-tile"><i class="fas <?php echo $svc['icon']; ?>"></i></span>
                            <span>
                                <h4><?php echo $svc['title']; ?></h4>
                                <p><?php echo $svc['desc']; ?></p>
                            </span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="mega-footer">
                        <div>
                            <strong>Not sure where to start?</strong>
                            <p>Book a 30-minute strategy call with our team.</p>
                        </div>
                        <a href="/contact.php" class="btn btn-sm btn-sky">Book a call <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <a href="/softwares.php" class="nav-link" data-nav-page="softwares.php,software_details.php">Softwares</a>
            <a href="/about.php" class="nav-link" data-nav-page="about.php">About</a>
            <a href="/careers.php" class="nav-link" data-nav-page="careers.php">Careers</a>
            <a href="/contact.php" class="nav-link" data-nav-page="contact.php">Contact</a>
        </nav>

        <div class="nav-actions">
            <?php if ($isLoggedIn): ?>
                <a href="/profile.php" class="nav-user">
                    <span class="nav-avatar"><?php echo htmlspecialchars($firstInitial); ?></span>
                    <span><?php echo htmlspecialchars($firstName ?: 'Account'); ?></span>
                </a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-sm btn-outline-light">Client Login</a>
            <?php endif; ?>
            <a href="/contact.php" class="btn btn-sm btn-sky">Start a Project <i class="fas fa-arrow-right"></i></a>
            <button type="button" class="nav-burger" id="mobileMenuBtn" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- Mobile drawer -->
<div class="mobile-drawer" id="mobileDrawer" aria-hidden="true">
    <div class="mobile-drawer-top">
        <a href="/index.php" aria-label="Hypecrews home">
            <img src="/graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews">
        </a>
        <button type="button" class="mobile-close" data-drawer-close aria-label="Close menu">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <nav aria-label="Mobile">
        <a href="/index.php" class="mobile-nav-link">Home <i class="fas fa-arrow-right"></i></a>
        <a href="/services.php" class="mobile-nav-link">Services <i class="fas fa-arrow-right"></i></a>
        <div class="mobile-services">
            <?php foreach ($navServices as $svc): ?>
            <a href="<?php echo $svc['href']; ?>"><i class="fas <?php echo $svc['icon']; ?>"></i> <?php echo $svc['title']; ?></a>
            <?php endforeach; ?>
        </div>
        <a href="/softwares.php" class="mobile-nav-link">Softwares <i class="fas fa-arrow-right"></i></a>
        <a href="/about.php" class="mobile-nav-link">About <i class="fas fa-arrow-right"></i></a>
        <a href="/careers.php" class="mobile-nav-link">Careers <i class="fas fa-arrow-right"></i></a>
        <a href="/contact.php" class="mobile-nav-link">Contact <i class="fas fa-arrow-right"></i></a>
        <?php if ($isLoggedIn): ?>
        <a href="/track_orders.php" class="mobile-nav-link">My Orders <i class="fas fa-arrow-right"></i></a>
        <a href="/profile.php" class="mobile-nav-link">My Profile <i class="fas fa-arrow-right"></i></a>
        <?php else: ?>
        <a href="/login.php" class="mobile-nav-link">Client Login <i class="fas fa-arrow-right"></i></a>
        <a href="/register.php" class="mobile-nav-link">Create Account <i class="fas fa-arrow-right"></i></a>
        <?php endif; ?>
    </nav>

    <a href="/contact.php" class="btn btn-sky btn-block">Start a Project <i class="fas fa-arrow-right"></i></a>

    <div class="mobile-drawer-meta">
        <p>Golaghat, Assam, India</p>
        <p><a href="mailto:info@hypecrews.com">info@hypecrews.com</a> · <a href="tel:+913613243276">+91 361 324 3276</a></p>
    </div>
</div>

<div class="scroll-progress" aria-hidden="true"></div>
