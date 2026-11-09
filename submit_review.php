<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header("Location: track_orders.php");
    exit();
}

// Get order details (ensure it belongs to the logged-in user and review is requested)
try {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ? AND o.review_requested = 1 AND o.status = 'completed'");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: track_orders.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}

// Check if user has already submitted a review for this order
try {
    $stmt = $pdo->prepare("SELECT * FROM order_reviews WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_review) {
        $review_submitted = true;
        $rating = $existing_review['rating'];
        $review_text = $existing_review['review'];
    } else {
        $review_submitted = false;
        $rating = 5;
        $review_text = '';
    }
} catch (PDOException $e) {
    $error = "Error checking existing review: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$review_submitted) {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $review_text = isset($_POST['review']) ? trim($_POST['review']) : '';

    // Validation
    if ($rating < 1 || $rating > 5) {
        $error = 'Rating must be between 1 and 5';
    } else {
        try {
            // Insert the review
            $stmt = $pdo->prepare("INSERT INTO order_reviews (order_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $rating, $review_text]);

            $success = "Review submitted successfully!";
            $review_submitted = true;
        } catch (PDOException $e) {
            $error = "Error submitting review: " . $e->getMessage();
        }
    }
}

$pageTitle = "Submit Review — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<style>
    .rating-stars { display: flex; gap: 8px; }
    .rating-stars .star {
        background: transparent; border: 0; padding: 4px; font-size: 28px; line-height: 1;
        color: #cfe0ea; cursor: pointer; transition: transform .18s ease, color .18s ease;
    }
    .rating-stars .star:hover { transform: translateY(-2px) scale(1.06); color: #f4b740; }
    .rating-stars .star.star-active { color: #f4b740; }
</style>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap">
    <div class="shell shell-wide" style="max-width: 1080px;">
        <a href="track_orders.php" style="display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; font-weight: 800; margin-bottom: 18px;" data-aos="fade-up"><i class="fas fa-arrow-left"></i> Back to orders</a>

        <div class="flex flex-wrap items-end justify-between gap-5" style="margin-bottom: 26px;" data-aos="fade-up">
            <div>
                <span class="eyebrow">Order #<?php echo $order['id']; ?></span>
                <h1 class="dash-title" style="margin-top: 10px;"><?php echo $review_submitted ? 'Your review.' : 'How did we do?'; ?></h1>
                <p style="font-size: 13.5px; color: var(--muted); margin-top: 6px;"><?php echo htmlspecialchars($order['order_title']); ?></p>
            </div>
            <span class="badge badge-green"><i class="fas fa-circle" style="font-size: 5px;"></i> Completed</span>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-error" data-aos="fade-up"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div>
        <?php endif; ?>

        <?php if (isset($success)): ?>
        <div class="alert alert-success" data-aos="fade-up"><i class="fas fa-circle-check"></i><span><?php echo htmlspecialchars($success); ?></span></div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-[1.55fr_.75fr] gap-6 items-start">
            <div style="display: grid; gap: 20px;">
                <!-- Order info -->
                <div class="dash-card" data-aos="fade-up">
                    <h2 style="font-size: 17px;"><i class="fas fa-file-contract" style="color: var(--blue); margin-right: 8px;"></i>Project details</h2>
                    <p style="font-size: 13.5px; color: var(--muted); line-height: 1.85; margin-top: 12px; white-space: pre-line;"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>
                    <div class="grid sm:grid-cols-2 gap-3" style="margin-top: 18px;">
                        <?php if ($order['tracking_id']): ?>
                        <div style="background: var(--mist); border: 1px solid var(--line); border-radius: 12px; padding: 14px;">
                            <p style="font-size: 9.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Tracking ID</p>
                            <p style="font-family: monospace; font-size: 13px; font-weight: 700; color: var(--blue); margin-top: 5px;"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                        </div>
                        <?php endif; ?>
                        <div style="background: var(--mist); border: 1px solid var(--line); border-radius: 12px; padding: 14px;">
                            <p style="font-size: 9.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Status</p>
                            <p style="font-size: 13px; font-weight: 700; color: #15805a; margin-top: 5px;">Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Review -->
                <div class="dash-card" data-aos="fade-up" data-aos-delay="60">
                    <?php if ($review_submitted): ?>
                    <h2 style="font-size: 18px;"><i class="fas fa-star" style="color: #f4b740; margin-right: 8px;"></i>Thank you for your feedback</h2>
                    <p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;">Your review helps us keep raising the bar for every client.</p>

                    <div style="margin-top: 22px; padding: 22px; border: 1px solid var(--line); border-radius: 16px; background: #fbfdff;">
                        <div class="flex items-center gap-1" style="color: #f4b740; font-size: 18px;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star" style="<?php echo ($i <= $rating) ? '' : 'color:#dbe7ef;'; ?>"></i>
                            <?php endfor; ?>
                            <span style="font-size: 11.5px; font-weight: 800; color: var(--muted); margin-left: 8px;"><?php echo $rating; ?>.0 / 5</span>
                        </div>
                        <?php if (!empty($review_text)): ?>
                        <p style="font-size: 13.5px; color: #3c556b; line-height: 1.85; margin-top: 12px; white-space: pre-line;"><?php echo nl2br(htmlspecialchars($review_text)); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap items-center gap-3" style="margin-top: 22px;">
                        <a href="track_orders.php" class="btn btn-primary">Back to orders <i class="fas fa-arrow-right"></i></a>
                        <a href="/contact.php" class="btn btn-outline">Start another project</a>
                    </div>
                    <?php else: ?>
                    <h2 style="font-size: 18px;"><i class="fas fa-pen-to-square" style="color: var(--blue); margin-right: 8px;"></i>Share your experience</h2>
                    <p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;">Tell us what worked and what we could do better. Honest feedback only.</p>

                    <form method="POST" style="margin-top: 22px;">
                        <div class="field">
                            <label class="label">Your rating *</label>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="star <?php echo ($i <= $rating) ? 'star-active' : ''; ?>" data-rating="<?php echo $i; ?>" aria-label="<?php echo $i; ?> star">
                                        <i class="fas fa-star"></i>
                                    </button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" id="rating" name="rating" value="<?php echo $rating; ?>">
                        </div>

                        <div class="field" style="margin-bottom: 22px;">
                            <label for="review" class="label">Review <span style="color:var(--muted-2);font-weight:600;">(optional)</span></label>
                            <textarea id="review" name="review" rows="5" class="input" style="resize: vertical; padding: 14px 16px;" placeholder="Share your experience with this service…"><?php echo htmlspecialchars($review_text); ?></textarea>
                        </div>

                        <div class="flex flex-wrap justify-end gap-3">
                            <button type="button" onclick="window.location.href='track_orders.php'" class="btn btn-outline">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit review <i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Summary -->
            <aside class="side-card" style="position: sticky; top: 104px;" data-aos="fade-up" data-aos-delay="100">
                <h2 style="font-size: 15px; margin-bottom: 6px;">Order summary</h2>
                <div class="data-row" style="padding-top: 12px;"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Order ID</span><span style="font-size: 13px; font-weight: 700; font-family: monospace; color: var(--navy);">#<?php echo $order['id']; ?></span></div>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Created</span><span style="font-size: 12.5px; font-weight: 700; color: var(--navy);"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></span></div>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Last update</span><span style="font-size: 12.5px; font-weight: 700; color: var(--navy);"><?php echo date('M j, Y', strtotime($order['updated_at'])); ?></span></div>
                <?php if ($order['tracking_id']): ?>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Tracking</span><span style="font-size: 12px; font-weight: 700; font-family: monospace; color: var(--blue);"><?php echo htmlspecialchars($order['tracking_id']); ?></span></div>
                <?php endif; ?>
                <div style="margin-top: 16px; padding: 14px; border-radius: 12px; background: var(--mist); border: 1px solid var(--line);">
                    <p style="font-size: 11px; color: var(--muted); line-height: 1.7;"><i class="fas fa-circle-info" style="margin-right: 5px; color: var(--blue);"></i>Reviews are linked to your order and help future clients choose confidently.</p>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.rating-stars .star');
        const ratingInput = document.getElementById('rating');

        if (!ratingInput) return;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                ratingInput.value = rating;

                stars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('star-active');
                    } else {
                        s.classList.remove('star-active');
                    }
                });
            });
        });
    });
</script>
</body>
</html>
