<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'dashboard';

// Fetch dashboard statistics
try {
    // Total orders
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders");
    $stmt->execute();
    $total_orders = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total users
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM users");
    $stmt->execute();
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total reviews
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM order_reviews");
    $stmt->execute();
    $total_reviews = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total queries
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM service_queries");
    $stmt->execute();
    $total_queries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Recent orders (limit to 5)
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent queries (limit to 5)
    $stmt = $pdo->prepare("SELECT * FROM service_queries ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_queries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error fetching dashboard data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066cc', // Apple Blue
                        apple_text: '#1d1d1f', // Apple Dark text
                        apple_muted: '#86868b', // Apple Muted text
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'Roboto', 'sans-serif']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f5f5f7; 
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            position: relative;
        }
        
        /* The colorful blurred background that makes the glassmorphism visible */
        .glass-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: #f5f5f9;
            overflow: hidden;
            pointer-events: none;
        }
        
        .glass-bg::before, .glass-bg::after, .glass-blob {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
        }
        
        .glass-bg::before {
            background: #dbeafe; /* Light blue */
            width: 600px;
            height: 600px;
            top: -100px;
            right: -100px;
        }
        
        .glass-bg::after {
            background: #f3e8ff; /* Light purple */
            width: 500px;
            height: 500px;
            bottom: -100px;
            left: 10%;
        }
        
        .glass-blob {
            background: #e0f2fe; /* Sky blue */
            width: 400px;
            height: 400px;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Apple-style thin, invisible scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        
        /* Glass panel utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white">
    
    <!-- Abstract blurred colorful background -->
    <div class="glass-bg">
        <div class="glass-blob"></div>
    </div>

    <div class="flex h-screen overflow-hidden relative z-10">
        
        <!-- Sidebar Wrapper -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content - Apple Glass UI Theme -->
        <div class="flex-1 flex flex-col h-full text-apple_text overflow-hidden relative">
            
            <!-- Apple-style Glass Header -->
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div>
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Dashboard</h1>
                    <p class="text-sm text-apple_muted mt-1 font-medium">Welcome back to Hypecrews</p>
                </div>
                <div class="hidden md:flex items-center gap-3 bg-white/40 backdrop-blur-md px-5 py-2.5 rounded-full border border-white/60 shadow-sm">
                    <i class="far fa-calendar text-apple_text"></i>
                    <span class="text-sm font-semibold text-apple_text"><?php echo date('l, F j'); ?></span>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-600 font-medium">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <!-- Metrics Section -->
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-apple_text mb-6">Overview</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Metric Card 1 -->
                        <div class="glass-panel rounded-3xl p-7 hover:bg-white/80 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-box text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Orders</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_orders); ?></p>
                        </div>
                        
                        <!-- Metric Card 2 -->
                        <div class="glass-panel rounded-3xl p-7 hover:bg-white/80 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-600 flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Users</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_users); ?></p>
                        </div>
                        
                        <!-- Metric Card 3 -->
                        <div class="glass-panel rounded-3xl p-7 hover:bg-white/80 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-star text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Reviews</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_reviews); ?></p>
                        </div>
                        
                        <!-- Metric Card 4 -->
                        <div class="glass-panel rounded-3xl p-7 hover:bg-white/80 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-600 flex items-center justify-center mb-4 shadow-inner">
                                <i class="fas fa-question-circle text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Active Queries</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_queries); ?></p>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Data Tables Section -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    
                    <!-- Recent Orders Table -->
                    <div class="glass-panel rounded-3xl p-7 flex flex-col h-full">
                        <div class="flex justify-between items-end mb-6">
                            <h3 class="text-xl font-bold text-apple_text">Recent Orders</h3>
                            <a href="orders.php" class="text-sm font-medium text-primary hover:underline bg-white/40 px-3 py-1.5 rounded-full border border-white/60">See All</a>
                        </div>
                        <div class="overflow-x-auto flex-grow">
                            <table class="w-full text-left border-collapse">
                                <tbody class="divide-y divide-black/5">
                                    <?php if (empty($recent_orders)): ?>
                                        <tr><td class="py-8 text-center text-apple_muted">No orders found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_orders as $order): ?>
                                        <tr class="group hover:bg-white/30 transition-colors rounded-xl">
                                            <td class="py-4 pr-4 pl-2 rounded-l-xl">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-11 h-11 rounded-full bg-white/50 border border-white/60 flex items-center justify-center text-apple_muted shadow-sm group-hover:bg-primary group-hover:text-white transition-colors">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-apple_text text-sm"><?php echo htmlspecialchars($order['order_title']); ?></div>
                                                        <div class="text-xs text-apple_muted mt-0.5 font-medium">ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?> &bull; <?php echo $order['user_id'] ? htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) : "Guest"; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right pr-2 rounded-r-xl">
                                                <?php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-500/10 text-yellow-700 border-yellow-500/20',
                                                    'in_review' => 'bg-blue-500/10 text-blue-700 border-blue-500/20',
                                                    'approved' => 'bg-indigo-500/10 text-indigo-700 border-indigo-500/20',
                                                    'processing' => 'bg-purple-500/10 text-purple-700 border-purple-500/20',
                                                    'in_production' => 'bg-cyan-500/10 text-cyan-700 border-cyan-500/20',
                                                    'quality_check' => 'bg-teal-500/10 text-teal-700 border-teal-500/20',
                                                    'ready_for_delivery' => 'bg-emerald-500/10 text-emerald-700 border-emerald-500/20',
                                                    'shipped' => 'bg-blue-500/10 text-blue-700 border-blue-500/20',
                                                    'delivered' => 'bg-green-500/10 text-green-700 border-green-500/20',
                                                    'revision_requested' => 'bg-orange-500/10 text-orange-700 border-orange-500/20',
                                                    'on_hold' => 'bg-amber-500/10 text-amber-700 border-amber-500/20',
                                                    'completed' => 'bg-green-500/10 text-green-700 border-green-500/20',
                                                    'cancelled' => 'bg-red-500/10 text-red-700 border-red-500/20'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-3 py-1 text-[11px] uppercase tracking-wider font-bold rounded-full border <?php echo $statusClasses[$order['status']]; ?> shadow-sm">
                                                    <?php echo str_replace('_', ' ', $order['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Recent Queries Table -->
                    <div class="glass-panel rounded-3xl p-7 flex flex-col h-full">
                        <div class="flex justify-between items-end mb-6">
                            <h3 class="text-xl font-bold text-apple_text">Recent Queries</h3>
                            <a href="queries.php" class="text-sm font-medium text-primary hover:underline bg-white/40 px-3 py-1.5 rounded-full border border-white/60">See All</a>
                        </div>
                        <div class="overflow-x-auto flex-grow">
                            <table class="w-full text-left border-collapse">
                                <tbody class="divide-y divide-black/5">
                                    <?php if (empty($recent_queries)): ?>
                                        <tr><td class="py-8 text-center text-apple_muted">No queries found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_queries as $query): ?>
                                        <tr class="group hover:bg-white/30 transition-colors rounded-xl">
                                            <td class="py-4 pr-4 pl-2 rounded-l-xl">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-11 h-11 rounded-full bg-white/50 border border-white/60 flex items-center justify-center text-apple_muted shadow-sm group-hover:bg-primary group-hover:text-white transition-colors">
                                                        <i class="fas fa-inbox"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-apple_text text-sm"><?php echo htmlspecialchars($query['service_name']); ?></div>
                                                        <div class="text-xs text-apple_muted mt-0.5 font-medium"><?php echo htmlspecialchars($query['name']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right pr-2 rounded-r-xl">
                                                <div class="inline-flex items-center px-3 py-1 text-[11px] uppercase tracking-wider font-bold rounded-full border bg-black/5 border-black/5 text-apple_muted shadow-sm">
                                                    <?php 
                                                    $query_time = strtotime($query['created_at']);
                                                    $current_time = time();
                                                    $time_diff = $current_time - $query_time;
                                                    
                                                    if ($time_diff < 60) {
                                                        echo "JUST NOW";
                                                    } elseif ($time_diff < 3600) {
                                                        $minutes = floor($time_diff / 60);
                                                        echo $minutes . "M AGO";
                                                    } elseif ($time_diff < 86400) {
                                                        $hours = floor($time_diff / 3600);
                                                        echo $hours . "H AGO";
                                                    } else {
                                                        echo strtoupper(date('M j', $query_time));
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                
                <!-- Footer within content -->
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>