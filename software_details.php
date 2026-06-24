<?php
session_start();
require_once 'config/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: softwares.php");
    exit;
}

$id = $_GET['id'];

// Fetch software details
$stmt = $pdo->prepare("SELECT * FROM softwares WHERE id = ?");
$stmt->execute([$id]);
$software = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$software) {
    header("Location: softwares.php");
    exit;
}

$pageTitle = $software['name'] . " - Hypecrews Softwares";
$isLoggedIn = isset($_SESSION['user_id']);

// Fetch screenshots
$stmt = $pdo->prepare("SELECT * FROM software_screenshots WHERE software_id = ? ORDER BY display_order ASC, id ASC");
$stmt->execute([$id]);
$screenshots = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <?php if (!empty($software['keywords'])): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($software['keywords']); ?>">
    <meta name="description" content="<?php echo htmlspecialchars(substr($software['description'], 0, 150)); ?>...">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#6366f1', secondary: '#8b5cf6', dark: '#0f172a', light: '#1e293b' }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>
        body { background-color: #0f172a; color: #fff; }
        .glass-panel { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.05); }
        /* Hide scrollbar for screenshot carousel */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
    <?php include 'components/google_analytics.php'; ?>
</head>
<body class="antialiased selection:bg-primary selection:text-white flex flex-col min-h-screen relative">
    
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-primary/20 rounded-full blur-[120px] mix-blend-screen"></div>
    </div>

    <div class="relative z-50">
        <?php include 'components/nav.php'; ?>
    </div>

    <!-- Main Content -->
    <main class="flex-grow relative z-10 -mt-16">
        <!-- Banner Background -->
        <div class="w-full h-[30vh] md:h-[45vh] relative bg-gray-900" data-aos="fade-in">
            <?php if (!empty($software['banner_path'])): ?>
                <img src="<?php echo htmlspecialchars($software['banner_path']); ?>" alt="Banner" class="w-full h-full object-cover opacity-70">
            <?php else: ?>
                <div class="w-full h-full bg-gradient-to-br from-indigo-900 to-gray-900"></div>
            <?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-transparent to-transparent"></div>
        </div>

        <!-- Profile Header (Overlaps Banner) -->
        <div class="container mx-auto px-4 relative z-20 -mt-16 md:-mt-24 mb-10">
            <div class="flex flex-col md:flex-row md:items-end gap-5 md:gap-8">
                
                <!-- Logo -->
                <div class="w-28 h-28 md:w-40 md:h-40 rounded-[2rem] bg-dark border-4 border-[#0f172a] overflow-hidden shadow-2xl flex-shrink-0 z-10" data-aos="fade-up">
                    <?php if (!empty($software['logo_path'])): ?>
                        <img src="<?php echo htmlspecialchars($software['logo_path']); ?>" alt="Logo" class="w-full h-full object-cover bg-white">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-500">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Title & Meta -->
                <div class="flex-grow w-full md:pb-2" data-aos="fade-up" data-aos-delay="100">
                    <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-3 tracking-tight"><?php echo htmlspecialchars($software['name']); ?></h1>
                    
                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <span class="bg-primary/20 text-primary px-3 py-1 rounded-full font-semibold border border-primary/20">v<?php echo htmlspecialchars($software['version']); ?></span>
                        <span class="text-gray-400 bg-white/5 px-3 py-1 rounded-full"><i class="far fa-calendar-alt mr-1"></i> <?php echo date('M Y', strtotime($software['created_at'])); ?></span>
                        <span class="text-gray-400 bg-white/5 px-3 py-1 rounded-full"><i class="fas fa-desktop mr-1"></i> <?php echo htmlspecialchars($software['platform']); ?></span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="w-full md:w-auto mt-6 md:mt-0 md:pb-2 flex-shrink-0" data-aos="fade-up" data-aos-delay="200">
                    <?php if ($software['file_type'] == 'upload' && !empty($software['file_path'])): ?>
                        <a href="<?php echo htmlspecialchars($software['file_path']); ?>" download class="flex items-center justify-center bg-primary hover:bg-indigo-600 text-white font-bold py-3.5 px-8 rounded-xl shadow-[0_0_20px_rgba(99,102,241,0.4)] transition-all transform hover:-translate-y-1 w-full md:w-auto text-lg" onclick="startDownload(event, this.href)">
                            <i class="fas fa-download mr-3"></i> Download App
                        </a>
                    <?php elseif ($software['file_type'] == 'google_drive' && !empty($software['file_path'])): ?>
                        <!-- Google Drive Proxy Download -->
                        <a href="download.php?id=<?php echo $software['id']; ?>" class="flex items-center justify-center bg-primary hover:bg-indigo-600 text-white font-bold py-3.5 px-8 rounded-xl shadow-[0_0_20px_rgba(99,102,241,0.4)] transition-all transform hover:-translate-y-1 w-full md:w-auto text-lg" onclick="startDownload(event, this.href)">
                            <i class="fas fa-cloud-download-alt mr-3"></i> Download App
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Main Content Column -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Screenshots Carousel -->
                    <?php if (!empty($screenshots)): ?>
                    <div class="glass-panel rounded-3xl p-6" data-aos="fade-up">
                        <h3 class="text-xl font-bold mb-5 flex items-center"><i class="far fa-image mr-3 text-primary"></i> Screenshots</h3>
                        <div class="flex overflow-x-auto gap-4 pb-4 no-scrollbar snap-x">
                            <?php foreach ($screenshots as $ss): ?>
                                <!-- Responsive width: 75% on mobile, smaller on md/lg -->
                                <div class="snap-center shrink-0 w-[75%] sm:w-[45%] md:w-[35%] lg:w-[30%] rounded-2xl overflow-hidden border border-white/10 relative group cursor-pointer shadow-lg" onclick="openLightbox('<?php echo htmlspecialchars($ss['image_path']); ?>')">
                                    <img src="<?php echo htmlspecialchars($ss['image_path']); ?>" alt="Screenshot" class="w-full h-auto object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <i class="fas fa-expand text-white text-3xl"></i>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="glass-panel rounded-2xl p-6 md:p-8" data-aos="fade-up">
                        <h3 class="text-xl font-bold mb-6 flex items-center"><i class="fas fa-align-left mr-3 text-primary"></i> About this Software</h3>
                        <div class="text-gray-300 leading-relaxed mb-6 space-y-4">
                            <?php 
                                $desc = htmlspecialchars($software['description']);
                                // Remove weird leading spaces/tabs from each line
                                $desc = preg_replace('/^[ \t]+/m', '', $desc);
                                // Split by double newlines to create distinct paragraphs
                                $paragraphs = preg_split('/\n\s*\n/', trim($desc));
                                foreach ($paragraphs as $p) {
                                    echo '<p class="whitespace-pre-line">' . $p . '</p>';
                                }
                            ?>
                        </div>
                        
                        <!-- Tags / Keywords -->
                        <?php if (!empty($software['keywords'])): ?>
                        <div class="mt-8 pt-6 border-t border-white/5">
                            <h4 class="text-sm font-semibold text-gray-400 mb-3 uppercase tracking-wider">Tags & Keywords</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php 
                                $tags = explode(',', $software['keywords']);
                                foreach($tags as $tag): 
                                    $tag = trim($tag);
                                    if(empty($tag)) continue;
                                ?>
                                <span class="bg-white/5 border border-white/10 text-gray-300 px-3 py-1 rounded-full text-xs hover:bg-primary/20 hover:border-primary/50 hover:text-white transition-colors cursor-default">
                                    #<?php echo htmlspecialchars($tag); ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sidebar Column -->
                <div class="space-y-6">
                    
                    <!-- App Store Links -->
                    <?php if (!empty($software['playstore_link']) || !empty($software['appstore_link']) || !empty($software['windows_store_link'])): ?>
                    <div class="glass-panel rounded-2xl p-6" data-aos="fade-up" data-aos-delay="100">
                        <h3 class="text-lg font-bold mb-4">Available on Stores</h3>
                        <div class="space-y-3">
                            <?php if (!empty($software['playstore_link'])): ?>
                                <a href="<?php echo htmlspecialchars($software['playstore_link']); ?>" target="_blank" class="flex items-center justify-between bg-[#1f2937] hover:bg-[#374151] p-3 rounded-xl border border-white/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <i class="fab fa-google-play text-green-400 text-xl w-6"></i>
                                        <span class="font-medium">Google Play</span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-gray-500 text-sm"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!empty($software['appstore_link'])): ?>
                                <a href="<?php echo htmlspecialchars($software['appstore_link']); ?>" target="_blank" class="flex items-center justify-between bg-[#1f2937] hover:bg-[#374151] p-3 rounded-xl border border-white/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <i class="fab fa-apple text-gray-200 text-xl w-6"></i>
                                        <span class="font-medium">App Store</span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-gray-500 text-sm"></i>
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($software['windows_store_link'])): ?>
                                <a href="<?php echo htmlspecialchars($software['windows_store_link']); ?>" target="_blank" class="flex items-center justify-between bg-[#1f2937] hover:bg-[#374151] p-3 rounded-xl border border-white/5 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <i class="fab fa-windows text-blue-400 text-xl w-6"></i>
                                        <span class="font-medium">Windows Store</span>
                                    </div>
                                    <i class="fas fa-external-link-alt text-gray-500 text-sm"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Info Card -->
                    <div class="glass-panel rounded-2xl p-6" data-aos="fade-up" data-aos-delay="200">
                        <h3 class="text-lg font-bold mb-4">Technical Details</h3>
                        <ul class="space-y-4 text-sm">
                            <li class="flex justify-between border-b border-white/5 pb-2">
                                <span class="text-gray-400">Version</span>
                                <span class="font-semibold"><?php echo htmlspecialchars($software['version']); ?></span>
                            </li>
                            <li class="flex justify-between border-b border-white/5 pb-2">
                                <span class="text-gray-400">Platforms</span>
                                <span class="font-semibold text-right"><?php echo htmlspecialchars($software['platform']); ?></span>
                            </li>
                            <li class="flex justify-between border-b border-white/5 pb-2">
                                <span class="text-gray-400">Last Updated</span>
                                <span class="font-semibold"><?php echo date('M d, Y', strtotime($software['updated_at'] ?? $software['created_at'])); ?></span>
                            </li>
                            <li class="flex justify-between pb-2">
                                <span class="text-gray-400">Developer</span>
                                <span class="font-semibold text-primary">Hypecrews</span>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 z-[100] bg-black/95 hidden items-center justify-center backdrop-blur-md opacity-0 transition-opacity duration-300" onclick="closeLightbox()">
        <span class="absolute top-6 right-6 text-white text-4xl cursor-pointer hover:text-red-500 transition-colors">&times;</span>
        <img id="lightbox-img" class="max-w-[90%] max-h-[90vh] rounded-lg shadow-2xl" src="">
    </div>

    <!-- Download Loading Modal -->
    <div id="download-modal" class="fixed inset-0 z-[110] bg-black/90 hidden items-center justify-center backdrop-blur-sm opacity-0 transition-opacity duration-300">
        <div class="bg-gray-900 border border-white/10 rounded-2xl p-8 max-w-sm w-full text-center shadow-2xl transform scale-95 transition-transform duration-300" id="download-modal-content">
            <!-- Loading State -->
            <div id="download-loading">
                <div class="inline-block relative w-16 h-16 mb-6">
                    <div class="absolute inset-0 rounded-full border-4 border-white/10"></div>
                    <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
                    <i class="fas fa-download absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Preparing Download</h3>
                <p class="text-gray-400 text-sm">Please wait while we fetch your file...</p>
            </div>
            
            <!-- Success State -->
            <div id="download-success" class="hidden">
                <div class="w-16 h-16 bg-green-500/20 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Download Started!</h3>
                <p class="text-gray-400 text-sm mb-6">Your download should begin automatically.</p>
                <button onclick="closeDownloadModal()" class="bg-white/10 hover:bg-white/20 text-white px-6 py-2 rounded-lg font-medium transition-colors w-full">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openLightbox(src) {
            const modal = document.getElementById('lightbox');
            const img = document.getElementById('lightbox-img');
            img.src = src;
            modal.style.display = 'flex';
            setTimeout(() => modal.classList.remove('opacity-0'), 10);
        }
        function closeLightbox() {
            const modal = document.getElementById('lightbox');
            modal.classList.add('opacity-0');
            setTimeout(() => modal.style.display = 'none', 300);
        }

        // Download Handler
        function startDownload(e, url) {
            e.preventDefault(); // Prevent immediate download
            
            const modal = document.getElementById('download-modal');
            const loadingState = document.getElementById('download-loading');
            const successState = document.getElementById('download-success');
            const modalContent = document.getElementById('download-modal-content');
            
            // Reset modal state
            loadingState.style.display = 'block';
            successState.style.display = 'none';
            
            // Show modal
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
            
            // Simulate preparation time (2 seconds)
            setTimeout(() => {
                // Trigger actual download (creates an invisible iframe or direct redirect)
                window.location.href = url;
                
                // Show success state
                loadingState.style.display = 'none';
                successState.style.display = 'block';
            }, 2000);
        }

        function closeDownloadModal() {
            const modal = document.getElementById('download-modal');
            const modalContent = document.getElementById('download-modal-content');
            
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    </script>

    <!-- Footer -->
    <div class="relative z-10">
        <?php include 'components/footer.php'; ?>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true, offset: 50 });
    </script>
</body>
</html>

