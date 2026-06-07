<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';

$error = '';
$success = '';

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    header('Location: orders.php');
    exit;
}

// Get all users for the dropdown
try {
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name FROM users ORDER BY first_name, last_name");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}

// Get the order details
try {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: orders.php');
        exit;
    }
} catch (PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = isset($_POST['user_id']) && !empty($_POST['user_id']) ? $_POST['user_id'] : null;
    $order_title = isset($_POST['order_title']) ? trim($_POST['order_title']) : '';
    $order_description = isset($_POST['order_description']) ? trim($_POST['order_description']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'pending';
    $custom_status = isset($_POST['custom_status']) ? trim($_POST['custom_status']) : '';
    $request_review = isset($_POST['request_review']) ? 1 : 0;
    
    // Always preserve the existing tracking ID - never change it
    $tracking_id = $order['tracking_id'];
    
    // Validation
    if (empty($order_title)) {
        $error = 'Order title is required';
    } elseif (empty($order_description)) {
        $error = 'Order description is required';
    } else {
        try {
            // Check if status has changed
            $stmt_check = $pdo->prepare("SELECT status, custom_status, review_requested FROM orders WHERE id = ?");
            $stmt_check->execute([$order_id]);
            $old_order = $stmt_check->fetch(PDO::FETCH_ASSOC);
            
            // Update the order (tracking ID is preserved)
            $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, order_title = ?, order_description = ?, tracking_id = ?, status = ?, custom_status = ?, review_requested = ? WHERE id = ?");
            $stmt->execute([$user_id, $order_title, $order_description, $tracking_id, $status, $custom_status, $request_review, $order_id]);
            
            // If status has changed, record it in history
            if ($old_order && ($old_order['status'] != $status || $old_order['custom_status'] != $custom_status)) {
                $stmt_history = $pdo->prepare("INSERT INTO order_status_history (order_id, status, custom_status) VALUES (?, ?, ?)");
                $stmt_history->execute([$order_id, $status, $custom_status]);
                
                logAdminActivity($pdo, 'UPDATE_ORDER_STATUS', "Updated order status for tracking: $tracking_id to '$status'");
            } else {
                logAdminActivity($pdo, 'UPDATE_ORDER', "Updated order details for tracking: $tracking_id");
            }
            
            $success = "Order updated successfully";
            
            // Refresh order data
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Error updating order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order - Hypecrews Admin</title>
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
        
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #0066cc;
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.2);
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
                        <h1 class="text-3xl font-bold text-apple_text tracking-tight">Edit Order #<?php echo $order['id']; ?></h1>
                    </div>
                </div>
            </header>
            
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if ($success): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm max-w-4xl mx-auto">
                    <form method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="user_id" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Assign to User (Optional)</label>
                                <select 
                                    id="user_id" 
                                    name="user_id" 
                                    class="w-full input-field bg-white/60 border border-black/10 rounded-xl px-5 py-3 text-apple_text focus:outline-none shadow-sm font-medium">
                                    <option value="">Select a user (optional)</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo ($order['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (@' . $user['username'] . ')'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="tracking_id" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Tracking ID (Cannot be changed)</label>
                                <input 
                                    type="text" 
                                    id="tracking_id" 
                                    name="tracking_id" 
                                    class="w-full input-field bg-black/5 border border-black/5 rounded-xl px-5 py-3 text-apple_muted focus:outline-none font-mono cursor-not-allowed"
                                    placeholder="Auto-generated tracking ID"
                                    value="<?php echo htmlspecialchars($order['tracking_id']); ?>" readonly>
                                <p class="text-xs text-apple_muted mt-2"><i class="fas fa-lock mr-1"></i> This ID was auto-generated and cannot be changed</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="order_title" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Order Title *</label>
                            <input 
                                type="text" 
                                id="order_title" 
                                name="order_title" 
                                required
                                class="w-full input-field bg-white/60 border border-black/10 rounded-xl px-5 py-3 text-apple_text focus:outline-none shadow-sm font-medium text-lg"
                                placeholder="Enter order title"
                                value="<?php echo htmlspecialchars($order['order_title']); ?>">
                        </div>
                        
                        <div>
                            <label for="order_description" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Order Description *</label>
                            <textarea 
                                id="order_description" 
                                name="order_description" 
                                rows="5"
                                required
                                class="w-full input-field bg-white/60 border border-black/10 rounded-xl px-5 py-3 text-apple_text focus:outline-none shadow-sm font-medium leading-relaxed"
                                placeholder="Enter order description"><?php echo htmlspecialchars($order['order_description']); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-black/5 pt-8">
                            <div>
                                <label for="status" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Status</label>
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="w-full input-field bg-white/60 border border-black/10 rounded-xl px-5 py-3 text-apple_text focus:outline-none shadow-sm font-bold">
                                    <option value="pending" <?php echo ($order['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_review" <?php echo ($order['status'] == 'in_review') ? 'selected' : ''; ?>>In Review</option>
                                    <option value="approved" <?php echo ($order['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="processing" <?php echo ($order['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="in_production" <?php echo ($order['status'] == 'in_production') ? 'selected' : ''; ?>>In Production</option>
                                    <option value="quality_check" <?php echo ($order['status'] == 'quality_check') ? 'selected' : ''; ?>>Quality Check</option>
                                    <option value="ready_for_delivery" <?php echo ($order['status'] == 'ready_for_delivery') ? 'selected' : ''; ?>>Ready for Delivery</option>
                                    <option value="shipped" <?php echo ($order['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo ($order['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="revision_requested" <?php echo ($order['status'] == 'revision_requested') ? 'selected' : ''; ?>>Revision Requested</option>
                                    <option value="on_hold" <?php echo ($order['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                                    <option value="completed" <?php echo ($order['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo ($order['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="custom_status" class="block text-sm font-semibold text-apple_muted uppercase tracking-wider mb-2">Custom Status (Optional)</label>
                                <input 
                                    type="text" 
                                    id="custom_status" 
                                    name="custom_status" 
                                    class="w-full input-field bg-white/60 border border-black/10 rounded-xl px-5 py-3 text-apple_text focus:outline-none shadow-sm font-medium"
                                    placeholder="e.g. Waiting for client response"
                                    value="<?php echo htmlspecialchars($order['custom_status']); ?>">
                            </div>
                        </div>
                        
                        <div class="flex items-center bg-white/40 p-5 rounded-xl border border-black/5 mt-4">
                            <input 
                                type="checkbox" 
                                id="request_review" 
                                name="request_review" 
                                value="1" 
                                <?php echo ($order['review_requested']) ? 'checked' : ''; ?>
                                class="w-5 h-5 text-primary bg-white border-gray-300 rounded focus:ring-primary focus:ring-2 cursor-pointer">
                            <label for="request_review" class="ml-3 block font-semibold text-apple_text cursor-pointer">
                                Request Review from User <span class="text-xs font-normal text-apple_muted block">Check this box to prompt the user to review the order when completed.</span>
                            </label>
                        </div>
                        
                        <div class="flex justify-end space-x-4 pt-8 border-t border-black/5 mt-8">
                            <button type="button" onclick="window.location.href='view_order.php?id=<?php echo $order['id']; ?>'" class="bg-black/5 hover:bg-black/10 text-apple_text font-bold py-3 px-8 rounded-full transition-colors shadow-sm">
                                Cancel
                            </button>
                            <button type="submit" class="bg-primary/90 hover:bg-primary text-white font-bold py-3 px-8 rounded-full transition-colors shadow-md">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>