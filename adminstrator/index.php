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
                        primary: '#1d4ed8', // Enterprise Blue (Tailwind blue-700)
                        secondary: '#334155', // Slate 700
                        gov_bg: '#f8fafc', // Slate 50
                        gov_card: '#ffffff',
                        gov_border: '#e2e8f0', // Slate 200
                        gov_text: '#0f172a', // Slate 900
                        gov_text_muted: '#64748b' // Slate 500
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0f172a; /* Sidebar background context */
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        /* Custom scrollbar for modern enterprise look */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Wrapper - Ensure dark context is maintained for sidebar component -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content - Light Enterprise Theme -->
        <div class="flex-1 flex flex-col h-full bg-gov_bg text-gov_text overflow-hidden relative">
            
            <!-- Enterprise Header -->
            <header class="bg-white border-b border-gov_border px-8 py-5 flex justify-between items-center shadow-sm z-10">
                <div>
                    <h1 class="text-2xl font-bold text-gov_text tracking-tight">Dashboard Overview</h1>
                    <p class="text-sm text-gov_text_muted mt-1 font-medium">Hypecrews Administrative Portal</p>
                </div>
                <div class="hidden md:flex items-center gap-4 bg-slate-100 px-4 py-2 rounded-lg border border-slate-200">
                    <i class="far fa-calendar-alt text-primary"></i>
                    <span class="text-sm font-semibold text-secondary"><?php echo date('l, F j, Y'); ?></span>
                </div>
            </header>
            
            <!-- Scrollable Content Area -->
            <div class="flex-1 overflow-y-auto p-8">
                
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-md bg-red-50 border-l-4 border-red-600 shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3"></i>
                        <p class="text-red-800 font-medium"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Metrics Section -->
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-secondary border-b border-gov_border pb-2 mb-6 uppercase tracking-wider">Key Performance Indicators</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        
                        <!-- Metric Card 1 -->
                        <div class="bg-white rounded-lg border border-gov_border p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gov_text_muted uppercase tracking-wider mb-1">Total Orders</p>
                                    <p class="text-3xl font-bold text-gov_text"><?php echo number_format($total_orders); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded bg-blue-50 flex items-center justify-center border border-blue-100">
                                    <i class="fas fa-box text-primary text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center text-sm text-green-600 font-medium">
                                <i class="fas fa-arrow-up mr-1"></i> System metric
                            </div>
                        </div>
                        
                        <!-- Metric Card 2 -->
                        <div class="bg-white rounded-lg border border-gov_border p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gov_text_muted uppercase tracking-wider mb-1">Total Users</p>
                                    <p class="text-3xl font-bold text-gov_text"><?php echo number_format($total_users); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded bg-indigo-50 flex items-center justify-center border border-indigo-100">
                                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center text-sm text-green-600 font-medium">
                                <i class="fas fa-arrow-up mr-1"></i> Registered users
                            </div>
                        </div>
                        
                        <!-- Metric Card 3 -->
                        <div class="bg-white rounded-lg border border-gov_border p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gov_text_muted uppercase tracking-wider mb-1">Total Reviews</p>
                                    <p class="text-3xl font-bold text-gov_text"><?php echo number_format($total_reviews); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded bg-emerald-50 flex items-center justify-center border border-emerald-100">
                                    <i class="fas fa-star text-emerald-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center text-sm text-gov_text_muted font-medium">
                                <i class="fas fa-chart-line mr-1"></i> Feedback collected
                            </div>
                        </div>
                        
                        <!-- Metric Card 4 -->
                        <div class="bg-white rounded-lg border border-gov_border p-6 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gov_text_muted uppercase tracking-wider mb-1">Active Queries</p>
                                    <p class="text-3xl font-bold text-gov_text"><?php echo number_format($total_queries); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded bg-orange-50 flex items-center justify-center border border-orange-100">
                                    <i class="fas fa-question-circle text-orange-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center text-sm text-gov_text_muted font-medium">
                                <i class="fas fa-headset mr-1"></i> Support requests
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                <!-- Data Tables Section -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                    
                    <!-- Recent Orders Table -->
                    <div class="bg-white rounded-lg border border-gov_border shadow-sm flex flex-col">
                        <div class="p-6 border-b border-gov_border flex justify-between items-center bg-slate-50/50 rounded-t-lg">
                            <h3 class="text-lg font-bold text-gov_text flex items-center"><i class="fas fa-file-invoice text-primary mr-2"></i> Recent Order Operations</h3>
                            <a href="orders.php" class="text-sm font-semibold text-primary hover:text-blue-800 transition-colors">View Directory &rarr;</a>
                        </div>
                        <div class="p-0 overflow-x-auto flex-grow">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-gov_border text-xs uppercase tracking-wider text-gov_text_muted font-bold">
                                        <th class="px-6 py-4">Reference ID / Subject</th>
                                        <th class="px-6 py-4">Requester</th>
                                        <th class="px-6 py-4 text-right">Current Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gov_border">
                                    <?php if (empty($recent_orders)): ?>
                                        <tr><td colspan="3" class="px-6 py-8 text-center text-gov_text_muted">No records found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_orders as $order): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gov_text"><?php echo htmlspecialchars($order['order_title']); ?></div>
                                                <div class="text-xs text-gov_text_muted mt-1 font-mono">ORD-<?php echo str_pad($order['id'], 5, '0', STR_PAD_LEFT); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gov_text">
                                                    <?php echo $order['user_id'] ? htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) : "System / Unassigned"; ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <?php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    'in_review' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'approved' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                    'processing' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                    'in_production' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                                    'quality_check' => 'bg-teal-100 text-teal-800 border-teal-200',
                                                    'ready_for_delivery' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                    'shipped' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                                    'revision_requested' => 'bg-orange-100 text-orange-800 border-orange-200',
                                                    'on_hold' => 'bg-amber-100 text-amber-800 border-amber-200',
                                                    'completed' => 'bg-green-100 text-green-800 border-green-200',
                                                    'cancelled' => 'bg-red-100 text-red-800 border-red-200'
                                                ];
                                                ?>
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold rounded-md border <?php echo $statusClasses[$order['status']]; ?>">
                                                    <?php echo strtoupper(str_replace('_', ' ', $order['status'])); ?>
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
                    <div class="bg-white rounded-lg border border-gov_border shadow-sm flex flex-col">
                        <div class="p-6 border-b border-gov_border flex justify-between items-center bg-slate-50/50 rounded-t-lg">
                            <h3 class="text-lg font-bold text-gov_text flex items-center"><i class="fas fa-inbox text-primary mr-2"></i> Communications & Inquiries</h3>
                            <a href="queries.php" class="text-sm font-semibold text-primary hover:text-blue-800 transition-colors">View All &rarr;</a>
                        </div>
                        <div class="p-0 overflow-x-auto flex-grow">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-gov_border text-xs uppercase tracking-wider text-gov_text_muted font-bold">
                                        <th class="px-6 py-4">Service Inquiry</th>
                                        <th class="px-6 py-4">Submitted By</th>
                                        <th class="px-6 py-4 text-right">Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gov_border">
                                    <?php if (empty($recent_queries)): ?>
                                        <tr><td colspan="3" class="px-6 py-8 text-center text-gov_text_muted">No records found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recent_queries as $query): ?>
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-semibold text-gov_text"><?php echo htmlspecialchars($query['service_name']); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gov_text"><?php echo htmlspecialchars($query['name']); ?></div>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm text-gov_text_muted">
                                                <?php 
                                                $query_time = strtotime($query['created_at']);
                                                $current_time = time();
                                                $time_diff = $current_time - $query_time;
                                                
                                                if ($time_diff < 60) {
                                                    echo "Just now";
                                                } elseif ($time_diff < 3600) {
                                                    $minutes = floor($time_diff / 60);
                                                    echo $minutes . " min ago";
                                                } elseif ($time_diff < 86400) {
                                                    $hours = floor($time_diff / 3600);
                                                    echo $hours . " hr ago";
                                                } else {
                                                    echo date('M j, Y', $query_time);
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
                <div class="mt-8 pt-6 border-t border-gov_border text-center text-sm text-gov_text_muted pb-4">
                    &copy; <?php echo date('Y'); ?> Hypecrews Administrative System. All rights reserved. Authorized personnel only.
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>