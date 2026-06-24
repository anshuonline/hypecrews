<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';

$error = '';
$success = '';
$current_page = 'orders';

// Get all users for the dropdown
try {
    $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, email, mobile_number FROM users ORDER BY first_name, last_name");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $order_title = isset($_POST['order_title']) ? trim($_POST['order_title']) : '';
    $order_description = isset($_POST['order_description']) ? trim($_POST['order_description']) : '';
    $tracking_id = isset($_POST['tracking_id']) ? trim($_POST['tracking_id']) : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'pending';
    $custom_status = isset($_POST['custom_status']) ? trim($_POST['custom_status']) : '';
    
    // Always generate a unique tracking ID (either from form or auto-generated)
    if (empty($tracking_id)) {
        $tracking_id = 'hypecrews' . rand(100000, 999999);
    }
    
    // Ensure uniqueness
    $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE tracking_id = ?");
    $stmt_check->execute([$tracking_id]);
    
    // If not unique, generate again
    while ($stmt_check->fetchColumn() > 0) {
        $tracking_id = 'hypecrews' . rand(100000, 999999);
        $stmt_check->execute([$tracking_id]);
    }
    
    // Validation
    if (empty($user_id)) {
        $error = 'Please select a user for this order';
    } elseif (empty($order_title)) {
        $error = 'Order title is required';
    } elseif (empty($order_description)) {
        $error = 'Order description is required';
    } else {
        try {
            // Insert the order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_title, order_description, tracking_id, status, custom_status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $order_title, $order_description, $tracking_id, $status, $custom_status]);
            
            // Get the inserted order ID
            $order_id = $pdo->lastInsertId();
            
            // Record initial status in history
            $stmt_history = $pdo->prepare("INSERT INTO order_status_history (order_id, status, custom_status) VALUES (?, ?, ?)");
            $stmt_history->execute([$order_id, $status, $custom_status]);
            
            logAdminActivity($pdo, 'ADD_ORDER', "Added new order for User ID: $user_id. Tracking: $tracking_id");
            
            $success = "Order added successfully";
            
            // Clear form data
            $user_id = '';
            $order_title = '';
            $order_description = '';
            $tracking_id = '';
            $status = 'pending';
            $custom_status = '';
        } catch (PDOException $e) {
            $error = "Error adding order: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Order - Hypecrews Admin</title>
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
        
        .user-search-results {
            position: absolute;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(0,0,0,0.1);
            border-radius: 1rem;
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
            width: 100%;
            display: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: 0.5rem;
        }
        
        .user-search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: background 0.2s;
        }
        
        .user-search-item:hover {
            background: rgba(0, 102, 204, 0.08);
        }
        
        .user-search-item:last-child {
            border-bottom: none;
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Add New Order</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <button onclick="window.location.href='orders.php'" class="bg-black/5 hover:bg-black/10 text-apple_text font-semibold py-2.5 px-5 rounded-full flex items-center transition-colors border border-black/5 shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Orders
                    </button>
                </div>
            </header>
            
            <!-- Content -->
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
                
                <div class="glass-panel rounded-3xl p-8 max-w-4xl shadow-sm">
                    <form method="POST" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label for="user_id" class="block text-sm font-semibold text-apple_muted mb-2">Assign to User <span class="text-red-500">*</span></label>
                                
                                <!-- User Search -->
                                <div class="mb-3 relative">
                                    <input type="text" id="user_search" placeholder="Search by email, phone, username, or ID..." class="flex-1 px-4 py-3 bg-white/50 border border-black/10 rounded-xl text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 w-full shadow-sm placeholder-gray-400 font-medium">
                                    <p class="text-[11px] text-apple_muted mt-2 font-medium">START TYPING TO SEARCH</p>
                                </div>
                                
                                <select 
                                    id="user_id" 
                                    name="user_id" 
                                    required
                                    class="w-full bg-white/50 border border-black/10 rounded-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 appearance-none shadow-sm font-medium">
                                    <option value="">Select a user...</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (@' . $user['username'] . ')'); ?>
                                        - <?php echo htmlspecialchars($user['email']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label for="tracking_id" class="block text-sm font-semibold text-apple_muted mb-2">Tracking ID</label>
                                <div class="flex">
                                    <input 
                                        type="text" 
                                        id="tracking_id" 
                                        name="tracking_id" 
                                        class="flex-1 bg-white/50 border border-black/10 rounded-l-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm font-medium" 
                                        placeholder="Auto-generated"
                                        value="<?php echo isset($_POST['tracking_id']) ? htmlspecialchars($_POST['tracking_id']) : 'hypecrews' . rand(100000, 999999); ?>" 
                                        readonly>
                                    <button type="button" id="generateTrackingId" class="bg-primary/10 hover:bg-primary/20 text-primary px-5 border border-primary/20 border-l-0 rounded-r-xl transition-colors shadow-sm">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <p class="text-[11px] text-apple_muted mt-2 font-medium uppercase">Format: hypecrewsXXXXXX (auto-generated)</p>
                            </div>
                        </div>
                        
                        <div>
                            <label for="order_title" class="block text-sm font-semibold text-apple_muted mb-2">Order Title <span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                id="order_title" 
                                name="order_title" 
                                required
                                class="w-full bg-white/50 border border-black/10 rounded-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm font-medium placeholder-gray-400"
                                placeholder="Enter order title"
                                value="<?php echo isset($_POST['order_title']) ? htmlspecialchars($_POST['order_title']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="order_description" class="block text-sm font-semibold text-apple_muted mb-2">Order Description <span class="text-red-500">*</span></label>
                            <textarea 
                                id="order_description" 
                                name="order_description" 
                                rows="5"
                                required
                                class="w-full bg-white/50 border border-black/10 rounded-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm font-medium placeholder-gray-400 resize-none"
                                placeholder="Enter full order description and requirements"><?php echo isset($_POST['order_description']) ? htmlspecialchars($_POST['order_description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-black/5 pt-8">
                            <div>
                                <label for="status" class="block text-sm font-semibold text-apple_muted mb-2">Initial Status</label>
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="w-full bg-white/50 border border-black/10 rounded-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 appearance-none shadow-sm font-medium">
                                    <option value="pending" <?php echo (isset($_POST['status']) && $_POST['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_review" <?php echo (isset($_POST['status']) && $_POST['status'] == 'in_review') ? 'selected' : ''; ?>>In Review</option>
                                    <option value="approved" <?php echo (isset($_POST['status']) && $_POST['status'] == 'approved') ? 'selected' : ''; ?>>Approved</option>
                                    <option value="processing" <?php echo (isset($_POST['status']) && $_POST['status'] == 'processing') ? 'selected' : ''; ?>>Processing</option>
                                    <option value="in_production" <?php echo (isset($_POST['status']) && $_POST['status'] == 'in_production') ? 'selected' : ''; ?>>In Production</option>
                                    <option value="quality_check" <?php echo (isset($_POST['status']) && $_POST['status'] == 'quality_check') ? 'selected' : ''; ?>>Quality Check</option>
                                    <option value="ready_for_delivery" <?php echo (isset($_POST['status']) && $_POST['status'] == 'ready_for_delivery') ? 'selected' : ''; ?>>Ready for Delivery</option>
                                    <option value="shipped" <?php echo (isset($_POST['status']) && $_POST['status'] == 'shipped') ? 'selected' : ''; ?>>Shipped</option>
                                    <option value="delivered" <?php echo (isset($_POST['status']) && $_POST['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="revision_requested" <?php echo (isset($_POST['status']) && $_POST['status'] == 'revision_requested') ? 'selected' : ''; ?>>Revision Requested</option>
                                    <option value="on_hold" <?php echo (isset($_POST['status']) && $_POST['status'] == 'on_hold') ? 'selected' : ''; ?>>On Hold</option>
                                    <option value="completed" <?php echo (isset($_POST['status']) && $_POST['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo (isset($_POST['status']) && $_POST['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="custom_status" class="block text-sm font-semibold text-apple_muted mb-2">Custom Note (Optional)</label>
                                <input 
                                    type="text" 
                                    id="custom_status" 
                                    name="custom_status" 
                                    class="w-full bg-white/50 border border-black/10 rounded-xl px-4 py-3 text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/50 shadow-sm font-medium placeholder-gray-400"
                                    placeholder="E.g., Waiting for client assets"
                                    value="<?php echo isset($_POST['custom_status']) ? htmlspecialchars($_POST['custom_status']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4 pt-4">
                            <button type="button" onclick="window.location.href='orders.php'" class="px-6 py-3 rounded-full bg-black/5 hover:bg-black/10 font-bold text-apple_text transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-8 py-3 rounded-full bg-primary hover:bg-blue-600 text-white font-bold shadow-md transition-colors">
                                Create Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // AJAX user search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const userSearchInput = document.getElementById('user_search');
            const userSelect = document.getElementById('user_id');
            const searchResultsContainer = document.createElement('div');
            searchResultsContainer.className = 'user-search-results';
            userSearchInput.parentNode.appendChild(searchResultsContainer);
            
            let searchTimeout;
            
            userSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value.trim();
                
                if (searchTerm.length < 2) {
                    searchResultsContainer.style.display = 'none';
                    return;
                }
                
                searchTimeout = setTimeout(function() {
                    fetch('search_users.php?term=' + encodeURIComponent(searchTerm))
                        .then(response => response.json())
                        .then(users => {
                            displaySearchResults(users);
                        })
                        .catch(error => {
                            console.error('Error fetching users:', error);
                            searchResultsContainer.style.display = 'none';
                        });
                }, 300);
            });
            
            function displaySearchResults(users) {
                if (users.length === 0) {
                    searchResultsContainer.style.display = 'none';
                    return;
                }
                
                searchResultsContainer.innerHTML = '';
                
                users.forEach(user => {
                    const userItem = document.createElement('div');
                    userItem.className = 'user-search-item text-sm text-apple_text';
                    userItem.innerHTML = `
                        <div class="font-bold">${user.first_name} ${user.last_name} <span class="text-apple_muted font-medium">(@${user.username})</span></div>
                        <div class="text-[11px] text-apple_muted mt-0.5">${user.email} &bull; ${user.mobile_number}</div>
                    `;
                    
                    userItem.addEventListener('click', function() {
                        userSelect.value = user.id;
                        searchResultsContainer.style.display = 'none';
                        userSearchInput.value = '';
                    });
                    
                    searchResultsContainer.appendChild(userItem);
                });
                
                searchResultsContainer.style.display = 'block';
            }
            
            document.addEventListener('click', function(event) {
                if (!userSearchInput.contains(event.target) && !searchResultsContainer.contains(event.target)) {
                    searchResultsContainer.style.display = 'none';
                }
            });
                
            // Tracking ID generation
            const generateBtn = document.getElementById('generateTrackingId');
            const trackingIdInput = document.getElementById('tracking_id');
                
            generateBtn.addEventListener('click', function() {
                const newTrackingId = 'hypecrews' + Math.floor(Math.random() * 900000 + 100000);
                trackingIdInput.value = newTrackingId;
            });
        });
    </script>
</body>
</html>