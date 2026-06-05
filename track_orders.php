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
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders - Peak Experience</title>
    <!-- SEO Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="Track your Hypecrews orders and monitor the live status of your digital service projects.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4',
                        dark: '#0B0F19',
                        surface: '#151b2b'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0B0F19; color: #f8fafc; overflow-x: hidden; }
        .ambient-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            background: radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.05), transparent 25%),
                        radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.05), transparent 25%);
            pointer-events: none;
        }
        .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .input-glass:focus {
            background: rgba(0, 0, 0, 0.2);
            border-color: #6366f1;
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.3);
            outline: none;
        }
        .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-up.active { opacity: 1; transform: translate(0); }
        
        .order-row { transition: all 0.3s ease; }
        .order-row:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: translateX(10px);
            border-color: rgba(99, 102, 241, 0.3);
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-32 pb-20 min-h-screen relative z-10">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
            <div class="mb-10 reveal-up text-center">
                <h1 class="font-heading text-4xl md:text-5xl font-black mb-4 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Order Telemetry</h1>
                <p class="text-gray-400 font-light">Monitor the live status and execution progress of your digital assets.</p>
            </div>
            
            <!-- Search Form -->
            <div class="mb-10 reveal-up" style="transition-delay: 100ms;">
                <form method="GET" class="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
                    <div class="relative flex-grow">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Enter Tracking ID..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full input-glass rounded-full py-4 pl-12 pr-6 focus:ring-2 focus:ring-primary/50"
                        >
                    </div>
                    <button type="submit" class="px-8 py-4 rounded-full bg-gradient-to-r from-primary to-secondary text-white font-bold hover:shadow-[0_0_20px_rgba(99,102,241,0.4)] hover:scale-105 transition-all whitespace-nowrap">
                        Scan Status
                    </button>
                    <?php if (!empty($search)): ?>
                    <a href="track_orders.php" class="px-6 py-4 rounded-full border border-gray-600 hover:bg-gray-800 text-gray-300 font-bold transition-colors flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="mb-8 p-4 rounded-2xl bg-red-900/20 border border-red-500/30 backdrop-blur-md reveal-up flex items-center shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-4 animate-pulse"></i>
                <p class="text-red-200 font-medium"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="glass-panel rounded-3xl overflow-hidden reveal-up" style="transition-delay: 200ms;">
                <?php if (empty($orders)): ?>
                <div class="text-center py-20 px-4 relative overflow-hidden">
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-primary/5 rounded-full blur-[50px]"></div>
                    <i class="fas fa-satellite-dish text-6xl text-gray-600 mb-6 relative z-10"></i>
                    <h3 class="text-2xl font-heading font-bold mb-3 relative z-10 text-white">
                        <?php if (!empty($search)): ?>
                            No telemetry found for "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            Awaiting Telemetry
                        <?php endif; ?>
                    </h3>
                    <p class="text-gray-400 mb-8 max-w-md mx-auto relative z-10 font-light">
                        <?php if (!empty($search)): ?>
                            Verify the Tracking ID and scan again, or <a href="track_orders.php" class="text-primary hover:text-indigo-400 underline decoration-primary/30 underline-offset-4">view all active orders</a>.
                        <?php else: ?>
                            You have no active operations in the system. Initiate a new project to see live tracking.
                        <?php endif; ?>
                    </p>
                    <a href="index.php" class="relative z-10 px-8 py-3.5 rounded-full bg-white text-dark font-bold hover:scale-105 transition-transform shadow-[0_0_20px_rgba(255,255,255,0.1)] inline-block">
                        Deploy New Project <i class="fas fa-rocket ml-2"></i>
                    </a>
                </div>
                <?php else: ?>
                <div class="p-6 md:p-8">
                    <div class="hidden md:grid grid-cols-12 gap-4 text-xs font-semibold tracking-wider text-gray-500 uppercase mb-4 px-6">
                        <div class="col-span-5">Project Details</div>
                        <div class="col-span-3 text-center">Tracking Node</div>
                        <div class="col-span-2 text-center">Status</div>
                        <div class="col-span-2 text-right">Deployment Date</div>
                    </div>
                    
                    <div class="space-y-3">
                        <?php foreach ($orders as $index => $order): ?>
                        <div class="order-row group block md:grid grid-cols-12 gap-4 items-center bg-white/5 border border-white/5 rounded-2xl p-5 cursor-pointer reveal-up" style="transition-delay: <?php echo ($index * 50) + 300; ?>ms;" onclick="window.location.href='order_details.php?id=<?php echo $order['id']; ?>'">
                            <!-- Project Details -->
                            <div class="col-span-5 mb-4 md:mb-0">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 flex items-center justify-center shrink-0 shadow-inner">
                                        <i class="fas fa-layer-group text-primary"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-white group-hover:text-primary transition-colors text-lg font-heading"><?php echo htmlspecialchars($order['order_title']); ?></h4>
                                        <p class="text-sm text-gray-400 font-light truncate max-w-xs"><?php echo substr(htmlspecialchars($order['order_description']), 0, 60); ?>...</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tracking Node -->
                            <div class="col-span-3 mb-4 md:mb-0 md:text-center">
                                <span class="md:hidden text-xs text-gray-500 uppercase font-semibold mr-2">ID:</span>
                                <?php if ($order['tracking_id']): ?>
                                <span class="font-mono text-sm bg-black/30 px-3 py-1 rounded-md border border-white/10 text-gray-300"><?php echo htmlspecialchars($order['tracking_id']); ?></span>
                                <?php else: ?>
                                <span class="text-gray-500 text-sm italic">Pending Node</span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Status -->
                            <div class="col-span-2 mb-4 md:mb-0 md:text-center">
                                <span class="md:hidden text-xs text-gray-500 uppercase font-semibold mr-2">Status:</span>
                                <?php
                                $statusStyles = [
                                    'pending' => 'border-yellow-500/30 text-yellow-400 bg-yellow-500/10 shadow-[0_0_10px_rgba(234,179,8,0.2)]',
                                    'in_review' => 'border-blue-500/30 text-blue-400 bg-blue-500/10 shadow-[0_0_10px_rgba(59,130,246,0.2)]',
                                    'approved' => 'border-indigo-500/30 text-indigo-400 bg-indigo-500/10 shadow-[0_0_10px_rgba(99,102,241,0.2)]',
                                    'processing' => 'border-purple-500/30 text-purple-400 bg-purple-500/10 shadow-[0_0_10px_rgba(168,85,247,0.2)]',
                                    'in_production' => 'border-cyan-500/30 text-cyan-400 bg-cyan-500/10 shadow-[0_0_10px_rgba(6,182,212,0.2)]',
                                    'quality_check' => 'border-teal-500/30 text-teal-400 bg-teal-500/10 shadow-[0_0_10px_rgba(20,184,166,0.2)]',
                                    'ready_for_delivery' => 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10 shadow-[0_0_10px_rgba(16,185,129,0.2)]',
                                    'shipped' => 'border-blue-500/30 text-blue-400 bg-blue-500/10 shadow-[0_0_10px_rgba(59,130,246,0.2)]',
                                    'delivered' => 'border-green-500/30 text-green-400 bg-green-500/10 shadow-[0_0_10px_rgba(34,197,94,0.2)]',
                                    'revision_requested' => 'border-orange-500/30 text-orange-400 bg-orange-500/10 shadow-[0_0_10px_rgba(249,115,22,0.2)]',
                                    'on_hold' => 'border-yellow-500/30 text-yellow-400 bg-yellow-500/10 shadow-[0_0_10px_rgba(234,179,8,0.2)]',
                                    'completed' => 'border-green-500/30 text-green-400 bg-green-500/10 shadow-[0_0_10px_rgba(34,197,94,0.2)]',
                                    'cancelled' => 'border-red-500/30 text-red-400 bg-red-500/10 shadow-[0_0_10px_rgba(239,68,68,0.2)]'
                                ];
                                $style = $statusStyles[$order['status']] ?? 'border-gray-500/30 text-gray-400 bg-gray-500/10';
                                ?>
                                <div class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border <?php echo $style; ?>">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                        <?php echo str_replace('_', ' ', $order['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Date -->
                            <div class="col-span-2 md:text-right flex items-center justify-between md:justify-end">
                                <span class="md:hidden text-xs text-gray-500 uppercase font-semibold mr-2">Date:</span>
                                <div class="flex items-center gap-3">
                                    <span class="text-sm text-gray-400"><?php echo date('M j, Y', strtotime($order['created_at'] . ' UTC')); ?></span>
                                    <i class="fas fa-chevron-right text-gray-600 group-hover:text-primary group-hover:translate-x-1 transition-all"></i>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, { threshold: 0.1 });
            
            document.querySelectorAll('.reveal-up').forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>