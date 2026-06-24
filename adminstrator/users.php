<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'users';

// Handle search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get users (with or without search)
try {
    if (!empty($search)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ? ORDER BY created_at DESC");
        $searchTerm = "%{$search}%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users ORDER BY created_at DESC");
        $stmt->execute();
    }
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching users: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Hypecrews Admin</title>
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
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight">Manage Users</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="add_user.php" class="bg-primary/10 hover:bg-primary/20 text-primary font-bold py-2.5 px-5 rounded-full flex items-center transition-colors border border-primary/20 shadow-sm">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add User
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
                
                <!-- Search Form -->
                <div class="mb-8">
                    <form method="GET" class="flex max-w-2xl">
                        <input type="text" name="search" placeholder="Search by username, name, or email..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 px-5 py-3 glass-panel border border-white/60 rounded-l-full focus:outline-none focus:ring-2 focus:ring-primary/50 text-apple_text shadow-sm placeholder-gray-400 font-medium">
                        <button type="submit" class="bg-primary/10 hover:bg-primary/20 text-primary px-6 rounded-r-full border border-primary/20 border-l-0 shadow-sm transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="users.php" class="ml-3 glass-panel bg-gray-50/50 hover:bg-gray-100/50 px-5 rounded-full flex items-center text-apple_muted transition-colors shadow-sm font-medium">
                                <i class="fas fa-times mr-2"></i> Clear
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="glass-panel rounded-[2rem] p-8 shadow-sm flex flex-col">
                    <?php if (empty($users)): ?>
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-gray-100/50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner border border-white">
                            <i class="fas fa-users text-4xl text-gray-400"></i>
                        </div>
                        <p class="text-apple_muted text-lg font-medium">No users found.</p>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-apple_muted border-b border-black/5 text-sm uppercase tracking-wider">
                                    <th class="pb-4 font-semibold px-4">User</th>
                                    <th class="pb-4 font-semibold px-4">Contact</th>
                                    <th class="pb-4 font-semibold px-4">Company</th>
                                    <th class="pb-4 font-semibold px-4 text-right">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-black/5">
                                <?php foreach ($users as $user): ?>
                                <tr onclick="window.location='viewuserdata.php?id=<?php echo $user['id']; ?>'" class="hover:bg-white/30 transition-colors group cursor-pointer relative">
                                    <td class="py-5 px-4 rounded-l-2xl">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-primary mr-3 shadow-inner">
                                                <span class="font-bold text-sm"><?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?></span>
                                            </div>
                                            <div>
                                                <p class="font-bold text-apple_text group-hover:text-primary transition-colors"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                                <p class="text-xs text-primary/70 font-medium mt-0.5">@<?php echo htmlspecialchars($user['username']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-5 px-4">
                                        <p class="font-medium text-apple_text"><a href="mailto:<?php echo htmlspecialchars($user['email']); ?>" onclick="event.stopPropagation();" class="hover:underline text-primary/80"><?php echo htmlspecialchars($user['email']); ?></a></p>
                                        <p class="text-xs text-apple_muted font-medium mt-0.5"><?php echo htmlspecialchars($user['mobile_number']); ?></p>
                                    </td>
                                    <td class="py-5 px-4">
                                        <?php if ($user['company_name']): ?>
                                        <p class="font-semibold text-apple_text"><?php echo htmlspecialchars($user['company_name']); ?></p>
                                        <?php if ($user['company_website']): ?>
                                        <p class="text-xs mt-0.5"><a href="<?php echo htmlspecialchars($user['company_website']); ?>" target="_blank" onclick="event.stopPropagation();" class="text-primary hover:underline font-medium"><i class="fas fa-external-link-alt text-[10px] mr-1"></i>Website</a></p>
                                        <?php endif; ?>
                                        <?php else: ?>
                                        <span class="text-xs text-apple_muted italic font-medium bg-white/50 px-2 py-1 rounded-md border border-black/5">No company info</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-5 px-4 text-right rounded-r-2xl">
                                        <div class="flex items-center justify-end space-x-3">
                                            <span class="text-xs text-apple_muted font-medium bg-black/5 px-3 py-1.5 rounded-lg whitespace-nowrap">
                                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                            </span>
                                            <div class="w-8 h-8 rounded-full bg-white shadow-sm border border-gray-100 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all transform group-hover:scale-110">
                                                <i class="fas fa-chevron-right text-xs"></i>
                                            </div>
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