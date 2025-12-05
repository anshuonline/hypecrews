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
        
        .rating-stars {
            color: #fbbf24;
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
                    <h2 class="text-2xl font-bold">Manage Reviews</h2>
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
                            <input type="text" name="search" placeholder="Search by username, tracking ID, or order title..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-4 py-2 bg-dark border border-gray-700 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-r-lg">
                                <i class="fas fa-search text-white"></i>
                            </button>
                            <?php if (!empty($search)): ?>
                                <a href="reviews.php" class="ml-2 bg-gray-600 hover:bg-gray-700 px-4 rounded-lg flex items-center">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    
                    <?php if (empty($reviews)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-star text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No reviews found</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Order</th>
                                    <th class="pb-3">User</th>
                                    <th class="pb-3">Rating</th>
                                    <th class="pb-3">Review</th>
                                    <th class="pb-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium"><?php echo htmlspecialchars($review['order_title']); ?></p>
                                        <p class="text-sm text-gray-400">Order #<?php echo $review['order_id']; ?></p>
                                        <?php if ($review['tracking_id']): ?>
                                        <p class="text-xs text-gray-500">Tracking: <?php echo htmlspecialchars($review['tracking_id']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($review['first_name'] . ' ' . $review['last_name']); ?></p>
                                        <p class="text-sm text-gray-400">@<?php echo htmlspecialchars($review['username']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <div class="rating-stars flex text-lg">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star <?php echo ($i <= $review['rating']) ? '' : 'text-gray-400'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <p class="text-sm text-gray-400 mt-1"><?php echo $review['rating']; ?>/5</p>
                                    </td>
                                    <td class="py-4">
                                        <?php if ($review['review']): ?>
                                        <p><?php echo nl2br(htmlspecialchars(substr($review['review'], 0, 100))); ?><?php echo strlen($review['review']) > 100 ? '...' : ''; ?></p>
                                        <?php else: ?>
                                        <p class="text-gray-400">No review provided</p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y', strtotime($review['created_at'])); ?>
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