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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Activity Logs</h1>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                
                <!-- Filters -->
                <div class="glass-panel p-6 rounded-[2rem] mb-8 shadow-sm">
                    <form method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by Admin name or details..." class="w-full px-5 py-3 glass-panel bg-white/50 border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text placeholder-gray-400 font-medium shadow-sm">
                        </div>
                        <div>
                            <select name="action_filter" class="w-full md:w-64 px-5 py-3 glass-panel bg-white/50 border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text font-medium appearance-none shadow-sm">
                                <option value="">All Actions</option>
                                <?php foreach($actions as $action): ?>
                                    <option value="<?php echo htmlspecialchars($action); ?>" <?php echo $action === $action_filter ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($action); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-8 py-3 rounded-xl font-bold transition-colors shadow-md flex items-center justify-center">
                            Filter
                        </button>
                        <?php if(!empty($search) || !empty($action_filter)): ?>
                            <a href="activity_logs.php" class="bg-black/5 hover:bg-black/10 text-apple_text px-6 py-3 rounded-xl font-semibold text-center transition-colors shadow-sm flex items-center justify-center">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="glass-panel rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <?php if (empty($logs)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-clipboard-list text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No activity logs found.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-apple_muted border-b border-black/5 text-sm uppercase tracking-wider">
                                    <th class="pb-4 font-semibold px-4 min-w-[150px]">Timestamp</th>
                                    <th class="pb-4 font-semibold px-4 min-w-[200px]">Admin</th>
                                    <th class="pb-4 font-semibold px-4 min-w-[150px]">Action</th>
                                    <th class="pb-4 font-semibold px-4">Details</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-white/30 transition-colors group">
                                    <td class="py-5 px-4 rounded-l-2xl">
                                        <div class="flex items-center text-sm font-medium text-apple_muted">
                                            <i class="far fa-clock mr-2.5 text-black/20"></i>
                                            <div>
                                                <div class="text-apple_text font-semibold"><?php echo date('M j, Y', strtotime($log['created_at'])); ?></div>
                                                <div class="text-xs text-primary/60"><?php echo date('h:i A', strtotime($log['created_at'])); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($log['profile_image'])): ?>
                                                <div class="w-10 h-10 rounded-full mr-3 overflow-hidden border border-white/60 bg-white/50 shrink-0 cursor-pointer hover:border-primary transition-colors shadow-sm" onclick="zoomImage(this.querySelector('img').src)">
                                                    <img src="../<?php echo htmlspecialchars($log['profile_image']); ?>" class="w-full h-full object-cover">
                                                </div>
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-blue-50 text-primary border border-blue-100 flex items-center justify-center mr-3 text-sm font-bold shadow-inner shrink-0">
                                                    <?php echo substr(htmlspecialchars($log['admin_username']), 0, 1); ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <span class="font-bold text-apple_text text-sm"><?php echo htmlspecialchars($log['admin_username']); ?></span>
                                                <p class="text-[11px] font-semibold text-apple_muted uppercase tracking-wider mt-0.5">Administrator</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <?php
                                            $action = htmlspecialchars($log['action_type']);
                                            $badgeClass = 'bg-gray-100 text-gray-700 border-gray-200';
                                            $icon = 'fa-dot-circle';
                                            
                                            if ($action === 'LOGIN') {
                                                $badgeClass = 'bg-green-50 text-green-700 border-green-200';
                                                $icon = 'fa-sign-in-alt';
                                            } elseif ($action === 'LOGOUT') {
                                                $badgeClass = 'bg-red-50 text-red-700 border-red-200';
                                                $icon = 'fa-sign-out-alt';
                                            } elseif (strpos($action, 'UPDATE') !== false || strpos($action, 'EDIT') !== false) {
                                                $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                                                $icon = 'fa-edit';
                                            } elseif (strpos($action, 'CREATE') !== false || strpos($action, 'ADD') !== false) {
                                                $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                                                $icon = 'fa-plus-circle';
                                            } elseif (strpos($action, 'DELETE') !== false) {
                                                $badgeClass = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $icon = 'fa-trash-alt';
                                            }
                                        ?>
                                        <span class="<?php echo $badgeClass; ?> px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider border shadow-sm flex items-center w-max">
                                            <i class="fas <?php echo $icon; ?> mr-2 opacity-70 text-[10px]"></i>
                                            <?php echo $action; ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-apple_text text-sm leading-relaxed max-w-md font-medium rounded-r-2xl">
                                        <?php echo htmlspecialchars($log['description']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="mt-8 flex justify-center space-x-2">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&action_filter=<?php echo urlencode($action_filter); ?>" 
                               class="w-10 h-10 flex items-center justify-center rounded-full text-sm font-bold shadow-sm transition-all <?php echo $page === $i ? 'bg-primary text-white shadow-md transform scale-105' : 'bg-white/50 text-apple_text hover:bg-white/80 border border-black/5'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php endif; ?>
                </div>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Zoom Modal -->
    <div id="imageZoomModal" class="fixed inset-0 bg-black/40 backdrop-blur-md z-[100] hidden items-center justify-center p-4 transition-opacity" onclick="closeZoom()">
        <button class="absolute top-6 right-6 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full text-white text-2xl flex items-center justify-center transition-colors shadow-lg focus:outline-none backdrop-blur-lg" onclick="closeZoom()"><i class="fas fa-times"></i></button>
        <img id="zoomedImage" src="" class="max-w-full max-h-[90vh] rounded-[2rem] shadow-2xl object-contain border-4 border-white/80" onclick="event.stopPropagation()">
    </div>

    <script>
        function zoomImage(src) {
            const modal = document.getElementById('imageZoomModal');
            const img = document.getElementById('zoomedImage');
            img.src = src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeZoom() {
            const modal = document.getElementById('imageZoomModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</body>
</html>

