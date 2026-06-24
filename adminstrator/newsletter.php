<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'newsletter';

// Handle export request
if (isset($_GET['export']) && $_GET['export'] === 'xls') {
    // Set headers for Excel export
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="newsletter_subscribers.xls"');
    
    // Fetch all subscribers
    try {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT email, subscribed_at, ip_address FROM newsletter_subscriptions WHERE email LIKE ? ORDER BY subscribed_at DESC");
            $searchTerm = "%{$search}%";
            $stmt->execute([$searchTerm]);
        } else {
            $stmt = $pdo->prepare("SELECT email, subscribed_at, ip_address FROM newsletter_subscriptions ORDER BY subscribed_at DESC");
            $stmt->execute();
        }
        
        $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Output Excel format
        echo "Email\tSubscribed At\tIP Address\n";
        
        foreach ($subscribers as $subscriber) {
            echo htmlspecialchars($subscriber['email']) . "\t" . 
                 htmlspecialchars($subscriber['subscribed_at']) . "\t" . 
                 htmlspecialchars($subscriber['ip_address'] ?? 'N/A') . "\n";
        }
        
        exit;
    } catch (PDOException $e) {
        // Handle error silently or log it
        exit;
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get newsletter subscriptions (with or without search)
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM newsletter_subscriptions WHERE email LIKE ? ORDER BY subscribed_at DESC");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM newsletter_subscriptions ORDER BY subscribed_at DESC");
        $stmt->execute();
    }
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching subscribers: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter Subscribers - Hypecrews Admin</title>
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Newsletter Subscribers</h1>
                </div>
                <?php if (!empty($subscribers)): ?>
                <div class="flex items-center space-x-4">
                    <a href="?export=xls<?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" class="bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2.5 px-5 rounded-full flex items-center transition-colors border border-green-200 shadow-sm">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export to Excel
                    </a>
                </div>
                <?php endif; ?>
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
                        <input type="text" name="search" placeholder="Search by email..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-5 py-3 glass-panel border border-white/60 rounded-l-full focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm placeholder-gray-400 font-medium">
                        <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary px-6 rounded-r-full border border-primary/20 border-l-0 shadow-sm transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="newsletter.php" class="ml-3 glass-panel bg-gray-50/50 hover:bg-gray-100/50 px-5 rounded-full flex items-center text-apple_muted transition-colors shadow-sm font-medium">
                                <i class="fas fa-times mr-2"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <?php if (empty($subscribers)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-envelope text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No subscribers found.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-apple_muted border-b border-black/5 text-sm uppercase tracking-wider">
                                    <th class="pb-4 font-semibold px-4">Email</th>
                                    <th class="pb-4 font-semibold px-4">Subscribed At</th>
                                    <th class="pb-4 font-semibold px-4">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($subscribers as $subscriber): ?>
                                <tr class="hover:bg-white/30 transition-colors group">
                                    <td class="py-5 px-4 rounded-l-2xl">
                                        <div class="flex items-center">
                                            <div class="w-9 h-9 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-primary/70 mr-3 shadow-inner">
                                                <i class="fas fa-at text-xs"></i>
                                            </div>
                                            <p class="font-bold text-apple_text"><?php echo htmlspecialchars($subscriber['email']); ?></p>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4 text-apple_muted font-medium">
                                        <?php echo date('M j, Y g:i A', strtotime($subscriber['subscribed_at'])); ?>
                                    </td>
                                    <td class="py-5 px-4 rounded-r-2xl">
                                        <span class="text-xs text-apple_muted font-mono bg-white/50 px-2.5 py-1 rounded-md border border-white/60 shadow-sm">
                                            <?php echo htmlspecialchars($subscriber['ip_address'] ?? 'N/A'); ?>
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
