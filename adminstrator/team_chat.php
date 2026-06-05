<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'team_chat';

$admin_id = $_SESSION['admin_id'];

// Fetch all other admins
try {
    $stmt = $pdo->prepare("SELECT id, username, profile_image FROM administrators WHERE id != ? ORDER BY username ASC");
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
        
        /* Custom Scrollbar for chat */
        .chat-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .chat-scroll::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5); 
        }
        .chat-scroll::-webkit-scrollbar-thumb {
            background: #475569; 
            border-radius: 10px;
        }
        .chat-scroll::-webkit-scrollbar-thumb:hover {
            background: #64748b; 
        }

        .link-text {
            color: #60a5fa;
            text-decoration: underline;
        }
        
        .image-preview {
            max-width: 250px;
            max-height: 250px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 5px;
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white overflow-hidden">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:flex-row h-screen relative overflow-hidden">
            
            <!-- Mobile Contacts Overlay Background -->
            <div id="contacts-overlay" class="fixed inset-0 bg-black/60 z-10 hidden md:hidden backdrop-blur-sm" onclick="toggleContacts()"></div>
            
            <!-- Chat Contacts Sidebar (Left) -->
            <div id="contacts-sidebar" class="absolute md:relative z-20 w-full md:w-80 bg-light border-r border-gray-800 flex flex-col h-full shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300">
                <div class="p-4 border-b border-gray-800 bg-dark shrink-0 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Chats</h2>
                    <button class="md:hidden text-gray-400 hover:text-white" onclick="toggleContacts()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto chat-scroll p-2 space-y-1">
                    <!-- Group Chat Option -->
                    <a href="?chat=group" class="flex items-center p-3 rounded-lg transition-colors <?php echo $chat_with === 'group' ? 'bg-primary/20 border border-primary/50' : 'hover:bg-gray-800'; ?>">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-4 shadow-lg shrink-0">
                            <i class="fas fa-users text-white"></i>
                        </div>
                        <div>
                            <p class="font-bold text-white">Team Group Chat</p>
                            <p class="text-xs text-gray-400">Chat with all admins</p>
                        </div>
                    </a>
                    
                    <div class="pt-4 pb-2 px-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Direct Messages</p>
                    </div>
                    
                    <!-- Other Admins -->
                    <?php if (!empty($other_admins)): ?>
                        <?php foreach($other_admins as $oa): ?>
                            <a href="?chat=<?php echo $oa['id']; ?>" class="flex items-center p-3 rounded-lg transition-colors <?php echo $chat_with == $oa['id'] ? 'bg-primary/20 border border-primary/50' : 'hover:bg-gray-800'; ?>">
                                <?php if (!empty($oa['profile_image'])): ?>
                                    <div class="w-12 h-12 rounded-full mr-4 overflow-hidden border border-gray-600 bg-dark shrink-0">
                                        <img src="../<?php echo htmlspecialchars($oa['profile_image']); ?>" class="w-full h-full object-cover">
                                    </div>
                                <?php else: ?>
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-gray-700 to-gray-600 flex items-center justify-center mr-4 shadow-lg shrink-0 text-lg font-bold">
                                        <?php echo substr(htmlspecialchars($oa['username']), 0, 1); ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <p class="font-medium text-white"><?php echo htmlspecialchars($oa['username']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 p-3">No other admins found.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat Window (Center) -->
            <div class="flex-1 flex flex-col bg-dark relative h-full border-t md:border-t-0 border-gray-800 w-full min-w-0">
                
                <!-- Chat Header -->
                <div class="p-4 bg-light border-b border-gray-800 flex items-center justify-between shadow-md shrink-0 w-full">
                    <div class="flex items-center">
                        <button class="md:hidden mr-3 text-gray-400 hover:text-white" onclick="toggleContacts()">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <?php 
                        $chat_title = "Team Group Chat";
                        $chat_icon = '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3 shadow-lg"><i class="fas fa-users text-white"></i></div>';
                        
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
                                if (!empty($dm_user['profile_image'])) {
                                    $chat_icon = '<div class="w-10 h-10 rounded-full mr-3 overflow-hidden border border-gray-600 bg-dark shrink-0"><img src="../' . htmlspecialchars($dm_user['profile_image']) . '" class="w-full h-full object-cover"></div>';
                                } else {
                                    $chat_icon = '<div class="w-10 h-10 rounded-full bg-gradient-to-br from-gray-700 to-gray-600 flex items-center justify-center mr-3 shadow-lg shrink-0 font-bold">' . substr($chat_title, 0, 1) . '</div>';
                                }
                            }
                        }
                        ?>
                        <?php echo $chat_icon; ?>
                        <div>
                            <h2 class="text-lg font-bold text-white"><?php echo $chat_title; ?></h2>
                            <p class="text-xs text-green-400"><i class="fas fa-circle text-[8px] mr-1"></i> Online</p>
                        </div>
                    </div>
                    
                    <!-- Toggle Details Button -->
                    <button class="text-gray-400 hover:text-white" onclick="toggleDetails()">
                        <i class="fas fa-info-circle text-xl"></i>
                    </button>
                </div>
                
                <!-- Messages Area -->
                <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-4 space-y-4" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%231e293b\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    <!-- Messages loaded via JS -->
                    <div class="text-center text-gray-500 text-sm mt-10">Loading messages...</div>
                </div>
                
                <!-- Image Preview Area -->
                <div id="imagePreviewArea" class="hidden p-2 bg-dark border-t border-gray-800 flex items-center justify-between">
                    <div class="flex items-center">
                        <img id="previewImg" src="" class="h-12 rounded border border-gray-700 mr-3">
                        <span class="text-xs text-gray-400">Attached Image</span>
                    </div>
                    <button type="button" class="text-red-400 hover:text-red-300" onclick="clearImage()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Chat Input Area -->
                <div class="p-3 bg-light border-t border-gray-800 shrink-0 w-full pb-safe">
                    <form id="chatForm" class="flex gap-2 items-center">
                        <input type="hidden" id="chatWith" value="<?php echo htmlspecialchars($chat_with); ?>">
                        
                        <!-- Attachments & Meetings -->
                        <div class="flex gap-1 shrink-0">
                            <label class="cursor-pointer text-gray-400 hover:text-white p-2 rounded-full hover:bg-gray-700 transition-colors">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="imageInput" class="hidden" accept="image/png, image/jpeg, image/webp" onchange="previewImage(this)">
                            </label>
                            <button type="button" onclick="openMeetingModal()" class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-gray-700 transition-colors">
                                <i class="fas fa-calendar-alt"></i>
                            </button>
                        </div>
                        
                        <input type="text" id="messageInput" autocomplete="off" placeholder="Message or paste a link..." class="flex-1 bg-dark border border-gray-700 rounded-full px-4 md:px-6 py-2 md:py-3 text-sm md:text-base text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <button type="submit" class="bg-primary hover:bg-indigo-600 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center shadow-lg transition-transform hover:scale-105 shrink-0">
                            <i class="fas fa-paper-plane text-sm md:text-base"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Details Sidebar (Right) - Upcoming Meetings & Pins -->
            <div id="details-overlay" class="fixed inset-0 bg-black/60 z-10 hidden backdrop-blur-sm" onclick="toggleDetails()"></div>
            <div id="details-sidebar" class="absolute z-30 w-full md:w-80 bg-light border-l border-gray-800 flex flex-col h-full shrink-0 transform translate-x-full transition-transform duration-300 right-0 top-0 bottom-0 shadow-2xl">
                <div class="p-4 border-b border-gray-800 bg-dark shrink-0 flex items-center justify-between">
                    <h2 class="text-lg font-bold"><i class="fas fa-info-circle mr-2 text-primary"></i>Chat Details</h2>
                    <button class="text-gray-400 hover:text-white" onclick="toggleDetails()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="flex-1 overflow-y-auto chat-scroll p-4 space-y-6">
                    
                    <!-- Pinned Messages Section -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3"><i class="fas fa-thumbtack mr-2"></i>Pinned Messages</h3>
                        <div id="pinnedMessagesList" class="space-y-3">
                            <div class="text-sm text-gray-600 text-center py-4">Loading pins...</div>
                        </div>
                    </div>
                    
                    <hr class="border-gray-800">
                    
                    <!-- Upcoming Meetings Section -->
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3"><i class="fas fa-calendar-check mr-2"></i>Upcoming Meetings</h3>
                        <div id="upcomingMeetingsList" class="space-y-3">
                            <div class="text-sm text-gray-600 text-center py-4">Loading meetings...</div>
                        </div>
                    </div>
                    
                </div>
            </div>
            
        </div>
    </div>

    <!-- Schedule Meeting Modal -->
    <div id="meetingModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-light rounded-xl w-full max-w-md shadow-2xl border border-gray-800 overflow-hidden transform scale-95 transition-transform duration-300" id="meetingModalContent">
            <div class="p-4 bg-dark border-b border-gray-800 flex justify-between items-center">
                <h3 class="font-bold text-lg"><i class="fas fa-calendar-plus text-primary mr-2"></i>Schedule Meeting</h3>
                <button onclick="closeMeetingModal()" class="text-gray-400 hover:text-white"><i class="fas fa-times"></i></button>
            </div>
            <form id="meetingForm" class="p-5 space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Meeting Title/Message</label>
                    <input type="text" id="meetTitle" required class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Date & Time</label>
                    <input type="datetime-local" id="meetTime" required class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" style="color-scheme: dark;">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Meeting Link (Zoom, Meet, etc)</label>
                    <input type="url" id="meetLink" required class="w-full bg-dark border border-gray-700 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-primary" placeholder="https://zoom.us/j/...">
                </div>
                <div class="pt-4">
                    <button type="submit" class="w-full bg-primary hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Send Invitation
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Zoom Image Modal -->
    <div id="zoomModal" class="fixed inset-0 bg-black/90 z-50 hidden flex items-center justify-center p-4" onclick="closeZoom()">
        <img id="zoomImg" src="" class="max-w-full max-h-full object-contain shadow-2xl rounded">
        <button class="absolute top-4 right-4 text-white text-3xl opacity-70 hover:opacity-100"><i class="fas fa-times"></i></button>
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
        }
        function closeZoom() {
            document.getElementById('zoomModal').classList.add('hidden');
        }
        
        function openMeetingModal() {
            document.getElementById('meetingModal').classList.remove('hidden');
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
                return `<a href="${url}" target="_blank" class="link-text">${url}</a>`;
            });
        }

        function fetchMessages() {
            fetch('api_chat.php?chat_with=' + chatWith)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMessages(data.data);
                        updateDetailsPanels(data.data);
                    }
                })
                .catch(err => console.error("Error fetching messages:", err));
        }

        function renderMessages(messages) {
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div class="text-center text-gray-500 text-sm mt-10">No messages yet. Say hi!</div>';
                return;
            }
            
            // Check if there are new messages or a pin state changed
            const currentHashes = messages.map(m => m.id + '_' + m.is_pinned).join(',');
            if (messagesDiv.dataset.lastHash !== currentHashes) {
                messagesDiv.dataset.lastHash = currentHashes;
                let html = '';
                
                messages.forEach(msg => {
                    const isMine = msg.is_mine;
                    const isPinned = parseInt(msg.is_pinned) === 1;
                    const isDeleted = parseInt(msg.is_deleted) === 1;
                    
                    const avatar = msg.profile_image 
                        ? `<img src="../${msg.profile_image}" class="w-8 h-8 rounded-full object-cover">`
                        : `<div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-xs font-bold">${msg.initial}</div>`;
                    
                    // Format message content
                    let contentHtml = '';
                    
                    if (isDeleted) {
                        contentHtml = `<div class="text-gray-400/80 italic text-sm"><i class="fas fa-ban mr-1"></i> This message was deleted</div>`;
                    } else {
                        // Pin badge
                        if (isPinned) {
                            contentHtml += `<div class="text-[10px] text-amber-400 mb-1 font-bold flex items-center"><i class="fas fa-thumbtack mr-1"></i> Pinned</div>`;
                        }
                        
                        // Text with links
                        if (msg.message) {
                            contentHtml += `<div>${linkify(escapeHtml(msg.message))}</div>`;
                        }
                        
                        // Attached Image
                        if (msg.image_url) {
                            contentHtml += `<img src="../${msg.image_url}" class="image-preview" onclick="zoomImage(this.src)">`;
                        }
                        
                        // Meeting Card
                        if (msg.meeting_time) {
                            const mDate = new Date(msg.meeting_time);
                            contentHtml += `
                            <div class="mt-2 bg-dark/50 border border-gray-600 rounded-lg p-3 w-64">
                                <div class="flex items-center text-primary font-bold mb-2">
                                    <i class="fas fa-calendar-alt mr-2 text-lg"></i>
                                    Meeting Invite
                                </div>
                                <div class="text-xs text-gray-300 mb-3 font-mono bg-black/30 p-2 rounded">
                                    ${mDate.toLocaleString()}
                                </div>
                                <a href="${msg.meeting_link}" target="_blank" class="block text-center w-full bg-green-600 hover:bg-green-500 text-white text-sm font-bold py-2 rounded transition-colors">
                                    <i class="fas fa-video mr-1"></i> Join Meeting
                                </a>
                            </div>`;
                        }
                    }
                    
                    // Pin & Delete Buttons
                    const pinBtn = !isDeleted ? `<button onclick="togglePin(${msg.id})" class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-500 hover:text-amber-400 ${isPinned ? '!text-amber-400 !opacity-100' : ''}"><i class="fas fa-thumbtack"></i></button>` : '';
                    
                    let deleteBtn = '';
                    if (isMine && !isDeleted) {
                        // Using ISO string to reliably parse date
                        // MySQL format: YYYY-MM-DD HH:MM:SS -> replace space with T
                        const tStr = msg.created_at.replace(' ', 'T');
                        const msgTime = new Date(tStr).getTime();
                        const now = new Date().getTime();
                        if (now - msgTime <= 10000) { // 10 seconds
                            deleteBtn = `<button onclick="deleteMessage(${msg.id})" class="opacity-0 group-hover:opacity-100 transition-opacity text-gray-500 hover:text-red-400 ml-2"><i class="fas fa-trash"></i></button>`;
                        }
                    }

                    if (isMine) {
                        html += `
                        <div class="flex justify-end mb-4 group">
                            <div class="flex items-end max-w-[85%]">
                                <div class="mr-3 mb-2 shrink-0 flex items-center">
                                    ${pinBtn}
                                    ${deleteBtn}
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-xs text-gray-400 mb-1 mr-1">You, ${msg.time}</span>
                                    <div class="bg-primary text-white p-3 rounded-2xl rounded-tr-none shadow-md break-words">
                                        ${contentHtml}
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    } else {
                        html += `
                        <div class="flex justify-start mb-4 group">
                            <div class="flex items-end max-w-[85%]">
                                <div class="mr-2 mt-auto mb-1 shrink-0">
                                    ${avatar}
                                </div>
                                <div class="flex flex-col items-start">
                                    <span class="text-xs text-gray-400 mb-1 ml-1">${escapeHtml(msg.username)}, ${msg.time}</span>
                                    <div class="bg-gray-700 text-white p-3 rounded-2xl rounded-tl-none shadow-md break-words">
                                        ${contentHtml}
                                    </div>
                                </div>
                                <div class="ml-3 mb-2 shrink-0">
                                    ${pinBtn}
                                </div>
                            </div>
                        </div>`;
                    }
                });
                
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
                pinnedList.innerHTML = '<div class="text-xs text-gray-500 text-center py-2">No pinned messages</div>';
            } else {
                pinnedList.innerHTML = pinned.map(m => `
                    <div class="bg-dark p-3 rounded-lg border border-gray-700 text-sm">
                        <div class="text-xs text-primary mb-1 font-bold">${m.username}</div>
                        <div class="text-gray-300 truncate">${escapeHtml(m.message) || (m.image_url ? '[Image]' : '[Meeting]')}</div>
                    </div>
                `).join('');
            }
            
            // Update Upcoming Meetings
            const now = new Date();
            const meetings = messages.filter(m => m.meeting_time && new Date(m.meeting_time) > now)
                                     .sort((a,b) => new Date(a.meeting_time) - new Date(b.meeting_time));
            
            const meetingList = document.getElementById('upcomingMeetingsList');
            if (meetings.length === 0) {
                meetingList.innerHTML = '<div class="text-xs text-gray-500 text-center py-2">No upcoming meetings</div>';
            } else {
                meetingList.innerHTML = meetings.map(m => `
                    <div class="bg-dark p-3 rounded-lg border border-gray-700 text-sm">
                        <div class="font-bold text-white mb-1">${escapeHtml(m.message)}</div>
                        <div class="text-xs text-amber-400 mb-2 font-mono">${new Date(m.meeting_time).toLocaleString()}</div>
                        <a href="${m.meeting_link}" target="_blank" class="text-xs text-green-400 hover:text-green-300"><i class="fas fa-video mr-1"></i>Join Link</a>
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
