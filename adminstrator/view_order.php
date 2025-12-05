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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        
        .sidebar {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(10px);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .nav-link {
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body class="text-white">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0 flex flex-col">
            <div class="p-6 border-b border-gray-800">
                <h1 class="text-2xl font-bold">Hypecrews <span class="text-primary">Admin</span></h1>
            </div>
            
            <nav class="flex-1 py-6">
                <a href="index.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="orders.php" class="nav-link active flex items-center px-6 py-3 text-white">
                    <i class="fas fa-box mr-3"></i>
                    <span>Orders</span>
                </a>
                <a href="users.php" class="nav-link flex items-center px-6 py-3 text-gray-400 hover:text-white">
                    <i class="fas fa-users mr-3"></i>
                    <span>Users</span>
                </a>
            </nav>
            
            <div class="p-6 border-t border-gray-800">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center mr-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <p class="font-medium"><?php echo htmlspecialchars($admin_username); ?></p>
                        <p class="text-sm text-gray-400">Administrator</p>
                    </div>
                </div>
                <a href="logout.php" class="mt-4 flex items-center text-gray-400 hover:text-white">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-dark border-b border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">View Order #<?php echo $order['id']; ?></h2>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.location.href='orders.php'" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Orders
                        </button>
                    </div>
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
                
                <div class="bg-light rounded-xl p-6 mb-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold"><?php echo htmlspecialchars($order['order_title']); ?></h3>
                            <p class="text-gray-400">Order #<?php echo $order['id']; ?> • Created on <?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                        </div>
                        <div>
                            <?php
                            $statusClasses = [
                                'pending' => 'bg-yellow-500/10 text-yellow-500',
                                'processing' => 'bg-blue-500/10 text-blue-500',
                                'shipped' => 'bg-indigo-500/10 text-indigo-500',
                                'delivered' => 'bg-green-500/10 text-green-500',
                                'cancelled' => 'bg-red-500/10 text-red-500'
                            ];
                            ?>
                            <span class="status-badge <?php echo $statusClasses[$order['status']]; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold text-lg mb-4">Order Details</h4>
                            <div class="space-y-4">
                                <div>
                                    <p class="text-gray-400 text-sm">Description</p>
                                    <p class="mt-1"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>
                                </div>
                                
                                <?php if ($order['tracking_id']): ?>
                                <div>
                                    <p class="text-gray-400 text-sm">Tracking ID</p>
                                    <p class="font-mono mt-1"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($order['custom_status']): ?>
                                <div>
                                    <p class="text-gray-400 text-sm">Custom Status</p>
                                    <p class="mt-1"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <p class="text-gray-400 text-sm">Last Updated</p>
                                    <p class="mt-1"><?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="font-bold text-lg mb-4">Assigned User</h4>
                            <?php if ($order['user_id']): ?>
                            <div class="bg-dark/50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center mr-4">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        <p class="text-gray-400">@<?php echo htmlspecialchars($order['username']); ?></p>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($order['email']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="bg-dark/50 rounded-lg p-4 text-center">
                                <p class="text-gray-400">No user assigned to this order</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4">
                    <button onclick="window.location.href='edit_order.php?id=<?php echo $order['id']; ?>'" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg flex items-center">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>