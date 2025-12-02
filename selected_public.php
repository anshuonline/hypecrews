<?php
require_once 'config/db.php';

// Fetch only selected submissions
try {
    $stmt = $pdo->prepare("SELECT full_name, age, music_type, experience, photo_path, youtube_link FROM audition_submissions WHERE selected = 1 ORDER BY id DESC");
    $stmt->execute();
    $selectedSubmissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selected Participants - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
        }
        
        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .video-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .video-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .candidate-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            border-color: #6366f1;
        }
        
        .status-badge {
            transition: all 0.3s ease;
        }
        
        .status-badge.selected {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Navigation -->
    <?php include 'components/nav.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8 pt-20">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold mb-4">Our Selected Talents</h1>
                <p class="text-gray-400 max-w-2xl mx-auto">Discover the exceptional musicians who have been chosen to join our program. These talented individuals have demonstrated outstanding skills and creativity in their auditions.</p>
            </div>
            
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
            
            <?php if (empty($selectedSubmissions)): ?>
            <div class="bg-light/50 backdrop-blur-sm rounded-xl p-12 text-center border border-gray-700">
                <i class="fas fa-star text-yellow-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">No Selected Participants Yet</h3>
                <p class="text-gray-400 max-w-md mx-auto">We're still reviewing auditions. Please check back later to see our selected talents.</p>
            </div>
            <?php else: ?>
            <!-- Selected Participants Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($selectedSubmissions as $submission): ?>
                <div class="candidate-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <?php if (!empty($submission['photo_path']) && file_exists($submission['photo_path'])): ?>
                                <img class="h-16 w-16 rounded-full object-cover" src="<?php echo htmlspecialchars($submission['photo_path']); ?>" alt="Profile">
                                <?php else: ?>
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-500"></i>
                                </div>
                                <?php endif; ?>
                                <div class="ml-4">
                                    <h3 class="text-lg font-bold"><?php echo htmlspecialchars($submission['full_name']); ?></h3>
                                    <p class="text-gray-400 text-sm"><?php echo htmlspecialchars($submission['age']); ?> years old</p>
                                </div>
                            </div>
                            <span class="status-badge selected text-white py-1 px-3 rounded-full text-xs font-semibold">
                                <i class="fas fa-star mr-1"></i> Selected
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-music text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo htmlspecialchars(strtoupper($submission['music_type'])); ?></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo htmlspecialchars($submission['experience']); ?> years experience</span>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <?php if (!empty($submission['youtube_link'])): ?>
                            <button onclick="openVideoModal('<?php echo htmlspecialchars($submission['youtube_link']); ?>')" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-700 hover:to-purple-700 text-white py-2 rounded-lg flex items-center justify-center">
                                <i class="fab fa-youtube mr-2"></i> Watch Performance
                            </button>
                            <?php else: ?>
                            <div class="text-center py-2 text-gray-500">
                                <i class="fas fa-video-slash mr-2"></i> No performance video
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="bg-dark rounded-xl p-6 max-w-4xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Audition Performance</h3>
                <button onclick="closeVideoModal()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="aspect-w-16 aspect-h-9">
                <iframe id="videoFrame" class="w-full h-96 rounded-lg" src="" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    
    <script>
        // Open video modal with YouTube embed
        function openVideoModal(youtubeLink) {
            const videoId = getYoutubeId(youtubeLink);
            if (videoId) {
                const embedUrl = `https://www.youtube.com/embed/${videoId}`;
                document.getElementById('videoFrame').src = embedUrl;
                document.getElementById('videoModal').classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
        
        // Close video modal
        function closeVideoModal() {
            document.getElementById('videoModal').classList.remove('active');
            document.getElementById('videoFrame').src = '';
            document.body.style.overflow = 'auto';
        }
        
        // Extract YouTube video ID from URL (handles regular videos, shorts, and embed URLs)
        function getYoutubeId(url) {
            // Handle different YouTube URL formats
            const regExp = /^(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([^#&?]+)/;
            const match = url.match(regExp);
            
            if (match && match[1]) {
                // For shorts URLs, the ID is after /shorts/
                if (url.includes('/shorts/')) {
                    const shortsMatch = url.match(/youtube\.com\/shorts\/([^?#&]+)/);
                    return shortsMatch ? shortsMatch[1] : null;
                }
                // For youtu.be URLs
                else if (url.includes('youtu.be')) {
                    const shortMatch = url.match(/youtu\.be\/([^?#&]+)/);
                    return shortMatch ? shortMatch[1] : null;
                }
                // For regular watch URLs
                else if (url.includes('watch?v=')) {
                    const watchMatch = url.match(/[?&]v=([^?#&]+)/);
                    return watchMatch ? watchMatch[1] : null;
                }
                // For embed URLs
                else if (url.includes('/embed/')) {
                    const embedMatch = url.match(/embed\/([^?#&]+)/);
                    return embedMatch ? embedMatch[1] : null;
                }
                return match[1];
            }
            
            return null;
        }
        
        // Close modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });
    </script>
</body>
</html>