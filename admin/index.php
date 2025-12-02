<?php
require_once 'auth.php';
require_once '../config/db.php';

// Set page title
$pageTitle = 'Admin Dashboard';

// Handle marking candidate as selected
if (isset($_POST['mark_selected']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $adminId = $_SESSION['admin_id'];
    try {
        // First, get the candidate details before updating
        $stmt = $pdo->prepare("SELECT full_name, email FROM audition_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Update the candidate status
        $stmt = $pdo->prepare("UPDATE audition_submissions SET selected = 1, selected_by_admin_id = ?, deselected_by_admin_id = NULL WHERE id = ?");
        $stmt->execute([$adminId, $id]);
        $success = "Candidate marked as selected!";
        
        // Store candidate info for email notification
        $_SESSION['selected_candidate_name'] = $candidate['full_name'];
        $_SESSION['selected_candidate_email'] = $candidate['email'];
        $_SESSION['send_selection_email'] = true;
        
        // Debug info
        $_SESSION['debug_email_info'] = [
            'candidate_name' => $candidate['full_name'],
            'candidate_email' => $candidate['email'],
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => 'selection'
        ];
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle deselecting candidate
if (isset($_POST['deselect_candidate']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $adminId = $_SESSION['admin_id'];
    try {
        $stmt = $pdo->prepare("UPDATE audition_submissions SET selected = 0, selected_by_admin_id = NULL, deselected_by_admin_id = ? WHERE id = ?");
        $stmt->execute([$adminId, $id]);
        $success = "Candidate deselected!";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get admin info for 2FA status
$adminId = $_SESSION['admin_id'];
try {
    $stmt = $pdo->prepare("SELECT google_auth_enabled FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    $is2FAEnabled = $admin ? $admin['google_auth_enabled'] : 0;
} catch (PDOException $e) {
    $is2FAEnabled = 0;
}

// Get filter values
$filterDate = isset($_GET['date']) ? $_GET['date'] : '';
$filterMusicType = isset($_GET['music_type']) ? $_GET['music_type'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
$filterSearch = isset($_GET['search']) ? $_GET['search'] : '';
$filterId = isset($_GET['id']) ? $_GET['id'] : '';

// Build the query with filters
$sql = "SELECT a.*, adm.username as selected_by_admin, adm2.username as deselected_by_admin FROM audition_submissions a 
        LEFT JOIN admins adm ON a.selected_by_admin_id = adm.id 
        LEFT JOIN admins adm2 ON a.deselected_by_admin_id = adm2.id";

$conditions = [];
$params = [];

// Add ID filter
if (!empty($filterId)) {
    $conditions[] = "a.id = ?";
    $params[] = $filterId;
}

// Add date filter
if (!empty($filterDate)) {
    $conditions[] = "DATE(a.submission_date) = ?";
    $params[] = $filterDate;
}

// Add music type filter
if (!empty($filterMusicType)) {
    $conditions[] = "a.music_type = ?";
    $params[] = $filterMusicType;
}

// Add status filter
if (!empty($filterStatus)) {
    if ($filterStatus == 'selected') {
        $conditions[] = "a.selected = 1";
    } elseif ($filterStatus == 'pending') {
        $conditions[] = "a.selected = 0";
    }
}

// Add search filter
if (!empty($filterSearch)) {
    $conditions[] = "(a.full_name LIKE ? OR a.email LIKE ? OR a.phone LIKE ?)";
    $params[] = "%$filterSearch%";
    $params[] = "%$filterSearch%";
    $params[] = "%$filterSearch%";
}

// Add conditions to query
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY a.id DESC";

// Fetch all submissions with admin information and filters
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Fetch distinct music types for filter dropdown
try {
    $stmt = $pdo->prepare("SELECT DISTINCT music_type FROM audition_submissions ORDER BY music_type");
    $stmt->execute();
    $musicTypes = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $musicTypes = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hypecrews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- EmailJS Integration -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
        window.addEventListener('load', function() {
            emailjs.init("oPmjy2TPAxXRfhT-P"); // Public key
        });
    </script>
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
        
        .dashboard-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 1rem;
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
        
        .status-badge.pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
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
        
        .action-btn {
            transition: all 0.3s ease;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
        }
        
        .filter-section {
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Navigation -->
    <?php include '../components/admin_nav.php'; ?>
    
    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold">Audition Submissions</h1>
                    <p class="text-gray-400 mt-2">Manage all audition submissions in one place</p>
                </div>
                <div class="flex space-x-4">
                    <a href="selected.php" class="bg-gradient-to-r from-green-600 to-emerald-700 hover:from-green-700 hover:to-emerald-800 text-white px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-star mr-2"></i> Selected Candidates
                    </a>
                </div>
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
            
            <?php if (isset($_SESSION['debug_email_info'])): ?>
            <div class="mb-6 p-4 rounded-lg bg-blue-900/50 border border-blue-700 backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-500 text-xl mr-3"></i>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Email Debug Information</h3>
                        <p class="text-blue-300">Action: <?php echo htmlspecialchars($_SESSION['debug_email_info']['action']); ?></p>
                        <p class="text-blue-300">Candidate: <?php echo htmlspecialchars($_SESSION['debug_email_info']['candidate_name']); ?></p>
                        <p class="text-blue-300">Email: <?php echo htmlspecialchars($_SESSION['debug_email_info']['candidate_email']); ?></p>
                        <p class="text-blue-300">Time: <?php echo htmlspecialchars($_SESSION['debug_email_info']['timestamp']); ?></p>
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
            
            <!-- Filter Section -->
            <div class="filter-section bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700 mb-8">
                <h2 class="text-xl font-bold mb-4 flex items-center">
                    <i class="fas fa-filter mr-2"></i> Filter Submissions
                </h2>
                <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Participant ID</label>
                        <input type="number" name="id" placeholder="Enter ID..." value="<?php echo htmlspecialchars($filterId); ?>" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Submission Date</label>
                        <input type="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Music Type</label>
                        <select name="music_type" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All Types</option>
                            <?php foreach ($musicTypes as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($filterMusicType == $type) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars(ucfirst($type)); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Status</label>
                        <select name="status" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">All Status</option>
                            <option value="pending" <?php echo ($filterStatus == 'pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="selected" <?php echo ($filterStatus == 'selected') ? 'selected' : ''; ?>>Selected</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" placeholder="Name, Email, Phone..." value="<?php echo htmlspecialchars($filterSearch); ?>" class="w-full bg-dark border border-gray-600 rounded-lg px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i> Apply Filters
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($filterId) || !empty($filterDate) || !empty($filterMusicType) || !empty($filterStatus) || !empty($filterSearch)): ?>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <a href="index.php" class="text-sm text-primary hover:text-indigo-300 flex items-center">
                        <i class="fas fa-times-circle mr-1"></i> Clear All Filters
                    </a>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['debug_email'])): ?>
                <div class="mt-4 pt-4 border-t border-gray-700">
                    <button id="testEmailBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        Test Email Functionality
                    </button>
                </div>
                <script>
                document.getElementById('testEmailBtn').addEventListener('click', function() {
                    // Create debug element
                    var debugDiv = document.createElement('div');
                    debugDiv.id = 'email-debug-info';
                    debugDiv.style.position = 'fixed';
                    debugDiv.style.top = '10px';
                    debugDiv.style.right = '10px';
                    debugDiv.style.backgroundColor = 'rgba(0,0,0,0.8)';
                    debugDiv.style.color = 'white';
                    debugDiv.style.padding = '15px';
                    debugDiv.style.borderRadius = '5px';
                    debugDiv.style.zIndex = '9999';
                    debugDiv.style.maxWidth = '300px';
                    debugDiv.innerHTML = '<h3 style="margin: 0 0 10px 0; color: #6366f1;">Email Debug Info</h3>';
                    document.body.appendChild(debugDiv);
                    
                    // Add debug info
                    var debugInfo = {
                        to_name: 'Test User',
                        to_email: 'test@example.com',
                        candidate_name: 'Test User',
                        message: 'This is a test email from Hypecrews.',
                        timestamp: new Date().toISOString()
                    };
                    
                    debugDiv.innerHTML += '<p><strong>Sending test email to:</strong> ' + debugInfo.to_email + '</p>';
                    debugDiv.innerHTML += '<p><strong>Name:</strong> ' + debugInfo.to_name + '</p>';
                    debugDiv.innerHTML += '<p><strong>Status:</strong> <span id="email-status">Sending...</span></p>';
                    
                    // Send email using EmailJS
                    emailjs.send('service_jvpy9a4', 'template_qhsqlyk', debugInfo)
                        .then(function(response) {
                            console.log('SUCCESS!', response.status, response.text);
                            document.getElementById('email-status').innerHTML = '<span style="color: green;">Sent Successfully</span>';
                            debugDiv.innerHTML += '<p><strong>Response:</strong> ' + response.status + '</p>';
                        }, function(error) {
                            console.log('FAILED...', error);
                            document.getElementById('email-status').innerHTML = '<span style="color: red;">Failed</span>';
                            debugDiv.innerHTML += '<p><strong>Error:</strong> <span style="color: red;">' + JSON.stringify(error) + '</span></p>';
                        });
                    
                    // Auto-remove debug info after 10 seconds
                    setTimeout(function() {
                        if (document.getElementById('email-debug-info')) {
                            document.getElementById('email-debug-info').style.opacity = '0.5';
                        }
                    }, 10000);
                });
                </script>
                <?php endif; ?>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-blue-500/20 mr-4">
                            <i class="fas fa-users text-blue-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400">Total Submissions</p>
                            <p class="text-2xl font-bold"><?php echo count($submissions); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-green-500/20 mr-4">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400">Selected Candidates</p>
                            <p class="text-2xl font-bold">
                                <?php 
                                $selectedCount = 0;
                                foreach ($submissions as $submission) {
                                    if (!empty($submission['selected']) && $submission['selected'] == 1) {
                                        $selectedCount++;
                                    }
                                }
                                echo $selectedCount;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-light/50 backdrop-blur-sm rounded-xl p-6 border border-gray-700">
                    <div class="flex items-center">
                        <div class="p-3 rounded-lg bg-purple-500/20 mr-4">
                            <i class="fas fa-video text-purple-500 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-gray-400">With Videos</p>
                            <p class="text-2xl font-bold">
                                <?php 
                                $videoCount = 0;
                                foreach ($submissions as $submission) {
                                    if (!empty($submission['youtube_link'])) {
                                        $videoCount++;
                                    }
                                }
                                echo $videoCount;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Candidates Grid -->
            <?php if (empty($submissions)): ?>
            <div class="bg-light/50 backdrop-blur-sm rounded-xl p-12 text-center border border-gray-700">
                <i class="fas fa-users text-gray-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">No Submissions Found</h3>
                <p class="text-gray-400 max-w-md mx-auto">
                    <?php if (!empty($filterDate) || !empty($filterMusicType) || !empty($filterStatus) || !empty($filterSearch)): ?>
                        No submissions match your filter criteria. <a href="index.php" class="text-primary hover:underline">Clear filters</a> to see all submissions.
                    <?php else: ?>
                        There are currently no audition submissions. Candidates will appear here once they submit their auditions.
                    <?php endif; ?>
                </p>
            </div>
            <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($submissions as $submission): ?>
                <div class="candidate-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300" onclick="viewDetails(<?php echo $submission['id']; ?>)">
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center">
                                <?php if (!empty($submission['photo_path']) && file_exists('../' . $submission['photo_path'])): ?>
                                <img class="h-16 w-16 rounded-full object-cover" src="../<?php echo htmlspecialchars($submission['photo_path']); ?>" alt="Profile">
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
                            <?php if (!empty($submission['selected']) && $submission['selected'] == 1): ?>
                            <span class="status-badge selected text-white py-1 px-3 rounded-full text-xs font-semibold">
                                <i class="fas fa-star mr-1"></i> Selected
                            </span>
                            <?php else: ?>
                            <span class="status-badge pending text-white py-1 px-3 rounded-full text-xs font-semibold">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (!empty($submission['selected']) && $submission['selected'] == 1 && !empty($submission['selected_by_admin'])): ?>
                        <div class="text-xs text-gray-500 mb-2">
                            <i class="fas fa-user-shield mr-1"></i> Selected by <?php echo htmlspecialchars($submission['selected_by_admin']); ?>
                        </div>
                        <?php elseif (empty($submission['selected']) || $submission['selected'] != 1): ?>
                            <?php if (!empty($submission['deselected_by_admin'])): ?>
                            <div class="text-xs text-gray-500 mb-2">
                                <i class="fas fa-user-slash mr-1"></i> Deselected by <?php echo htmlspecialchars($submission['deselected_by_admin']); ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-id-card text-gray-500 w-5"></i>
                                <span class="ml-2">ID: <?php echo htmlspecialchars($submission['id']); ?></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-gray-500 w-5"></i>
                                <span class="ml-2 truncate"><?php echo htmlspecialchars($submission['email']); ?></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo htmlspecialchars($submission['phone']); ?></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-music text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo htmlspecialchars(strtoupper($submission['music_type'])); ?></span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo htmlspecialchars($submission['experience']); ?> years experience</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-calendar text-gray-500 w-5"></i>
                                <span class="ml-2"><?php echo date('M j, Y', strtotime($submission['submission_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <?php if (!empty($submission['youtube_link'])): ?>
                            <button onclick="openVideoModal('<?php echo htmlspecialchars($submission['youtube_link']); ?>'); event.stopPropagation()" class="action-btn flex items-center text-sm text-primary hover:text-secondary">
                                <i class="fab fa-youtube mr-2"></i> Watch Video
                            </button>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">No video</span>
                            <?php endif; ?>
                            
                            <div class="flex space-x-2">
                                <?php if (empty($submission['selected']) || $submission['selected'] != 1): ?>
                                <button onclick="markAsSelected(<?php echo $submission['id']; ?>); event.stopPropagation()" class="action-btn text-green-500 hover:text-green-400" title="Mark as Selected">
                                    <i class="fas fa-star"></i>
                                </button>
                                <?php else: ?>
                                <button onclick="deselectCandidate(<?php echo $submission['id']; ?>); event.stopPropagation()" class="action-btn text-yellow-500 hover:text-yellow-400" title="Deselect Candidate">
                                    <i class="fas fa-minus-circle"></i>
                                </button>
                                <?php endif; ?>
                                <button onclick="viewDetails(<?php echo $submission['id']; ?>); event.stopPropagation()" class="action-btn text-blue-500 hover:text-blue-400" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
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
    
    <!-- EmailJS Integration -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
        (function(){
            emailjs.init("YOUR_PUBLIC_KEY"); // Replace with your EmailJS public key
        })();
    </script>
    
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
        
        // Mark candidate as selected
        function markAsSelected(id) {
            if (confirm('Are you sure you want to mark this candidate as selected?')) {
                // Create form data
                const formData = new FormData();
                formData.append('mark_selected', '1');
                formData.append('id', id);
                
                // Send request
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Refresh the page to show updated status
                    location.reload();
                })
                .catch(error => {
                    alert('Error marking candidate as selected. Please try again.');
                });
            }
        }
        
        // Deselect candidate
        function deselectCandidate(id) {
            if (confirm('Are you sure you want to deselect this candidate?')) {
                // Create form data
                const formData = new FormData();
                formData.append('deselect_candidate', '1');
                formData.append('id', id);
                
                // Send request
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Refresh the page to show updated status
                    location.reload();
                })
                .catch(error => {
                    alert('Error deselecting candidate. Please try again.');
                });
            }
        }
        
        // View candidate details
        function viewDetails(id) {
            window.location.href = 'candidate_details.php?id=' + id;
        }
        
        // Close modal when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });
        
        // Check if we need to send a selection email
        <?php if (isset($_SESSION['send_selection_email']) && $_SESSION['send_selection_email'] && isset($_SESSION['selected_candidate_name']) && isset($_SESSION['selected_candidate_email'])): ?>
        window.addEventListener('load', function() {
            // Create debug element
            var debugDiv = document.createElement('div');
            debugDiv.id = 'email-debug-info';
            debugDiv.style.position = 'fixed';
            debugDiv.style.top = '10px';
            debugDiv.style.right = '10px';
            debugDiv.style.backgroundColor = 'rgba(0,0,0,0.8)';
            debugDiv.style.color = 'white';
            debugDiv.style.padding = '15px';
            debugDiv.style.borderRadius = '5px';
            debugDiv.style.zIndex = '9999';
            debugDiv.style.maxWidth = '300px';
            debugDiv.innerHTML = '<h3 style="margin: 0 0 10px 0; color: #6366f1;">Email Debug Info</h3>';
            document.body.appendChild(debugDiv);
            
            // Add debug info
            var debugInfo = {
                to_name: '<?php echo addslashes($_SESSION['selected_candidate_name']); ?>',
                to_email: '<?php echo addslashes($_SESSION['selected_candidate_email']); ?>',
                candidate_name: '<?php echo addslashes($_SESSION['selected_candidate_name']); ?>',
                message: 'Congratulations! You have been selected for the next round of auditions.',
                timestamp: new Date().toISOString()
            };
            
            debugDiv.innerHTML += '<p><strong>Sending email to:</strong> ' + debugInfo.to_email + '</p>';
            debugDiv.innerHTML += '<p><strong>Name:</strong> ' + debugInfo.to_name + '</p>';
            debugDiv.innerHTML += '<p><strong>Status:</strong> <span id="email-status">Sending...</span></p>';
            
            // Validate email address before sending
            if (!debugInfo.to_email || debugInfo.to_email === '' || !isValidEmail(debugInfo.to_email)) {
                document.getElementById('email-status').innerHTML = '<span style="color: red;">Invalid Email Address</span>';
                debugDiv.innerHTML += '<p><strong>Error:</strong> <span style="color: red;">Invalid or empty email address: "' + debugInfo.to_email + '"</span></p>';
                return;
            }
            
            // Send email using EmailJS with selection template parameters
            var templateParams = {
                to_name: debugInfo.to_name,
                to_email: debugInfo.to_email,
                candidate_name: debugInfo.candidate_name,
                subject: 'Hypecrews - Congratulations! You\'ve Been Selected',
                heading: 'Congratulations!',
                status_message: 'You have been selected for the next round of auditions.',
                status: 'Selected',
                status_color: '#10b981',
                message: 'We\'re excited to inform you that you\'ve been selected to move forward in the Hypecrews audition process. Our team was impressed with your submission and we\'d like to invite you to the next stage.'
            };            
            emailjs.send('service_jvpy9a4', 'template_qhsqlyk', templateParams)
                .then(function(response) {
                    console.log('SUCCESS!', response.status, response.text);
                    document.getElementById('email-status').innerHTML = '<span style="color: green;">Sent Successfully</span>';
                    debugDiv.innerHTML += '<p><strong>Response:</strong> ' + response.status + '</p>';
                    
                    // Clear session variables via AJAX
                    fetch('../clear_email_session.php')
                        .then(response => response.json())
                        .then(data => console.log('Session cleared:', data))
                        .catch(error => console.log('Failed to clear session:', error));
                }, function(error) {
                    console.log('FAILED...', error);
                    document.getElementById('email-status').innerHTML = '<span style="color: red;">Failed</span>';
                    debugDiv.innerHTML += '<p><strong>Error:</strong> <span style="color: red;">' + JSON.stringify(error) + '</span></p>';
                    
                    // Clear session variables even on failure
                    fetch('../clear_email_session.php')
                        .then(response => response.json())
                        .then(data => console.log('Session cleared:', data))
                        .catch(error => console.log('Failed to clear session:', error));
                });
            
            // Auto-remove debug info after 10 seconds
            setTimeout(function() {
                if (document.getElementById('email-debug-info')) {
                    document.getElementById('email-debug-info').style.opacity = '0.5';
                }
            }, 10000);
        });
        <?php endif; ?>
        
        // Email validation function
        function isValidEmail(email) {
            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
    </script>
</body>
</html>