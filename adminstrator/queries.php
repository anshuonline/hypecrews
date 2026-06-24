<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'queries';

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['query_id']) && isset($_POST['status'])) {
    $query_id = intval($_POST['query_id']);
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE service_queries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $query_id]);
        
        logAdminActivity($pdo, 'UPDATE_QUERY', "Updated query #{$query_id} status to {$status}");
        
        $success = "Query status updated successfully";
    } catch (PDOException $e) {
        $error = "Error updating query status: " . $e->getMessage();
    }
}

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Get queries with filtering
try {
    $sql = "SELECT * FROM service_queries";
    $params = [];
    
    if (!empty($search) || !empty($status_filter)) {
        $sql .= " WHERE";
        $conditions = [];
        
        if (!empty($search)) {
            $conditions[] = " (service_name LIKE ? OR name LIKE ? OR email LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($status_filter)) {
            $conditions[] = " status = ?";
            $params[] = $status_filter;
        }
        
        $sql .= implode(" AND", $conditions);
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $queries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching queries: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Queries - Hypecrews Admin</title>
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
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Manage Service Queries</h1>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($success)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Search and Filter Form -->
                <div class="mb-8">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-grow">
                            <input type="text" name="search" placeholder="Search by service, name, or email..." value="<?php echo htmlspecialchars($search); ?>" class="w-full px-5 py-3 glass-panel border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm placeholder-gray-400 font-medium">
                        </div>
                        <div>
                            <select name="status" class="px-5 py-3 glass-panel border border-white/60 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm font-medium appearance-none">
                                <option value="">All Statuses</option>
                                <option value="new" <?php echo ($status_filter == 'new') ? 'selected' : ''; ?>>New</option>
                                <option value="in_progress" <?php echo ($status_filter == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo ($status_filter == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                            </select>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-6 py-3 rounded-xl transition-colors shadow-md flex items-center justify-center">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($search) || !empty($status_filter)): ?>
                                <a href="queries.php" class="glass-panel bg-gray-50/50 hover:bg-gray-100/50 px-6 py-3 rounded-xl flex items-center text-apple_muted transition-colors shadow-sm font-medium">
                                    <i class="fas fa-times mr-2"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <?php if (empty($queries)): ?>
                <div class="text-center py-20 glass-panel rounded-3xl max-w-2xl mx-auto shadow-sm">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                        <i class="fas fa-question-circle text-4xl text-gray-400"></i>
                    </div>
                    <p class="text-apple_muted text-lg font-medium">No queries found.</p>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($queries as $query): ?>
                    <?php
                        $statusClasses = [
                            'new' => 'bg-yellow-500/10 text-yellow-700 border-yellow-500/20',
                            'in_progress' => 'bg-blue-500/10 text-blue-700 border-blue-500/20',
                            'resolved' => 'bg-green-500/10 text-green-700 border-green-500/20'
                        ];
                        $statusLabels = [
                            'new' => 'New',
                            'in_progress' => 'In Progress',
                            'resolved' => 'Resolved'
                        ];
                    ?>
                    <!-- Query Card - Apple Glass Style -->
                    <div class="glass-panel rounded-3xl p-6 hover:bg-white/70 transition-all duration-300 transform hover:-translate-y-1 flex flex-col h-full relative group">
                        
                        <div class="flex justify-between items-start mb-5">
                            <span class="status-badge border <?php echo $statusClasses[$query['status']]; ?> shadow-sm">
                                <?php echo $statusLabels[$query['status']]; ?>
                            </span>
                            <span class="text-[11px] font-semibold text-apple_muted flex items-center bg-white/50 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/60 shadow-sm">
                                <i class="far fa-clock mr-1.5"></i> <?php echo date('M j, Y', strtotime($query['created_at'])); ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-apple_text mb-4 line-clamp-2 leading-tight group-hover:text-primary transition-colors" title="<?php echo htmlspecialchars($query['service_name']); ?>">
                            <?php echo htmlspecialchars($query['service_name']); ?>
                        </h3>
                        
                        <div class="flex flex-col gap-2.5 mb-5 bg-white/40 p-4 rounded-2xl border border-white/60 shadow-sm group-hover:bg-white/60 transition-colors">
                            <p class="text-sm text-apple_text font-semibold flex items-center">
                                <span class="w-6 h-6 rounded-full bg-blue-50 text-primary flex items-center justify-center mr-2.5 shadow-inner shrink-0">
                                    <i class="fas fa-user text-[10px]"></i>
                                </span>
                                <span class="truncate"><?php echo htmlspecialchars($query['name']); ?></span>
                                <?php if ($query['company']): ?>
                                <span class="text-xs text-apple_muted ml-1.5 truncate font-medium">(<?php echo htmlspecialchars($query['company']); ?>)</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-sm text-primary/80 font-medium flex items-center">
                                <span class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mr-2.5 shadow-inner shrink-0">
                                    <i class="fas fa-envelope text-[10px]"></i>
                                </span>
                                <a href="mailto:<?php echo htmlspecialchars($query['email']); ?>" class="truncate hover:underline"><?php echo htmlspecialchars($query['email']); ?></a>
                            </p>
                            <?php if ($query['phone']): ?>
                            <p class="text-sm text-apple_muted font-medium flex items-center">
                                <span class="w-6 h-6 rounded-full bg-green-50 text-green-600 flex items-center justify-center mr-2.5 shadow-inner shrink-0">
                                    <i class="fas fa-phone-alt text-[10px]"></i>
                                </span>
                                <a href="tel:<?php echo htmlspecialchars($query['phone']); ?>" class="hover:text-apple_text transition-colors"><?php echo htmlspecialchars($query['phone']); ?></a>
                            </p>
                            <?php endif; ?>
                        </div>

                        <div class="flex-grow mb-6">
                            <div class="text-apple_muted text-sm leading-relaxed line-clamp-3 relative font-medium">
                                <?php echo nl2br(htmlspecialchars($query['message'])); ?>
                            </div>
                        </div>

                        <div class="flex justify-between items-center border-t border-black/5 pt-5 mt-auto">
                            <button onclick="showStatusModal(<?php echo $query['id']; ?>, '<?php echo $query['status']; ?>')" class="text-sm font-semibold text-apple_muted hover:text-primary flex items-center px-3 py-1.5 rounded-lg hover:bg-primary/5 transition-colors">
                                <i class="fas fa-tasks mr-2"></i> Update Status
                            </button>
                            <button onclick="showQueryDetails(<?php echo $query['id']; ?>)" class="text-sm bg-black/5 hover:bg-black/10 text-apple_text px-4 py-2 rounded-full font-bold transition-all flex items-center shadow-sm">
                                <i class="fas fa-eye mr-2"></i> View Details
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Query Details Modal -->
    <div id="queryDetailsModal" class="fixed inset-0 bg-black/20 backdrop-blur-md z-50 hidden items-center justify-center p-4">
        <div class="glass-panel bg-white/80 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white max-w-2xl w-full max-h-[90vh] overflow-y-auto transform transition-all">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-apple_text">Query Details</h3>
                    <button onclick="closeQueryDetailsModal()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_muted transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="queryDetailsContent" class="text-apple_text">
                    <!-- Content loaded via AJAX from get_query_details.php -->
                    <div class="flex justify-center py-10">
                        <i class="fas fa-circle-notch fa-spin text-primary text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black/20 backdrop-blur-md z-50 hidden items-center justify-center p-4">
        <div class="glass-panel bg-white/80 rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white max-w-md w-full transform transition-all">
            <div class="p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-apple_text">Update Query Status</h3>
                    <button onclick="closeStatusModal()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_muted transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" id="queryIdInput" name="query_id" value="">
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-apple_muted mb-2">Select Status</label>
                        <select name="status" class="w-full px-4 py-3 bg-white/50 border border-black/10 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text font-medium appearance-none shadow-sm">
                            <option value="new">New</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" onclick="closeStatusModal()" class="px-5 py-2.5 rounded-full bg-black/5 hover:bg-black/10 font-semibold text-apple_text transition-colors">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-full bg-primary hover:bg-blue-600 text-white font-bold transition-colors shadow-md">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Show query details modal
        function showQueryDetails(queryId) {
            // Show loading state first
            document.getElementById('queryDetailsContent').innerHTML = '<div class="flex justify-center py-10"><i class="fas fa-circle-notch fa-spin text-primary text-3xl"></i></div>';
            document.getElementById('queryDetailsModal').classList.remove('hidden');
            document.getElementById('queryDetailsModal').classList.add('flex');
            
            fetch(`get_query_details.php?id=${queryId}`)
                .then(response => response.text())
                .then(data => {
                    // Update content. Note: get_query_details.php might need style updates too to match the light theme
                    document.getElementById('queryDetailsContent').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('queryDetailsContent').innerHTML = '<div class="p-4 bg-red-50 text-red-600 rounded-xl border border-red-200">Error loading details. Please try again.</div>';
                });
        }
        
        // Close query details modal
        function closeQueryDetailsModal() {
            document.getElementById('queryDetailsModal').classList.add('hidden');
            document.getElementById('queryDetailsModal').classList.remove('flex');
        }
        
        // Show status update modal
        function showStatusModal(queryId, currentStatus) {
            document.getElementById('queryIdInput').value = queryId;
            document.getElementById('statusForm').querySelector('select[name="status"]').value = currentStatus;
            document.getElementById('statusModal').classList.remove('hidden');
            document.getElementById('statusModal').classList.add('flex');
        }
        
        // Close status update modal
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
            document.getElementById('statusModal').classList.remove('flex');
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const queryDetailsModal = document.getElementById('queryDetailsModal');
            const statusModal = document.getElementById('statusModal');
            
            if (event.target == queryDetailsModal) {
                closeQueryDetailsModal();
            }
            
            if (event.target == statusModal) {
                closeStatusModal();
            }
        }
    </script>
</body>
</html>
