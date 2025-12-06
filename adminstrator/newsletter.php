<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'newsletter';

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
    </style>
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
                    <h2 class="text-2xl font-bold">Newsletter Subscribers</h2>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
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
                            <input type="text" name="search" placeholder="Search by email..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-4 py-2 bg-dark border border-gray-700 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-r-lg">
                                <i class="fas fa-search text-white"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="newsletter.php" class="ml-2 bg-gray-600 hover:bg-gray-700 px-4 rounded-lg flex items-center">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (empty($subscribers)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-envelope text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No subscribers found</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Email</th>
                                    <th class="pb-3">Subscribed At</th>
                                    <th class="pb-3">IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subscribers as $subscriber): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium"><?php echo htmlspecialchars($subscriber['email']); ?></p>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y g:i A', strtotime($subscriber['subscribed_at'])); ?>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo htmlspecialchars($subscriber['ip_address'] ?? 'N/A'); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>