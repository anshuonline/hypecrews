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
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YCMZ1CPN6G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YCMZ1CPN6G');
</script>
</head>
<body class="text-apple_text">

    <!-- Abstract blurred colorful background -->
    <div class="glass-bg">
        <div class="glass-blob"></div>
    </div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Apple-style Glass Header -->
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div>
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Manage Orders</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="window.location.href='add_order.php'" class="bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 px-5 rounded-full flex items-center transition-colors border border-primary/20 shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        New Order
                    </button>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($success)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Search Form -->
                <div class="mb-8">
                    <form method="GET" class="flex max-w-2xl">
                        <input type="text" name="search" placeholder="Search by username, email, mobile, or tracking ID..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-5 py-3 glass-panel border border-white/60 rounded-l-full focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm placeholder-gray-400">
                        <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary px-6 rounded-r-full border border-primary/20 border-l-0 shadow-sm transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="orders.php" class="ml-3 glass-panel bg-gray-50/50 hover:bg-gray-100/50 px-5 rounded-full flex items-center text-apple_muted transition-colors shadow-sm">
                                <i class="fas fa-times mr-2"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <?php if (empty($orders)): ?>
                <div class="text-center py-20 glass-panel rounded-3xl max-w-2xl mx-auto shadow-sm">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fas fa-box-open text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-apple_muted text-lg font-medium mb-6">No orders found.</p>
                    <button onclick="window.location.href='add_order.php'" class="bg-primary text-white hover:bg-blue-600 font-bold py-3 px-6 rounded-full transition-colors shadow-md">
                        Create Your First Order
                    </button>
                </div>
                <?php else: ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($orders as $order): ?>
                    <?php
                        $statusClasses = [
                            'pending' => 'bg-yellow-500/10 text-yellow-700 border border-yellow-500/20',
                            'in_review' => 'bg-blue-500/10 text-blue-700 border border-blue-500/20',
                            'approved' => 'bg-indigo-500/10 text-indigo-700 border border-indigo-500/20',
                            'processing' => 'bg-purple-500/10 text-purple-700 border border-purple-500/20',
                            'in_production' => 'bg-cyan-500/10 text-cyan-700 border border-cyan-500/20',
                            'quality_check' => 'bg-teal-500/10 text-teal-700 border border-teal-500/20',
                            'ready_for_delivery' => 'bg-emerald-500/10 text-emerald-700 border border-emerald-500/20',
                            'shipped' => 'bg-blue-500/10 text-blue-700 border border-blue-500/20',
                            'delivered' => 'bg-green-500/10 text-green-700 border border-green-500/20',
                            'revision_requested' => 'bg-orange-500/10 text-orange-700 border border-orange-500/20',
                            'on_hold' => 'bg-amber-500/10 text-amber-700 border border-amber-500/20',
                            'completed' => 'bg-green-500/10 text-green-700 border border-green-500/20',
                            'cancelled' => 'bg-red-500/10 text-red-700 border border-red-500/20'
                        ];
                    ?>
                    
                    <!-- Order Card - Apple Glass Style -->
                    <div class="glass-panel rounded-3xl p-6 hover:bg-white/70 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full relative overflow-hidden group">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1 mr-3">
                                <h3 class="font-bold text-lg text-apple_text mb-2 leading-tight group-hover:text-primary transition-colors"><?php echo htmlspecialchars($order['order_title']); ?></h3>
                                <span class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-wider <?php echo $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-600 border border-gray-200'; ?> inline-flex items-center shadow-sm">
                                    <?php if($order['status'] === 'completed'): ?>
                                        <i class="fas fa-check-circle mr-1.5"></i>
                                    <?php elseif($order['status'] === 'pending'): ?>
                                        <i class="fas fa-clock mr-1.5"></i>
                                    <?php else: ?>
                                        <i class="fas fa-circle text-[8px] mr-1.5"></i>
                                    <?php endif; ?>
                                    <?php echo str_replace('_', ' ', $order['status']); ?>
                                </span>
                            </div>
                            <div class="text-right shrink-0 mt-1">
                                <?php if ($order['tracking_id']): ?>
                                    <p class="text-[10px] text-apple_muted font-mono bg-white/50 backdrop-blur-md px-2 py-1 rounded-md border border-white/60 shadow-sm flex items-center gap-1.5" title="Tracking ID">
                                        <i class="fas fa-hashtag text-gray-400"></i> <?php echo htmlspecialchars($order['tracking_id']); ?>
                                    </p>
                                <?php else: ?>
                                    <p class="text-[10px] text-gray-400 font-mono bg-white/30 backdrop-blur-md px-2 py-1 rounded-md border border-white/60 shadow-sm italic">
                                        No Tracking
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="mb-5 flex-1">
                            <p class="text-sm text-apple_muted line-clamp-2 leading-relaxed"><?php echo htmlspecialchars($order['order_description']); ?></p>
                        </div>
                        
                        <!-- User Details -->
                        <div class="flex items-center mb-5 bg-white/40 p-3 rounded-2xl border border-white/60 shadow-sm group-hover:bg-white/60 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-blue-50/50 border border-blue-100 flex items-center justify-center mr-3 shrink-0 shadow-inner">
                                <i class="fas fa-user text-primary/70"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <?php if ($order['user_id']): ?>
                                    <p class="text-sm font-semibold text-apple_text truncate"><?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?></p>
                                    <p class="text-xs text-primary/70 font-medium truncate">@<?php echo htmlspecialchars($order['username']); ?></p>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 italic">Guest / No user</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Footer Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-black/5 mt-auto">
                            <span class="text-xs text-apple_muted font-medium flex items-center bg-black/5 px-2.5 py-1.5 rounded-lg">
                                <i class="far fa-calendar-alt mr-1.5 opacity-70"></i> <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </span>
                            <div class="flex space-x-2">
                                <button onclick="showStatusModal(<?php echo $order['id']; ?>, '<?php echo $order['status']; ?>', '<?php echo addslashes(htmlspecialchars($order['custom_status'] ?? '')); ?>')" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="Update Status">
                                    <i class="fas fa-tasks text-[13px]"></i>
                                </button>
                                <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-black/5 hover:bg-primary/10 hover:text-primary text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="Edit Order">
                                    <i class="fas fa-edit text-[13px]"></i>
                                </a>
                                <a href="view_order.php?id=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="View Order">
                                    <i class="fas fa-eye text-[13px]"></i>
                                </a>
                                <a href="?delete=<?php echo $order['id']; ?>" class="w-8 h-8 rounded-full bg-black/5 hover:bg-red-500/10 hover:text-red-600 text-apple_muted flex items-center justify-center transition-colors shadow-sm" onclick="return confirm('Are you sure you want to delete this order?')" title="Delete Order">
                                    <i class="fas fa-trash text-[13px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
                
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black/20 backdrop-blur-md z-50 hidden items-center justify-center p-4">
        <div class="glass-panel bg-white/80 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white max-w-md w-full transform transition-all">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-apple_text">Update Status</h3>
                    <button onclick="closeStatusModal()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_muted transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" id="orderIdInput" name="order_id" value="">
                    
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-apple_muted mb-2">Select Status</label>
                        <select name="status" id="statusSelect" class="w-full px-4 py-3 bg-white/50 border border-black/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text font-medium appearance-none shadow-sm">
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
                    
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-apple_muted mb-2">Custom Note (Optional)</label>
                        <input type="text" name="custom_status" id="customStatusInput" class="w-full px-4 py-3 bg-white/50 border border-black/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text placeholder-gray-400 shadow-sm" placeholder="E.g., Developer Assigned">
                        <p class="text-xs text-apple_muted mt-2">This will be shown in the timeline to the user.</p>
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" onclick="closeStatusModal()" class="px-5 py-2.5 rounded-full bg-black/5 hover:bg-black/10 font-semibold text-apple_text transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-full bg-primary hover:bg-blue-600 text-white font-bold transition-colors shadow-md">
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
