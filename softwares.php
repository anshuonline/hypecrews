<?php
session_start();
$pageTitle = "Softwares - Hypecrews";
require_once 'config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);

try {
    $stmt = $pdo->prepare("SELECT * FROM softwares ORDER BY created_at DESC");
    $stmt->execute();
    $softwares = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching softwares.";
    $softwares = [];
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#6366f1', secondary: '#8b5cf6', dark: '#0f172a', light: '#1e293b' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <style>
        body { background-color: #0f172a; color: #fff; }
        .glass-card { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        .software-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -15px rgba(99, 102, 241, 0.3); border-color: rgba(99, 102, 241, 0.3); }
    </style>
    <?php include 'components/google_analytics.php'; ?>
</head>
<body class="antialiased selection:bg-primary selection:text-white flex flex-col min-h-screen relative">
    
    <!-- Background Effects -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-[120px] mix-blend-screen"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-secondary/20 rounded-full blur-[120px] mix-blend-screen"></div>
    </div>

    <!-- Navigation -->
    <div class="relative z-50">
        <?php include 'components/nav.php'; ?>
    </div>

    <!-- Main Content -->
    <main class="flex-grow relative z-10 pt-32 pb-24">
        <div class="container mx-auto px-4">
            
            <!-- Header -->
            <div class="text-center max-w-3xl mx-auto mb-16" data-aos="fade-up">
                <span class="text-primary font-semibold tracking-wider uppercase text-sm mb-4 block">Our Products</span>
                <h1 class="text-4xl md:text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white via-blue-100 to-white">
                    Softwares & Applications
                </h1>
                <p class="text-gray-400 text-lg">
                    Explore our collection of powerful, custom-built softwares designed to simplify workflows and boost productivity.
                </p>
            </div>

            <!-- Software Grid -->
            <?php if (empty($softwares)): ?>
                <div class="text-center py-20 glass-card rounded-2xl border border-white/10" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-laptop-code text-3xl text-gray-500"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2">No Softwares Yet</h3>
                    <p class="text-gray-400">We are working on some amazing products. Check back later!</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($softwares as $index => $app): ?>
                        <div class="glass-card rounded-2xl overflow-hidden software-card transition-all duration-300 flex flex-col h-full" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                            
                            <!-- Banner Image -->
                            <div class="relative h-48 w-full bg-gray-800 overflow-hidden group">
                                <?php if (!empty($app['banner_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($app['banner_path']); ?>" alt="<?php echo htmlspecialchars($app['name']); ?> Banner" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-800 to-gray-900">
                                        <i class="fas fa-laptop-code text-4xl text-gray-700"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute inset-0 bg-gradient-to-t from-dark to-transparent opacity-80"></div>
                            </div>

                            <!-- Content -->
                            <div class="p-6 flex-grow flex flex-col relative -mt-12">
                                <!-- Logo & Title Row -->
                                <div class="flex items-end gap-4 mb-4">
                                    <div class="w-20 h-20 rounded-2xl bg-dark border-4 border-dark overflow-hidden flex-shrink-0 shadow-xl shadow-black/50 z-10">
                                        <?php if (!empty($app['logo_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($app['logo_path']); ?>" alt="Logo" class="w-full h-full object-cover bg-white">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-500">
                                                <i class="fas fa-image text-2xl"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="pb-1">
                                        <h3 class="text-xl font-bold text-white line-clamp-1" title="<?php echo htmlspecialchars($app['name']); ?>"><?php echo htmlspecialchars($app['name']); ?></h3>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs font-semibold bg-primary/20 text-primary px-2 py-0.5 rounded-md border border-primary/20">v<?php echo htmlspecialchars($app['version']); ?></span>
                                            
                                            <!-- Platform Icons -->
                                            <?php 
                                            $plats = explode(', ', $app['platform']);
                                            if(in_array('Windows', $plats)) echo '<i class="fab fa-windows text-gray-400 text-sm" title="Windows"></i>';
                                            if(in_array('Mac/Apple', $plats)) echo '<i class="fab fa-apple text-gray-400 text-sm" title="Mac/Apple"></i>';
                                            if(in_array('Android', $plats)) echo '<i class="fab fa-android text-gray-400 text-sm" title="Android"></i>';
                                            if(in_array('Web', $plats)) echo '<i class="fas fa-globe text-gray-400 text-sm" title="Web"></i>';
                                            ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <p class="text-gray-400 text-sm line-clamp-3 mb-6 flex-grow">
                                    <?php echo htmlspecialchars($app['description']); ?>
                                </p>

                                <!-- Action Buttons -->
                                <div class="mt-auto">
                                    <a href="software_details.php?id=<?php echo $app['id']; ?>" class="w-full block text-center bg-white/5 hover:bg-white/10 border border-white/10 text-white font-medium py-3 rounded-xl transition-colors duration-300">
                                        View Details <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <div class="relative z-10">
        <?php include 'components/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    </script>
</body>
</html>

