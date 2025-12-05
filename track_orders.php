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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Orders - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
        
        tr:hover {
            cursor: pointer;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-bold mb-2">Track Your Orders</h1>
                <p class="text-gray-400">View the status of your orders and track their progress</p>
            </div>
            
            <!-- Search Form -->
            <div class="mb-6">
                <form method="GET" class="flex gap-2">
                    <div class="flex-grow">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by Tracking ID..." 
                            value="<?php echo htmlspecialchars($search); ?>"
                            class="w-full px-4 py-2 rounded-lg bg-light border border-gray-700 focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary text-white"
                        >
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                    <?php if (!empty($search)): ?>
                    <a href="track_orders.php" class="bg-gray-700 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg flex items-center">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                    <?php endif; ?>
                </form>
            </div>
            
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
            
            <div class="bg-light rounded-xl shadow-lg overflow-hidden">
                <?php if (empty($orders)): ?>
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-4xl text-gray-500 mb-4"></i>
                    <h3 class="text-xl font-bold mb-2">
                        <?php if (!empty($search)): ?>
                            No orders found for "<?php echo htmlspecialchars($search); ?>"
                        <?php else: ?>
                            No Orders Found
                        <?php endif; ?>
                    </h3>
                    <p class="text-gray-400 mb-6">
                        <?php if (!empty($search)): ?>
                            Try a different search term or <a href="track_orders.php" class="text-primary hover:underline">view all orders</a>.
                        <?php else: ?>
                            You don't have any orders yet.
                        <?php endif; ?>
                    </p>
                    <a href="index.php" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg inline-block">
                        Browse Services
                    </a>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 border-b border-gray-800">
                                <th class="pb-3 px-6">Order</th>
                                <th class="pb-3 px-6">Tracking ID</th>
                                <th class="pb-3 px-6">Status</th>
                                <th class="pb-3 px-6">Date</th>
                            </tr>
                        </thead>
                        <caption class="text-left text-sm text-gray-500 p-4">Click on any order to view its details and status timeline</caption>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                            <tr class="border-b border-gray-800 hover:bg-dark/50 cursor-pointer" onclick="window.location.href='order_details.php?id=<?php echo $order['id']; ?>'">
                                <td class="py-4 px-6">
                                    <p class="font-medium"><?php echo htmlspecialchars($order['order_title']); ?></p>
                                    <p class="text-sm text-gray-400"><?php echo substr(htmlspecialchars($order['order_description']), 0, 50); ?>...</p>
                                </td>
                                <td class="py-4 px-6">
                                    <?php if ($order['tracking_id']): ?>
                                    <span class="font-mono"><?php echo htmlspecialchars($order['tracking_id']); ?></span>
                                    <?php else: ?>
                                    <span class="text-gray-400">Not assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6">
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
                                    <?php if ($order['custom_status']): ?>
                                    <p class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-6 text-gray-400">
                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
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
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>