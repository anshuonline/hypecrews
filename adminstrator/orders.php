<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'orders';

// Handle delete request
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        
        logAdminActivity($pdo, 'DELETE_ORDER', "Deleted order ID: " . $_GET['delete']);
        
        $success = "Order deleted successfully";
    } catch (PDOException $e) {
        $error = "Error deleting order: " . $e->getMessage();
    }
}

// Handle fast status update
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $custom_status = isset($_POST['custom_status']) ? trim($_POST['custom_status']) : '';
    
    try {
        $stmt_check = $pdo->prepare("SELECT status, custom_status FROM orders WHERE id = ?");
        $stmt_check->execute([$order_id]);
        $old_order = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("UPDATE orders SET status = ?, custom_status = ? WHERE id = ?");
        $stmt->execute([$status, $custom_status, $order_id]);
        
        if ($old_order && ($old_order['status'] != $status || $old_order['custom_status'] != $custom_status)) {
            $stmt_history = $pdo->prepare("INSERT INTO order_status_history (order_id, status, custom_status) VALUES (?, ?, ?)");
            $stmt_history->execute([$order_id, $status, $custom_status]);
            
            logAdminActivity($pdo, 'UPDATE_ORDER_STATUS', "Fast updated order status for order ID: $order_id to '$status'");
        }
        
        $success = "Order status updated successfully";
    } catch (PDOException $e) {
        $error = "Error updating order status: " . $e->getMessage();
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
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($orders as $order): ?>
                        <?php
                            $statusClasses = [
                                'pending' => 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20',
                                'in_review' => 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
                                'approved' => 'bg-indigo-500/10 text-indigo-500 border border-indigo-500/20',
                                'processing' => 'bg-purple-500/10 text-purple-500 border border-purple-500/20',
                                'in_production' => 'bg-cyan-500/10 text-cyan-500 border border-cyan-500/20',
                                'quality_check' => 'bg-teal-500/10 text-teal-500 border border-teal-500/20',
                                'ready_for_delivery' => 'bg-green-500/10 text-green-500 border border-green-500/20',
                                'shipped' => 'bg-blue-500/10 text-blue-500 border border-blue-500/20',
                                'delivered' => 'bg-green-500/10 text-green-500 border border-green-500/20',
                                'revision_requested' => 'bg-orange-500/10 text-orange-500 border border-orange-500/20',
                                'on_hold' => 'bg-yellow-500/10 text-yellow-500 border border-yellow-500/20',
                                'completed' => 'bg-green-500/10 text-green-500 border border-green-500/20',
                                'cancelled' => 'bg-red-500/10 text-red-500 border border-red-500/20'
                            ];
                        ?>
                        <div class="bg-[#1e293b]/50 backdrop-blur-sm border border-white/5 rounded-2xl p-5 hover:shadow-[0_0_25px_rgba(99,102,241,0.15)] hover:-translate-y-1 hover:border-primary/30 transition-all duration-300 flex flex-col h-full group relative overflow-hidden">
                            <!-- Top Decorator -->
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1 mr-3">
                                    <h3 class="font-bold text-lg text-white mb-2 leading-tight group-hover:text-primary transition-colors"><?php echo htmlspecialchars($order['order_title']); ?></h3>
                                    <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider <?php echo $statusClasses[$order['status']] ?? 'bg-gray-500/10 text-gray-400 border border-gray-500/20'; ?> inline-flex items-center">
                                        <?php if($order['status'] === 'completed'): ?>
                                            <i class="fas fa-check-circle mr-1.5"></i>
                                        <?php elseif($order['status'] === 'pending'): ?>
                                            <i class="fas fa-clock mr-1.5"></i>
                                        <?php else: ?>
                                            <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                        <?php endif; ?>
                                        <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?>
                                    </span>
                                </div>
                                <div class="text-right shrink-0 mt-1">
                                    <?php if ($order['tracking_id']): ?>
                                        <p class="text-[10px] text-gray-400 font-mono bg-black/40 px-2 py-1 rounded border border-white/5 flex items-center gap-1.5" title="Tracking ID">
                                            <i class="fas fa-hashtag text-gray-500"></i> <?php echo htmlspecialchars($order['tracking_id']); ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-[10px] text-gray-600 font-mono bg-black/20 px-2 py-1 rounded border border-white/5 italic">
                                            No Tracking
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Description -->
                            <div class="mb-5 flex-1">
                                <p class="text-sm text-gray-400 line-clamp-2 leading-relaxed"><?php echo htmlspecialchars($order['order_description']); ?></p>
                            </div>
                            
                            <!-- User Details -->
                            <div class="flex items-center mb-5 bg-[#0f172a]/80 p-3 rounded-xl border border-white/5 group-hover:bg-[#0f172a] transition-colors">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-indigo-500/30 flex items-center justify-center mr-3 shrink-0">
                                    <i class="fas fa-user text-indigo-400"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <?php if ($order['user_id']): ?>
                                        <p class="text-sm font-semibold text-white truncate"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                        <p class="text-xs text-indigo-400 truncate">@<?php echo htmlspecialchars($order['username']); ?></p>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500 italic">No user assigned</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Footer Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-white/5 mt-auto">
                                <span class="text-xs text-gray-500 font-medium flex items-center bg-white/5 px-2 py-1 rounded">
                                    <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i> <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                                </span>
                                <div class="flex space-x-1.5">
                                    <button onclick="showStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>', '<?php echo addslashes(htmlspecialchars($order['custom_status'] ?? '')); ?>')" class="w-8 h-8 rounded-full bg-white/5 hover:bg-secondary/20 hover:text-secondary text-gray-400 flex items-center justify-center transition-colors" title="Update Status">
                                        <i class="fas fa-tasks text-[13px]"></i>
                                    </button>
                                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-white/5 hover:bg-primary/20 hover:text-primary text-gray-400 flex items-center justify-center transition-colors" title="Edit Order">
                                        <i class="fas fa-edit text-[13px]"></i>
                                    </a>
                                    <a href="view_order.php?id=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/20 hover:text-white text-gray-400 flex items-center justify-center transition-colors" title="View Order">
                                        <i class="fas fa-eye text-[13px]"></i>
                                    </a>
                                    <a href="?delete=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-white/5 hover:bg-red-500/20 hover:text-red-500 text-gray-400 flex items-center justify-center transition-colors" onclick="return confirm('Are you sure you want to delete this order?')" title="Delete Order">
                                        <i class="fas fa-trash text-[13px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-dark rounded-xl shadow-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Quick Update Status</h3>
                    <button onclick="closeStatusModal()" class="text-gray-400 hover:text-white focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" id="orderIdInput" name="order_id" value="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Select Status</label>
                        <select name="status" id="statusSelect" class="w-full px-4 py-2 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            <option value="pending">Pending</option>
                            <option value="in_review">In Review</option>
                            <option value="approved">Approved</option>
                            <option value="processing">Processing</option>
                            <option value="in_production">In Production</option>
                            <option value="quality_check">Quality Check</option>
                            <option value="ready_for_delivery">Ready for Delivery</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="revision_requested">Revision Requested</option>
                            <option value="on_hold">On Hold</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Custom Status (Optional)</label>
                        <input type="text" name="custom_status" id="customStatusInput" class="w-full px-4 py-2 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white" placeholder="E.g., Developer Assigned">
                        <p class="text-xs text-gray-500 mt-1">This will be shown in the timeline to the user.</p>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeStatusModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showStatusModal(orderId, currentStatus, customStatus) {
            document.getElementById('orderIdInput').value = orderId;
            document.getElementById('statusSelect').value = currentStatus;
            document.getElementById('customStatusInput').value = customStatus || '';
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const statusModal = document.getElementById('statusModal');
            if (event.target == statusModal) {
                closeStatusModal();
            }
        }
    </script>
</body>
</html>