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
    
    // Recent orders (limit to 3)
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 3");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Recent queries (limit to 3)
    $stmt = $pdo->prepare("SELECT * FROM service_queries ORDER BY created_at DESC LIMIT 3");
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0f172a',
                        light: '#1e293b'
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
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body class="text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-dark border-b border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Dashboard</h2>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-red-300"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-light rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mr-4">
                                <i class="fas fa-box text-primary text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Total Orders</p>
                                <p class="text-2xl font-bold"><?php echo $total_orders; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center mr-4">
                                <i class="fas fa-users text-secondary text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Total Users</p>
                                <p class="text-2xl font-bold"><?php echo $total_users; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-green-500/10 flex items-center justify-center mr-4">
                                <i class="fas fa-star text-green-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Reviews</p>
                                <p class="text-2xl font-bold"><?php echo $total_reviews; ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-yellow-500/10 flex items-center justify-center mr-4">
                                <i class="fas fa-question-circle text-yellow-500 text-xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-400">Queries</p>
                                <p class="text-2xl font-bold"><?php echo $total_queries; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-light rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4">Recent Orders</h3>
                        <div class="space-y-4">
                            <?php if (empty($recent_orders)): ?>
                                <p class="text-gray-400 text-center py-4">No orders yet</p>
                            <?php else: ?>
                                <?php foreach ($recent_orders as $order): ?>
                                <div class="flex items-center justify-between p-3 bg-dark/50 rounded-lg">
                                    <div>
                                        <p class="font-medium"><?php echo htmlspecialchars($order['order_title']); ?></p>
                                        <p class="text-sm text-gray-400">
                                            <?php 
                                            if ($order['user_id']) {
                                                echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']);
                                            } else {
                                                echo "No user assigned";
                                            }
                                            ?>
                                        </p>
                                    </div>
                                    <?php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-500/10 text-yellow-500',
                                        'in_review' => 'bg-blue-500/10 text-blue-500',
                                        'approved' => 'bg-indigo-500/10 text-indigo-500',
                                        'processing' => 'bg-purple-500/10 text-purple-500',
                                        'in_production' => 'bg-cyan-500/10 text-cyan-500',
                                        'quality_check' => 'bg-teal-500/10 text-teal-500',
                                        'ready_for_delivery' => 'bg-green-500/10 text-green-500',
                                        'shipped' => 'bg-blue-500/10 text-blue-500',
                                        'delivered' => 'bg-green-500/10 text-green-500',
                                        'revision_requested' => 'bg-orange-500/10 text-orange-500',
                                        'on_hold' => 'bg-yellow-500/10 text-yellow-500',
                                        'completed' => 'bg-green-500/10 text-green-500',
                                        'cancelled' => 'bg-red-500/10 text-red-500'
                                    ];
                                    ?>
                                    <span class="px-3 py-1 <?php echo $statusClasses[$order['status']]; ?> rounded-full text-sm">
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="bg-light rounded-xl p-6">
                        <h3 class="text-lg font-bold mb-4">Recent Queries</h3>
                        <div class="space-y-4">
                            <?php if (empty($recent_queries)): ?>
                                <p class="text-gray-400 text-center py-4">No queries yet</p>
                            <?php else: ?>
                                <?php foreach ($recent_queries as $query): ?>
                                <div class="p-3 bg-dark/50 rounded-lg">
                                    <p class="font-medium"><?php echo htmlspecialchars($query['service_name']); ?></p>
                                    <p class="text-sm text-gray-400"><?php echo htmlspecialchars($query['name']); ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <?php 
                                        $query_time = strtotime($query['created_at']);
                                        $current_time = time();
                                        $time_diff = $current_time - $query_time;
                                        
                                        if ($time_diff < 60) {
                                            echo "Just now";
                                        } elseif ($time_diff < 3600) {
                                            $minutes = floor($time_diff / 60);
                                            echo $minutes . " minute" . ($minutes > 1 ? "s" : "") . " ago";
                                        } elseif ($time_diff < 86400) {
                                            $hours = floor($time_diff / 3600);
                                            echo $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
                                        } elseif ($time_diff < 2592000) {
                                            $days = floor($time_diff / 86400);
                                            echo $days . " day" . ($days > 1 ? "s" : "") . " ago";
                                        } else {
                                            echo date('M j, Y', $query_time);
                                        }
                                        ?>
                                    </p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>