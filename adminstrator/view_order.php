<?php
require_once 'auth.php';
require_once '../config/db.php';

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// Get the order details with user info
try {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: orders.php');
        exit;

    }
} catch (PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Order #<?php echo $order['id']; ?> - Hypecrews Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
        }
        
        .glass-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0;
            background: #f5f5f9; overflow: hidden; pointer-events: none;
        }
        
        .glass-bg::before, .glass-bg::after, .glass-blob {
            content: ''; position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6;
        }
        
        .glass-bg::before { background: #dbeafe; width: 600px; height: 600px; top: -100px; right: -100px; }
        .glass-bg::after { background: #f3e8ff; width: 500px; height: 500px; bottom: -100px; left: 10%; }
        .glass-blob { background: #e0f2fe; width: 400px; height: 400px; top: 40%; left: 50%; transform: translate(-50%, -50%); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        
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
<body class="text-apple_text">

    <div class="glass-bg"><div class="glass-blob"></div></div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl text-white">
            <?php 
            $current_page = 'orders';
            include 'components/sidebar.php'; 
            ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div class="flex items-center">
                    <a href="orders.php" class="mr-4 w-10 h-10 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_text transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-apple_text tracking-tight">View Order #<?php echo $order['id']; ?></h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 px-6 rounded-full flex items-center transition-colors border border-primary/20 shadow-sm">
                        <i class="fas fa-edit mr-2"></i> Edit Order
                    </a>
                </div>
            </header>
            
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-6 border-b border-black/5">
                        <div class="mb-4 md:mb-0">
                            <h3 class="text-2xl font-bold text-apple_text mb-1"><?php echo htmlspecialchars($order['order_title']); ?></h3>
                            <p class="text-sm font-medium text-apple_muted flex items-center">
                                <i class="far fa-clock mr-2"></i> Created on <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?>
                            </p>
                        </div>
                        <div>
                            <?php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'in_review' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'approved' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'processing' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                'in_production' => 'bg-orange-100 text-orange-800 border-orange-200',
                                'quality_check' => 'bg-teal-100 text-teal-800 border-teal-200',
                                'ready_for_delivery' => 'bg-green-50 text-green-700 border-green-200',
                                'shipped' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'delivered' => 'bg-green-100 text-green-800 border-green-200',
                                'revision_requested' => 'bg-pink-100 text-pink-800 border-pink-200',
                                'on_hold' => 'bg-gray-200 text-gray-800 border-gray-300',
                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-200'
                            ];
                            $statusClass = isset($statusClasses[$order['status']]) ? $statusClasses[$order['status']] : 'bg-gray-100 text-gray-800 border-gray-200';
                            ?>
                            <span class="px-4 py-2 rounded-full text-sm font-bold border uppercase tracking-wider <?php echo $statusClass; ?>">
                                <?php echo str_replace('_', ' ', $order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <h4 class="font-bold text-lg mb-5 text-apple_text flex items-center">
                                <i class="fas fa-file-alt text-primary mr-2"></i> Order Details
                            </h4>
                            <div class="space-y-6">
                                <div class="bg-white/50 p-5 rounded-2xl border border-black/5">
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-2">Description</p>
                                    <p class="text-apple_text leading-relaxed"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>
                                </div>
                                
                                <?php if ($order['tracking_id']): ?>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Tracking ID</p>
                                    <p class="font-mono text-apple_text bg-white/60 inline-block px-3 py-1.5 rounded-lg border border-black/5 font-medium"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($order['custom_status']): ?>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Custom Status</p>
                                    <p class="text-apple_text font-medium"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Last Updated</p>
                                    <p class="text-apple_text font-medium flex items-center">
                                        <i class="fas fa-history text-gray-400 mr-2 text-xs"></i>
                                        <?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg mb-5 text-apple_text flex items-center">
                                <i class="fas fa-user-circle text-primary mr-2"></i> Assigned User
                            </h4>
                            <?php if ($order['user_id']): ?>
                            <div class="bg-gradient-to-br from-white/80 to-white/40 rounded-2xl p-6 border border-white shadow-sm hover:shadow-md transition-shadow cursor-pointer group" onclick="window.location.href='viewuserdata.php?id=<?php echo $order['user_id']; ?>'">
                                <div class="flex items-center">
                                    <div class="w-14 h-14 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-primary mr-4 shadow-inner">
                                        <span class="font-bold text-lg"><?php echo strtoupper(substr($order['first_name'], 0, 1) . substr($order['last_name'], 0, 1)); ?></span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-bold text-lg text-apple_text group-hover:text-primary transition-colors"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                                <p class="text-sm text-primary/80 font-medium">@<?php echo htmlspecialchars($order['username']); ?></p>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                                                <i class="fas fa-chevron-right text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-black/5">
                                            <p class="text-sm text-apple_muted flex items-center font-medium">
                                                <i class="fas fa-envelope mr-2 text-gray-400"></i> <?php echo htmlspecialchars($order['email']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="bg-white/40 rounded-2xl p-8 border border-white text-center border-dashed">
                                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <i class="fas fa-user-slash text-xl"></i>
                                </div>
                                <p class="text-apple_muted font-medium">No user assigned to this order</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>