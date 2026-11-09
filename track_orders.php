<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get user's orders (with or without search)
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? AND tracking_id LIKE ? ORDER BY created_at DESC");
        $stmt->execute([$user_id, "%{$search}%"]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}

$totalOrders = count($orders);
$activeOrders = 0;
$completedOrders = 0;
foreach ($orders as $o) {
    if (in_array($o['status'], ['completed', 'delivered'])) $completedOrders++;
    elseif ($o['status'] !== 'cancelled') $activeOrders++;
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
$pageTitle = "Track Orders — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-5" style="margin-bottom: 26px;" data-aos="fade-up">
            <div>
                <span class="eyebrow">Order tracking</span>
                <h1 class="dash-title" style="margin-top: 12px;">Your projects, in motion.</h1>
                <p style="font-size: 13.5px; color: var(--muted); margin-top: 6px;">Monitor live status and progress across every engagement.</p>
            </div>
            <a href="/contact.php" class="btn btn-primary">Start a new project <i class="fas fa-arrow-right"></i></a>
        </div>

        <!-- Stats -->
        <div class="grid sm:grid-cols-3 gap-4" style="margin-bottom: 22px;" data-aos="fade-up">
            <div class="card" style="padding: 22px;"><p style="font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Total orders</p><strong style="display:block;font-family:var(--font-head);font-size:28px;color:var(--navy);margin-top:5px;"><?php echo $totalOrders; ?></strong></div>
            <div class="card" style="padding: 22px;"><p style="font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">In progress</p><strong style="display:block;font-family:var(--font-head);font-size:28px;color:var(--blue);margin-top:5px;"><?php echo $activeOrders; ?></strong></div>
            <div class="card" style="padding: 22px;"><p style="font-size: 10px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted-2);">Completed</p><strong style="display:block;font-family:var(--font-head);font-size:28px;color:#15805a;margin-top:5px;"><?php echo $completedOrders; ?></strong></div>
        </div>

        <!-- Search -->
        <div class="card" style="padding: 18px; margin-bottom: 22px;" data-aos="fade-up">
            <form method="GET" class="flex flex-wrap gap-3 items-center">
                <div class="flex items-center gap-2" style="flex: 1; min-width: 240px; border: 1px solid var(--line); border-radius: 999px; padding: 4px 6px 4px 16px; background: #fbfdff;">
                    <i class="fas fa-magnifying-glass" style="color: var(--muted-2); font-size: 13px;"></i>
                    <input type="text" name="search" placeholder="Enter tracking ID…" value="<?php echo htmlspecialchars($search); ?>" style="border: 0; outline: 0; background: transparent; flex: 1; font-size: 13px; color: var(--ink); padding: 9px 4px;">
                    <?php if (!empty($search)): ?><a href="track_orders.php" aria-label="Clear search" style="color: var(--muted-2); padding: 6px;"><i class="fas fa-xmark"></i></a><?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Search orders</button>
            </form>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div>
        <?php endif; ?>

        <!-- Orders -->
        <div class="dash-card" style="padding: 0; overflow: hidden;" data-aos="fade-up">
            <?php if (empty($orders)): ?>
            <div style="text-align: center; padding: 64px 24px;">
                <div class="icon-tile icon-tile-lg" style="margin: 0 auto 18px;"><i class="fas fa-satellite-dish"></i></div>
                <h3 style="font-size: 22px;">
                    <?php if (!empty($search)): ?>No orders found for “<?php echo htmlspecialchars($search); ?>”<?php else: ?>No orders yet<?php endif; ?>
                </h3>
                <p style="font-size: 13px; color: var(--muted); margin-top: 8px; max-width: 440px; margin-inline: auto;">
                    <?php if (!empty($search)): ?>Check the tracking ID and try again, or <a href="track_orders.php" style="color: var(--blue); font-weight: 700;">view all orders</a>.<?php else: ?>Your active projects will appear here as soon as work begins.<?php endif; ?>
                </p>
                <a href="/contact.php" class="btn btn-primary" style="margin-top: 20px;">Start a project <i class="fas fa-arrow-right"></i></a>
            </div>
            <?php else: ?>
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--line); background: var(--mist); display: none;" class="md:grid" id="ordersHead">
                <div class="grid grid-cols-12 gap-4" style="font-size: 9.5px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; color: var(--muted-2);">
                    <div class="col-span-6">Project</div>
                    <div class="col-span-2">Tracking ID</div>
                    <div class="col-span-2">Status</div>
                    <div class="col-span-2" style="text-align: right;">Date</div>
                </div>
            </div>
            <div>
                <?php foreach ($orders as $order): ?>
                <a href="order_details.php?id=<?php echo $order['id']; ?>" style="display: block; padding: 18px 20px; border-bottom: 1px solid var(--line); transition: background .25s ease;" onmouseover="this.style.background='#f7fbfe'" onmouseout="this.style.background='transparent'">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-12 md:col-span-6" style="display: flex; gap: 14px; align-items: flex-start; margin-bottom: 8px;">
                            <div class="icon-tile icon-tile-sm" style="flex-shrink: 0;"><i class="fas fa-layer-group"></i></div>
                            <div style="min-width: 0;">
                                <h3 style="font-size: 15px; color: var(--navy);"><?php echo htmlspecialchars($order['order_title']); ?></h3>
                                <p style="font-size: 12px; color: var(--muted); margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 320px;"><?php echo htmlspecialchars(substr($order['order_description'], 0, 70)); ?>…</p>
                            </div>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <?php if ($order['tracking_id']): ?>
                            <span style="font-family: monospace; font-size: 11.5px; background: #f1f6fa; border: 1px solid var(--line); border-radius: 7px; padding: 4px 8px; color: var(--muted);"><?php echo htmlspecialchars($order['tracking_id']); ?></span>
                            <?php else: ?>
                            <span style="font-size: 11.5px; color: var(--muted-2); font-style: italic;">Pending</span>
                            <?php endif; ?>
                        </div>
                        <div class="col-span-4 md:col-span-2">
                            <span class="badge <?php echo $statusClass[$order['status']] ?? 'badge-slate'; ?>"><span style="width:4px;height:4px;border-radius:99px;background:currentColor;display:inline-block;"></span><?php echo str_replace('_', ' ', $order['status']); ?></span>
                        </div>
                        <div class="col-span-4 md:col-span-2" style="text-align: left;">
                            <span style="font-size: 11.5px; color: var(--muted);"><?php echo date('M j, Y', strtotime($order['created_at'] . ' UTC')); ?></span>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
</body>
</html>
