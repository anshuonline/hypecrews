<?php
require_once 'auth.php';
require_once '../config/db.php';
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
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-800">
                                    <th class="pb-3 w-16">Icon</th>
                                    <th class="pb-3">Name / Version</th>
                                    <th class="pb-3">Platforms</th>
                                    <th class="pb-3">Pricing</th>
                                    <th class="pb-3">Added On</th>
                                    <th class="pb-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($softwares as $software): ?>
                                <tr class="border-b border-gray-800 hover:bg-dark/50">
                                    <td class="py-4">
                                        <?php if (!empty($software['logo_path'])): ?>
                                            <img src="../<?php echo htmlspecialchars($software['logo_path']); ?>" alt="Logo" class="w-12 h-12 object-cover rounded-lg bg-dark">
                                        <?php else: ?>
                                            <div class="w-12 h-12 rounded-lg bg-dark flex items-center justify-center text-gray-500">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4">
                                        <p class="font-medium text-lg"><?php echo htmlspecialchars($software['name']); ?></p>
                                        <p class="text-sm text-gray-400">v<?php echo htmlspecialchars($software['version']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <p><?php echo htmlspecialchars($software['platform']); ?></p>
                                    </td>
                                    <td class="py-4">
                                        <?php if ($software['is_paid']): ?>
                                            <span class="bg-purple-900/50 text-purple-300 px-3 py-1 rounded-full text-xs font-medium border border-purple-800">Paid - ₹<?php echo number_format($software['price'], 2); ?></span>
                                        <?php else: ?>
                                            <span class="bg-green-900/50 text-green-300 px-3 py-1 rounded-full text-xs font-medium border border-green-800">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 text-gray-400">
                                        <?php echo date('M j, Y', strtotime($software['created_at'])); ?>
                                    </td>
                                    <td class="py-4 text-right space-x-2">
                                        <a href="../software_details.php?id=<?php echo $software['id']; ?>" target="_blank" class="text-blue-400 hover:text-blue-300" title="View Public Page">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="software_edit.php?id=<?php echo $software['id']; ?>" class="text-yellow-400 hover:text-yellow-300" title="Edit Software">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="?delete=<?php echo $software['id']; ?>" onclick="return confirm('Are you sure you want to delete this software? All related files will be removed.');" class="text-red-400 hover:text-red-300" title="Delete Software">
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
