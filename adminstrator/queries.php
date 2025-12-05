<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'queries';

// Handle status update
if (isset($_POST['update_status']) && isset($_POST['query_id']) && isset($_POST['status'])) {
    $query_id = intval($_POST['query_id']);
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE service_queries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $query_id]);
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
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
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
                    <h2 class="text-2xl font-bold">Manage Service Queries</h2>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (isset($success)): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <div>
                            <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
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
                    <!-- Search and Filter Form -->
                    <div class="mb-6">
                        <form method="GET" class="flex flex-wrap gap-4">
                            <div class="flex-grow">
                                <input type="text" name="search" placeholder="Search by service, name, or email..." value="<?php echo htmlspecialchars($search); ?>" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            </div>
                            <div>
                                <select name="status" class="px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                                    <option value="">All Statuses</option>
                                    <option value="new" <?php echo ($status_filter == 'new') ? 'selected' : ''; ?>>New</option>
                                    <option value="in_progress" <?php echo ($status_filter == 'in_progress') ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="resolved" <?php echo ($status_filter == 'resolved') ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="bg-primary hover:bg-indigo-700 px-4 rounded-lg">
                                    <i class="fas fa-search text-white"></i>
                                </button>
                                <?php if (!empty($search) || !empty($status_filter)): ?>
                                    <a href="queries.php" class="bg-gray-600 hover:bg-gray-700 px-4 rounded-lg flex items-center">
                                        <i class="fas fa-times mr-2"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    
                    <?php if (empty($queries)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-question-circle text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No queries found</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Service</th>
                                    <th class="pb-3">Customer</th>
                                    <th class="pb-3">Contact</th>
                                    <th class="pb-3">Message</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($queries as $query): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium"><?php echo htmlspecialchars($query['service_name']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($query['name']); ?></p>
                                        <?php if ($query['company']): ?>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($query['company']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($query['email']); ?></p>
                                        <?php if ($query['phone']): ?>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($query['phone']); ?></p>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo nl2br(htmlspecialchars(substr($query['message'], 0, 100))); ?><?php echo strlen($query['message']) > 100 ? '...' : ''; ?></p>
                                    </td>
                                    <td class="py-4">
                                        <?php
                                        $statusClasses = [
                                            'new' => 'bg-yellow-500/10 text-yellow-500',
                                            'in_progress' => 'bg-blue-500/10 text-blue-500',
                                            'resolved' => 'bg-green-500/10 text-green-500'
                                        ];
                                        $statusLabels = [
                                            'new' => 'New',
                                            'in_progress' => 'In Progress',
                                            'resolved' => 'Resolved'
                                        ];
                                        ?>
                                        <span class="status-badge <?php echo $statusClasses[$query['status']]; ?>">
                                            <?php echo $statusLabels[$query['status']]; ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y', strtotime($query['created_at'])); ?>
                                    </td>
                                    <td class="py-4">
                                        <button onclick="showQueryDetails(<?php echo $query['id']; ?>)" class="text-primary hover:text-indigo-400 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="showStatusModal(<?php echo $query['id']; ?>, '<?php echo $query['status']; ?>')" class="text-gray-400 hover:text-white">
                                            <i class="fas fa-edit"></i>
                                        </button>
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
    
    <!-- Query Details Modal -->
    <div id="queryDetailsModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-dark rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Query Details</h3>
                    <button onclick="closeQueryDetailsModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="queryDetailsContent"></div>
            </div>
        </div>
    </div>
    
    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-dark rounded-xl shadow-lg max-w-md w-full">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Update Query Status</h3>
                    <button onclick="closeStatusModal()" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" id="queryIdInput" name="query_id" value="">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Select Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-dark border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white">
                            <option value="new">New</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="closeStatusModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-4 rounded-lg">
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
            fetch(`get_query_details.php?id=${queryId}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('queryDetailsContent').innerHTML = data;
                    document.getElementById('queryDetailsModal').classList.remove('hidden');
                    document.getElementById('queryDetailsModal').classList.add('flex');
                })
                .catch(error => {
                    console.error('Error:', error);
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