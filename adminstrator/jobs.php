<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'jobs';

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        
        logAdminActivity($pdo, 'DELETE_JOB', "Deleted job ID: " . $_GET['delete']);
        
        $success = "Job deleted successfully.";
    } catch (PDOException $e) {
        $error = "Error deleting job: " . $e->getMessage();
    }
}

// Handle status change
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE jobs SET status = IF(status='active', 'closed', 'active') WHERE id = ?");
        $stmt->execute([$_GET['toggle_status']]);
        
        logAdminActivity($pdo, 'TOGGLE_JOB_STATUS', "Toggled status for job ID: " . $_GET['toggle_status']);
        
        header("Location: jobs.php");
        exit;
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Get jobs
try {
    $stmt = $pdo->prepare("SELECT * FROM jobs ORDER BY created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching jobs: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Jobs - Hypecrews Admin</title>
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
                    <h2 class="text-2xl font-bold">Manage Jobs</h2>
                    <a href="add_job.php" class="bg-primary hover:bg-indigo-700 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i> Add Job
                    </a>
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
                
                <div class="bg-light rounded-xl p-6">
                    <?php if (empty($jobs)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-briefcase text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No jobs posted yet</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Title / Department</th>
                                    <th class="pb-3">Location / Type</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Posted On</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jobs as $job): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium text-lg"><?php echo htmlspecialchars($job['title']); ?></p>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($job['department']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($job['location']); ?></p>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($job['employment_type']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <?php if ($job['status'] == 'active'): ?>
                                            <span class="bg-green-900 text-green-300 px-3 py-1 rounded-full text-xs">Active</span>
                                        <?php else: ?>
                                            <span class="bg-red-900 text-red-300 px-3 py-1 rounded-full text-xs">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y', strtotime($job['created_at'])); ?>
                                    </td>
                                    <td class="py-4 text-right space-x-2">
                                        <a href="?toggle_status=<?php echo $job['id']; ?>" class="text-blue-400 hover:text-blue-300" title="Toggle Status">
                                            <i class="fas fa-sync-alt"></i>
                                        </a>
                                        <a href="add_job.php?id=<?php echo $job['id']; ?>" class="text-yellow-400 hover:text-yellow-300" title="Edit Job">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $job['id']; ?>" onclick="return confirm('Are you sure you want to delete this job?');" class="text-red-400 hover:text-red-300" title="Delete Job">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
