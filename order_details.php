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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Hypecrews</title>
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
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .timeline-item {
            position: relative;
            padding-left: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .timeline-item:before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0.5rem;
            width: 0.75rem;
            height: 0.75rem;
            border-radius: 50%;
            background-color: #6366f1;
        }
        
        .timeline-item:after {
            content: '';
            position: absolute;
            left: 0.875rem;
            top: 1.5rem;
            width: 0.125rem;
            height: calc(100% + 1rem);
            background-color: #6366f1;
        }
        
        .timeline-item:last-child:after {
            display: none;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <a href="track_orders.php" class="inline-flex items-center text-primary hover:text-indigo-300 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Orders
                </a>
                <h1 class="text-3xl font-bold">Order Details</h1>
                <p class="text-gray-400">Order #<?php echo $order['id']; ?> • <?php echo htmlspecialchars($order['order_title']); ?></p>
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
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Information -->
                <div class="lg:col-span-2">
                    <div class="bg-light rounded-xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Order Information</h2>
                        
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
                            
                            <div>
                                <p class="text-gray-400 text-sm">Current Status</p>
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
                                <div class="mt-1">
                                    <span class="status-badge <?php echo $statusClasses[$order['status']]; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($order['status']))); ?>
                                    </span>
                                    <?php if ($order['custom_status']): ?>
                                    <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($order['status'] == 'completed' && $order['review_requested']): ?>
                                    <div class="mt-3">
                                        <a href="submit_review.php?order_id=<?php echo $order['id']; ?>" class="inline-flex items-center text-primary hover:text-indigo-300">
                                            <i class="fas fa-star mr-1"></i> Submit Review
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Timeline -->
                    <div class="bg-light rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-4">Status Timeline</h2>
                        
                        <?php if (empty($status_history)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-history text-3xl text-gray-500 mb-3"></i>
                            <p class="text-gray-400">No status history available</p>
                        </div>
                        <?php else: ?>
                        <div class="space-y-6">
                            <?php foreach ($status_history as $history): ?>
                            <div class="timeline-item">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <?php
                                        $statusClass = $statusClasses[$history['status']] ?? 'bg-gray-500/10 text-gray-500';
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($history['status']))); ?>
                                        </span>
                                        <?php if ($history['custom_status']): ?>
                                        <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($history['custom_status']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-400 whitespace-nowrap">
                                        <?php echo date('M j, Y g:i A', strtotime($history['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- Current status (from orders table) -->
                            <div class="timeline-item">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="status-badge <?php echo $statusClasses[$order['status']]; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', htmlspecialchars($order['status']))); ?> (Current)
                                        </span>
                                        <?php if ($order['custom_status']): ?>
                                        <p class="text-sm text-gray-400 mt-2"><?php echo htmlspecialchars($order['custom_status']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-sm text-gray-400 whitespace-nowrap">
                                        <?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div>
                    <div class="bg-light rounded-xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm">Order ID</p>
                                <p class="font-mono">#<?php echo $order['id']; ?></p>
                            </div>
                            
                            <div>
                                <p class="text-gray-400 text-sm">Created</p>
                                <p><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                            
                            <div>
                                <p class="text-gray-400 text-sm">Last Updated</p>
                                <p><?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?></p>
                            </div>
                            
                            <?php if ($order['tracking_id']): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Tracking ID</p>
                                <p class="font-mono"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>