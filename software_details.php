<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: softwares.php");
    exit;
}

$id = $_GET['id'];

// Fetch software details
$stmt = $pdo->prepare("SELECT * FROM softwares WHERE id = ?");
$stmt->execute([$id]);
$software = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$software) {
    header("Location: softwares.php");
    exit;
}

$pageTitle = $software['name'] . " — Hypecrews Softwares";
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch screenshots
$stmt = $pdo->prepare("SELECT * FROM software_screenshots WHERE software_id = ? ORDER BY display_order ASC, id ASC");
$stmt->execute([$id]);
$screenshots = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageDescription = !empty($software['description']) ? substr(strip_tags($software['description']), 0, 160) : 'Download ' . $software['name'] . ' by Hypecrews.';
$pageKeywords = $software['keywords'] ?? '';
$canonical = 'https://hypecrews.com/software_details.php?id=' . urlencode($id);
include 'components/head.php';
?>
<style>
    .lightbox { position: fixed; inset: 0; z-index: 1100; display: none; align-items: center; justify-content: center; background: rgba(5, 14, 25, .94); backdrop-filter: blur(8px); padding: 20px; cursor: zoom-out; }
    .lightbox.is-open { display: flex; }
    .lightbox img { max-width: 92%; max-height: 88vh; border-radius: 14px; box-shadow: 0 40px 100px rgba(0,0,0,.6); }
    .dl-modal { position: fixed; inset: 0; z-index: 1150; display: none; align-items: center; justify-content: center; background: rgba(5,14,25,.86); backdrop-filter: blur(6px); padding: 20px; }
    .dl-modal.is-open { display: flex; }
    .shot { cursor: zoom-in; overflow: hidden; border-radius: 16px; border: 1px solid var(--line); background: #fff; }
    .shot img { width: 100%; height: 210px; object-fit: cover; transition: transform .5s var(--ease); }
    .shot:hover img { transform: scale(1.04); }
</style>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ========================================================== HEADER -->
<section style="position: relative; padding: 148px 0 54px; background: linear-gradient(145deg, var(--navy-deep), var(--navy) 60%, #0b2f52); overflow: hidden;">
    <?php if (!empty($software['banner_path'])): ?>
    <img src="<?php echo htmlspecialchars($software['banner_path']); ?>" alt="<?php echo htmlspecialchars($software['name']); ?> banner" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: .22;">
    <?php endif; ?>
    <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(18,38,63,.55), rgba(18,38,63,.92));"></div>
    <div class="arc-lines"></div>
    <div class="shell shell-wide relative z-1">
        <a href="/softwares.php" class="chip chip-light" style="margin-bottom: 22px; font-size: 11px;"><i class="fas fa-arrow-left"></i> All softwares</a>
        <div class="grid lg:grid-cols-[1fr_auto] gap-8 items-end">
            <div class="flex flex-wrap items-end gap-6">
                <div style="width: 104px; height: 104px; border-radius: 26px; background: #fff; border: 1px solid rgba(255,255,255,.25); box-shadow: var(--shadow-lg); overflow: hidden; flex-shrink: 0;" data-aos="zoom-in">
                    <?php if (!empty($software['logo_path'])): ?>
                        <img src="<?php echo htmlspecialchars($software['logo_path']); ?>" alt="<?php echo htmlspecialchars($software['name']); ?> logo" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--muted-2);"><i class="fas fa-image" style="font-size: 30px;"></i></div>
                    <?php endif; ?>
                </div>
                <div data-aos="fade-up">
                    <h1 class="display-2" style="color: #fff; font-size: clamp(2rem, 4vw, 3.2rem);"><?php echo htmlspecialchars($software['name']); ?></h1>
                    <div class="flex flex-wrap items-center gap-2" style="margin-top: 16px;">
                        <span class="badge" style="background: rgba(96,193,229,.16); color: var(--ice); border-color: rgba(96,193,229,.32);">v<?php echo htmlspecialchars($software['version']); ?></span>
                        <span class="chip chip-light" style="font-size: 11px;"><i class="far fa-calendar"></i> <?php echo date('M Y', strtotime($software['created_at'])); ?></span>
                        <span class="chip chip-light" style="font-size: 11px;"><i class="fas fa-desktop"></i> <?php echo htmlspecialchars($software['platform']); ?></span>
                    </div>
                </div>
            </div>
            <div data-aos="fade-up" data-aos-delay="120">
                <?php if ($software['file_type'] == 'upload' && !empty($software['file_path'])): ?>
                    <a href="<?php echo htmlspecialchars($software['file_path']); ?>" download class="btn btn-sky btn-lg" onclick="startDownload(event, this.href)"><i class="fas fa-download"></i> Download app</a>
                <?php elseif ($software['file_type'] == 'google_drive' && !empty($software['file_path'])): ?>
                    <a href="download.php?id=<?php echo $software['id']; ?>" class="btn btn-sky btn-lg" onclick="startDownload(event, this.href)"><i class="fas fa-cloud-arrow-down"></i> Download app</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ BODY -->
