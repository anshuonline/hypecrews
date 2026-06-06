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
                        apple_bg: '#f5f5f7', // Apple Light Gray background
                        apple_card: '#ffffff', // White cards
                        apple_text: '#1d1d1f', // Apple Dark text
                        apple_muted: '#86868b', // Apple Muted text
                        apple_border: 'rgba(0,0,0,0.05)'
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'Roboto', 'sans-serif']
                    },
                    boxShadow: {
                        'apple': '0 4px 24px rgba(0, 0, 0, 0.04)',
                        'apple-hover': '0 10px 40px rgba(0, 0, 0, 0.08)'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0f172a; /* Keep dark context for sidebar if needed */
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        /* Apple-style thin, invisible scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Wrapper - Ensure dark context is maintained for sidebar component -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl border-r border-white/5">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content - Apple Light UI Theme -->
        <div class="flex-1 flex flex-col h-full bg-apple_bg text-apple_text overflow-hidden relative">
            
            <!-- Apple-style Header (Clean, blurry if possible, simple) -->
            <header class="bg-white/80 backdrop-blur-xl border-b border-apple_border px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div>
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Dashboard</h1>
                    <p class="text-sm text-apple_muted mt-1 font-medium">Welcome back to Hypecrews</p>
                </div>
                <div class="hidden md:flex items-center gap-3 bg-black/5 px-5 py-2.5 rounded-full">
                    <i class="far fa-calendar text-apple_text"></i>
                    <span class="text-sm font-semibold text-apple_text"><?php echo date('l, F j'); ?></span>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto p-10">
                
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-2xl bg-red-50 border border-red-100 shadow-apple text-red-600 font-medium">
                    <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <!-- Metrics Section -->
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-apple_text mb-6">Overview</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Metric Card 1 -->
                        <div class="bg-apple_card rounded-[2rem] p-7 shadow-apple hover:shadow-apple-hover transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center mb-4">
                                <i class="fas fa-box text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Orders</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_orders); ?></p>
                        </div>
                        
                        <!-- Metric Card 2 -->
                        <div class="bg-apple_card rounded-[2rem] p-7 shadow-apple hover:shadow-apple-hover transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Users</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_users); ?></p>
                        </div>
                        
                        <!-- Metric Card 3 -->
                        <div class="bg-apple_card rounded-[2rem] p-7 shadow-apple hover:shadow-apple-hover transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center mb-4">
                                <i class="fas fa-star text-xl"></i>
                            </div>
                            <p class="text-sm font-semibold text-apple_muted mb-1">Total Reviews</p>
                            <p class="text-4xl font-bold text-apple_text tracking-tight"><?php echo number_format($total_reviews); ?></p>
                        </div>
                        
                        <!-- Metric Card 4 -->
                        <div class="bg-apple_card rounded-[2rem] p-7 shadow-apple hover:shadow-apple-hover transition-all duration-300 transform hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center mb-4">
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
                    <div class="bg-apple_card rounded-[2rem] shadow-apple p-7">
                        <div class="flex justify-between items-end mb-6">
                            <h3 class="text-xl font-bold text-apple_text">Recent Orders</h3>
                            <a href="orders.php" class="text-sm font-medium text-primary hover:underline">See All</a>
                        </div>
                        <div class="overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <tbody class="divide-y divide-black/5">
                                    <?php if (empty($recent_orders)): ?>
                                        <tr><td class="py-8 text-center text-apple_muted">No orders found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_orders as $order): ?>
                                        <tr class="group">
                                            <td class="py-4 pr-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-apple_muted group-hover:bg-primary group-hover:text-white transition-colors">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-apple_text text-sm"><?php echo htmlspecialchars($order['order_title']); ?></div>
                                                        <div class="text-xs text-apple_muted mt-0.5">ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?> &bull; <?php echo $order['user_id'] ? htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) : "Guest"; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right">
                                                <?php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-50 text-yellow-600',
                                                    'in_review' => 'bg-blue-50 text-blue-600',
                                                    'approved' => 'bg-indigo-50 text-indigo-600',
                                                    'processing' => 'bg-purple-50 text-purple-600',
                                                    'in_production' => 'bg-cyan-50 text-cyan-600',
                                                    'quality_check' => 'bg-teal-50 text-teal-600',
                                                    'ready_for_delivery' => 'bg-emerald-50 text-emerald-600',
                                                    'shipped' => 'bg-blue-50 text-blue-600',
                                                    'delivered' => 'bg-green-50 text-green-600',
                                                    'revision_requested' => 'bg-orange-50 text-orange-600',
                                                    'on_hold' => 'bg-amber-50 text-amber-600',
                                                    'completed' => 'bg-green-50 text-green-600',
                                                    'cancelled' => 'bg-red-50 text-red-600'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full <?php echo $statusClasses[$order['status']]; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
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
                    <div class="bg-apple_card rounded-[2rem] shadow-apple p-7">
                        <div class="flex justify-between items-end mb-6">
                            <h3 class="text-xl font-bold text-apple_text">Recent Queries</h3>
                            <a href="queries.php" class="text-sm font-medium text-primary hover:underline">See All</a>
                        </div>
                        <div class="overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <tbody class="divide-y divide-black/5">
                                    <?php if (empty($recent_queries)): ?>
                                        <tr><td class="py-8 text-center text-apple_muted">No queries found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_queries as $query): ?>
                                        <tr class="group">
                                            <td class="py-4 pr-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-apple_muted group-hover:bg-primary group-hover:text-white transition-colors">
                                                        <i class="fas fa-inbox"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-apple_text text-sm"><?php echo htmlspecialchars($query['service_name']); ?></div>
                                                        <div class="text-xs text-apple_muted mt-0.5"><?php echo htmlspecialchars($query['name']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 text-right text-xs font-medium text-apple_muted">
                                                <?php 
                                                $query_time = strtotime($query['created_at']);
                                                $current_time = time();
                                                $time_diff = $current_time - $query_time;
                                                
                                                if ($time_diff < 60) {
                                                    echo "Just now";
                                                } elseif ($time_diff < 3600) {
                                                    $minutes = floor($time_diff / 60);
                                                    echo $minutes . "m ago";
                                                } elseif ($time_diff < 86400) {
                                                    $hours = floor($time_diff / 3600);
                                                    echo $hours . "h ago";
                                                } else {
                                                    echo date('M j', $query_time);
                                                }
                                                ?>
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
                <div class="mt-12 text-center text-sm font-medium text-apple_muted">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>