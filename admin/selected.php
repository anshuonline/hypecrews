<?php
require_once 'auth.php';
require_once '../config/db.php';

// Set page title
$pageTitle = 'Selected Candidates';

// Handle deselecting candidate
if (isset($_POST['deselect_candidate']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $adminId = $_SESSION['admin_id'];
    try {
        // First, get the candidate details before updating
        $stmt = $pdo->prepare("SELECT full_name, email FROM audition_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $candidate = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("UPDATE audition_submissions SET selected = 0, selected_by_admin_id = NULL, deselected_by_admin_id = ? WHERE id = ?");
        $stmt->execute([$adminId, $id]);
        // Success message will be returned in JSON response
        // $success = "Candidate deselected!";
        
        // Store candidate info for email notification
        $_SESSION['deselected_candidate_name'] = $candidate['full_name'];
        $_SESSION['deselected_candidate_email'] = $candidate['email'];
        $_SESSION['send_deselection_email'] = true;
        
        // Return candidate data for AJAX response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Candidate deselected!',
                'candidate_name' => $candidate['full_name'],
                'candidate_email' => $candidate['email']
            ]);
            exit;
        }
        
        // Debug info
        $_SESSION['debug_email_info'] = [
            'candidate_name' => $candidate['full_name'],
            'candidate_email' => $candidate['email'],
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => 'deselection'
        ];
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

// Fetch only selected submissions with admin information
try {
    $stmt = $pdo->prepare("SELECT a.*, adm.username as selected_by_admin, adm2.username as deselected_by_admin FROM audition_submissions a LEFT JOIN admins adm ON a.selected_by_admin_id = adm.id LEFT JOIN admins adm2 ON a.deselected_by_admin_id = adm2.id WHERE a.selected = 1 ORDER BY a.id DESC");
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
    <title>Selected Candidates - Hypecrews</title>
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
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.9));
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        
        .dashboard-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
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
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
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
                    <h1 class="text-3xl font-bold">Selected Candidates</h1>
                    <p class="text-gray-400 mt-2">View and manage selected audition candidates</p>
                </div>
                <div class="text-sm text-gray-400">
                    Total Selected: <?php echo count($selectedSubmissions); ?>
                </div>
            </div>
            
            <?php if (isset($_GET['debug_email'])): ?>
            <div class="mb-6 p-4 rounded-lg bg-blue-900/50 border border-blue-700 backdrop-blur-sm">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg mb-2">Email Debug Mode</h3>
                        <p class="text-blue-300">Click the buttons below to test email functionality</p>
                    </div>
                    <div class="flex space-x-2">
                        <button id="testSelectionEmailBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                            Test Selection Email
                        </button>
                        <button id="testDeselectionEmailBtn" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg">
                            Test Deselection Email
                        </button>
                    </div>
                </div>
            </div>
            <script>
            document.getElementById('testSelectionEmailBtn').addEventListener('click', function() {
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
                    subject: 'Hypecrews - Congratulations! You\'ve Been Selected',
                    heading: 'Congratulations!',
                    status_message: 'You have been selected for the next round of auditions.',
                    status: 'Selected',
                    status_color: '#10b981',
                    message: 'We\'re excited to inform you that you\'ve been selected to move forward in the Hypecrews audition process. Our team was impressed with your submission and we\'d like to invite you to the next stage.'
                };
                
                debugDiv.innerHTML += '<p><strong>Sending selection email to:</strong> ' + debugInfo.to_email + '</p>';
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
            
            document.getElementById('testDeselectionEmailBtn').addEventListener('click', function() {
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
                    subject: 'Hypecrews - Application Status Update',
                    heading: 'Application Status Update',
                    status_message: 'We regret to inform you that your application status for Hypecrews has been updated.',
                    status: 'Not Selected',
                    status_color: '#f59e0b',
                    message: 'We appreciate your interest in Hypecrews and the time you invested in your audition. While this opportunity didn\'t work out, we encourage you to continue pursuing your musical journey.'
                };
                
                debugDiv.innerHTML += '<p><strong>Sending deselection email to:</strong> ' + debugInfo.to_email + '</p>';
                debugDiv.innerHTML += '<p><strong>Name:</strong> ' + debugInfo.to_name + '</p>';
                debugDiv.innerHTML += '<p><strong>Status:</strong> <span id="email-status">Sending...</span></p>';
                
                // Send email using EmailJS
                emailjs.send('service_jvpy9a4', 'template_fa790nn', debugInfo)
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
                        <?php if (isset($_SESSION['send_deselection_email']) && $_SESSION['send_deselection_email']): ?>
                        <p class="text-yellow-300 mt-2">Deselection email queued for sending</p>
                        <?php elseif (isset($_SESSION['send_selection_email']) && $_SESSION['send_selection_email']): ?>
                        <p class="text-green-300 mt-2">Selection email queued for sending</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (empty($selectedSubmissions)): ?>
            <div class="bg-light/50 backdrop-blur-sm rounded-xl p-12 text-center border border-gray-700">
                <i class="fas fa-star text-yellow-500 text-5xl mb-4"></i>
                <h3 class="text-xl font-bold mb-2">No Selected Candidates</h3>
                <p class="text-gray-400 max-w-md mx-auto">You haven't selected any candidates yet. Go to the <a href="index.php" class="text-primary hover:underline">dashboard</a> to review submissions and select candidates.</p>
            </div>
            <?php else: ?>
            <!-- Selected Candidates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($selectedSubmissions as $submission): ?>
                <div class="candidate-card bg-light/50 backdrop-blur-sm rounded-xl border border-gray-700 overflow-hidden cursor-pointer hover:shadow-lg transition-shadow duration-300" onclick="viewDetails(<?php echo $submission['id']; ?>)">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
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
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-900/50 text-yellow-300">
                                Selected
                            </span>
                        </div>
                        
                        <?php if (!empty($submission['selected_by_admin'])): ?>
                        <div class="mt-2 text-xs text-gray-500">
                            Selected by: <?php echo htmlspecialchars($submission['selected_by_admin']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mt-4 space-y-3">
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
                        </div>
                        
                        <div class="mt-6 flex justify-between">
                            <?php if (!empty($submission['youtube_link'])): ?>
                            <button onclick="openVideoModal('<?php echo htmlspecialchars($submission['youtube_link']); ?>'); event.stopPropagation()" class="flex items-center text-sm text-primary hover:text-secondary">
                                <i class="fab fa-youtube mr-2"></i> Watch Video
                            </button>
                            <?php else: ?>
                            <span class="text-sm text-gray-500">No video</span>
                            <?php endif; ?>
                            
                            <div class="flex space-x-2">
                                <button onclick="deselectCandidate(<?php echo $submission['id']; ?>); event.stopPropagation()" class="flex items-center text-sm text-yellow-500 hover:text-yellow-400" title="Deselect Candidate">
                                    <i class="fas fa-minus-circle mr-1"></i> Deselect
                                </button>
                                <button onclick="viewDetails(<?php echo $submission['id']; ?>); event.stopPropagation()" class="flex items-center text-sm text-blue-500 hover:text-blue-400">
                                    <i class="fas fa-eye mr-1"></i> Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
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
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                // Send email immediately and wait for it to complete
                                sendDeselectionEmail(data.candidate_name, data.candidate_email);
                                
                                // Wait a bit for the email to be sent, then reload the page
                                setTimeout(function() {
                                    location.reload();
                                }, 3000); // Wait 3 seconds to ensure email is sent
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deselecting candidate. Please try again.');
                        });
                    }
                }
                
                // Function to send deselection email
                function sendDeselectionEmail(candidateName, candidateEmail) {
                    // Validate email
                    if (!candidateEmail || candidateEmail === '' || !isValidEmail(candidateEmail)) {
                        console.log('Invalid email address:', candidateEmail);
                        alert('Invalid email address for candidate: ' + candidateEmail);
                        return;
                    }
                    
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
                    debugDiv.innerHTML += '<p><strong>Sending deselect email to:</strong> ' + candidateEmail + '</p>';
                    debugDiv.innerHTML += '<p><strong>Name:</strong> ' + candidateName + '</p>';
                    debugDiv.innerHTML += '<p><strong>Status:</strong> <span id="email-status">Sending...</span></p>';
                    
                    // Prepare template parameters
                    var templateParams = {
                        to_name: candidateName,
                        to_email: candidateEmail,
                        candidate_name: candidateName,
                        subject: 'Hypecrews - Application Status Update',
                        heading: 'Application Status Update',
                        status_message: 'We regret to inform you that your application status for Hypecrews has been updated.',
                        status: 'Not Selected',
                        status_color: '#f59e0b',
                        message: 'We appreciate your interest in Hypecrews and the time you invested in your audition. While this opportunity didn\'t work out, we encourage you to continue pursuing your musical journey.'
                    };
                    
                    // Send email using EmailJS
                    emailjs.send('service_jvpy9a4', 'template_fa790nn', templateParams)
                        .then(function(response) {
                            console.log('Deselection email sent successfully!', response.status, response.text);
                            document.getElementById('email-status').innerHTML = '<span style="color: green;">Sent Successfully</span>';
                            debugDiv.innerHTML += '<p><strong>Response:</strong> ' + response.status + '</p>';
                            
                            // Auto-remove debug info after 5 seconds
                            setTimeout(function() {
                                if (document.getElementById('email-debug-info')) {
                                    document.getElementById('email-debug-info').style.opacity = '0.5';
                                }
                            }, 5000);
                        }, function(error) {
                            console.log('Failed to send deselection email...', error);
                            document.getElementById('email-status').innerHTML = '<span style="color: red;">Failed</span>';
                            debugDiv.innerHTML += '<p><strong>Error:</strong> <span style="color: red;">' + JSON.stringify(error) + '</span></p>';
                            alert('Failed to send email notification. Please check the console for details.');
                            
                            // Auto-remove debug info after 5 seconds
                            setTimeout(function() {
                                if (document.getElementById('email-debug-info')) {
                                    document.getElementById('email-debug-info').style.opacity = '0.5';
                                }
                            }, 5000);
                        });
                }
                
                // View candidate details
                function viewDetails(id) {
                    // In a full implementation, this would show a detailed view of the candidate
                    window.location.href = 'candidate_details.php?id=' + id;
                }
                
                // Close modal when clicking outside
                document.getElementById('videoModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeVideoModal();
                    }
                });
                
                // Check if we need to send a deselection email
                <?php if (isset($_SESSION['send_deselection_email']) && $_SESSION['send_deselection_email'] && isset($_SESSION['deselected_candidate_name']) && isset($_SESSION['deselected_candidate_email'])): ?>
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
                        to_name: '<?php echo addslashes($_SESSION['deselected_candidate_name']); ?>',
                        to_email: '<?php echo addslashes($_SESSION['deselected_candidate_email']); ?>',
                        candidate_name: '<?php echo addslashes($_SESSION['deselected_candidate_name']); ?>',
                        message: 'We regret to inform you that your selection status has been changed. Please contact us for more information.',
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
                    
                    // Send email using EmailJS with deselect template parameters
                    var templateParams = {
                        to_name: debugInfo.to_name,
                        to_email: debugInfo.to_email,
                        candidate_name: debugInfo.candidate_name,
                        subject: 'Hypecrews - Application Status Update',
                        heading: 'Application Status Update',
                        status_message: 'We regret to inform you that your application status for Hypecrews has been updated.',
                        status: 'Not Selected',
                        status_color: '#f59e0b',
                        message: 'We appreciate your interest in Hypecrews and the time you invested in your audition. While this opportunity didn\'t work out, we encourage you to continue pursuing your musical journey.'
                    };
                    
                    emailjs.send('service_jvpy9a4', 'template_fa790nn', templateParams)
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
            
        </div>
    </div>
</body>
</html>