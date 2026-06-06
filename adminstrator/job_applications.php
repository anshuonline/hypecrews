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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Job Applications</h1>
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
                
                <?php if (isset($success)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <?php if (empty($applications)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No applications received yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-apple_muted border-b border-black/5 text-sm uppercase tracking-wider">
                                    <th class="pb-4 font-semibold px-4">Applicant</th>
                                    <th class="pb-4 font-semibold px-4">Applied For</th>
                                    <th class="pb-4 font-semibold px-4">Status</th>
                                    <th class="pb-4 font-semibold px-4">Date</th>
                                    <th class="pb-4 font-semibold px-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($applications as $app): ?>
                                <tr class="hover:bg-white/30 transition-colors group">
                                    <td class="py-5 px-4 rounded-l-2xl">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-primary mr-3 shadow-inner">
                                                <span class="font-bold text-sm"><?php echo strtoupper(substr($app['applicant_name'], 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-[15px] text-apple_text"><?php echo htmlspecialchars($app['applicant_name']); ?></p>
                                                <p class="text-xs font-semibold text-primary/70"><a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="hover:underline"><?php echo htmlspecialchars($app['email']); ?></a></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <p class="font-bold text-[14px] text-apple_text bg-white/50 inline-block px-3 py-1.5 rounded-lg border border-black/5 shadow-sm"><?php echo htmlspecialchars($app['job_title'] ?? 'Unknown Job'); ?></p>
                                    </td>
                                    <td class="py-5 px-4">
                                        <?php 
                                            $status_colors = [
                                                'new' => 'bg-blue-50 text-blue-700 border border-blue-200',
                                                'reviewed' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                                'shortlisted' => 'bg-green-50 text-green-700 border border-green-200',
                                                'rejected' => 'bg-red-50 text-red-700 border border-red-200'
                                            ];
                                            $icons = [
                                                'new' => 'fa-star',
                                                'reviewed' => 'fa-eye',
                                                'shortlisted' => 'fa-check-circle',
                                                'rejected' => 'fa-times-circle'
                                            ];
                                            $color = $status_colors[$app['status']] ?? 'bg-gray-100 text-gray-700 border border-gray-200';
                                            $icon = $icons[$app['status']] ?? 'fa-circle';
                                        ?>
                                        <span class="<?php echo $color; ?> px-3 py-1.5 rounded-full text-[11px] font-bold uppercase tracking-wider shadow-sm flex items-center w-max">
                                            <i class="fas <?php echo $icon; ?> mr-1.5 opacity-70"></i>
                                            <?php echo htmlspecialchars($app['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4">
                                        <span class="text-sm font-medium text-apple_text flex flex-col">
                                            <span><i class="far fa-calendar-alt mr-1.5 text-apple_muted"></i><?php echo date('M j, Y', strtotime($app['created_at'])); ?></span>
                                            <span class="text-[11px] text-apple_muted font-semibold mt-0.5 ml-5"><?php echo date('h:i A', strtotime($app['created_at'])); ?></span>
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-right rounded-r-2xl">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="view_application.php?id=<?php echo $app['id']; ?>" class="bg-primary/10 hover:bg-primary hover:text-white text-primary font-bold py-2 px-4 rounded-full text-xs transition-colors shadow-sm" title="View Details">
                                                View Details
                                            </a>
                                            <a href="?delete=<?php echo $app['id']; ?>" onclick="return confirm('Are you sure you want to delete this application?');" class="w-8 h-8 rounded-full bg-white/50 border border-black/5 flex items-center justify-center text-apple_muted hover:text-red-500 hover:bg-red-50 transition-colors shadow-sm" title="Delete Application">
                                                <i class="fas fa-trash text-[13px]"></i>
                                            </a>
                                        </div>
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
