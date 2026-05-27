<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'job_applications';

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        // First get resume path to delete file
        $stmt = $pdo->prepare("SELECT resume_path FROM job_applications WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $app = $stmt->fetch();
        if ($app && !empty($app['resume_path'])) {
            $file_path = '../' . $app['resume_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM job_applications WHERE id = ?");
        $stmt->execute([$_GET['delete']]);
        $success = "Application deleted successfully.";
    } catch (PDOException $e) {
        $error = "Error deleting application: " . $e->getMessage();
    }
}

// Get applications with job titles
try {
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title 
        FROM job_applications a 
        LEFT JOIN jobs j ON a.job_id = j.id 
        ORDER BY a.created_at DESC
    ");
    $stmt->execute();
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching applications: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Applications - Hypecrews Admin</title>
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
        <?php include 'components/sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-dark border-b border-gray-800 p-6">
                <h2 class="text-2xl font-bold">Job Applications</h2>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                    <p class="text-red-300"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <p class="text-green-300"><i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="bg-light rounded-xl p-6">
                    <?php if (empty($applications)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-file-alt text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No applications received yet</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3">Applicant</th>
                                    <th class="pb-3">Applied For</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Date</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <p class="font-medium"><?php echo htmlspecialchars($app['applicant_name']); ?></p>
                                        <p class="text-sm text-gray-400"><?php echo htmlspecialchars($app['email']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($app['job_title'] ?? 'Unknown Job'); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <?php 
                                            $status_colors = [
                                                'new' => 'bg-blue-900 text-blue-300',
                                                'reviewed' => 'bg-yellow-900 text-yellow-300',
                                                'shortlisted' => 'bg-green-900 text-green-300',
                                                'rejected' => 'bg-red-900 text-red-300'
                                            ];
                                            $color = $status_colors[$app['status']] ?? 'bg-gray-700 text-gray-300';
                                        ?>
                                        <span class="<?php echo $color; ?> px-3 py-1 rounded-full text-xs uppercase tracking-wider font-semibold">
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 text-gray-400 text-sm">
                                        <?php echo date('M j, Y H:i', strtotime($app['created_at'])); ?>
                                    </td>
                                    <td class="py-4 text-right space-x-3">
                                        <a href="view_application.php?id=<?php echo $app['id']; ?>" class="text-primary hover:text-indigo-300 font-medium">
                                            View Details
                                        </a>
                                        <a href="?delete=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?');" class="text-red-400 hover:text-red-300">
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