<section class="section" style="padding-top: 64px; background: var(--mist);">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-[1.7fr_.9fr] gap-8 items-start">
            <!-- Main -->
            <div style="display: grid; gap: 26px;">
                <?php if (!empty($screenshots)): ?>
                <div class="card" style="padding: clamp(20px, 3vw, 32px);" data-aos="fade-up">
                    <div class="flex items-center justify-between" style="margin-bottom: 18px;">
                        <h2 style="font-size: 19px;"><i class="far fa-images" style="color: var(--blue); margin-right: 8px;"></i>Screenshots</h2>
                        <div class="flex gap-2">
                            <div class="swiper-button-prev" style="position: static; margin: 0; width: 34px; height: 34px;"></div>
                            <div class="swiper-button-next" style="position: static; margin: 0; width: 34px; height: 34px;"></div>
                        </div>
                    </div>
                    <div class="swiper" data-swiper="screenshots">
                        <div class="swiper-wrapper">
                            <?php foreach ($screenshots as $ss): ?>
                            <div class="swiper-slide">
                                <div class="shot" onclick="openLightbox('<?php echo htmlspecialchars($ss['image_path']); ?>')">
                                    <img src="<?php echo htmlspecialchars($ss['image_path']); ?>" alt="<?php echo htmlspecialchars($software['name']); ?> screenshot" loading="lazy">
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card" style="padding: clamp(22px, 3.4vw, 38px);" data-aos="fade-up">
                    <h2 style="font-size: 22px;"><i class="fas fa-align-left" style="color: var(--blue); margin-right: 8px;"></i>About this software</h2>
                    <div style="margin-top: 18px; display: grid; gap: 14px;">
                        <?php
                            $desc = htmlspecialchars($software['description']);
                            $desc = preg_replace('/^[ \t]+/m', '', $desc);
                            $paragraphs = preg_split('/\n\s*\n/', trim($desc));
                            foreach ($paragraphs as $p) {
                                echo '<p style="font-size: 13.5px; color: var(--muted); line-height: 1.9; white-space: pre-line;">' . $p . '</p>';
                            }
                        ?>
                    </div>
                    <?php if (!empty($software['keywords'])): ?>
                    <div style="margin-top: 26px; padding-top: 20px; border-top: 1px solid var(--line);">
                        <p style="font-size: 10px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; color: var(--muted-2); margin-bottom: 10px;">Tags &amp; keywords</p>
                        <div class="flex flex-wrap gap-2">
                            <?php
                            $tags = explode(',', $software['keywords']);
                            foreach ($tags as $tag):
                                $tag = trim($tag);
                                if (empty($tag)) continue;
                            ?>
                            <span class="chip" style="font-size: 11px;">#<?php echo htmlspecialchars($tag); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside style="display: grid; gap: 18px;">
                <?php if (!empty($software['playstore_link']) || !empty($software['appstore_link']) || !empty($software['windows_store_link'])): ?>
                <div class="side-card" data-aos="fade-up">
                    <h3 style="font-size: 15px; margin-bottom: 14px;">Available on stores</h3>
                    <div style="display: grid; gap: 9px;">
                        <?php if (!empty($software['playstore_link'])): ?>
                        <a href="<?php echo htmlspecialchars($software['playstore_link']); ?>" target="_blank" rel="noopener" class="flex items-center justify-between" style="padding: 12px 14px; border: 1px solid var(--line); border-radius: 12px; background: #fbfdff;"><span class="flex items-center gap-3" style="font-size: 13px; font-weight: 700;"><i class="fab fa-google-play" style="color: #15805a;"></i> Google Play</span><i class="fas fa-arrow-up-right-from-square" style="font-size: 10px; color: var(--muted-2);"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($software['appstore_link'])): ?>
                        <a href="<?php echo htmlspecialchars($software['appstore_link']); ?>" target="_blank" rel="noopener" class="flex items-center justify-between" style="padding: 12px 14px; border: 1px solid var(--line); border-radius: 12px; background: #fbfdff;"><span class="flex items-center gap-3" style="font-size: 13px; font-weight: 700;"><i class="fab fa-apple"></i> App Store</span><i class="fas fa-arrow-up-right-from-square" style="font-size: 10px; color: var(--muted-2);"></i></a>
                        <?php endif; ?>
                        <?php if (!empty($software['windows_store_link'])): ?>
                        <a href="<?php echo htmlspecialchars($software['windows_store_link']); ?>" target="_blank" rel="noopener" class="flex items-center justify-between" style="padding: 12px 14px; border: 1px solid var(--line); border-radius: 12px; background: #fbfdff;"><span class="flex items-center gap-3" style="font-size: 13px; font-weight: 700;"><i class="fab fa-windows" style="color: var(--blue);"></i> Windows Store</span><i class="fas fa-arrow-up-right-from-square" style="font-size: 10px; color: var(--muted-2);"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="side-card" data-aos="fade-up" data-aos-delay="60">
                    <h3 style="font-size: 15px; margin-bottom: 14px;">Technical details</h3>
                    <div class="data-row" style="padding-top: 0;"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Version</span><span style="font-size: 13px; font-weight: 700; color: var(--navy);"><?php echo htmlspecialchars($software['version']); ?></span></div>
                    <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Platforms</span><span style="font-size: 13px; font-weight: 700; color: var(--navy);"><?php echo htmlspecialchars($software['platform']); ?></span></div>
                    <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Last updated</span><span style="font-size: 13px; font-weight: 700; color: var(--navy);"><?php echo date('M d, Y', strtotime($software['updated_at'] ?? $software['created_at'])); ?></span></div>
                    <div class="data-row" style="border-bottom: 0;"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Developer</span><span style="font-size: 13px; font-weight: 700; color: var(--blue);">Hypecrews</span></div>
                </div>

                <div class="card-dark" style="padding: 24px; position: relative; overflow: hidden;" data-aos="fade-up" data-aos-delay="120">
                    <div class="arc-lines"></div>
                    <div class="relative z-1">
                        <h3 style="font-size: 16px; color: #fff;">Need help installing?</h3>
                        <p style="font-size: 12px; color: rgba(255,255,255,.6); margin-top: 7px; line-height: 1.7;">Our team can help with setup, migration or a custom build for your business.</p>
                        <a href="/contact.php" class="btn btn-sky btn-sm btn-block" style="margin-top: 15px;">Talk to support <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ===================================================== MORE PRODUCTS -->
