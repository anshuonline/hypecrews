<?php
require_once 'auth.php';
require_once '../config/db.php';

// Set page title
$pageTitle = 'Candidate Details';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$candidateId = (int)$_GET['id'];
$adminId = $_SESSION['admin_id'];

// Fetch candidate details with admin information
try {
    $stmt = $pdo->prepare("SELECT a.*, 
                          adm.username as selected_by_admin, 
                          adm2.username as deselected_by_admin 
                          FROM audition_submissions a 
                          LEFT JOIN admins adm ON a.selected_by_admin_id = adm.id 
                          LEFT JOIN admins adm2 ON a.deselected_by_admin_id = adm2.id 
                          WHERE a.id = ?");
    $stmt->execute([$candidateId]);
    $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$candidate) {
        echo "<p>Candidate not found.</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p>Error fetching candidate details: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Details - Hypecrews Admin</title>
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
        
        .details-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
        }
        
        .profile-card {
            transition: all 0.3s ease;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }
        
        .info-card {
            transition: all 0.3s ease;
            border-left: 4px solid #6366f1;
        }
        
        .info-card:hover {
            border-left-color: #8b5cf6;
            transform: translateY(-3px);
        }
        
        .status-badge {
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge.selected {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        
        .status-badge.pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .video-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
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
        
        .action-btn {
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        /* Add pulse animation to selected badge */
        .status-badge.selected {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Navigation -->
    <?php include '../components/admin_nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold flex items-center">
                        <i class="fas fa-user-circle text-primary mr-3"></i> Candidate Details
                    </h1>
                    <p class="text-gray-400 mt-2">Detailed information about the candidate</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button onclick="window.location.href='index.php'" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                    </button>
                    <button onclick="window.location.href='selected.php'" class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                        <i class="fas fa-star mr-2"></i> Selected Candidates
                    </button>
                </div>
            </div>
            
            <div class="details-container rounded-2xl shadow-xl p-6 border border-gray-700">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Profile Section -->
                    <div class="lg:col-span-1">
                        <div class="profile-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden">
                            <div class="p-6">
                                <div class="flex flex-col items-center mb-6">
                                    <?php if (!empty($candidate['photo_path']) && file_exists('../' . $candidate['photo_path'])): ?>
                                        <div class="relative">
                                            <img class="w-32 h-32 rounded-full object-cover border-4 border-primary shadow-lg" src="../<?php echo htmlspecialchars($candidate['photo_path']); ?>" alt="Profile Photo">
                                            <div class="absolute bottom-0 right-0 bg-primary rounded-full p-2">
                                                <i class="fas fa-camera text-white"></i>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="relative">
                                            <div class="bg-gray-200 border-2 border-dashed rounded-full w-32 h-32 flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500 text-4xl"></i>
                                            </div>
                                            <div class="absolute bottom-0 right-0 bg-gray-500 rounded-full p-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <h2 class="text-2xl font-bold mt-4"><?php echo htmlspecialchars($candidate['full_name']); ?></h2>
                                    <p class="text-gray-400"><?php echo htmlspecialchars($candidate['age']); ?> years old</p>
                                </div>
                                
                                <!-- Status Badge -->
                                <div class="mb-6 text-center">
                                    <?php if (!empty($candidate['selected']) && $candidate['selected'] == 1): ?>
                                        <div class="status-badge selected text-white py-3 px-6 rounded-full inline-flex items-center mx-auto">
                                            <i class="fas fa-star mr-2"></i> Selected
                                        </div>
                                        <?php if (!empty($candidate['selected_by_admin'])): ?>
                                            <div class="mt-3 text-sm text-gray-400">
                                                <i class="fas fa-user-shield mr-2"></i> Selected by <?php echo htmlspecialchars($candidate['selected_by_admin']); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="status-badge pending text-white py-3 px-6 rounded-full inline-flex items-center mx-auto">
                                            <i class="fas fa-clock mr-2"></i> Pending
                                        </div>
                                        <?php if (!empty($candidate['deselected_by_admin'])): ?>
                                            <div class="mt-3 text-sm text-gray-400">
                                                <i class="fas fa-user-slash mr-2"></i> Deselected by <?php echo htmlspecialchars($candidate['deselected_by_admin']); ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Candidate ID -->
                                <div class="mb-6 bg-dark/30 rounded-lg p-3 text-center">
                                    <p class="text-gray-400 text-sm">Candidate ID</p>
                                    <p class="font-mono font-bold">#<?php echo htmlspecialchars($candidate['id']); ?></p>
                                </div>
                                
                                <!-- Contact Info -->
                                <div class="space-y-4">
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg hover:bg-dark/50 transition-all duration-300">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-envelope text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Email</p>
                                            <p class="font-medium"><?php echo htmlspecialchars($candidate['email']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg hover:bg-dark/50 transition-all duration-300">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-phone text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Phone</p>
                                            <p class="font-medium"><?php echo htmlspecialchars($candidate['phone']); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg hover:bg-dark/50 transition-all duration-300">
                                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-calendar text-primary"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Submission Date</p>
                                            <p class="font-medium"><?php echo date('M j, Y \a\t g:i A', strtotime($candidate['submission_date'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Details Section -->
                    <div class="lg:col-span-2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <!-- Music Information Card -->
                            <div class="info-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                                <h3 class="text-xl font-bold mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                        <i class="fas fa-music text-primary"></i>
                                    </div>
                                    Music Information
                                </h3>
                                <div class="space-y-4">
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-purple-500/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-tags text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Music Type</p>
                                            <p class="font-medium"><?php echo htmlspecialchars(strtoupper($candidate['music_type'])); ?></p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-purple-500/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-chart-line text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Experience</p>
                                            <p class="font-medium"><?php echo htmlspecialchars($candidate['experience']); ?> years</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center p-3 bg-dark/30 rounded-lg">
                                        <div class="w-8 h-8 rounded-full bg-purple-500/10 flex items-center justify-center mr-3">
                                            <i class="fas fa-guitar text-purple-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-400 text-sm">Instruments</p>
                                            <p class="font-medium"><?php echo !empty($candidate['instruments']) ? htmlspecialchars($candidate['instruments']) : 'Not specified'; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Address Card -->
                            <div class="info-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6 hover:shadow-lg transition-all duration-300">
                                <h3 class="text-xl font-bold mb-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center mr-3">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    Address
                                </h3>
                                <div class="p-3 bg-dark/30 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-home text-blue-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-gray-300"><?php echo nl2br(htmlspecialchars($candidate['address'])); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Video Section -->
                        <?php if (!empty($candidate['youtube_link'])): ?>
                        <div class="info-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
                            <h3 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fab fa-youtube text-red-500 mr-3"></i> Audition Video
                            </h3>
                            <div class="bg-dark/50 rounded-lg p-4">
                                <button onclick="openVideoModal('<?php echo htmlspecialchars($candidate['youtube_link']); ?>')" class="w-full flex items-center justify-center bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-3 px-4 rounded-lg transition-all duration-300">
                                    <i class="fas fa-play mr-2"></i> Watch Audition Video
                                </button>
                                <p class="mt-3 text-sm text-gray-400 text-center">Click to view <?php echo htmlspecialchars($candidate['full_name']); ?>'s audition video</p>
                            </div>
                            <div class="mt-4">
                                <p class="text-gray-400 text-sm">Video Link:</p>
                                <p class="text-primary text-sm truncate"><?php echo htmlspecialchars($candidate['youtube_link']); ?></p>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="info-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 p-6">
                            <h3 class="text-xl font-bold mb-4 flex items-center">
                                <i class="fab fa-youtube text-red-500 mr-3"></i> Audition Video
                            </h3>
                            <div class="bg-dark/50 rounded-lg p-4 text-center">
                                <p class="text-gray-400">No YouTube video provided by this candidate.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Video Modal -->
    <div class="video-modal" id="videoModal">
        <div class="bg-dark rounded-xl p-6 max-w-4xl w-full mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Audition Video</h3>
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