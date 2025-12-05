<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'orders';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = "Order deleted successfully";
    } catch (PDOException $e) {
        $error = "Error deleting order: " . $e->getMessage();
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get orders with user info (with or without search)
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name, u.email, u.mobile_number FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE u.username LIKE ? OR u.email LIKE ? OR u.mobile_number LIKE ? OR o.tracking_id LIKE ? ORDER BY o.created_at DESC");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
        $stmt->execute();
    }
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Hypecrews Admin</title>
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
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
                    <h2 class="text-2xl font-bold">Manage Orders</h2>
                    <div class="flex items-center space-x-4">
                        <button onclick="window.location.href='add_order.php'" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Add Order
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (isset($success)): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
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
                
                <div class="bg-light rounded-xl p-6">
                    <!-- Search Form -->
                    <div class="mb-6">
                        <form method="GET" class="flex">
                            <input type="text" name="search" placeholder="Search by username, email, mobile, or tracking ID..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-4 py-2 bg-dark border border-gray-700 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-r-lg">
                                <i class="fas fa-search text-white"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="orders.php" class="ml-2 bg-gray-600 hover:bg-gray-700 px-4 rounded-lg flex items-center">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (empty($orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-box-open text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No orders found</p>
                        <button onclick="window.location.href='add_order.php'" class="mt-4 bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                            Create Your First Order
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Order</th>
                                    <th class="pb-3">User</th>
                                    <th class="pb-3">Tracking ID</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium"><?php echo htmlspecialchars($order['order_title']); ?></p>
                                        <p class="text-sm text-gray-400"><?php echo substr(htmlspecialchars($order['order_description']), 0, 50); ?>...</p>
                                    </td>
                                    <td class="py-4">
                                        <?php if ($order['user_id']): ?>
                                        <p><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        <p class="text-sm text-gray-400">@<?php echo htmlspecialchars($order['username']); ?></p>
                                        <?php else: ?>
                                        <p class="text-gray-400">No user assigned</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <?php if ($order['tracking_id']): ?>
                                        <span class="font-mono"><?php echo htmlspecialchars($order['tracking_id']); ?></span>
                                        <?php else: ?>
                                        <span class="text-gray-400">Not assigned</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
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
                                        <span class="status-badge <?php echo $statusClasses[$order['status']]; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                    </td>
                                    <td class="py-4">
                                        <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="text-primary hover:text-indigo-400 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="text-gray-400 hover:text-white mr-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?delete=<?php echo $order['id']; ?>" class="text-red-500 hover:text-red-400" onclick="return confirm('Are you sure you want to delete this order?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>