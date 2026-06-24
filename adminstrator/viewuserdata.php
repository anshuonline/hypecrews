<?php
require_once 'auth.php';
require_once '../config/db.php';

$current_page = 'users'; // Keep sidebar active on users
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    header("Location: users.php");
    exit();
}

try {
    // 1. Fetch User Profile
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }

    // 2. Fetch Orders
    $stmt_orders = $pdo->prepare("SELECT o.*, 
        (SELECT rating FROM order_reviews WHERE order_id = o.id LIMIT 1) as rating 
        FROM orders o WHERE user_id = ? ORDER BY created_at DESC");
    $stmt_orders->execute([$user_id]);
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch Support Sessions
    $stmt_sessions = $pdo->prepare("SELECT * FROM support_sessions WHERE user_id = ? ORDER BY created_at DESC");
    $stmt_sessions->execute([$user_id]);
    $sessions = $stmt_sessions->fetchAll(PDO::FETCH_ASSOC);

    // 4. Fetch Service Queries (matched by email)
    $stmt_queries = $pdo->prepare("SELECT * FROM service_queries WHERE email = ? ORDER BY created_at DESC");
    $stmt_queries->execute([$user['email']]);
    $queries = $stmt_queries->fetchAll(PDO::FETCH_ASSOC);

    // 5. Fetch User Notes
    $stmt_notes = $pdo->prepare("SELECT un.*, a.username as admin_name FROM user_notes un JOIN administrators a ON un.admin_id = a.id WHERE un.user_id = ? ORDER BY un.created_at DESC");
    $stmt_notes->execute([$user_id]);
    $notes = $stmt_notes->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Helper for status badge colors
function getStatusColor($status) {
    switch ($status) {
        case 'completed':
        case 'delivered':
        case 'resolved':
            return 'bg-green-100 text-green-800 border-green-200';
        case 'pending':
        case 'new':
            return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        case 'cancelled':
            return 'bg-red-100 text-red-800 border-red-200';
        default:
            return 'bg-blue-100 text-blue-800 border-blue-200';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User: <?php echo htmlspecialchars($user['username']); ?> - Hypecrews Admin</title>
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

        /* Tab Styles */
        .tab-btn.active {
            border-bottom: 2px solid #0066cc;
            color: #0066cc;
            font-weight: 600;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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

    <div class="glass-bg"><div class="glass-blob"></div></div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div class="flex items-center">
                    <a href="users.php" class="mr-4 w-10 h-10 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_text transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-apple_text tracking-tight">User Profile</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="support_chats.php" class="bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 px-5 rounded-full flex items-center transition-colors border border-primary/20 shadow-sm">
                        <i class="fas fa-comment-dots mr-2"></i> Message User
                    </a>
                </div>
            </header>
            
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left Column: Personal Info -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Main Card -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm text-center">
                            <div class="w-24 h-24 rounded-full bg-gradient-to-tr from-primary to-blue-300 flex items-center justify-center text-white text-3xl font-bold mx-auto mb-4 shadow-lg border-4 border-white">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                            </div>
                            <h2 class="text-2xl font-bold text-apple_text"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                            <p class="text-primary font-medium mb-4">@<?php echo htmlspecialchars($user['username']); ?></p>
                            
                            <div class="flex justify-center space-x-2 mb-6">
                                <span class="bg-black/5 px-3 py-1 rounded-full text-xs font-semibold text-apple_muted flex items-center">
                                    <i class="fas fa-calendar-alt mr-1.5"></i> Joined <?php echo date('M Y', strtotime($user['created_at'])); ?>
                                </span>
                            </div>

                            <hr class="border-black/5 mb-6">
                            
                            <div class="space-y-4 text-left">
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Email</p>
                                    <p class="font-medium text-apple_text flex items-center">
                                        <i class="fas fa-envelope text-gray-400 mr-2 w-4 text-center"></i>
                                        <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" class="hover:underline text-primary"><?php echo htmlspecialchars($user['email']); ?></a>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Phone</p>
                                    <p class="font-medium text-apple_text flex items-center">
                                        <i class="fas fa-phone text-gray-400 mr-2 w-4 text-center"></i>
                                        <?php echo htmlspecialchars($user['mobile_number']); ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">Location</p>
                                    <p class="font-medium text-apple_text flex items-center">
                                        <i class="fas fa-globe-americas text-gray-400 mr-2 w-4 text-center"></i>
                                        <?php echo htmlspecialchars($user['country']); ?> (Age: <?php echo $user['age']; ?>)
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-1">IP Address</p>
                                    <p class="font-medium text-apple_text flex items-center">
                                        <i class="fas fa-network-wired text-gray-400 mr-2 w-4 text-center"></i>
                                        <?php echo htmlspecialchars($user['ip_address'] ?? 'N/A'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Company Card -->
                        <div class="glass-panel rounded-[2rem] p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-apple_text mb-4 flex items-center">
                                <i class="fas fa-building text-primary mr-2"></i> Company Info
                            </h3>
                            <?php if ($user['company_name']): ?>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-0.5">Company Name</p>
                                        <p class="font-medium text-apple_text"><?php echo htmlspecialchars($user['company_name']); ?></p>
                                    </div>
                                    <?php if ($user['company_website']): ?>
                                    <div>
                                        <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-0.5">Website</p>
                                        <a href="<?php echo htmlspecialchars($user['company_website']); ?>" target="_blank" class="text-primary hover:underline font-medium text-sm">
                                            <?php echo htmlspecialchars($user['company_website']); ?> <i class="fas fa-external-link-alt text-[10px] ml-1"></i>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-sm text-apple_muted italic">No company information provided.</p>
                            <?php endif; ?>
                        </div>

                        <!-- System Details -->
                        <div class="glass-panel rounded-[2rem] p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-apple_text mb-4 flex items-center">
                                <i class="fas fa-desktop text-primary mr-2"></i> System
                            </h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-0.5">Last Active</p>
                                    <p class="font-medium text-apple_text text-sm">
                                        <?php echo !empty($user['last_active_at']) ? date('M j, Y h:i A', strtotime($user['last_active_at'])) : 'Unknown'; ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-apple_muted uppercase tracking-wider font-semibold mb-0.5">Account Created</p>
                                    <p class="font-medium text-apple_text text-sm">
                                        <?php echo date('M j, Y h:i A', strtotime($user['created_at'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Tabs (Orders, Chats, Queries) -->
                    <div class="lg:col-span-2">
                        <div class="glass-panel rounded-[2rem] shadow-sm overflow-hidden flex flex-col h-[calc(100vh-140px)]">
                            
                            <!-- Tab Headers -->
                            <div class="flex border-b border-black/5 bg-white/40">
                                <button onclick="openTab('orders')" class="tab-btn active flex-1 py-4 text-sm font-medium text-apple_muted hover:text-apple_text transition-colors text-center">
                                    <i class="fas fa-shopping-cart mr-2"></i> Orders (<?php echo count($orders); ?>)
                                </button>
                                <button onclick="openTab('chats')" class="tab-btn flex-1 py-4 text-sm font-medium text-apple_muted hover:text-apple_text transition-colors text-center">
                                    <i class="fas fa-comments mr-2"></i> Chats (<?php echo count($sessions); ?>)
                                </button>
                                <button onclick="openTab('queries')" class="tab-btn flex-1 py-4 text-sm font-medium text-apple_muted hover:text-apple_text transition-colors text-center">
                                    <i class="fas fa-question-circle mr-2"></i> Queries (<?php echo count($queries); ?>)
                                </button>
                                <button onclick="openTab('notes')" class="tab-btn flex-1 py-4 text-sm font-medium text-apple_muted hover:text-apple_text transition-colors text-center">
                                    <i class="fas fa-sticky-note mr-2"></i> Notes (<?php echo count($notes); ?>)
                                </button>
                            </div>

                            <!-- Tab Contents -->
                            <div class="flex-1 overflow-y-auto p-6 bg-white/20">
                                
                                <!-- Orders Tab -->
                                <div id="orders" class="tab-content active space-y-4">
                                    <?php if (empty($orders)): ?>
                                        <div class="text-center py-12">
                                            <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-apple_muted">This user hasn't placed any orders yet.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($orders as $order): ?>
                                            <div class="bg-white/60 border border-white/80 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow">
                                                <div class="flex justify-between items-start mb-2">
                                                    <div>
                                                        <h4 class="font-bold text-apple_text text-lg">
                                                            <a href="view_order.php?id=<?php echo $order['id']; ?>" class="hover:text-primary transition-colors">
                                                                <?php echo htmlspecialchars($order['order_title']); ?>
                                                            </a>
                                                        </h4>
                                                        <p class="text-xs text-apple_muted font-mono mt-1">#<?php echo htmlspecialchars($order['tracking_id']); ?></p>
                                                    </div>
                                                    <span class="text-[10px] uppercase font-bold px-2.5 py-1 rounded-md border <?php echo getStatusColor($order['status']); ?>">
                                                        <?php echo str_replace('_', ' ', $order['status']); ?>
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600 line-clamp-2 mb-3"><?php echo htmlspecialchars($order['order_description']); ?></p>
                                                <div class="flex justify-between items-center pt-3 border-t border-black/5 text-xs text-gray-500">
                                                    <span><i class="far fa-clock mr-1"></i> <?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
                                                    <?php if ($order['rating']): ?>
                                                        <span class="text-yellow-500"><i class="fas fa-star"></i> <?php echo $order['rating']; ?>/5</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Chats Tab -->
                                <div id="chats" class="tab-content space-y-4">
                                    <?php if (empty($sessions)): ?>
                                        <div class="text-center py-12">
                                            <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-apple_muted">No support chat sessions found.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($sessions as $session): ?>
                                            <div class="bg-white/60 border border-white/80 rounded-xl p-5 shadow-sm">
                                                <div class="flex justify-between items-center mb-3">
                                                    <h4 class="font-bold text-apple_text"><i class="fas fa-headset text-primary mr-2"></i> Session #<?php echo $session['id']; ?></h4>
                                                    <span class="text-xs font-semibold px-2 py-1 rounded-md <?php echo $session['is_resolved'] ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'; ?>">
                                                        <?php echo $session['is_resolved'] ? 'Resolved' : 'Active'; ?>
                                                    </span>
                                                </div>
                                                <div class="text-xs text-apple_muted flex justify-between">
                                                    <span><i class="far fa-calendar-alt mr-1"></i> <?php echo date('M j, Y h:i A', strtotime($session['created_at'])); ?></span>
                                                    <a href="support_chats.php" class="text-primary hover:underline font-medium">View in Chat Dashboard</a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Queries Tab -->
                                <div id="queries" class="tab-content space-y-4">
                                    <?php if (empty($queries)): ?>
                                        <div class="text-center py-12">
                                            <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-apple_muted">No service queries submitted.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($queries as $query): ?>
                                            <div class="bg-white/60 border border-white/80 rounded-xl p-5 shadow-sm">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h4 class="font-bold text-apple_text"><?php echo htmlspecialchars($query['service_name']); ?></h4>
                                                    <span class="text-[10px] uppercase font-bold px-2 py-1 rounded-md border <?php echo getStatusColor($query['status']); ?>">
                                                        <?php echo $query['status']; ?>
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-700 bg-white/50 p-3 rounded-lg border border-black/5 my-3">
                                                    "<?php echo htmlspecialchars($query['message']); ?>"
                                                </p>
                                                <div class="text-xs text-gray-500 text-right">
                                                    <?php echo date('M j, Y h:i A', strtotime($query['created_at'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Notes Tab -->
                                <div id="notes" class="tab-content space-y-4">
                                    <?php if (empty($notes)): ?>
                                        <div class="text-center py-12">
                                            <i class="fas fa-sticky-note text-4xl text-gray-300 mb-4"></i>
                                            <p class="text-apple_muted">No internal notes for this user.</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($notes as $note): ?>
                                            <div class="bg-yellow-50/80 border border-yellow-200 rounded-xl p-5 shadow-sm relative">
                                                <i class="fas fa-thumbtack absolute top-3 right-4 text-yellow-400 transform rotate-45"></i>
                                                <p class="text-sm text-yellow-900 whitespace-pre-wrap pr-6"><?php echo htmlspecialchars($note['note']); ?></p>
                                                <div class="mt-3 pt-3 border-t border-yellow-200/50 flex justify-between text-[11px] font-medium text-yellow-700">
                                                    <span>By <?php echo htmlspecialchars($note['admin_name']); ?></span>
                                                    <span><?php echo date('M j, Y h:i A', strtotime($note['created_at'])); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function openTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.remove('active');
            });
            // Remove active class from buttons
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active');
            });
            // Show selected tab
            document.getElementById(tabId).classList.add('active');
            // Add active class to clicked button
            event.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>

