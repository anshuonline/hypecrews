<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'softwares';

// Handle deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $id = $_GET['delete'];
        
        // Get software details to delete files
        $stmt = $pdo->prepare("SELECT logo_path, banner_path, file_type, file_path FROM softwares WHERE id = ?");
        $stmt->execute([$id]);
        $software = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($software) {
            // Delete associated screenshots
            $stmt = $pdo->prepare("SELECT image_path FROM software_screenshots WHERE software_id = ?");
            $stmt->execute([$id]);
            $screenshots = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($screenshots as $ss) {
                if (!empty($ss['image_path']) && file_exists('../' . $ss['image_path'])) {
                    unlink('../' . $ss['image_path']);
                }
            }
            
            // Delete main files
            if (!empty($software['logo_path']) && file_exists('../' . $software['logo_path'])) {
                unlink('../' . $software['logo_path']);
            }
            if (!empty($software['banner_path']) && file_exists('../' . $software['banner_path'])) {
                unlink('../' . $software['banner_path']);
            }
            if ($software['file_type'] == 'upload' && !empty($software['file_path']) && file_exists('../' . $software['file_path'])) {
                unlink('../' . $software['file_path']);
            }
            
            // Delete from database (cascades screenshots)
            $stmt = $pdo->prepare("DELETE FROM softwares WHERE id = ?");
            $stmt->execute([$id]);
            
            logAdminActivity($pdo, 'DELETE_SOFTWARE', "Deleted software: " . $software['name'] . " (ID: $id)");
            
            $success = "Software deleted successfully.";
        }
    } catch (PDOException $e) {
        $error = "Error deleting software: " . $e->getMessage();
    }
}

// Get softwares
try {
    $stmt = $pdo->prepare("SELECT * FROM softwares ORDER BY created_at DESC");
    $stmt->execute();
    $softwares = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching softwares: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Softwares - Hypecrews Admin</title>
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Manage Softwares</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="software_add.php" class="bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 px-5 rounded-full flex items-center transition-colors border border-primary/20 shadow-sm">
                        <i class="fas fa-plus mr-2"></i>
                        Add Software
                    </a>
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
                    <?php if (empty($softwares)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-laptop-code text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No softwares added yet.</p>
                    </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($softwares as $software): ?>
                        <div class="glass-panel bg-white/50 border border-white/60 rounded-[1.5rem] p-6 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group relative overflow-hidden">
                            <!-- Top Decorator -->
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-400 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Header (Icon & Name) -->
                            <div class="flex items-start gap-4 mb-5">
                                <div class="shrink-0">
                                    <?php if (!empty($software['logo_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($software['logo_path']); ?>" alt="Logo" class="w-16 h-16 object-cover rounded-[1rem] shadow-sm border border-black/5 bg-white group-hover:scale-105 transition-transform duration-300">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-[1rem] bg-blue-50 flex items-center justify-center text-primary border border-blue-100 shadow-sm group-hover:scale-105 transition-transform duration-300">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-apple_text mb-1 leading-tight group-hover:text-primary transition-colors truncate" title="<?php echo htmlspecialchars($software['name']); ?>"><?php echo htmlspecialchars($software['name']); ?></h3>
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold tracking-wider uppercase bg-black/5 text-apple_muted border border-black/5">
                                        v<?php echo htmlspecialchars($software['version']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Platform & Pricing -->
                            <div class="mb-6 space-y-2.5">
                                <div class="flex items-center text-sm bg-white/60 rounded-xl p-2.5 border border-black/5 group-hover:bg-white transition-colors shadow-sm">
                                    <div class="w-7 text-center text-primary mr-2"><i class="fas fa-desktop"></i></div>
                                    <span class="text-apple_text font-semibold truncate" title="<?php echo htmlspecialchars($software['platform']); ?>"><?php echo htmlspecialchars($software['platform']); ?></span>
                                </div>
                                <div class="flex items-center text-sm bg-white/60 rounded-xl p-2.5 border border-black/5 group-hover:bg-white transition-colors shadow-sm">
                                    <div class="w-7 text-center text-primary mr-2"><i class="fas fa-tag"></i></div>
                                    <?php if ($software['is_paid']): ?>
                                        <span class="text-indigo-600 font-bold">₹<?php echo number_format($software['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-green-600 font-bold">Free</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Footer Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-black/5 mt-auto">
                                <span class="text-xs text-apple_muted font-semibold flex items-center">
                                    <i class="far fa-calendar-alt mr-1.5 opacity-70"></i> <?php echo date('M j, Y', strtotime($software['created_at'])); ?>
                                </span>
                                <div class="flex space-x-2">
                                    <a href="../software_details.php?id=<?php echo $software['id']; ?>" target="_blank" class="w-8 h-8 rounded-full bg-black/5 hover:bg-primary hover:text-white text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="View Public Page">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="software_edit.php?id=<?php echo $software['id']; ?>" class="w-8 h-8 rounded-full bg-black/5 hover:bg-amber-500 hover:text-white text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="Edit Software">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <a href="?delete=<?php echo $software['id']; ?>" onclick="return confirm('Are you sure you want to delete this software? All related files will be removed.');" class="w-8 h-8 rounded-full bg-black/5 hover:bg-red-500 hover:text-white text-apple_muted flex items-center justify-center transition-colors shadow-sm" title="Delete Software">
                                        <i class="fas fa-trash text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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

