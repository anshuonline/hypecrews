<?php
session_start();
$pageTitle = "Careers — Hypecrews";
require_once 'config/db.php';

$success = '';
$error = '';

// Handle job application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $cover_letter = trim($_POST['cover_letter']);

    // File upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['resume']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if ($_FILES['resume']['size'] <= 5242880) {
                $new_filename = uniqid('resume_') . '.' . $ext;
                $upload_dir = 'uploads/resumes/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $destination = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $destination)) {
                    $resume_path = $destination;
                } else {
                    $error = "Failed to upload resume.";
                }
            } else {
                $error = "Resume size must be less than 5MB.";
            }
        } else {
            $error = "Invalid file format. Only PDF, DOC, and DOCX are allowed.";
        }
    } else {
        $error = "Resume is required.";
    }

    // Validate mandatory fields
    if (empty($phone)) {
        $error = "Phone number is required.";
    }
    if (empty($cover_letter)) {
        $error = "Cover letter is required.";
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM job_applications WHERE job_id = ? AND email = ?");
            $stmt->execute([$job_id, $email]);
            if ($stmt->fetch()) {
                $error = "You have already applied for this job with this email address.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO job_applications (job_id, applicant_name, email, phone, resume_path, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$job_id, $name, $email, $phone, $resume_path, $cover_letter]);
                $success = "Your application has been submitted successfully!";

                setcookie("applied_job_" . $job_id, "1", time() + (86400 * 30), "/");
                $_COOKIE["applied_job_" . $job_id] = "1";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch active jobs
try {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE status = 'active' ORDER BY created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jobs = [];
}
$pageDescription = "Join Hypecrews. Explore open roles in design, development, marketing and content with a premium digital brand company from Assam, India.";
$canonical = "https://hypecrews.com/careers.php";
include 'components/head.php';
?>
<style>
    .job-modal { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 18px; background: rgba(11,28,47,.6); backdrop-filter: blur(6px); }
    .job-modal.active { display: flex; }
    .job-modal-panel { width: 100%; max-width: 640px; max-height: 90vh; overflow-y: auto; background: #fff; border-radius: 24px; box-shadow: var(--shadow-lg); position: relative; }
</style>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="page-hero">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 300px; height: 300px; top: 0; right: -6%;"></div>
    <div class="shell shell-wide relative z-1">
        <div class="grid lg:grid-cols-[1.1fr_.9fr] gap-14 items-center">
            <div>
                <div class="chip" data-aos="fade-up"><span style="width:7px;height:7px;border-radius:99px;background:#1a9f6e;box-shadow:0 0 0 4px rgba(26,159,110,.13);display:inline-block;"></span> We’re hiring across teams</div>
                <h1 class="display-1 text-balance" style="margin-top: 22px;" data-aos="fade-up" data-aos-delay="60">
                    Build your future<br>with <span class="gradient-text">Hypecrews.</span>
                </h1>
                <p class="lead" style="margin-top: 20px;" data-aos="fade-up" data-aos-delay="120">
                    We’re always looking for talented people to join our growing team. Explore open roles and take your career to the next level in a culture built on craft, curiosity and ownership.
                </p>
                <div class="flex flex-wrap gap-3" style="margin-top: 30px;" data-aos="fade-up" data-aos-delay="180">
                    <a href="#openings" class="btn btn-primary btn-lg">View open roles <i class="fas fa-arrow-down"></i></a>
                    <a href="mailto:careers@hypecrews.com" class="btn btn-outline btn-lg">Send a speculative CV</a>
                </div>
            </div>
            <div data-aos="fade-left" data-aos-delay="120">
                <div class="grid grid-cols-2 gap-4">
                    <div class="card card-pad" style="padding: 24px;"><div class="icon-tile icon-tile-sm"><i class="fas fa-rocket"></i></div><strong style="display:block;font-family:var(--font-head);font-size:20px;color:var(--navy);margin-top:14px;">Fast growth</strong><p style="font-size:12px;color:var(--muted);margin-top:4px;line-height:1.6;">Real ownership from day one.</p></div>
                    <div class="card card-pad" style="padding: 24px;"><div class="icon-tile icon-tile-sm"><i class="fas fa-globe"></i></div><strong style="display:block;font-family:var(--font-head);font-size:20px;color:var(--navy);margin-top:14px;">Remote friendly</strong><p style="font-size:12px;color:var(--muted);margin-top:4px;line-height:1.6;">Work from where you do best.</p></div>
                    <div class="card card-pad" style="padding: 24px;"><div class="icon-tile icon-tile-sm"><i class="fas fa-graduation-cap"></i></div><strong style="display:block;font-family:var(--font-head);font-size:20px;color:var(--navy);margin-top:14px;">Keep learning</strong><p style="font-size:12px;color:var(--muted);margin-top:4px;line-height:1.6;">Budgets and time to grow.</p></div>
                    <div class="card card-pad" style="padding: 24px;"><div class="icon-tile icon-tile-sm"><i class="fas fa-heart"></i></div><strong style="display:block;font-family:var(--font-head);font-size:20px;color:var(--navy);margin-top:14px;">Real balance</strong><p style="font-size:12px;color:var(--muted);margin-top:4px;line-height:1.6;">Sustainable pace, no burnout.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================================================== OPENINGS -->
<section class="section" id="openings" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="section-head section-head-center" data-aos="fade-up">
            <span class="eyebrow eyebrow-center">Open positions</span>
            <h2 class="display-2">Find your next role.</h2>
            <p class="lead">Current openings across design, development, marketing and content. Don’t see a fit? Send a speculative application — we read every one.</p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="alert alert-error" style="max-width: 900px; margin: 0 auto 24px;" data-aos="fade-up"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
        <div class="alert alert-success" style="max-width: 900px; margin: 0 auto 24px;" data-aos="fade-up"><i class="fas fa-circle-check"></i><span><?php echo htmlspecialchars($success); ?></span></div>
        <?php endif; ?>

        <div style="max-width: 900px; margin-inline: auto; display: grid; gap: 18px;">
            <?php if (empty($jobs)): ?>
                <div class="card" style="padding: 56px 26px; text-align: center;" data-aos="fade-up">
                    <div class="icon-tile icon-tile-lg" style="margin: 0 auto 18px;"><i class="fas fa-briefcase"></i></div>
                    <h3 style="font-size: 24px;">No open positions right now</h3>
                    <p style="font-size: 13.5px; color: var(--muted); margin-top: 8px;">We currently don’t have any open positions. Please check back later — or send us a speculative application.</p>
                    <a href="mailto:careers@hypecrews.com" class="btn btn-primary" style="margin-top: 22px;">Send a speculative CV <i class="fas fa-arrow-right"></i></a>
                </div>
            <?php else: ?>
                <?php foreach ($jobs as $i => $job): ?>
                <article class="card card-hover" style="padding: clamp(22px, 3.4vw, 34px);" data-aos="fade-up" data-aos-delay="<?php echo ($i % 3) * 60; ?>">
                    <div class="flex flex-wrap items-start justify-between gap-5">
                        <div>
                            <h3 style="font-size: clamp(20px, 2.6vw, 26px);"><?php echo htmlspecialchars($job['title']); ?></h3>
                            <div class="flex flex-wrap gap-2" style="margin-top: 14px;">
                                <span class="chip" style="font-size: 11px;"><i class="fas fa-building" style="color: var(--blue);"></i> <?php echo htmlspecialchars($job['department']); ?></span>
                                <span class="chip" style="font-size: 11px;"><i class="fas fa-location-dot" style="color: var(--blue);"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                <span class="chip" style="font-size: 11px;"><i class="fas fa-clock" style="color: var(--blue);"></i> <?php echo htmlspecialchars($job['employment_type']); ?></span>
                            </div>
                        </div>
                        <?php $hasApplied = isset($_COOKIE['applied_job_' . $job['id']]); ?>
                        <?php if ($hasApplied): ?>
                            <button disabled class="btn btn-outline btn-sm" style="cursor: not-allowed;"><i class="fas fa-circle-check" style="color:#15805a;"></i> Applied</button>
                        <?php else: ?>
                            <button type="button" onclick="openApplyModal(<?php echo $job['id']; ?>, '<?php echo addslashes($job['title']); ?>')" class="btn btn-primary btn-sm">Apply now <i class="fas fa-arrow-right"></i></button>
                        <?php endif; ?>
                    </div>
                    <div style="height: 1px; background: var(--line); margin: 20px 0;"></div>
                    <div class="grid md:grid-cols-2 gap-7">
                        <div>
                            <h4 style="font-size: 13px; text-transform: uppercase; letter-spacing: .1em; color: var(--muted-2); margin-bottom: 8px;">Description</h4>
                            <p style="font-size: 13px; color: var(--muted); line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($job['description']); ?></p>
                        </div>
                        <?php if (!empty($job['requirements'])): ?>
                        <div>
                            <h4 style="font-size: 13px; text-transform: uppercase; letter-spacing: .1em; color: var(--muted-2); margin-bottom: 8px;">Requirements</h4>
                            <p style="font-size: 13px; color: var(--muted); line-height: 1.8; white-space: pre-wrap;"><?php echo htmlspecialchars($job['requirements']); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- ========================================================== CULTURE -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-2 gap-14 items-center">
            <div data-aos="fade-right">
                <span class="eyebrow">Life at Hypecrews</span>
                <h2 class="display-2" style="margin-top: 16px;">Craft over noise.<br><span class="gradient-text">Ownership always.</span></h2>
                <p class="lead" style="margin-top: 18px;">We’re a small, senior team that cares about the details. You’ll work directly with founders and clients, ship real work quickly, and see the impact of what you make.</p>
                <div class="grid sm:grid-cols-2 gap-5" style="margin-top: 28px;">
                    <div><h3 style="font-size: 14.5px;">No politics</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.7; margin-top: 4px;">Good ideas win, regardless of title.</p></div>
                    <div><h3 style="font-size: 14.5px;">Senior mentorship</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.7; margin-top: 4px;">Learn from people who’ve done it before.</p></div>
                    <div><h3 style="font-size: 14.5px;">Flexible hours</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.7; margin-top: 4px;">Focus on outcomes, not clock-watching.</p></div>
                    <div><h3 style="font-size: 14.5px;">Real progression</h3><p style="font-size: 12.5px; color: var(--muted); line-height: 1.7; margin-top: 4px;">Clear paths and honest feedback.</p></div>
                </div>
            </div>
            <div data-aos="fade-left">
                <div style="border-radius: 28px; overflow: hidden; border: 1px solid var(--line); box-shadow: var(--shadow-md);">
                    <img src="/graphics/516as4d8a16s5d64as.jpg" alt="The Hypecrews team" style="width: 100%; height: 420px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================================================= APPLICATION MODAL -->
<div id="applyModal" class="job-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="job-modal-panel">
        <div class="flex items-center justify-between" style="padding: 22px 26px; border-bottom: 1px solid var(--line); background: var(--mist); border-radius: 24px 24px 0 0;">
            <div>
                <p style="font-size: 10px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: var(--blue);">Application</p>
                <h3 id="modalTitle" style="font-size: 20px; margin-top: 3px;">Apply for job</h3>
            </div>
            <button type="button" onclick="closeApplyModal()" aria-label="Close" style="width: 38px; height: 38px; border-radius: 99px; border: 1px solid var(--line); background: #fff; color: var(--muted);"><i class="fas fa-times"></i></button>
        </div>
        <form action="careers.php" method="POST" enctype="multipart/form-data" style="padding: 26px;">
            <input type="hidden" name="apply_job" value="1">
            <input type="hidden" name="job_id" id="modalJobId" value="">

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="field" style="margin-bottom: 0;"><label class="label">Full name</label><input type="text" name="name" required class="input" placeholder="Your name"></div>
                <div class="field" style="margin-bottom: 0;"><label class="label">Email address</label><input type="email" name="email" required class="input" placeholder="you@email.com"></div>
            </div>
            <div class="field" style="margin-top: 18px;"><label class="label">Phone number</label><input type="tel" name="phone" required class="input" placeholder="+91 9876543210"></div>
            <div class="field"><label class="label">Resume</label><input type="file" name="resume" accept=".pdf,.doc,.docx" required class="input" style="padding: 12px; cursor: pointer;"><p style="font-size: 11px; color: var(--muted-2); margin-top: 6px;"><i class="fas fa-circle-info" style="margin-right: 4px;"></i> PDF, DOC or DOCX. Max size 5MB.</p></div>
            <div class="field"><label class="label">Cover letter</label><textarea name="cover_letter" rows="5" required class="textarea" placeholder="Tell us why you’re a great fit for this role…"></textarea></div>
            <button type="submit" class="btn btn-primary btn-block">Submit application <i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>

<script>
function openApplyModal(jobId, jobTitle) {
    document.getElementById('modalJobId').value = jobId;
    document.getElementById('modalTitle').innerText = 'Apply: ' + jobTitle;
    document.getElementById('applyModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}
function closeApplyModal() {
    document.getElementById('applyModal').classList.remove('active');
    document.body.style.overflow = '';
}
window.addEventListener('click', function (event) {
    const modal = document.getElementById('applyModal');
    if (event.target == modal) closeApplyModal();
});
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeApplyModal();
});
</script>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
