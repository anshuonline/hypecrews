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
                    <h2 class="text-2xl font-bold">Manage Softwares</h2>
                    <a href="software_add.php" class="bg-primary hover:bg-indigo-700 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-plus mr-2"></i> Add Software
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
                    <?php if (empty($softwares)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-laptop-code text-4xl text-gray-500 mb-4"></i>
                        <p class="text-gray-400">No softwares added yet</p>
                    </div>
                    <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        <?php foreach ($softwares as $software): ?>
                        <div class="bg-[#1e293b]/50 backdrop-blur-sm border border-white/5 rounded-2xl p-5 hover:shadow-[0_0_25px_rgba(99,102,241,0.15)] hover:-translate-y-1 hover:border-primary/30 transition-all duration-300 flex flex-col h-full group relative overflow-hidden">
                            <!-- Top Decorator -->
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Header (Icon & Name) -->
                            <div class="flex items-start gap-4 mb-4">
                                <div class="shrink-0">
                                    <?php if (!empty($software['logo_path'])): ?>
                                        <img src="../<?php echo htmlspecialchars($software['logo_path']); ?>" alt="Logo" class="w-16 h-16 object-cover rounded-xl shadow-lg border border-white/10 bg-[#0f172a] group-hover:scale-105 transition-transform duration-300">
                                    <?php else: ?>
                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-gray-400 border border-white/10 shadow-lg group-hover:scale-105 transition-transform duration-300">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-bold text-lg text-white mb-1 leading-tight group-hover:text-primary transition-colors truncate" title="<?php echo htmlspecialchars($software['name']); ?>"><?php echo htmlspecialchars($software['name']); ?></h3>
                                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-mono bg-white/5 text-gray-400 border border-white/5">
                                        v<?php echo htmlspecialchars($software['version']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Platform & Pricing -->
                            <div class="mb-5 space-y-2">
                                <div class="flex items-center text-sm bg-black/20 rounded-lg p-2 border border-white/5 group-hover:bg-black/40 transition-colors">
                                    <div class="w-6 text-center text-gray-500 mr-2"><i class="fas fa-desktop"></i></div>
                                    <span class="text-gray-300 truncate" title="<?php echo htmlspecialchars($software['platform']); ?>"><?php echo htmlspecialchars($software['platform']); ?></span>
                                </div>
                                <div class="flex items-center text-sm bg-black/20 rounded-lg p-2 border border-white/5 group-hover:bg-black/40 transition-colors">
                                    <div class="w-6 text-center text-gray-500 mr-2"><i class="fas fa-tag"></i></div>
                                    <?php if ($software['is_paid']): ?>
                                        <span class="text-purple-400 font-medium">₹<?php echo number_format($software['price'], 2); ?></span>
                                    <?php else: ?>
                                        <span class="text-green-400 font-medium">Free</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Footer Actions -->
                            <div class="flex items-center justify-between pt-4 border-t border-white/5 mt-auto">
                                <span class="text-xs text-gray-500 font-medium flex items-center bg-white/5 px-2 py-1 rounded">
                                    <i class="far fa-calendar-alt mr-1.5 text-gray-400"></i> <?php echo date('M j, Y', strtotime($software['created_at'])); ?>
                                </span>
                                <div class="flex space-x-1.5">
                                    <a href="../software_details.php?id=<?php echo $software['id']; ?>" target="_blank" class="w-8 h-8 rounded-full bg-white/5 hover:bg-blue-500/20 hover:text-blue-400 text-gray-400 flex items-center justify-center transition-colors tooltip" title="View Public Page">
                                        <i class="fas fa-eye text-[13px]"></i>
                                    </a>
                                    <a href="software_edit.php?id=<?php echo $software['id']; ?>" class="w-8 h-8 rounded-full bg-white/5 hover:bg-yellow-500/20 hover:text-yellow-400 text-gray-400 flex items-center justify-center transition-colors tooltip" title="Edit Software">
                                        <i class="fas fa-edit text-[13px]"></i>
                                    </a>
                                    <a href="?delete=<?php echo $software['id']; ?>" onclick="return confirm('Are you sure you want to delete this software? All related files will be removed.');" class="w-8 h-8 rounded-full bg-white/5 hover:bg-red-500/20 hover:text-red-500 text-gray-400 flex items-center justify-center transition-colors tooltip" title="Delete Software">
                                        <i class="fas fa-trash text-[13px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