<section class="section-sm" style="background: #fff; border-top: 1px solid var(--line);">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="eyebrow">Keep exploring</span>
                <h2 style="font-size: clamp(1.5rem, 2.6vw, 2rem); margin-top: 10px;">More from our catalog.</h2>
            </div>
            <a href="/softwares.php" class="btn btn-primary">View all softwares <i class="fas fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- ========================================================= LIGHTBOX -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span style="position: absolute; top: 22px; right: 28px; color: #fff; font-size: 34px; cursor: pointer;">&times;</span>
    <img id="lightbox-img" src="" alt="Screenshot preview">
</div>

<!-- ================================================== DOWNLOAD MODAL -->
<div id="download-modal" class="dl-modal">
    <div id="download-modal-content" style="background: #fff; border-radius: 24px; padding: 36px 28px; max-width: 380px; width: 100%; text-align: center; box-shadow: var(--shadow-lg);">
        <div id="download-loading">
            <div style="position: relative; width: 62px; height: 62px; margin: 0 auto 20px;">
                <div style="position: absolute; inset: 0; border-radius: 99px; border: 4px solid #e9f3f9;"></div>
                <div style="position: absolute; inset: 0; border-radius: 99px; border: 4px solid var(--blue); border-top-color: transparent; animation: spinSlow 1s linear infinite;"></div>
                <i class="fas fa-download" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; color: var(--blue);"></i>
            </div>
            <h3 style="font-size: 19px;">Preparing download</h3>
            <p style="font-size: 12.5px; color: var(--muted); margin-top: 6px;">Please wait while we fetch your file…</p>
        </div>
        <div id="download-success" style="display: none;">
            <div class="icon-tile icon-tile-lg" style="margin: 0 auto 18px; background: #eafaf3; border-color: #c9f0df; color: #15805a;"><i class="fas fa-check"></i></div>
            <h3 style="font-size: 19px;">Download started!</h3>
            <p style="font-size: 12.5px; color: var(--muted); margin: 6px 0 18px;">Your download should begin automatically.</p>
            <button type="button" onclick="closeDownloadModal()" class="btn btn-primary btn-block">Close</button>
        </div>
    </div>
</div>

<script>
function openLightbox(src) {
    document.getElementById('lightbox-img').src = src;
    document.getElementById('lightbox').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('is-open');
    document.body.style.overflow = '';
}
function startDownload(e, url) {
    e.preventDefault();
    const modal = document.getElementById('download-modal');
    const loadingState = document.getElementById('download-loading');
    const successState = document.getElementById('download-success');
    loadingState.style.display = 'block';
    successState.style.display = 'none';
    modal.classList.add('is-open');
    setTimeout(function () {
        window.location.href = url;
        loadingState.style.display = 'none';
        successState.style.display = 'block';
    }, 1600);
}
function closeDownloadModal() {
    document.getElementById('download-modal').classList.remove('is-open');
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') { closeLightbox(); closeDownloadModal(); }
});
</script>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
