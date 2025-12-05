<?php
require_once 'auth.php';
require_once '../config/db.php';

$error = '';
$success = '';

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                    userItem.className = 'user-search-item';
                    userItem.innerHTML = `
                        <div class="font-medium">${user.first_name} ${user.last_name} <span class="text-gray-400">(@${user.username})</span></div>
                        <div class="text-sm text-gray-400">${user.email} - ${user.mobile_number}</div>
                    `;
                    
                    userItem.addEventListener('click', function() {
                        // Set the selected user in the dropdown
                        userSelect.value = user.id;
                        // Hide search results
                        searchResultsContainer.style.display = 'none';
                        // Clear search input
                        userSearchInput.value = '';
                    });
                    
                    searchResultsContainer.appendChild(userItem);
                });
                
                searchResultsContainer.style.display = 'block';
            }
            
            // Hide search results when clicking outside
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
        
        .user-search-results {
            position: absolute;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 0.5rem;
            max-height: 200px;
            overflow-y: auto;
            z-index: 100;
            width: 100%;
            display: none;
        }
        
        .user-search-item {
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #334155;
        }
        
        .user-search-item:hover {
            background: #334155;
        }
        
        .user-search-item:last-child {
            border-bottom: none;
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
                    <h2 class="text-2xl font-bold">Add New Order</h2>
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
                                <label for="user_id" class="block text-sm font-medium text-gray-300 mb-2">Assign to User <span class="text-red-500">*</span></label>
                                
                                <!-- User Search -->
                                <div class="mb-3 relative">
                                    <input type="text" id="user_search" placeholder="Search by email, phone, username, or ID..." class="flex-1 px-3 py-2 bg-dark/50 border border-gray-700 rounded-lg text-white text-sm focus:outline-none focus:ring-1 focus:ring-primary w-full">
                                    <p class="text-xs text-gray-400 mt-1">Start typing to search for users</p>
                                </div>
                                
                                <select 
                                    id="user_id" 
                                    name="user_id" 
                                    required
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                                    <option value="">Select a user...</option>
                                    <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>" <?php echo (isset($_POST['user_id']) && $_POST['user_id'] == $user['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (@' . $user['username'] . ')'); ?>
                                        - <?php echo htmlspecialchars($user['email']); ?>
                                        - <?php echo htmlspecialchars($user['mobile_number']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Search and select a user for this order</p>
                            </div>
                            
                            <div>
                                <label for="tracking_id" class="block text-sm font-medium text-gray-300 mb-2">Tracking ID (Auto-generated)</label>
                                <div class="flex">
                                    <input 
                                        type="text" 
                                        id="tracking_id" 
                                        name="tracking_id" 
                                        class="flex-1 input-field bg-dark/50 border border-gray-700 rounded-l-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" 
                                        placeholder="Auto-generated"
                                        value="<?php echo isset($_POST['tracking_id']) ? htmlspecialchars($_POST['tracking_id']) : 'hypecrews' . rand(100000, 999999); ?>" 
                                        readonly>
                                    <button type="button" id="generateTrackingId" class="bg-primary hover:bg-indigo-700 text-white px-4 rounded-r-lg">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Format: hypecrewsXXXXXX (auto-generated, click <i class="fas fa-sync-alt"></i> to regenerate)</p>
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
                                value="<?php echo isset($_POST['order_title']) ? htmlspecialchars($_POST['order_title']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="order_description" class="block text-sm font-medium text-gray-300 mb-2">Order Description *</label>
                            <textarea 
                                id="order_description" 
                                name="order_description" 
                                rows="4"
                                required
                                class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                placeholder="Enter order description"><?php echo isset($_POST['order_description']) ? htmlspecialchars($_POST['order_description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
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
                                <label for="custom_status" class="block text-sm font-medium text-gray-300 mb-2">Custom Status (Optional)</label>
                                <input 
                                    type="text" 
                                    id="custom_status" 
                                    name="custom_status" 
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Enter custom status"
                                    value="<?php echo isset($_POST['custom_status']) ? htmlspecialchars($_POST['custom_status']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="window.location.href='orders.php'" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                                Cancel
                            </button>
                            <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg">
                                Add Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>