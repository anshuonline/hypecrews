<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header("Location: track_orders.php");
    exit();
}

// Get order details (ensure it belongs to the logged-in user)
try {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: track_orders.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}

// Get order status history
try {
    $stmt = $pdo->prepare("SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at ASC");
    $stmt->execute([$order_id]);
    $status_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching order history: " . $e->getMessage();
}

$statusClass = [
    'pending' => 'badge-amber',
    'in_review' => 'badge-sky',
    'approved' => 'badge-sky',
    'processing' => 'badge-sky',
    'in_production' => 'badge-sky',
    'quality_check' => 'badge-amber',
    'ready_for_delivery' => 'badge-green',
    'shipped' => 'badge-sky',
    'delivered' => 'badge-green',
    'revision_requested' => 'badge-amber',
    'on_hold' => 'badge-amber',
    'completed' => 'badge-green',
    'cancelled' => 'badge-red',
];
$currentClass = $statusClass[$order['status']] ?? 'badge-slate';

$pageTitle = "Order #" . $order['id'] . " — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap">
    <div class="shell shell-wide">
        <a href="track_orders.php" style="display: inline-flex; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; font-weight: 800; margin-bottom: 18px;" data-aos="fade-up"><i class="fas fa-arrow-left"></i> Back to orders</a>

        <div class="flex flex-wrap items-end justify-between gap-5" style="margin-bottom: 26px;" data-aos="fade-up">
            <div>
                <span class="eyebrow">Order #<?php echo $order['id']; ?></span>
                <h1 class="dash-title" style="margin-top: 10px;"><?php echo htmlspecialchars($order['order_title']); ?></h1>
                <div class="flex flex-wrap items-center gap-2" style="margin-top: 12px;">
                    <span class="badge <?php echo $currentClass; ?>"><span style="width:4px;height:4px;border-radius:99px;background:currentColor;display:inline-block;"></span><?php echo str_replace('_', ' ', $order['status']); ?></span>
                    <?php if ($order['tracking_id']): ?><span class="chip" style="font-size: 11px; font-family: monospace;"><?php echo htmlspecialchars($order['tracking_id']); ?></span><?php endif; ?>
                    <span class="chip" style="font-size: 11px;"><i class="far fa-calendar" style="color: var(--blue);"></i> <?php echo date('M j, Y', strtotime($order['created_at'] . ' UTC')); ?></span>
                </div>
            </div>
            <button type="button" onclick="window.print()" class="btn btn-outline"><i class="fas fa-print"></i> Print / export</button>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div>
        <?php endif; ?>

        <div class="grid lg:grid-cols-[1.65fr_.85fr] gap-7 items-start">
            <div style="display: grid; gap: 22px;">
                <!-- Specs -->
                <div class="dash-card" data-aos="fade-up">
                    <h2 style="font-size: 18px;"><i class="fas fa-file-contract" style="color: var(--blue); margin-right: 8px;"></i>Project brief</h2>
                    <p style="font-size: 13.5px; color: var(--muted); line-height: 1.9; margin-top: 14px; white-space: pre-line;"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>

                    <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 22px;">
                        <?php if ($order['tracking_id']): ?>
                        <div style="background: var(--mist); border: 1px solid var(--line); border-radius: 14px; padding: 16px;">
                            <p style="font-size: 9.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Tracking ID</p>
                            <p style="font-family: monospace; font-size: 14px; font-weight: 700; color: var(--blue); margin-top: 6px; letter-spacing: .06em;"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                        </div>
                        <?php endif; ?>
                        <div style="background: var(--mist); border: 1px solid var(--line); border-radius: 14px; padding: 16px;">
                            <p style="font-size: 9.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Current status</p>
                            <div style="margin-top: 8px;"><span class="badge <?php echo $currentClass; ?>"><?php echo str_replace('_', ' ', $order['status']); ?></span></div>
                        </div>
                    </div>

                    <?php if ($order['custom_status']): ?>
                    <div class="alert alert-info" style="margin: 18px 0 0;"><i class="fas fa-circle-info"></i><span><?php echo htmlspecialchars($order['custom_status']); ?></span></div>
                    <?php endif; ?>

                    <?php if ($order['status'] == 'completed' && $order['review_requested']): ?>
                    <a href="submit_review.php?order_id=<?php echo $order['id']; ?>" class="btn btn-sky btn-block" style="margin-top: 18px;"><i class="fas fa-star"></i> Submit a review</a>
                    <?php endif; ?>
                </div>

                <!-- Timeline -->
                <div class="dash-card" data-aos="fade-up" data-aos-delay="60">
                    <h2 style="font-size: 18px; margin-bottom: 20px;"><i class="fas fa-timeline" style="color: var(--blue); margin-right: 8px;"></i>Progress timeline</h2>

                    <?php if (empty($status_history) && empty($order['status'])): ?>
                    <p style="font-size: 13px; color: var(--muted);"><i class="fas fa-history" style="margin-right: 6px;"></i>No history yet.</p>
                    <?php else: ?>
                    <div class="timeline">
                        <?php foreach ($status_history as $index => $history): ?>
                        <div class="tl-item" data-aos="fade-up">
                            <span class="tl-dot"></span>
                            <div class="tl-card">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <span class="badge <?php echo $statusClass[$history['status']] ?? 'badge-slate'; ?>" style="margin-bottom: 6px;"><?php echo str_replace('_', ' ', htmlspecialchars($history['status'])); ?></span>
                                        <?php if ($history['custom_status']): ?><p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;"><?php echo htmlspecialchars($history['custom_status']); ?></p><?php endif; ?>
                                    </div>
                                    <div class="tl-time" style="text-align: right; white-space: nowrap;">
                                        <span style="display: block;"><?php echo date('M j, Y', strtotime($history['created_at'] . ' UTC')); ?></span>
                                        <span style="display: block;"><?php echo date('h:i A', strtotime($history['created_at'] . ' UTC')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <div class="tl-item is-current" data-aos="fade-up">
                            <span class="tl-dot"></span>
                            <div class="tl-card" style="background: #f2fbfe; border-color: rgba(0,90,156,.32);">
                                <div class="flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <span class="badge <?php echo $currentClass; ?>" style="margin-bottom: 6px;"><span style="width:4px;height:4px;border-radius:99px;background:currentColor;display:inline-block;"></span><?php echo str_replace('_', ' ', htmlspecialchars($order['status'])); ?> · current</span>
                                        <?php if ($order['custom_status']): ?><p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;"><?php echo htmlspecialchars($order['custom_status']); ?></p><?php endif; ?>
                                    </div>
                                    <div class="tl-time" style="text-align: right; white-space: nowrap; color: var(--blue);">
                                        <span style="display: block;"><?php echo date('M j, Y', strtotime($order['updated_at'] . ' UTC')); ?></span>
                                        <span style="display: block;"><?php echo date('h:i A', strtotime($order['updated_at'] . ' UTC')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="side-card" style="position: sticky; top: 104px;" data-aos="fade-up" data-aos-delay="100">
                <h2 style="font-size: 15px; margin-bottom: 6px;">Order summary</h2>
                <div class="data-row" style="padding-top: 12px;"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Order ID</span><span style="font-size: 13px; font-weight: 700; font-family: monospace; color: var(--navy);">#<?php echo $order['id']; ?></span></div>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Placed on</span><span style="font-size: 13px; font-weight: 700; color: var(--navy);"><?php echo date('M j, Y', strtotime($order['created_at'] . ' UTC')); ?></span></div>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Last update</span><span style="font-size: 13px; font-weight: 700; color: var(--navy);"><?php echo date('M j, Y', strtotime($order['updated_at'] . ' UTC')); ?></span></div>
                <?php if ($order['tracking_id']): ?>
                <div class="data-row"><span style="font-size: 10px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">Tracking ID</span><span style="font-size: 12px; font-weight: 700; font-family: monospace; color: var(--blue);"><?php echo htmlspecialchars($order['tracking_id']); ?></span></div>
                <?php endif; ?>

                <a href="/support_chat.php" class="btn btn-primary btn-block btn-sm" style="margin-top: 18px;"><i class="fas fa-comments"></i> Discuss this order</a>
                <button type="button" onclick="window.print()" class="btn btn-outline btn-block btn-sm" style="margin-top: 9px;"><i class="fas fa-print"></i> Print details</button>

                <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--line);">
                    <p style="font-size: 11px; color: var(--muted-2); line-height: 1.7;"><i class="fas fa-circle-info" style="margin-right: 4px;"></i>Need a change? Message the team and we’ll update your project brief.</p>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
</body>
</html>
