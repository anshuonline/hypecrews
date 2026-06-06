<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'team_chat';

$admin_id = $_SESSION['admin_id'];

// Fetch all other admins
try {
    $stmt = $pdo->prepare("SELECT id, username, profile_image, last_active, special_tag FROM administrators WHERE id != ? ORDER BY username ASC");
    $stmt->execute([$admin_id]);
    $other_admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$chat_with = isset($_GET['chat']) ? $_GET['chat'] : 'group';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Chat - Hypecrews Admin</title>
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

        /* Glass panel utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }
        
        /* Custom Scrollbar for chat */
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: transparent; 
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.15); 
            border-radius: 10px;
        }
        .chat-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.25); 
        }

        .link-text {
            color: #0066cc;
            text-decoration: underline;
        }
        
        .image-preview {
            max-width: 250px;
            max-height: 250px;
            border-radius: 12px;
            cursor: pointer;
            margin-top: 8px;
            border: 1px solid rgba(0,0,0,0.1);
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .tag-gradient {
            background: linear-gradient(270deg, #0066cc, #8b5cf6, #ec4899, #0066cc);
            background-size: 300% 300%;
            animation: gradientShift 3s ease infinite;
        }
        .shine-effect {
            position: relative;
            overflow: hidden;
        }
        .shine-effect::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 50%, rgba(255,255,255,0) 100%);
            animation: shine 2.5s infinite;
        }
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-apple_text overflow-hidden">

    <!-- Abstract blurred colorful background -->
    <div class="glass-bg">
        <div class="glass-blob"></div>
    </div>

    <div class="flex h-[100dvh] relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-30 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:flex-row h-[100dvh] relative overflow-hidden">
            
            <!-- Mobile Contacts Overlay Background -->
            <div id="contacts-overlay" class="fixed inset-0 bg-black/40 z-10 hidden md:hidden backdrop-blur-sm" onclick="toggleContacts()"></div>
            
            <!-- Chat Contacts Sidebar (Left) -->
            <div id="contacts-sidebar" class="absolute md:relative z-20 w-full md:w-80 glass-panel bg-white/70 border-r border-black/5 flex flex-col h-full shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 shadow-xl">
                
                <!-- Sidebar Header with Search -->
                <div class="p-5 shrink-0 flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-apple_text tracking-tight">Messages</h2>
                        <button class="md:hidden text-apple_muted hover:text-primary transition-colors" onclick="toggleContacts()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Search Input -->
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" placeholder="Search chats..." class="w-full bg-white/50 border border-white rounded-2xl pl-11 pr-4 py-3 text-sm text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all placeholder-gray-400 font-medium">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto chat-scroll px-3 pb-4 space-y-1">
                    
                    <!-- Group Chat Option -->
                    <a href="?chat=group" class="group flex items-center p-3 rounded-2xl transition-all duration-300 <?php echo $chat_with === 'group' ? 'bg-primary/10 border border-primary/20 shadow-sm' : 'hover:bg-white/50 border border-transparent'; ?>">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center mr-4 shadow-sm border border-blue-200 shrink-0 transform group-hover:scale-105 transition-transform duration-300 text-primary">
                            <i class="fas fa-users text-lg"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-apple_text truncate text-base group-hover:text-primary transition-colors">Team Group Chat</p>
                            <p class="text-[11px] font-medium text-apple_muted truncate">General discussion</p>
                        </div>
                        <?php if($chat_with === 'group'): ?>
                            <div class="w-2.5 h-2.5 rounded-full bg-primary shadow-sm"></div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="pt-6 pb-2 px-3 flex items-center justify-between">
                        <p class="text-[11px] font-bold text-apple_muted uppercase tracking-widest">Direct Messages</p>
                        <span class="bg-black/5 text-apple_muted font-bold text-[10px] px-2 py-0.5 rounded-full"><?php echo count($other_admins); ?></span>
                    </div>
                    
                    <!-- Other Admins -->
                    <?php if (!empty($other_admins)): ?>
                        <?php foreach($other_admins as $oa): ?>
                            <a href="?chat=<?php echo $oa['id']; ?>" class="group flex items-center p-3 rounded-2xl transition-all duration-300 <?php echo $chat_with == $oa['id'] ? 'bg-primary/10 border border-primary/20 shadow-sm' : 'hover:bg-white/50 border border-transparent'; ?>">
                                <div class="relative mr-4 shrink-0 transform group-hover:scale-105 transition-transform duration-300">
                                    <?php if (!empty($oa['profile_image'])): ?>
                                        <div class="w-12 h-12 rounded-full overflow-hidden border border-white/60 bg-white shadow-sm relative group/img">
                                            <img src="../<?php echo htmlspecialchars($oa['profile_image']); ?>" class="w-full h-full object-cover cursor-pointer transition-transform duration-300 group-hover/img:scale-110" onclick="event.preventDefault(); event.stopPropagation(); zoomImage(this.src)">
                                            <div class="absolute inset-0 bg-black/20 hidden group-hover/img:flex items-center justify-center cursor-pointer" onclick="event.preventDefault(); event.stopPropagation(); zoomImage(this.previousElementSibling.src)">
                                                <i class="fas fa-search-plus text-white text-xs shadow-sm"></i>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center border border-blue-100 text-primary text-lg font-bold shadow-sm">
                                            <?php echo substr(htmlspecialchars($oa['username']), 0, 1); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php 
                                        $is_online = false;
                                        if (!empty($oa['last_active'])) {
                                            $last_active = strtotime($oa['last_active']);
                                            if (time() - $last_active <= 30) {
                                                $is_online = true;
                                            }
                                        }
                                    ?>
                                    <!-- Online Indicator -->
                                    <div id="online-dot-<?php echo $oa['id']; ?>" class="absolute bottom-0 right-0 w-3.5 h-3.5 <?php echo $is_online ? 'bg-green-500' : 'bg-gray-400'; ?> border-2 border-white rounded-full transition-colors duration-300 shadow-sm"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="font-bold text-apple_text truncate text-[15px] group-hover:text-primary transition-colors"><?php echo htmlspecialchars($oa['username']); ?></p>
                                        <?php if (!empty($oa['special_tag'])): ?>
                                            <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold tracking-wider text-white uppercase shadow-sm shrink-0 tag-gradient shine-effect"><?php echo htmlspecialchars($oa['special_tag']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[11px] font-medium text-apple_muted truncate mt-0.5">Administrator</p>
                                </div>
                                <?php if($chat_with == $oa['id']): ?>
                                    <div class="w-2.5 h-2.5 rounded-full bg-primary shadow-sm"></div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center p-6 text-center bg-white/30 rounded-2xl border border-black/5 mt-2">
                            <i class="fas fa-user-slash text-apple_muted text-3xl mb-3 opacity-50"></i>
                            <p class="text-sm text-apple_muted font-medium">No other admins found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat Window (Center) -->
            <div class="flex-1 flex flex-col relative h-full border-t md:border-t-0 border-black/5 w-full min-w-0">
                
                <!-- Chat Header -->
                <div class="glass-panel px-6 py-4 border-b border-black/5 flex items-center justify-between shadow-sm shrink-0 w-full z-10 rounded-none bg-white/70">
                    <div class="flex items-center">
                        <button class="md:hidden mr-4 text-apple_muted hover:text-primary transition-colors" onclick="toggleContacts()">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <?php 
                        $chat_title = "Team Group Chat";
                        $chat_icon = '<div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mr-3 shadow-sm border border-blue-100 text-primary"><i class="fas fa-users"></i></div>';
                        
                        if ($chat_with !== 'group') {
                            // Find the user details
                            $dm_user = null;
                            foreach($other_admins as $oa) {
                                if ($oa['id'] == $chat_with) {
                                    $dm_user = $oa;
                                    break;
                                }
                            }
                            if ($dm_user) {
                                $chat_title = htmlspecialchars($dm_user['username']);
                                $dm_is_online = false;
                                if (!empty($dm_user['last_active'])) {
                                    $dm_last_active = strtotime($dm_user['last_active']);
                                    if (time() - $dm_last_active <= 30) {
                                        $dm_is_online = true;
                                    }
                                }
                                if (!empty($dm_user['profile_image'])) {
                                    $chat_icon = '<div class="w-10 h-10 rounded-full mr-3 overflow-hidden border border-white/60 bg-white shrink-0 relative group/icon cursor-pointer shadow-sm"><img src="../' . htmlspecialchars($dm_user['profile_image']) . '" class="w-full h-full object-cover transition-transform duration-300 group-hover/icon:scale-110" onclick="zoomImage(this.src)"><div class="absolute inset-0 bg-black/20 hidden group-hover/icon:flex items-center justify-center" onclick="zoomImage(this.previousElementSibling.src)"><i class="fas fa-search-plus text-white text-xs"></i></div></div>';
                                } else {
                                    $chat_icon = '<div class="w-10 h-10 rounded-full bg-blue-50 text-primary border border-blue-100 flex items-center justify-center mr-3 shadow-sm shrink-0 font-bold">' . substr($chat_title, 0, 1) . '</div>';
                                }
                            }
                        }
                        ?>
                        <?php echo $chat_icon; ?>
                        <div>
                            <h2 class="text-lg font-bold text-apple_text leading-tight"><?php echo $chat_title; ?></h2>
                            <?php if ($chat_with === 'group'): ?>
                                <p class="text-[11px] font-semibold text-green-600 uppercase tracking-wider mt-0.5"><i class="fas fa-circle text-[8px] mr-1"></i> Active</p>
                            <?php else: ?>
                                <?php if (isset($dm_is_online) && $dm_is_online): ?>
                                    <p id="partnerStatus" class="text-[11px] font-semibold text-green-600 uppercase tracking-wider mt-0.5"><i class="fas fa-circle text-[8px] mr-1"></i> Online</p>
                                <?php else: ?>
                                    <p id="partnerStatus" class="text-[11px] font-semibold text-apple_muted uppercase tracking-wider mt-0.5"><i class="fas fa-circle text-[8px] mr-1"></i> Offline</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Toggle Details Button -->
                    <button class="w-10 h-10 rounded-full bg-white/50 border border-black/5 flex items-center justify-center text-apple_muted hover:text-primary hover:bg-white transition-all shadow-sm" onclick="toggleDetails()" title="Chat Details">
                        <i class="fas fa-info-circle text-lg"></i>
                    </button>
                </div>
                
                <!-- Messages Area -->
                <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-6 space-y-4 relative z-0">
                    <!-- Subtle background pattern for chat area -->
                    <div class="absolute inset-0 pointer-events-none opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                    
                    <!-- Messages loaded via JS -->
                    <div class="text-center text-apple_muted font-medium text-sm mt-10 relative z-10 flex flex-col items-center">
                        <i class="fas fa-circle-notch fa-spin text-primary text-2xl mb-3"></i>
                        Loading messages...
                    </div>
                </div>
                
                <!-- Image Preview Area -->
                <div id="imagePreviewArea" class="hidden p-3 glass-panel bg-white/80 border-t border-black/5 flex items-center justify-between z-10 rounded-none relative">
                    <div class="flex items-center">
                        <div class="p-1 bg-white border border-black/5 rounded-lg shadow-sm mr-3">
                            <img id="previewImg" src="" class="h-14 rounded object-cover">
                        </div>
                        <span class="text-xs font-bold text-apple_muted uppercase tracking-wider">Attached Image</span>
                    </div>
                    <button type="button" class="w-8 h-8 rounded-full bg-red-50 text-red-500 hover:bg-red-100 flex items-center justify-center transition-colors shadow-sm" onclick="clearImage()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Chat Input Area -->
                <div class="p-4 md:p-6 glass-panel bg-white/70 border-t border-black/5 shrink-0 w-full z-10 rounded-none pb-6 md:pb-6 relative">
                    <form id="chatForm" class="flex gap-3 items-center">
                        <input type="hidden" id="chatWith" value="<?php echo htmlspecialchars($chat_with); ?>">
                        
                        <!-- Attachments & Meetings -->
                        <div class="flex gap-2 shrink-0">
                            <label class="cursor-pointer w-10 h-10 md:w-12 md:h-12 rounded-full bg-white border border-black/5 flex items-center justify-center text-apple_muted hover:text-primary hover:shadow-md transition-all shadow-sm">
                                <i class="fas fa-image text-lg"></i>
                                <input type="file" id="imageInput" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="previewImage(this)">
                            </label>
                            <button type="button" onclick="openMeetingModal()" class="w-10 h-10 md:w-12 md:h-12 rounded-full bg-white border border-black/5 flex items-center justify-center text-apple_muted hover:text-primary hover:shadow-md transition-all shadow-sm">
                                <i class="fas fa-calendar-alt text-lg"></i>
                            </button>
                        </div>
                        
                        <input type="text" id="messageInput" autocomplete="off" placeholder="iMessage..." class="flex-1 bg-white/80 border border-black/10 rounded-full px-5 md:px-6 py-3 md:py-3.5 text-sm md:text-base text-apple_text focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all placeholder-gray-400 font-medium">
                        
                        <button type="submit" class="bg-primary hover:bg-blue-600 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center shadow-md transition-transform hover:scale-105 shrink-0">
                            <i class="fas fa-arrow-up text-sm md:text-base"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Details Sidebar (Right) - Upcoming Meetings & Pins -->
            <div id="details-overlay" class="fixed inset-0 bg-black/40 z-10 hidden backdrop-blur-sm" onclick="toggleDetails()"></div>
            <div id="details-sidebar" class="absolute z-30 w-full md:w-80 glass-panel bg-white/70 border-l border-black/5 flex flex-col h-full shrink-0 transform translate-x-full transition-transform duration-300 right-0 top-0 bottom-0 shadow-xl">
                <div class="p-5 border-b border-black/5 bg-white/50 shrink-0 flex items-center justify-between backdrop-blur-md">
                    <h2 class="text-lg font-bold text-apple_text flex items-center"><i class="fas fa-info-circle mr-2 text-primary"></i>Chat Details</h2>
                    <button class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_muted transition-colors" onclick="toggleDetails()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto chat-scroll p-5 space-y-8">
                    
                    <!-- Pinned Messages Section -->
                    <div>
                        <h3 class="text-[11px] font-bold text-apple_muted uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-thumbtack mr-2 text-amber-500"></i>Pinned Messages</h3>
                        <div id="pinnedMessagesList" class="space-y-3">
                            <div class="text-sm font-medium text-apple_muted text-center py-4">Loading pins...</div>
                        </div>
                    </div>
                    
                    <hr class="border-black/5">
                    
                    <!-- Upcoming Meetings Section -->
                    <div>
                        <h3 class="text-[11px] font-bold text-apple_muted uppercase tracking-widest mb-4 flex items-center"><i class="fas fa-calendar-check mr-2 text-primary"></i>Upcoming Meetings</h3>
                        <div id="upcomingMeetingsList" class="space-y-3">
                            <div class="text-sm font-medium text-apple_muted text-center py-4">Loading meetings...</div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>

    <!-- Schedule Meeting Modal -->
    <div id="meetingModal" class="fixed inset-0 bg-black/20 z-50 hidden flex items-center justify-center p-4 backdrop-blur-md">
        <div class="glass-panel bg-white/90 rounded-[2rem] w-full max-w-md shadow-2xl border border-white overflow-hidden transform scale-95 transition-transform duration-300" id="meetingModalContent">
            <div class="p-6 border-b border-black/5 flex justify-between items-center bg-white/50">
                <h3 class="font-bold text-xl text-apple_text flex items-center"><i class="fas fa-calendar-plus text-primary mr-3"></i>Schedule Meeting</h3>
                <button onclick="closeMeetingModal()" class="w-8 h-8 rounded-full bg-black/5 hover:bg-black/10 flex items-center justify-center text-apple_muted transition-colors focus:outline-none"><i class="fas fa-times"></i></button>
            </div>
            <form id="meetingForm" class="p-8 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Meeting Title/Message</label>
                    <input type="text" id="meetTitle" required class="w-full bg-white border border-black/10 rounded-xl px-4 py-3 text-apple_text font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Date & Time</label>
                    <input type="datetime-local" id="meetTime" required class="w-full bg-white border border-black/10 rounded-xl px-4 py-3 text-apple_text font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Meeting Link (Zoom, Meet, etc)</label>
                    <input type="url" id="meetLink" required class="w-full bg-white border border-black/10 rounded-xl px-4 py-3 text-apple_text font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all placeholder-gray-400" placeholder="https://zoom.us/j/...">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3.5 px-4 rounded-xl transition-colors shadow-md text-base">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Zoom Image Modal -->
    <div id="zoomModal" class="fixed inset-0 bg-black/40 z-[100] hidden flex items-center justify-center p-4 backdrop-blur-md" onclick="closeZoom()">
        <button class="absolute top-6 right-6 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full text-white text-2xl flex items-center justify-center transition-colors shadow-lg focus:outline-none backdrop-blur-lg"><i class="fas fa-times"></i></button>
        <img id="zoomImg" src="" class="max-w-full max-h-[90vh] object-contain shadow-2xl rounded-[2rem] border-4 border-white/80" onclick="event.stopPropagation()">
    </div>

    <!-- AJAX Script -->
    <script>
        function toggleContacts() {
            const sidebar = document.getElementById('contacts-sidebar');
            const overlay = document.getElementById('contacts-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
        
        function toggleDetails() {
            const sidebar = document.getElementById('details-sidebar');
            const overlay = document.getElementById('details-overlay');
            sidebar.classList.toggle('translate-x-full');
            overlay.classList.toggle('hidden');
        }

        function zoomImage(src) {
            document.getElementById('zoomImg').src = src;
            document.getElementById('zoomModal').classList.remove('hidden');
            document.getElementById('zoomModal').classList.add('flex');
        }
        function closeZoom() {
            document.getElementById('zoomModal').classList.add('hidden');
            document.getElementById('zoomModal').classList.remove('flex');
        }
        
        function openMeetingModal() {
            document.getElementById('meetingModal').classList.remove('hidden');
            document.getElementById('meetingModal').classList.add('flex');
            setTimeout(() => {
                document.getElementById('meetingModalContent').classList.remove('scale-95');
                document.getElementById('meetingModalContent').classList.add('scale-100');
            }, 10);
        }
        function closeMeetingModal() {
            document.getElementById('meetingModalContent').classList.remove('scale-100');
            document.getElementById('meetingModalContent').classList.add('scale-95');
            setTimeout(() => {
                document.getElementById('meetingModal').classList.add('hidden');
                document.getElementById('meetingModal').classList.remove('flex');
            }, 300);
        }
        
        // Image Preview logic
        let selectedFile = null;
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                if (file.size > 2 * 1024 * 1024) {
                    alert("Image exceeds 2MB limit!");
                    input.value = '';
                    return;
                }
                selectedFile = file;
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreviewArea').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
        function clearImage() {
            selectedFile = null;
            document.getElementById('imageInput').value = '';
            document.getElementById('imagePreviewArea').classList.add('hidden');
        }

        const chatWith = document.getElementById('chatWith').value;
        const messagesDiv = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        
        let lastMessageCount = 0;
        let isScrolledToBottom = true;
        let pinnedMessages = [];
        let upcomingMeetings = [];

        messagesDiv.addEventListener('scroll', () => {
            isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 20;
        });

        // Link auto-detection
        function linkify(text) {
            const urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function(url) {
                return `<a href="${url}" target="_blank" class="link-text hover:underline">${url}</a>`;
            });
        }

        function fetchMessages() {
            fetch('api_chat.php?chat_with=' + chatWith)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMessages(data.data);
                        updateDetailsPanels(data.data);
                        
                        if (chatWith !== 'group') {
                            const statusEl = document.getElementById('partnerStatus');
                            if (statusEl) {
                                if (data.partner_online) {
                                    statusEl.innerHTML = '<i class="fas fa-circle text-[8px] mr-1"></i> Online';
                                    statusEl.className = 'text-[11px] font-semibold text-green-600 uppercase tracking-wider mt-0.5';
                                } else {
                                    statusEl.innerHTML = '<i class="fas fa-circle text-[8px] mr-1"></i> Offline';
                                    statusEl.className = 'text-[11px] font-semibold text-apple_muted uppercase tracking-wider mt-0.5';
                                }
                            }
                        }
                        
                        // Update all sidebar online dots
                        if (data.online_admins) {
                            document.querySelectorAll('[id^="online-dot-"]').forEach(el => {
                                const adminId = parseInt(el.id.replace('online-dot-', ''));
                                if (data.online_admins.includes(adminId)) {
                                    el.classList.remove('bg-gray-400');
                                    el.classList.add('bg-green-500');
                                } else {
                                    el.classList.remove('bg-green-500');
                                    el.classList.add('bg-gray-400');
                                }
                            });
                        }
                    }
                })
                .catch(err => console.error("Error fetching messages:", err));
        }

        function renderMessages(messages) {
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div class="text-center text-apple_muted font-medium text-sm mt-10 relative z-10">No messages yet. Say hi!</div>';
                return;
            }
            
            // Check if there are new messages or a pin/delete state changed
            const currentHashes = messages.map(m => m.id + '_' + m.is_pinned + '_' + m.is_deleted).join(',');
            if (messagesDiv.dataset.lastHash !== currentHashes) {
                messagesDiv.dataset.lastHash = currentHashes;
                let html = '<div class="relative z-10">'; // Wrap messages to keep them above background pattern
                
                messages.forEach(msg => {
                    const isMine = msg.is_mine;
                    const isPinned = parseInt(msg.is_pinned) === 1;
                    const isDeleted = parseInt(msg.is_deleted) === 1;
                    
                    const avatar = msg.profile_image 
                        ? `<div class="w-8 h-8 rounded-full overflow-hidden border border-white/60 shadow-sm shrink-0 cursor-pointer hover:opacity-80 transition-opacity" onclick="zoomImage('../${msg.profile_image}')" title="Zoom Profile Picture"><img src="../${msg.profile_image}" class="w-full h-full object-cover"></div>`
                        : `<div class="w-8 h-8 rounded-full bg-blue-50 text-primary border border-blue-100 flex items-center justify-center text-xs font-bold shadow-sm shrink-0">${msg.initial}</div>`;
                    
                    // Format message content
                    let contentHtml = '';
                    
                    if (isDeleted) {
                        contentHtml = `<div class="text-black/40 italic text-sm font-medium"><i class="fas fa-ban mr-1.5"></i> This message was deleted</div>`;
                    } else {
                        // Pin badge
                        if (isPinned) {
                            contentHtml += `<div class="text-[10px] text-amber-500 mb-1 font-bold flex items-center uppercase tracking-wider"><i class="fas fa-thumbtack mr-1.5"></i> Pinned</div>`;
                        }
                        
                        // Text with links
                        if (msg.message) {
                            contentHtml += `<div class="text-[15px] font-medium leading-relaxed">${linkify(escapeHtml(msg.message))}</div>`;
                        }
                        
                        // Attached Image
                        if (msg.image_url) {
                            contentHtml += `<img src="../${msg.image_url}" class="image-preview" onclick="zoomImage(this.src)">`;
                        }
                        
                        // Meeting Card
                        if (msg.meeting_time) {
                            const mDate = new Date(msg.meeting_time);
                            contentHtml += `
                            <div class="mt-3 bg-white/90 border border-black/5 rounded-xl p-4 w-64 shadow-sm text-apple_text">
                                <div class="flex items-center text-primary font-bold mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-lg"></i>
                                    Meeting Invite
                                </div>
                                <div class="text-xs text-apple_muted font-semibold mb-3 font-mono bg-black/5 px-3 py-2 rounded-lg">
                                    ${mDate.toLocaleString()}
                                </div>
                                <a href="${msg.meeting_link}" target="_blank" class="block text-center w-full bg-primary hover:bg-blue-600 text-white text-sm font-bold py-2.5 rounded-lg transition-colors shadow-sm">
                                    <i class="fas fa-video mr-1.5"></i> Join Meeting
                                </a>
                            </div>`;
                        }
                    }
                    
                    // Pin & Delete Buttons
                    const pinBtn = !isDeleted ? `<button onclick="togglePin(${msg.id})" class="opacity-0 group-hover:opacity-100 transition-opacity w-7 h-7 rounded-full hover:bg-black/5 flex items-center justify-center text-apple_muted hover:text-amber-500 ${isPinned ? '!text-amber-500 !opacity-100' : ''}"><i class="fas fa-thumbtack text-xs"></i></button>` : '';
                    
                    let deleteBtn = '';
                    if (isMine && !isDeleted) {
                        // Using ISO string to reliably parse date
                        // MySQL format: YYYY-MM-DD HH:MM:SS -> replace space with T
                        const tStr = msg.created_at.replace(' ', 'T');
                        const msgTime = new Date(tStr).getTime();
                        const now = new Date().getTime();
                        if (now - msgTime <= 10000) { // 10 seconds
                            deleteBtn = `<button onclick="deleteMessage(${msg.id})" class="opacity-0 group-hover:opacity-100 transition-opacity w-7 h-7 rounded-full hover:bg-black/5 flex items-center justify-center text-apple_muted hover:text-red-500 ml-1"><i class="fas fa-trash text-xs"></i></button>`;
                        }
                    }

                    const specialTagBadge = msg.special_tag ? `<span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold tracking-wider text-white uppercase shadow-sm ml-2 align-middle tag-gradient shine-effect border border-white/20 inline-block">${escapeHtml(msg.special_tag)}</span>` : '';

                    if (isMine) {
                        html += `
                        <div class="flex justify-end mb-5 group">
                            <div class="flex items-end max-w-[85%]">
                                <div class="mr-2 mb-2 shrink-0 flex items-center">
                                    ${pinBtn}
                                    ${deleteBtn}
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-[11px] font-semibold text-apple_muted mb-1.5 mr-2 flex items-center">You${specialTagBadge}, ${msg.time}</span>
                                    <div class="bg-primary text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words border border-primary/20">
                                        ${contentHtml}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        html += `
                        <div class="flex justify-start mb-5 group">
                            <div class="flex items-end max-w-[85%]">
                                <div class="mr-3 mt-auto mb-1.5 shrink-0">
                                    ${avatar}
                                </div>
                                <div class="flex flex-col items-start">
                                    <span class="text-[11px] font-semibold text-apple_muted mb-1.5 ml-2 flex items-center">${escapeHtml(msg.username)}${specialTagBadge}, ${msg.time}</span>
                                    <div class="glass-panel bg-white/90 text-apple_text px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm break-words">
                                        ${contentHtml}
                                    </div>
                                </div>
                                <div class="ml-2 mb-2 shrink-0 flex items-center">
                                    ${pinBtn}
                                </div>
                            </div>
                        </div>`;
                    }
                });
                
                html += '</div>'; // close relative wrapper
                messagesDiv.innerHTML = html;
                
                if (isScrolledToBottom) {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        }
        
        function updateDetailsPanels(messages) {
            // Update Pinned
            const pinned = messages.filter(m => parseInt(m.is_pinned) === 1);
            const pinnedList = document.getElementById('pinnedMessagesList');
            if (pinned.length === 0) {
                pinnedList.innerHTML = '<div class="text-xs font-medium text-apple_muted text-center py-4 bg-white/50 rounded-xl border border-black/5">No pinned messages</div>';
            } else {
                pinnedList.innerHTML = pinned.map(m => `
                    <div class="bg-white/80 p-4 rounded-xl border border-black/5 shadow-sm text-sm relative group overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400"></div>
                        <div class="text-[10px] uppercase tracking-wider text-primary mb-1 font-bold">${m.username}</div>
                        <div class="text-apple_text font-medium truncate">${escapeHtml(m.message) || (m.image_url ? '<i class="fas fa-image text-apple_muted mr-1"></i> [Attached Image]' : '<i class="fas fa-calendar-alt text-apple_muted mr-1"></i> [Meeting Invite]')}</div>
                    </div>
                `).join('');
            }
            
            // Update Upcoming Meetings
            const now = new Date();
            const meetings = messages.filter(m => m.meeting_time && new Date(m.meeting_time) > now)
                                     .sort((a,b) => new Date(a.meeting_time) - new Date(b.meeting_time));
            
            const meetingList = document.getElementById('upcomingMeetingsList');
            if (meetings.length === 0) {
                meetingList.innerHTML = '<div class="text-xs font-medium text-apple_muted text-center py-4 bg-white/50 rounded-xl border border-black/5">No upcoming meetings</div>';
            } else {
                meetingList.innerHTML = meetings.map(m => `
                    <div class="bg-white/80 p-4 rounded-xl border border-black/5 shadow-sm text-sm relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                        <div class="font-bold text-apple_text mb-1.5">${escapeHtml(m.message)}</div>
                        <div class="text-[11px] font-semibold text-primary mb-3 bg-blue-50 inline-block px-2 py-1 rounded-md border border-blue-100">${new Date(m.meeting_time).toLocaleString()}</div>
                        <a href="${m.meeting_link}" target="_blank" class="block text-center w-full bg-black/5 hover:bg-black/10 text-apple_text text-xs font-bold py-2 rounded-lg transition-colors"><i class="fas fa-video mr-1.5 text-primary"></i>Join Link</a>
                    </div>
                `).join('');
            }
        }

        function togglePin(msgId) {
            const formData = new FormData();
            formData.append('action', 'pin');
            formData.append('message_id', msgId);
            
            fetch('api_chat.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => { if(d.status === 'success') fetchMessages(); })
            .catch(e => console.error("Pin error:", e));
        }
        
        function deleteMessage(msgId) {
            if(!confirm("Delete this message for everyone?")) return;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('message_id', msgId);
            
            fetch('api_chat.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(d => { 
                if(d.status === 'success') {
                    fetchMessages();
                } else {
                    alert("Error: " + d.message);
                }
            })
            .catch(e => console.error("Delete error:", e));
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msgText = messageInput.value.trim();
            if (!msgText && !selectedFile) return;
            
            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('chat_with', chatWith);
            formData.append('message', msgText);
            if (selectedFile) formData.append('image', selectedFile);
            
            // Reset input UI immediately
            messageInput.value = '';
            clearImage();
            
            fetch('api_chat.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    fetchMessages();
                    setTimeout(() => { messagesDiv.scrollTop = messagesDiv.scrollHeight; }, 100);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error("Send error:", err));
        });
        
        // Handle Meeting Submit
        document.getElementById('meetingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const title = document.getElementById('meetTitle').value.trim();
            const time = document.getElementById('meetTime').value;
            const link = document.getElementById('meetLink').value.trim();
            
            if(!title || !time || !link) return;
            
            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('chat_with', chatWith);
            formData.append('message', title);
            formData.append('meeting_time', time);
            formData.append('meeting_link', link);
            
            closeMeetingModal();
            
            // Clear form
            document.getElementById('meetTitle').value = '';
            document.getElementById('meetTime').value = '';
            document.getElementById('meetLink').value = '';
            
            fetch('api_chat.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    fetchMessages();
                    setTimeout(() => { messagesDiv.scrollTop = messagesDiv.scrollHeight; }, 100);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => console.error("Send error:", err));
        });

        // Helper to prevent XSS
        function escapeHtml(unsafe) {
            return (unsafe || '').toString()
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        // Initial fetch and poll
        fetchMessages();
        setInterval(fetchMessages, 3000);
        
        // Auto scroll on load
        setTimeout(() => {
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }, 500);

    </script>
</body>
</html>
