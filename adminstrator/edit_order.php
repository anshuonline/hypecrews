<?php
require_once 'auth.php';
require_once '../config/db.php';

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
    $tracking_id = isset($_POST['tracking_id']) ? trim($_POST['tracking_id']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'pending';
    $custom_status = isset($_POST['custom_status']) ? trim($_POST['custom_status']) : '';
    $request_review = isset($_POST['request_review']) ? 1 : 0;
    
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
            
            // Preserve existing tracking ID if not explicitly changed
            if (empty($tracking_id)) {
                $tracking_id = $order['tracking_id'];
            }
            
            // Update the order
            $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, order_title = ?, order_description = ?, tracking_id = ?, status = ?, custom_status = ?, review_requested = ? WHERE id = ?");
            $stmt->execute([$user_id, $order_title, $order_description, $tracking_id, $status, $custom_status, $request_review, $order_id]);
            
            // If status has changed, record it in history
            if ($old_order && ($old_order['status'] != $status || $old_order['custom_status'] != $custom_status)) {
                $stmt_history = $pdo->prepare("INSERT INTO order_status_history (order_id, status, custom_status) VALUES (?, ?, ?)");
                $stmt_history->execute([$order_id, $status, $custom_status]);
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
        
        .input-field {
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
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
                    <h2 class="text-2xl font-bold">Edit Order #<?php echo $order['id']; ?></h2>
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
                <?php if ($success): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
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
                    <form method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="user_id" class="block text-sm font-medium text-gray-300 mb-2">Assign to User (Optional)</label>
                                <select 
                                    id="user_id" 
                                    name="user_id" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Select a user (optional)</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo ($order['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (@' . $user['username'] . ')'); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="tracking_id" class="block text-sm font-medium text-gray-300 mb-2">Tracking ID (Auto-generated)</label>
                                <input 
                                    type="text" 
                                    id="tracking_id" 
                                    name="tracking_id" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Auto-generated tracking ID"
                                    value="<?php echo htmlspecialchars($order['tracking_id']); ?>" readonly>
                                <p class="text-xs text-gray-400 mt-1">This ID was auto-generated and cannot be changed</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="order_title" class="block text-sm font-medium text-gray-300 mb-2">Order Title *</label>
                            <input 
                                type="text" 
                                id="order_title" 
                                name="order_title" 
                                required
                                class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter order title"
                                value="<?php echo htmlspecialchars($order['order_title']); ?>">
                        </div>
                        
                        <div>
                            <label for="order_description" class="block text-sm font-medium text-gray-300 mb-2">Order Description *</label>
                            <textarea 
                                id="order_description" 
                                name="order_description" 
                                rows="4"
                                required
                                class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter order description"><?php echo htmlspecialchars($order['order_description']); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
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
                                <label for="custom_status" class="block text-sm font-medium text-gray-300 mb-2">Custom Status (Optional)</label>
                                <input 
                                    type="text" 
                                    id="custom_status" 
                                    name="custom_status" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Enter custom status"
                                    value="<?php echo htmlspecialchars($order['custom_status']); ?>">
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="request_review" 
                                name="request_review" 
                                value="1" 
                                <?php echo ($order['review_requested']) ? 'checked' : ''; ?>
                                class="w-4 h-4 text-primary bg-dark border-gray-700 rounded focus:ring-primary">
                            <label for="request_review" class="ml-2 block text-sm text-gray-300">
                                Request Review from User
                            </label>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="window.location.href='orders.php'" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg">
                                Update Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>