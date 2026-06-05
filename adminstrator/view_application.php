<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'job_applications';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: job_applications.php");
    exit;
}
$app_id = $_GET['id'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['status'])) {
    try {
        $stmt = $pdo->prepare("UPDATE job_applications SET status = ? WHERE id = ?");
        $stmt->execute([$_POST['status'], $app_id]);
        $success = "Application status updated.";
    } catch (PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Get application details
try {
    $stmt = $pdo->prepare("
        SELECT a.*, j.title as job_title, j.department, j.location 
        FROM job_applications a 
        LEFT JOIN jobs j ON a.job_id = j.id 
        WHERE a.id = ?
    ");
    $stmt->execute([$app_id]);
    $app = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$app) {
        die("Application not found.");
    }
} catch (PDOException $e) {
    die("Error fetching application: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application - Hypecrews Admin</title>
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
        select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #fff !important;
        }
        select:focus {
            border-color: #6366f1 !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white">
    <div class="flex h-screen">
        <?php include 'components/sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-dark border-b border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold">Application Details</h2>
                    <a href="job_applications.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
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
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-light rounded-xl p-6">
                            <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Applicant Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-gray-400 text-sm">Full Name</p>
                                    <p class="font-medium text-lg"><?php echo htmlspecialchars($app['applicant_name']); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Email Address</p>
                                    <p class="font-medium text-lg"><a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="text-primary hover:underline"><?php echo htmlspecialchars($app['email']); ?></a></p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Phone Number</p>
                                    <p class="font-medium text-lg"><?php echo htmlspecialchars($app['phone'] ?? 'N/A'); ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-sm">Applied On</p>
                                    <p class="font-medium text-lg"><?php echo date('F j, Y, g:i a', strtotime($app['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-light rounded-xl p-6">
                            <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Cover Letter</h3>
                            <div class="prose prose-invert max-w-none">
                                <?php if (!empty($app['cover_letter'])): ?>
                                    <p class="whitespace-pre-wrap text-gray-300"><?php echo htmlspecialchars($app['cover_letter']); ?></p>
                                <?php else: ?>
                                    <p class="text-gray-500 italic">No cover letter provided.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        <div class="bg-light rounded-xl p-6">
                            <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Application Status</h3>
                            <form method="POST" action="">
                                <div class="mb-4">
                                    <select name="status" class="w-full px-4 py-3 rounded-lg border">
                                        <option value="new" <?php echo ($app['status'] == 'new') ? 'selected' : ''; ?>>New</option>
                                        <option value="reviewed" <?php echo ($app['status'] == 'reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                                        <option value="shortlisted" <?php echo ($app['status'] == 'shortlisted') ? 'selected' : ''; ?>>Shortlisted</option>
                                        <option value="rejected" <?php echo ($app['status'] == 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                                    </select>
                                </div>
                                <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white font-bold py-3 rounded-lg transition-colors">
                                    Update Status
                                </button>
                            </form>
                        </div>
                        
                        <div class="bg-light rounded-xl p-6">
                            <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Job Details</h3>
                            <p class="font-medium text-lg mb-1"><?php echo htmlspecialchars($app['job_title']); ?></p>
                            <p class="text-gray-400 text-sm mb-1"><i class="fas fa-building mr-2"></i><?php echo htmlspecialchars($app['department'] ?? 'General'); ?></p>
                            <p class="text-gray-400 text-sm"><i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($app['location'] ?? 'Not specified'); ?></p>
                        </div>
                        
                        <div class="bg-light rounded-xl p-6">
                            <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Resume / CV</h3>
                            <?php if (!empty($app['resume_path'])): ?>
                                <a href="../<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank" class="flex items-center justify-center w-full bg-dark hover:bg-gray-800 border border-gray-700 text-white font-bold py-4 rounded-lg transition-colors">
                                    <i class="fas fa-file-download mr-3 text-2xl text-blue-400"></i> Download Resume
                                </a>
                            <?php else: ?>
                                <p class="text-gray-500 italic text-center py-4">No resume uploaded.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
