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

        select { 
            background-color: rgba(255, 255, 255, 0.8) !important; 
            border-color: rgba(0, 0, 0, 0.1) !important; 
            color: #1d1d1f !important; 
            font-weight: 600;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02) inset;
        }
        select:focus { 
            border-color: #0066cc !important; 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15) !important; 
            background-color: #ffffff !important;
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Application Details</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="job_applications.php" class="bg-black/5 hover:bg-black/10 text-apple_text font-semibold px-5 py-2.5 rounded-full flex items-center transition-colors shadow-sm border border-black/5">
                        <i class="fas fa-arrow-left mr-2"></i> Back
                    </a>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center max-w-6xl mx-auto">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center max-w-6xl mx-auto">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                    
                    <!-- Main Content Column -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Applicant Info Card -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm relative overflow-hidden">
                            <!-- Background Decorator -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 rounded-bl-full opacity-50 blur-xl"></div>
                            
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center border-b border-black/5 pb-4 relative z-10">
                                <i class="fas fa-user-circle text-primary mr-3 text-2xl"></i> Applicant Information
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                                <div class="bg-white/40 p-5 rounded-2xl border border-white/60">
                                    <p class="text-[10px] font-bold text-apple_muted uppercase tracking-wider mb-1">Full Name</p>
                                    <p class="font-bold text-lg text-apple_text"><?php echo htmlspecialchars($app['applicant_name']); ?></p>
                                </div>
                                <div class="bg-white/40 p-5 rounded-2xl border border-white/60">
                                    <p class="text-[10px] font-bold text-apple_muted uppercase tracking-wider mb-1">Email Address</p>
                                    <p class="font-bold text-lg"><a href="mailto:<?php echo htmlspecialchars($app['email']); ?>" class="text-primary hover:text-blue-700 hover:underline"><?php echo htmlspecialchars($app['email']); ?></a></p>
                                </div>
                                <div class="bg-white/40 p-5 rounded-2xl border border-white/60">
                                    <p class="text-[10px] font-bold text-apple_muted uppercase tracking-wider mb-1">Phone Number</p>
                                    <p class="font-bold text-lg text-apple_text"><?php echo htmlspecialchars($app['phone'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="bg-white/40 p-5 rounded-2xl border border-white/60">
                                    <p class="text-[10px] font-bold text-apple_muted uppercase tracking-wider mb-1">Applied On</p>
                                    <p class="font-bold text-base text-apple_text"><?php echo date('F j, Y, g:i a', strtotime($app['created_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cover Letter Card -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm">
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center border-b border-black/5 pb-4">
                                <i class="fas fa-file-signature text-indigo-500 mr-3 text-2xl"></i> Cover Letter
                            </h3>
                            <div class="bg-white/40 p-6 rounded-2xl border border-white/60 shadow-inner min-h-[200px]">
                                <?php if (!empty($app['cover_letter'])): ?>
                                    <p class="whitespace-pre-wrap text-apple_text leading-relaxed font-medium"><?php echo htmlspecialchars($app['cover_letter']); ?></p>
                                <?php else: ?>
                                    <div class="flex flex-col items-center justify-center h-full text-apple_muted opacity-70 pt-8 pb-4">
                                        <i class="fas fa-comment-slash text-4xl mb-3"></i>
                                        <p class="font-semibold">No cover letter provided.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar Column -->
                    <div class="space-y-8">
                        
                        <!-- Application Status -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm">
                            <h3 class="text-lg font-bold text-apple_text mb-5 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-tasks text-amber-500 mr-3"></i> Application Status
                            </h3>
                            <form method="POST" action="">
                                <div class="mb-5 relative">
                                    <select name="status" class="w-full px-4 py-4 rounded-xl border appearance-none pr-10 shadow-sm cursor-pointer text-base">
                                        <option value="new" <?php echo ($app['status'] == 'new') ? 'selected' : ''; ?>>🔵 New</option>
                                        <option value="reviewed" <?php echo ($app['status'] == 'reviewed') ? 'selected' : ''; ?>>🟡 Reviewed</option>
                                        <option value="shortlisted" <?php echo ($app['status'] == 'shortlisted') ? 'selected' : ''; ?>>🟢 Shortlisted</option>
                                        <option value="rejected" <?php echo ($app['status'] == 'rejected') ? 'selected' : ''; ?>>🔴 Rejected</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-apple_muted">
                                        <i class="fas fa-chevron-down text-sm"></i>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 flex justify-center items-center">
                                    <i class="fas fa-sync-alt mr-2"></i> Update Status
                                </button>
                            </form>
                        </div>
                        
                        <!-- Job Details Summary -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm bg-gradient-to-br from-white/60 to-blue-50/40">
                            <h3 class="text-lg font-bold text-apple_text mb-5 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-briefcase text-blue-500 mr-3"></i> Job Details
                            </h3>
                            <div class="space-y-3">
                                <p class="font-extrabold text-xl text-apple_text mb-3"><?php echo htmlspecialchars($app['job_title']); ?></p>
                                
                                <div class="flex items-center text-sm font-semibold text-apple_text bg-white/60 p-3 rounded-xl border border-white shadow-sm">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 mr-3">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <?php echo htmlspecialchars($app['department'] ?? 'General'); ?>
                                </div>
                                
                                <div class="flex items-center text-sm font-semibold text-apple_text bg-white/60 p-3 rounded-xl border border-white shadow-sm">
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 mr-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <?php echo htmlspecialchars($app['location'] ?? 'Not specified'); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Resume / CV -->
                        <div class="glass-panel rounded-[2rem] p-8 shadow-sm">
                            <h3 class="text-lg font-bold text-apple_text mb-5 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-file-pdf text-red-500 mr-3"></i> Resume / CV
                            </h3>
                            <?php if (!empty($app['resume_path'])): ?>
                                <a href="../<?php echo htmlspecialchars($app['resume_path']); ?>" target="_blank" class="flex items-center justify-center w-full bg-white/80 hover:bg-white border border-black/10 text-apple_text font-bold py-4 rounded-xl shadow-sm hover:shadow-md transition-all group">
                                    <i class="fas fa-file-download mr-3 text-2xl text-primary group-hover:-translate-y-1 transition-transform"></i> 
                                    <span>Download Resume</span>
                                </a>
                            <?php else: ?>
                                <div class="bg-black/5 rounded-xl p-6 text-center border border-dashed border-black/10">
                                    <i class="fas fa-file-excel text-3xl text-apple_muted mb-2 opacity-50"></i>
                                    <p class="text-sm font-bold text-apple_muted">No resume uploaded.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>
