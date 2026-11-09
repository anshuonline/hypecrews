<?php
session_start();
$pageTitle = "Softwares & Applications — Hypecrews";
require_once 'config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM softwares ORDER BY created_at DESC");
    $stmt->execute();
    $softwares = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching softwares.";
    $softwares = [];
}
$pageDescription = "Explore Hypecrews' collection of powerful, custom-built software products and applications designed to simplify workflows and boost productivity.";
$canonical = "https://hypecrews.com/softwares.php";
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="page-hero-sm">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 280px; height: 280px; top: 0; right: -4%;"></div>
    <div class="shell shell-wide relative z-1">
        <div class="section-head section-head-center" style="margin-bottom: 0;" data-aos="fade-up">
            <div class="chip" style="margin-bottom: 18px;"><i class="fas fa-laptop-code" style="color: var(--blue);"></i> Built by Hypecrews</div>
            <h1 class="display-1">Softwares &amp;<br><span class="gradient-text">applications.</span></h1>
            <p class="lead">Powerful, custom-built products designed to simplify workflows and boost productivity — for teams, creators and growing businesses.</p>
        </div>
    </div>
</section>

<!-- ========================================================= CATALOG -->
<section class="section" style="padding-top: 56px; background: var(--mist);">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-6 section-head" style="max-width: none;">
            <div>
                <span class="eyebrow" data-aos="fade-up">Product catalog</span>
                <h2 class="display-2" style="margin-top: 14px; font-size: clamp(1.8rem, 3vw, 2.5rem);" data-aos="fade-up" data-aos-delay="50">Our software collection.</h2>
            </div>
            <?php if (!empty($softwares)): ?>
            <div class="flex flex-wrap gap-2" data-aos="fade-up">
                <button type="button" class="chip filter-btn is-active" data-filter="all" style="cursor: pointer; border-color: var(--blue); color: var(--blue);">All</button>
                <button type="button" class="chip filter-btn" data-filter="Windows" style="cursor: pointer;">Windows</button>
                <button type="button" class="chip filter-btn" data-filter="Mac/Apple" style="cursor: pointer;">macOS</button>
                <button type="button" class="chip filter-btn" data-filter="Android" style="cursor: pointer;">Android</button>
                <button type="button" class="chip filter-btn" data-filter="Web" style="cursor: pointer;">Web</button>
            </div>
            <?php endif; ?>
        </div>

        <?php if (empty($softwares)): ?>
            <div class="card" style="padding: 64px 26px; text-align: center;" data-aos="fade-up">
                <div class="icon-tile icon-tile-lg" style="margin: 0 auto 18px;"><i class="fas fa-laptop-code"></i></div>
                <h3 style="font-size: 24px;">No softwares yet</h3>
                <p style="font-size: 13.5px; color: var(--muted); margin-top: 8px;">We’re working on some amazing products. Check back soon!</p>
            </div>
        <?php else: ?>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6" id="softwareGrid">
                <?php foreach ($softwares as $index => $app): ?>
                <article class="card card-hover software-card" style="overflow: hidden; display: flex; flex-direction: column;" data-platform="<?php echo htmlspecialchars($app['platform']); ?>" data-aos="fade-up" data-aos-delay="<?php echo ($index % 3) * 70; ?>">
                    <div style="position: relative; height: 190px; overflow: hidden; background: linear-gradient(145deg, var(--navy), var(--blue));">
                        <?php if (!empty($app['banner_path'])): ?>
                            <img src="<?php echo htmlspecialchars($app['banner_path']); ?>" alt="<?php echo htmlspecialchars($app['name']); ?> banner" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;"><i class="fas fa-laptop-code" style="font-size: 40px; color: rgba(255,255,255,.28);"></i></div>
                        <?php endif; ?>
                        <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 35%, rgba(11,28,47,.55));"></div>
                        <span class="badge" style="position: absolute; top: 14px; left: 14px; background: rgba(255,255,255,.92); color: var(--navy);">v<?php echo htmlspecialchars($app['version']); ?></span>
                    </div>

                    <div style="padding: 0 24px 24px; display: flex; flex-direction: column; flex-grow: 1; position: relative;">
                        <div style="width: 68px; height: 68px; border-radius: 18px; background: #fff; border: 1px solid var(--line); box-shadow: var(--shadow-sm); overflow: hidden; margin-top: -34px; position: relative; z-index: 1;">
                            <?php if (!empty($app['logo_path'])): ?>
                                <img src="<?php echo htmlspecialchars($app['logo_path']); ?>" alt="<?php echo htmlspecialchars($app['name']); ?> logo" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--muted-2);"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </div>

                        <h3 style="font-size: 19px; margin-top: 12px;"><?php echo htmlspecialchars($app['name']); ?></h3>
                        <div class="flex flex-wrap items-center gap-2" style="margin-top: 8px;">
                            <?php
                            $plats = explode(', ', $app['platform']);
                            if (in_array('Windows', $plats)) echo '<span class="badge badge-slate"><i class="fab fa-windows"></i> Windows</span>';
                            if (in_array('Mac/Apple', $plats)) echo '<span class="badge badge-slate"><i class="fab fa-apple"></i> macOS</span>';
                            if (in_array('Android', $plats)) echo '<span class="badge badge-slate"><i class="fab fa-android"></i> Android</span>';
                            if (in_array('Web', $plats)) echo '<span class="badge badge-slate"><i class="fas fa-globe"></i> Web</span>';
                            ?>
                        </div>
                        <p style="font-size: 12.5px; color: var(--muted); line-height: 1.75; margin-top: 12px; flex-grow: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($app['description']); ?></p>
                        <a href="software_details.php?id=<?php echo $app['id']; ?>" class="btn btn-primary btn-block btn-sm" style="margin-top: 18px;">View details <i class="fas fa-arrow-right"></i></a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <p id="filterEmpty" class="hidden" style="text-align:center;color:var(--muted);font-size:14px;padding:40px 0;">No products match this platform yet.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================================= BENEFITS -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Why our products</span>
            <h2 class="display-2">Built with the same care as our client work.</h2>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="card card-pad" data-aos="fade-up"><div class="icon-tile"><i class="fas fa-bolt"></i></div><h3 style="font-size:16px;margin-top:18px;">Fast by default</h3><p style="font-size:12.5px;color:var(--muted);line-height:1.7;margin-top:6px;">Lightweight builds that feel instant on any device.</p></div>
            <div class="card card-pad" data-aos="fade-up" data-aos-delay="60"><div class="icon-tile"><i class="fas fa-shield-halved"></i></div><h3 style="font-size:16px;margin-top:18px;">Secure &amp; private</h3><p style="font-size:12.5px;color:var(--muted);line-height:1.7;margin-top:6px;">Your data stays yours, with sensible defaults.</p></div>
            <div class="card card-pad" data-aos="fade-up" data-aos-delay="120"><div class="icon-tile"><i class="fas fa-wand-magic-sparkles"></i></div><h3 style="font-size:16px;margin-top:18px;">Designed to use</h3><p style="font-size:12.5px;color:var(--muted);line-height:1.7;margin-top:6px;">Clean interfaces that need no manual.</p></div>
            <div class="card card-pad" data-aos="fade-up" data-aos-delay="180"><div class="icon-tile"><i class="fas fa-headset"></i></div><h3 style="font-size:16px;margin-top:18px;">Real support</h3><p style="font-size:12.5px;color:var(--muted);line-height:1.7;margin-top:6px;">Talk to the team that actually built it.</p></div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.software-card');
    const empty = document.getElementById('filterEmpty');

    buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            buttons.forEach(function (b) {
                b.style.borderColor = 'var(--line)';
                b.style.color = 'var(--navy)';
            });
            btn.style.borderColor = 'var(--blue)';
            btn.style.color = 'var(--blue)';

            const filter = btn.dataset.filter;
            let visible = 0;
            cards.forEach(function (card) {
                const platforms = card.dataset.platform || '';
                const show = filter === 'all' || platforms.indexOf(filter) !== -1;
                card.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (empty) empty.classList.toggle('hidden', visible > 0);
        });
    });
});
</script>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
