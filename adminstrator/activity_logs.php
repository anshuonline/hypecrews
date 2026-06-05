<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'activity_logs';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$action_filter = isset($_GET['action_filter']) ? trim($_GET['action_filter']) : '';

// Query building
$query = "SELECT l.*, a.profile_image FROM admin_logs l LEFT JOIN administrators a ON l.admin_id = a.id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (l.admin_username LIKE ? OR l.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($action_filter)) {
    $query .= " AND l.action_type = ?";
    $params[] = $action_filter;
}

// Get total count for pagination
$countQuery = str_replace("SELECT l.*, a.profile_image", "SELECT COUNT(*)", $query);
$stmt_count = $pdo->prepare($countQuery);
$stmt_count->execute($params);
$total_logs = $stmt_count->fetchColumn();
$total_pages = ceil($total_logs / $limit);

// Fetch logs
$query .= " ORDER BY l.created_at DESC LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unique actions for filter
$stmt_actions = $pdo->query("SELECT DISTINCT action_type FROM admin_logs ORDER BY action_type");
$actions = $stmt_actions->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Hypecrews Admin</title>
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
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
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
                    <h2 class="text-2xl font-bold">Activity Logs</h2>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                
                <!-- Filters -->
                <div class="bg-light p-4 rounded-xl mb-6 shadow-md border border-gray-800">
                    <form method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Admin name or details..." class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                        </div>
                        <div>
                            <select name="action_filter" class="w-full md:w-64 px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                                <option value="">All Actions</option>
                                <?php foreach($actions as $action): ?>
                                    <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $action === $action_filter ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($action); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-primary hover:bg-indigo-600 px-6 py-2 rounded-lg font-medium transition-colors">
                            Filter
                        </button>
                        <?php if(!empty($search) || !empty($action_filter)): ?>
                            <a href="activity_logs.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg font-medium text-center transition-colors">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="bg-light rounded-xl p-6 shadow-lg border border-gray-800">
                    <?php if (empty($logs)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-clipboard-list text-5xl text-gray-600 mb-4"></i>
                        <p class="text-gray-400 text-lg">No activity logs found.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-4 font-semibold">Timestamp</th>
                                    <th class="pb-4 font-semibold">Admin</th>
                                    <th class="pb-4 font-semibold">Action</th>
                                    <th class="pb-4 font-semibold">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log): ?>
                                <tr class="border-b border-gray-800/50 hover:bg-dark/40 transition-colors">
                                    <td class="py-4 whitespace-nowrap text-sm text-gray-400">
                                        <?php echo date('M j, Y h:i A', strtotime($log['created_at'])); ?>
                                    </td>
                                    <td class="py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($log['profile_image'])): ?>
                                                <div class="w-8 h-8 rounded-full mr-3 overflow-hidden border border-gray-600 bg-dark shrink-0">
                                                    <img src="../<?php echo htmlspecialchars($log['profile_image']); ?>" class="w-full h-full object-cover">
                                                </div>
                                            <?php else: ?>
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 text-sm font-bold shadow-lg shrink-0">
                                                    <?php echo substr(htmlspecialchars($log['admin_username']), 0, 1); ?>
                                                </div>
                                            <?php endif; ?>
                                            <span class="font-medium"><?php echo htmlspecialchars($log['admin_username']); ?></span>
                                        </div>
                                    </td>
                                    <td class="py-4">
                                        <span class="bg-gray-800 text-indigo-300 px-3 py-1 rounded-md text-xs font-semibold border border-gray-700">
                                            <?php echo htmlspecialchars($log['action_type']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-gray-300 text-sm">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="mt-6 flex justify-center space-x-2">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&action_filter=<?php echo urlencode($action_filter); ?>" 
                               class="px-4 py-2 rounded-lg text-sm font-medium <?php echo $page === $i ? 'bg-primary text-white' : 'bg-gray-800 text-gray-400 hover:bg-gray-700'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
