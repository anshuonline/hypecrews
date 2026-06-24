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
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telemetry Stream #<?php echo $order['id']; ?> - Peak Experience</title>
    <!-- SEO Meta Tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="View detailed order information, status timeline, and tracking data for your Hypecrews service order.">
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
        .reveal-up { opacity: 0; transform: translateY(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-left { opacity: 0; transform: translateX(-30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-right { opacity: 0; transform: translateX(30px); transition: all 0.8s cubic-bezier(0.5, 0, 0, 1); }
        .reveal-up.active, .reveal-left.active, .reveal-right.active { opacity: 1; transform: translate(0); }
        .delay-100 { transition-delay: 100ms; }
        .delay-200 { transition-delay: 200ms; }
        
        @keyframes neonPulse {
            0% { box-shadow: 0 0 5px #6366f1, 0 0 10px #6366f1; }
            50% { box-shadow: 0 0 10px #8b5cf6, 0 0 20px #8b5cf6, 0 0 30px #8b5cf6; }
            100% { box-shadow: 0 0 5px #6366f1, 0 0 10px #6366f1; }
        }
        
        @keyframes neonLinePulse {
            0% { box-shadow: 0 0 2px #6366f1, 0 0 5px #6366f1; background-color: #6366f1; }
            50% { box-shadow: 0 0 5px #8b5cf6, 0 0 10px #8b5cf6; background-color: #8b5cf6; }
            100% { box-shadow: 0 0 2px #6366f1, 0 0 5px #6366f1; background-color: #6366f1; }
        }

        .timeline-item {
            position: relative;
            padding-left: 2.5rem;
            padding-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            background-color: #6366f1;
            animation: neonPulse 2s infinite alternate;
            z-index: 10;
        }
        
        .timeline-item:after {
            content: '';
            position: absolute;
            left: 0.9375rem;
            top: 1.5rem;
            width: 0.125rem;
            height: calc(100%);
            background-color: #6366f1;
            animation: neonLinePulse 2s infinite alternate;
            z-index: 5;
        }
        
        .timeline-item:last-child:after {
            display: none;
        }
        
        .timeline-item:hover {
            transform: translateX(5px);
        }
        
        .timeline-content {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 1rem;
            padding: 1rem;
            transition: all 0.3s ease;
        }
        
        .timeline-item:hover .timeline-content {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 0 15px rgba(99, 102, 241, 0.1);
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
    <?php include 'components/google_analytics.php'; ?>
</head>
<body class="antialiased selection:bg-primary selection:text-white">
    <div class="ambient-bg"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-32 pb-20 min-h-screen relative z-10">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl">
            <!-- Header section -->
            <div class="mb-10 reveal-up flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <a href="track_orders.php" class="inline-flex items-center text-gray-400 hover:text-primary transition-colors mb-4 group text-sm font-bold tracking-wider uppercase">
                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Return to Telemetry
                    </a>
                    <h1 class="font-heading text-4xl md:text-5xl font-black mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Node Data <span class="text-primary opacity-80">#<?php echo $order['id']; ?></span></h1>
                    <p class="text-gray-400 font-light"><?php echo htmlspecialchars($order['order_title']); ?></p>
                </div>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="mb-8 p-4 rounded-2xl bg-red-900/20 border border-red-500/30 backdrop-blur-md reveal-up flex items-center shadow-[0_0_20px_rgba(239,68,68,0.1)]">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mr-4 animate-pulse"></i>
                <p class="text-red-200 font-medium"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Details -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Order Specs -->
                    <div class="glass-panel rounded-3xl p-8 reveal-left relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-primary/5 rounded-full blur-[40px] group-hover:bg-primary/10 transition-all pointer-events-none"></div>
                        <h2 class="font-heading text-2xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-file-contract text-primary"></i> Operation Specifications
                        </h2>
                        
                        <div class="space-y-6 relative z-10">
                            <div class="bg-white/5 rounded-2xl p-5 border border-white/5">
                                <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase mb-2">Detailed Directive</p>
                                <p class="text-gray-300 font-light leading-relaxed"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>
                            </div>
                            
                            <?php
                            $statusStyles = [
                                'pending' => 'border-yellow-500/30 text-yellow-400 bg-yellow-500/10 shadow-[0_0_15px_rgba(234,179,8,0.2)]',
                                'in_review' => 'border-blue-500/30 text-blue-400 bg-blue-500/10 shadow-[0_0_15px_rgba(59,130,246,0.2)]',
                                'approved' => 'border-indigo-500/30 text-indigo-400 bg-indigo-500/10 shadow-[0_0_15px_rgba(99,102,241,0.2)]',
                                'processing' => 'border-purple-500/30 text-purple-400 bg-purple-500/10 shadow-[0_0_15px_rgba(168,85,247,0.2)]',
                                'in_production' => 'border-cyan-500/30 text-cyan-400 bg-cyan-500/10 shadow-[0_0_15px_rgba(6,182,212,0.2)]',
                                'quality_check' => 'border-teal-500/30 text-teal-400 bg-teal-500/10 shadow-[0_0_15px_rgba(20,184,166,0.2)]',
                                'ready_for_delivery' => 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.2)]',
                                'shipped' => 'border-blue-500/30 text-blue-400 bg-blue-500/10 shadow-[0_0_15px_rgba(59,130,246,0.2)]',
                                'delivered' => 'border-green-500/30 text-green-400 bg-green-500/10 shadow-[0_0_15px_rgba(34,197,94,0.2)]',
                                'revision_requested' => 'border-orange-500/30 text-orange-400 bg-orange-500/10 shadow-[0_0_15px_rgba(249,115,22,0.2)]',
                                'on_hold' => 'border-yellow-500/30 text-yellow-400 bg-yellow-500/10 shadow-[0_0_15px_rgba(234,179,8,0.2)]',
                                'completed' => 'border-green-500/30 text-green-400 bg-green-500/10 shadow-[0_0_15px_rgba(34,197,94,0.2)]',
                                'cancelled' => 'border-red-500/30 text-red-400 bg-red-500/10 shadow-[0_0_15px_rgba(239,68,68,0.2)]'
                            ];
                            $currentStyle = $statusStyles[$order['status']] ?? 'border-gray-500/30 text-gray-400 bg-gray-500/10';
                            ?>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <?php if ($order['tracking_id']): ?>
                                <div class="bg-white/5 rounded-2xl p-5 border border-white/5 flex flex-col justify-center">
                                    <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase mb-2">Tracking ID Code</p>
                                    <p class="font-mono text-lg text-primary tracking-widest bg-primary/5 py-2 px-3 rounded-md border border-primary/20 inline-block w-fit"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div class="bg-white/5 rounded-2xl p-5 border border-white/5 <?php echo !$order['tracking_id'] ? 'md:col-span-2' : ''; ?>">
                                    <p class="text-xs font-semibold tracking-wider text-gray-500 uppercase mb-3">Live Status Matrix</p>
                                    <div>
                                        <div class="inline-block px-4 py-1.5 text-xs font-bold uppercase tracking-widest rounded-full border <?php echo $currentStyle; ?>">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-current animate-pulse"></span>
                                                <?php echo str_replace('_', ' ', htmlspecialchars($order['status'])); ?>
                                            </span>
                                        </div>
                                        <?php if ($order['custom_status']): ?>
                                        <p class="text-sm text-gray-400 mt-3 italic">"<?php echo htmlspecialchars($order['custom_status']); ?>"</p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($order['status'] == 'completed' && $order['review_requested']): ?>
                                    <div class="mt-4 pt-4 border-t border-white/5">
                                        <a href="submit_review.php?order_id=<?php echo $order['id']; ?>" class="inline-flex items-center justify-center w-full px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500/20 to-orange-500/20 border border-amber-500/30 text-amber-400 font-bold hover:bg-amber-500/30 hover:shadow-[0_0_15px_rgba(245,158,11,0.2)] transition-all">
                                            <i class="fas fa-star mr-2"></i> Submit Deployment Review
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Neon Timeline -->
                    <div class="glass-panel rounded-3xl p-8 reveal-left delay-100 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-secondary/5 rounded-full blur-[60px] pointer-events-none"></div>
                        <h2 class="font-heading text-2xl font-bold mb-8 flex items-center gap-3">
                            <i class="fas fa-stream text-secondary"></i> Execution Timeline
                        </h2>
                        
                        <?php if (empty($status_history) && empty($order['status'])): ?>
                        <div class="text-center py-12 relative z-10">
                            <i class="fas fa-history text-4xl text-gray-600 mb-4"></i>
                            <p class="text-gray-400 font-light">Node history is empty.</p>
                        </div>
                        <?php else: ?>
                        <div class="relative z-10 pl-2">
                            <?php foreach ($status_history as $index => $history): ?>
                            <div class="timeline-item reveal-up" style="transition-delay: <?php echo $index * 100; ?>ms;">
                                <div class="timeline-content flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                                    <div>
                                        <?php
                                        $hStyle = $statusStyles[$history['status']] ?? 'border-gray-500/30 text-gray-400 bg-gray-500/10';
                                        ?>
                                        <div class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border mb-2 <?php echo $hStyle; ?>">
                                            <?php echo str_replace('_', ' ', htmlspecialchars($history['status'])); ?>
                                        </div>
                                        <?php if ($history['custom_status']): ?>
                                        <p class="text-sm text-gray-300"><?php echo htmlspecialchars($history['custom_status']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs font-mono text-gray-500 sm:text-right shrink-0">
                                        <span class="block"><?php echo date('M j, Y', strtotime($history['created_at'] . ' UTC')); ?></span>
                                        <span class="block"><?php echo date('h:i A', strtotime($history['created_at'] . ' UTC')); ?></span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Current status -->
                            <div class="timeline-item reveal-up" style="transition-delay: <?php echo count($status_history) * 100; ?>ms;">
                                <div class="timeline-content flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 bg-primary/5 border-primary/30 shadow-[0_0_20px_rgba(99,102,241,0.1)]">
                                    <div>
                                        <div class="inline-block px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-full border mb-2 <?php echo $currentStyle; ?>">
                                            <span class="flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current animate-pulse"></span>
                                                <?php echo str_replace('_', ' ', htmlspecialchars($order['status'])); ?> (Active Node)
                                            </span>
                                        </div>
                                        <?php if ($order['custom_status']): ?>
                                        <p class="text-sm text-gray-200"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-xs font-mono text-primary/70 sm:text-right shrink-0">
                                        <span class="block"><?php echo date('M j, Y', strtotime($order['updated_at'] . ' UTC')); ?></span>
                                        <span class="block"><?php echo date('h:i A', strtotime($order['updated_at'] . ' UTC')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="glass-panel rounded-3xl p-8 sticky top-32 reveal-right delay-200 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-full blur-[40px] group-hover:bg-accent/10 transition-all pointer-events-none"></div>
                        <h2 class="font-heading text-xl font-bold mb-6 flex items-center gap-3">
                            <i class="fas fa-server text-accent"></i> Node Summary
                        </h2>
                        
                        <div class="space-y-5 relative z-10">
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-hashtag w-4 text-indigo-400"></i> Operation ID</span>
                                <span class="text-sm font-mono font-bold text-white">#<?php echo $order['id']; ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-play-circle w-4 text-emerald-400"></i> Init Date</span>
                                <span class="text-sm font-medium"><?php echo date('M j, Y', strtotime($order['created_at'] . ' UTC')); ?></span>
                            </div>
                            
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-sync-alt w-4 text-cyan-400"></i> Last Sync</span>
                                <span class="text-sm font-medium"><?php echo date('M j, Y', strtotime($order['updated_at'] . ' UTC')); ?></span>
                            </div>
                            
                            <?php if ($order['tracking_id']): ?>
                            <div class="flex items-center justify-between border-b border-white/5 pb-3">
                                <span class="text-gray-400 text-sm flex items-center gap-2"><i class="fas fa-qrcode w-4 text-purple-400"></i> Trace Code</span>
                                <span class="text-xs font-mono bg-white/10 px-2 py-0.5 rounded text-gray-300"><?php echo htmlspecialchars($order['tracking_id']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-8">
                            <button onclick="window.print()" class="w-full px-5 py-3 rounded-xl border border-gray-600 text-gray-300 hover:text-white hover:bg-white/5 hover:border-gray-400 transition-all flex items-center justify-center gap-2 text-sm font-bold tracking-wider uppercase">
                                <i class="fas fa-print"></i> Export Log
                            </button>
                        </div>
                    </div>
                </div>
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
            
            document.querySelectorAll('.reveal-up, .reveal-left, .reveal-right').forEach(el => observer.observe(el));
        });
    </script>
    <script src="js/main.js"></script>
</body>
</html>
