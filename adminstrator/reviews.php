<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'reviews';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get reviews with order and user info (with or without search)
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT r.*, o.order_title, o.tracking_id, u.username, u.first_name, u.last_name FROM order_reviews r JOIN orders o ON r.order_id = o.id JOIN users u ON r.user_id = u.id WHERE u.username LIKE ? OR o.tracking_id LIKE ? OR o.order_title LIKE ? ORDER BY r.created_at DESC");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare("SELECT r.*, o.order_title, o.tracking_id, u.username, u.first_name, u.last_name FROM order_reviews r JOIN orders o ON r.order_id = o.id JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
        $stmt->execute();
    }
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching reviews: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Hypecrews Admin</title>
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
        
        .rating-stars {
            color: #f59e0b; /* Amber */
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
    <?php include '../components/google_analytics.php'; ?>
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Manage Reviews</h1>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Search Form -->
                <div class="mb-8">
                    <form method="GET" class="flex max-w-2xl">
                        <input type="text" name="search" placeholder="Search by username, tracking ID, or order title..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-5 py-3 glass-panel border border-white/60 rounded-l-full focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm placeholder-gray-400 font-medium">
                        <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary px-6 rounded-r-full border border-primary/20 border-l-0 shadow-sm transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="reviews.php" class="ml-3 glass-panel bg-gray-50/50 hover:bg-gray-100/50 px-5 rounded-full flex items-center text-apple_muted transition-colors shadow-sm font-medium">
                                <i class="fas fa-times mr-2"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <?php if (empty($reviews)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-star text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No reviews found.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-apple_muted border-b border-black/5 text-sm uppercase tracking-wider">
                                    <th class="pb-4 font-semibold px-4">Order</th>
                                    <th class="pb-4 font-semibold px-4">User</th>
                                    <th class="pb-4 font-semibold px-4">Rating</th>
                                    <th class="pb-4 font-semibold px-4">Review</th>
                                    <th class="pb-4 font-semibold px-4 text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($reviews as $review): ?>
                                <tr class="hover:bg-white/30 transition-colors group">
                                    <td class="py-5 px-4 rounded-l-2xl">
                                        <p class="font-bold text-apple_text"><?php echo htmlspecialchars($review['order_title']); ?></p>
                                        <p class="text-xs text-apple_muted font-medium mt-1">Order #<?php echo str_pad($review['order_id'], 5, '0', STR_PAD_LEFT); ?></p>
                                        <?php if ($review['tracking_id']): ?>
                                        <p class="text-[10px] text-primary/80 font-mono mt-0.5 bg-primary/5 inline-block px-1.5 py-0.5 rounded border border-primary/10">TRK: <?php echo htmlspecialchars($review['tracking_id']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4">
                                        <p class="font-semibold text-apple_text"><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></p>
                                        <p class="text-xs text-primary/70 font-medium">@<?php echo htmlspecialchars($review['username']); ?></p>
                                    </td>
                                    <td class="py-5 px-4">
                                        <div class="rating-stars flex text-base tracking-widest bg-white/40 inline-block px-2 py-1 rounded-lg border border-white/60 shadow-sm">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo ($i <= $review['rating']) ? '' : 'text-gray-300'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <?php if ($review['review']): ?>
                                        <p class="text-sm text-apple_muted leading-relaxed font-medium bg-white/30 p-3 rounded-xl border border-white/50 group-hover:bg-white/50 transition-colors">
                                            <?php echo nl2br(htmlspecialchars(substr($review['review'], 0, 120))); ?><?php echo strlen($review['review']) > 120 ? '...' : ''; ?>
                                        </p>
                                        <?php else: ?>
                                        <p class="text-sm text-gray-400 italic">No review text provided</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4 text-right rounded-r-2xl">
                                        <span class="text-xs text-apple_muted font-medium bg-black/5 px-3 py-1.5 rounded-lg whitespace-nowrap">
                                            <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
