<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'support_chats';

$admin_id = $_SESSION['admin_id'];

$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Auto delete exported sessions older than 1 hour
$pdo->exec("DELETE FROM support_sessions WHERE exported_at IS NOT NULL AND exported_at < NOW() - INTERVAL 1 HOUR");

try {
    $whereClause = "WHERE 1=1";
    if ($filter === 'open') {
        $whereClause .= " AND s.status = 'open'";
    } else if ($filter === 'resolved') {
        $whereClause .= " AND s.status IN ('resolved', 'archived')";
    } else if ($filter === 'mine') {
        $whereClause .= " AND s.assigned_admin_id = ?";
    }
    
    $params = [];
    if ($filter === 'mine') {
        $params[] = $admin_id;
    }
    
    if (!empty($search)) {
        if (strpos($search, '#') === 0 || is_numeric($search)) {
            $searchId = ltrim($search, '#');
            $whereClause .= " AND s.id = ?";
            $params[] = $searchId;
        } else {
            $whereClause .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.username LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
    }
    
    // Fetch threads (support sessions)
    $stmt = $pdo->prepare("
        SELECT s.id as session_id, s.topic, s.urgency, s.status, s.updated_at as last_activity, s.assigned_admin_id,
        u.id as user_id, u.username, u.first_name, u.last_name, 
        (SELECT message FROM support_chats WHERE session_id = s.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT COUNT(*) FROM support_chats WHERE session_id = s.id AND sender_type = 'user' AND is_read = 0) as unread_count
        FROM support_sessions s
        JOIN users u ON s.user_id = u.id
        $whereClause
        ORDER BY CASE WHEN s.status = 'open' THEN 1 ELSE 2 END ASC, unread_count DESC, s.updated_at DESC, s.id DESC
    ");
    $stmt->execute($params);
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$chat_with = isset($_GET['session']) ? $_GET['session'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Chats - Hypecrews Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#8b5cf6', // Purple for support chat
                        apple_text: '#1d1d1f',
                        apple_muted: '#86868b',
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f5f5f7; overflow: hidden; }
        .glass-bg { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: 0; background: #f5f5f9; }
        .glass-bg::before, .glass-bg::after { content: ''; position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.6; }
        .glass-bg::before { background: #e9d5ff; width: 600px; height: 600px; top: -100px; right: -100px; }
        .glass-bg::after { background: #dbeafe; width: 500px; height: 500px; bottom: -100px; left: 10%; }
        .glass-panel { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.8); }
        .chat-scroll::-webkit-scrollbar { width: 6px; }
        .chat-scroll::-webkit-scrollbar-track { background: transparent; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
    </style>
</head>
<body class="text-apple_text">
    <div class="glass-bg"></div>

    <div class="flex h-[100dvh] relative z-10">
        <!-- Sidebar -->
        <div class="bg-[#000000] h-full flex-shrink-0 z-30 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:flex-row h-full relative overflow-hidden">
            
            <!-- Threads Sidebar -->
            <div class="w-full md:w-80 glass-panel bg-white/70 border-r border-black/5 flex flex-col h-full shrink-0 z-20 shadow-xl <?php echo $chat_with ? 'hidden md:flex' : 'flex'; ?>">
                <div class="p-5 shrink-0 border-b border-black/5">
                    <h2 class="text-2xl font-bold tracking-tight mb-4 text-purple-600"><i class="fas fa-headset mr-2"></i> User Support</h2>
                    <div class="relative">
                        <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                        <input type="text" id="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search ticket # or name..." class="w-full bg-white/50 border border-white rounded-2xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all">
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="?filter=all" class="flex-1 text-center py-1.5 rounded-lg text-[10px] font-bold <?php echo ($filter === 'all') ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50'; ?>">All</a>
                        <a href="?filter=mine" class="flex-1 text-center py-1.5 rounded-lg text-[10px] font-bold <?php echo ($filter === 'mine') ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50'; ?>">Mine</a>
                        <a href="?filter=open" class="flex-1 text-center py-1.5 rounded-lg text-[10px] font-bold <?php echo ($filter === 'open') ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50'; ?>">Open</a>
                        <a href="?filter=resolved" class="flex-1 text-center py-1.5 rounded-lg text-[10px] font-bold <?php echo ($filter === 'resolved') ? 'bg-primary text-white' : 'bg-white border border-gray-200 text-gray-500 hover:bg-gray-50'; ?>">Resolved</a>
                    </div>
                </div>
                
                <div id="threadsSidebar" class="flex-1 overflow-y-auto chat-scroll px-3 py-2 space-y-1">
                    <?php if (empty($threads)): ?>
                        <div class="text-center p-6 text-apple_muted">No support chats yet.</div>
                    <?php else: ?>
                        <?php foreach($threads as $t): ?>
                            <a href="?session=<?php echo $t['session_id']; ?>&filter=<?php echo htmlspecialchars($filter); ?>&search=<?php echo htmlspecialchars($search); ?>" class="flex items-center p-3 rounded-2xl transition-all duration-300 <?php echo $chat_with == $t['session_id'] ? 'bg-primary/10 border border-primary/20 shadow-sm' : 'hover:bg-white/50 border border-transparent'; ?> <?php echo in_array($t['status'], ['resolved', 'archived']) ? 'opacity-60' : ''; ?>">
                                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg mr-3 shrink-0 relative">
                                    <?php echo substr(htmlspecialchars($t['first_name']), 0, 1); ?>
                                    
                                    <?php if ($t['unread_count'] > 0): ?>
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white"><?php echo $t['unread_count']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-0.5">
                                        <p class="font-bold text-[15px] truncate text-apple_text flex items-center gap-1">
                                            #<?php echo $t['session_id']; ?> - <?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name']); ?>
                                        </p>
                                        <span class="text-[10px] font-medium text-apple_muted shrink-0"><?php echo date('M d', strtotime($t['last_activity'])); ?></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <p class="text-[11px] font-medium <?php echo $t['urgency'] === 'urgent' ? 'text-red-500' : ($t['urgency'] === 'normal' ? 'text-emerald-500' : 'text-blue-500'); ?>"><?php echo htmlspecialchars($t['topic']); ?></p>
                                        <?php if(in_array($t['status'], ['resolved', 'archived'])): ?><span class="text-[9px] bg-gray-200 text-gray-500 px-1 rounded">Resolved</span><?php endif; ?>
                                    </div>
                                    <?php if($t['assigned_admin_name']): ?>
                                        <p class="text-[10px] text-purple-600 font-bold mb-0.5"><i class="fas fa-user-check"></i> <?php echo htmlspecialchars($t['assigned_admin_name']); ?></p>
                                    <?php endif; ?>
                                    <p class="text-[12px] text-apple_muted truncate <?php echo $t['unread_count'] > 0 ? 'font-bold text-apple_text' : ''; ?>"><?php echo htmlspecialchars($t['last_message']); ?></p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Chat Window -->
            <div class="flex-1 flex flex-col h-full bg-white/50 relative <?php echo !$chat_with ? 'hidden md:flex' : 'flex'; ?>">
                <?php if (!$chat_with): ?>
                    <div class="flex-1 flex flex-col items-center justify-center text-apple_muted">
                        <i class="fas fa-comments text-6xl mb-4 opacity-20"></i>
                        <p class="text-lg font-medium">Select a session to view support chat.</p>
                    </div>
                <?php else: ?>
                    <?php
                        // Get current session info
                        $s_stmt = $pdo->prepare("SELECT s.*, u.username, u.first_name, u.last_name, u.email, a.username as assigned_admin_name FROM support_sessions s JOIN users u ON s.user_id = u.id LEFT JOIN administrators a ON s.assigned_admin_id = a.id WHERE s.id = ?");
                        $s_stmt->execute([$chat_with]);
                        $session_data = $s_stmt->fetch();
                    ?>
                    <!-- Chat Header -->
                    <div class="px-6 py-4 glass-panel border-b border-black/5 flex items-center justify-between shadow-sm shrink-0 z-10 rounded-none">
                        <div class="flex items-center">
                            <a href="support_chats.php" class="md:hidden mr-4 w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-apple_text">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold mr-3 shrink-0">
                                <?php echo substr(htmlspecialchars($session_data['first_name']), 0, 1); ?>
                            </div>
                            <div class="cursor-pointer hover:bg-black/5 p-2 rounded-lg transition-colors -ml-2" onclick="toggleUserProfile()">
                                <h2 class="text-lg font-bold leading-tight flex items-center gap-2">
                                    <?php echo htmlspecialchars($session_data['first_name'] . ' ' . $session_data['last_name']); ?>
                                    <span class="text-xs bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded font-medium">#<?php echo $session_data['id']; ?></span>
                                </h2>
                                <p class="text-[11px] font-semibold text-apple_muted">
                                    <?php echo htmlspecialchars($session_data['topic']); ?> • 
                                    <span class="<?php echo $session_data['urgency'] === 'urgent' ? 'text-red-500' : ($session_data['urgency'] === 'normal' ? 'text-emerald-500' : 'text-blue-500'); ?>"><?php echo ucfirst($session_data['urgency']); ?></span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <?php if ($session_data['assigned_admin_id']): ?>
                                <span class="bg-purple-100 text-purple-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-purple-200 flex items-center gap-1.5">
                                    <i class="fas fa-user-check"></i> <?php echo htmlspecialchars($session_data['assigned_admin_name']); ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if ($session_data['assigned_admin_id'] != $admin_id): ?>
                                <button onclick="assignChat(<?php echo $session_data['id']; ?>)" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-500 hover:text-white border border-indigo-200 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm flex items-center gap-1.5">
                                    <i class="fas fa-hand-paper"></i> Assign to Me
                                </button>
                            <?php endif; ?>
                            
                            <?php if($session_data['assigned_admin_id'] == $admin_id): ?>
                                <button id="resolveBtn" onclick="resolveSession(<?php echo $session_data['id']; ?>)" class="bg-emerald-500/10 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-500/20 px-4 py-1.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2 <?php echo $session_data['status'] === 'open' ? 'block' : 'hidden'; ?>">
                                    <i class="fas fa-check-circle"></i> Resolve
                                </button>
                                <button id="reopenBtn" onclick="reopenChat(<?php echo $session_data['id']; ?>)" class="bg-amber-500/10 text-amber-600 hover:bg-amber-500 hover:text-white border border-amber-500/20 px-4 py-1.5 rounded-lg text-sm font-bold transition-all shadow-sm flex items-center gap-2 <?php echo $session_data['status'] !== 'open' ? 'block' : 'hidden'; ?>">
                                    <i class="fas fa-undo-alt"></i> Reopen
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Top Admin Notes Banner -->
                    <div id="topAdminNotes" class="<?php echo $session_data['admin_note'] ? 'flex' : 'hidden'; ?> px-6 py-3 bg-yellow-50/80 border-b border-yellow-200 shrink-0 z-10 shadow-sm backdrop-blur-sm relative group">
                        <div class="flex items-start gap-3 w-full">
                            <i class="fas fa-sticky-note text-yellow-500 mt-1"></i>
                            <div class="flex-1" id="topAdminNotesContent">
                                <p class="text-sm text-yellow-800 font-medium"><?php echo nl2br(htmlspecialchars($session_data['admin_note'] ?? '')); ?></p>
                            </div>
                            <button onclick="removeNote(<?php echo $session_data['id']; ?>)" title="Remove Note" class="opacity-0 group-hover:opacity-100 transition-opacity text-yellow-600 hover:text-red-500 p-1">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Messages Area -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-6 space-y-4 relative z-0">
                        <div class="text-center mt-10"><i class="fas fa-spinner fa-spin text-primary text-2xl"></i></div>
                    </div>
                    
                    <!-- Chat Input Container (Dynamic) -->
                    <div id="activeChatInputContainer" class="<?php echo $session_data['status'] === 'open' ? 'block' : 'hidden'; ?>">
                        <?php if($session_data['assigned_admin_id'] == $admin_id): ?>
                        <div class="p-4 md:p-6 glass-panel border-t border-black/5 shrink-0 z-10 rounded-none pb-6">
                            <form id="chatForm" class="flex gap-3">
                                <input type="hidden" id="chatSession" value="<?php echo htmlspecialchars($chat_with); ?>">
                                <div class="relative flex-1">
                                    <input type="text" id="messageInput" autocomplete="off" placeholder="Type reply as Admin..." class="w-full bg-white border border-black/10 rounded-full pl-5 pr-10 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all font-medium">
                                    <label class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-primary transition-colors p-2">
                                        <i class="fas fa-paperclip"></i>
                                        <input type="file" id="attachmentInput" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                                    </label>
                                </div>
                                <button type="submit" class="bg-primary hover:bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-md transition-transform hover:scale-105 shrink-0">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            <div id="attachmentPreview" class="hidden mt-3 text-sm text-primary font-medium items-center gap-2">
                                <i class="fas fa-image"></i> <span id="attachmentName"></span>
                                <button type="button" id="removeAttachment" class="text-red-500 hover:text-red-600 ml-2"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="p-4 md:p-6 glass-panel border-t border-black/5 shrink-0 z-10 rounded-none pb-6 text-center">
                            <p class="text-gray-500 text-sm mb-3">
                                <?php echo $session_data['assigned_admin_id'] ? 'This chat is assigned to another admin.' : 'This chat is not assigned to anyone.'; ?> 
                                You must assign it to yourself to reply.
                            </p>
                            <button onclick="assignChat(<?php echo $session_data['id']; ?>)" class="bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-lg text-sm font-bold transition-all shadow-sm">
                                <i class="fas fa-hand-paper mr-2"></i> Assign to Me
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Resolved Chat Container (Dynamic) -->
                    <div id="resolvedChatContainer" class="<?php echo $session_data['status'] !== 'open' ? 'block' : 'hidden'; ?>">
                    <?php 
                        $time_remaining = -1;
                        if ($session_data['exported_at']) {
                            $exported_time = strtotime($session_data['exported_at']);
                            $time_remaining = max(0, 3600 - (time() - $exported_time));
                            if ($time_remaining <= 0) {
                                $pdo->prepare("DELETE FROM support_sessions WHERE id = ?")->execute([$session_data['id']]);
                                echo "<script>window.location.href='support_chats.php';</script>";
                                exit;
                            }
                        }
                    ?>
                    <div class="p-4 md:p-6 glass-panel border-t border-black/5 shrink-0 z-10 rounded-none pb-6 text-center bg-gray-50/50">
                        <i class="fas fa-lock text-gray-400 text-3xl mb-3"></i>
                        <h4 class="text-lg font-bold text-gray-700 mb-2">This session is resolved and closed.</h4>
                        
                        <?php if ($session_data['admin_exported'] != 1): ?>
                            <p class="text-gray-500 text-sm mb-4">Export the chat history as a PDF. After exporting, a 1-hour deletion countdown will start for both you and the user.</p>
                            <button id="exportPdfBtn" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-sm transition-transform hover:scale-105 mb-4">
                                <i class="fas fa-file-pdf mr-2"></i> Export Chat to PDF
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($time_remaining >= 0): ?>
                            <p class="text-red-500 text-sm mb-3 font-bold">
                                <i class="fas fa-exclamation-triangle"></i> Chat will be auto-deleted in:
                            </p>
                            <div class="text-3xl font-black text-gray-800 tracking-widest mb-4" id="countdownTimer">
                                --:--
                            </div>
                            <script>
                                let timeRemaining = <?php echo $time_remaining; ?>;
                                const timerEl = document.getElementById('countdownTimer');
                                const countdownInterval = setInterval(() => {
                                    if (timeRemaining <= 0) {
                                        clearInterval(countdownInterval);
                                        timerEl.innerText = "00:00";
                                        window.location.reload();
                                    } else {
                                        let m = Math.floor(timeRemaining / 60);
                                        let s = timeRemaining % 60;
                                        timerEl.innerText = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
                                        timeRemaining--;
                                    }
                                }, 1000);
                            </script>
                        <?php endif; ?>
                    </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- User Profile Sidebar (Right) -->
            <?php if ($chat_with): ?>
            <!-- Mobile Overlay -->
            <div id="userProfileOverlay" class="fixed inset-0 bg-black/20 z-40 hidden xl:hidden" onclick="toggleUserProfile()"></div>
            
            <div id="userProfileSidebar" class="fixed inset-y-0 right-0 w-full max-w-sm bg-white/95 backdrop-blur-xl border-l border-black/5 flex flex-col h-full shrink-0 z-50 shadow-2xl overflow-y-auto transform translate-x-full transition-transform duration-300 xl:static xl:translate-x-0 xl:w-80 xl:bg-white/70 xl:shadow-xl xl:z-20 xl:flex">
                <div class="p-5 border-b border-black/5 flex items-center justify-between sticky top-0 bg-white/90 backdrop-blur z-10 shrink-0">
                    <div class="flex items-center gap-2">
                        <button onclick="toggleUserProfile()" class="xl:hidden w-8 h-8 flex items-center justify-center rounded-full bg-black/5 hover:bg-black/10 text-gray-600 transition-colors">
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <h2 class="text-lg font-bold text-gray-800"><i class="fas fa-user-circle mr-2 text-purple-600"></i> User Profile</h2>
                    </div>
                </div>
                
                <div id="userProfileContent" class="p-5 space-y-6">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-primary text-2xl"></i>
                        <p class="text-sm text-gray-500 mt-2">Loading profile...</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Image Lightbox -->
    <div id="imageLightbox" class="fixed inset-0 z-[100] bg-black/90 hidden flex items-center justify-center p-4 backdrop-blur-sm transition-opacity opacity-0">
        <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white/50 hover:text-white text-3xl transition-colors focus:outline-none">
            <i class="fas fa-times"></i>
        </button>
        <img id="lightboxImage" src="" alt="Zoomed Attachment" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl scale-95 transition-transform duration-300">
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        const chatSession = "<?php echo $chat_with ? htmlspecialchars($chat_with) : ''; ?>";
        const chatUserId = "<?php echo $chat_with ? htmlspecialchars($session_data['user_id']) : ''; ?>";
        let currentFilter = "<?php echo htmlspecialchars($filter); ?>";
        let searchQuery = "<?php echo htmlspecialchars($search); ?>";
        const messagesDiv = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const attachmentInput = document.getElementById('attachmentInput');
        const attachmentPreview = document.getElementById('attachmentPreview');
        const attachmentName = document.getElementById('attachmentName');
        const removeAttachment = document.getElementById('removeAttachment');
        const exportPdfBtn = document.getElementById('exportPdfBtn');
        
        let currentMessages = [];
        let isScrolledToBottom = true;
        let currentSessionStatus = '<?php echo isset($session_data) ? htmlspecialchars($session_data['status']) : ''; ?>';
        
        if(messagesDiv) {
            messagesDiv.addEventListener('scroll', () => {
                isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 20;
            });
        }
        
        function fetchThreads() {
            fetch('api_support_chat.php?action=list_threads&filter=' + encodeURIComponent(currentFilter) + '&search=' + encodeURIComponent(searchQuery))
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const sidebar = document.getElementById('threadsSidebar');
                        if (!sidebar) return;
                        
                        let html = '';
                        if (data.data.length === 0) {
                            html = '<div class="text-center p-6 text-apple_muted">No support chats found.</div>';
                        } else {
                            data.data.forEach(t => {
                                const isActive = (t.session_id == chatSession);
                                const bgClass = isActive ? 'bg-primary/10 border-primary/20 shadow-sm' : 'hover:bg-white/50 border-transparent';
                                const opacityClass = (t.status === 'resolved' || t.status === 'archived') ? 'opacity-60' : '';
                                
                                const initial = t.first_name ? t.first_name.charAt(0) : '?';
                                const unreadBadge = t.unread_count > 0 ? `<span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">${t.unread_count}</span>` : '';
                                
                                const urgencyColor = t.urgency === 'urgent' ? 'text-red-500' : (t.urgency === 'normal' ? 'text-emerald-500' : 'text-blue-500');
                                const resolvedBadge = (t.status === 'resolved' || t.status === 'archived') ? '<span class="text-[9px] bg-gray-200 text-gray-500 px-1 rounded">Resolved</span>' : '';
                                const lastMsgBold = t.unread_count > 0 ? 'font-bold text-apple_text' : '';
                                
                                const dateObj = new Date(t.last_activity);
                                const formattedDate = dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                                
                                html += `
                                <a href="?session=${t.session_id}&filter=${encodeURIComponent(currentFilter)}&search=${encodeURIComponent(searchQuery)}" class="flex items-center p-3 rounded-2xl transition-all duration-300 border ${bgClass} ${opacityClass}">
                                    <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg mr-3 shrink-0 relative">
                                        ${initial}
                                        ${unreadBadge}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-baseline mb-0.5">
                                            <p class="font-bold text-[15px] truncate text-apple_text">#${t.session_id} - ${escapeHtml(t.first_name + ' ' + t.last_name)}</p>
                                            <span class="text-[10px] font-medium text-apple_muted shrink-0">${formattedDate}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <p class="text-[11px] font-medium ${urgencyColor}">${escapeHtml(t.topic)}</p>
                                            ${resolvedBadge}
                                        </div>
                                        ${t.assigned_admin_name ? `<p class="text-[10px] text-purple-600 font-bold mb-0.5"><i class="fas fa-user-check"></i> ${escapeHtml(t.assigned_admin_name)}</p>` : ''}
                                        <p class="text-[12px] text-apple_muted truncate ${lastMsgBold}">${escapeHtml(t.last_message || '')}</p>
                                    </div>
                                </a>`;
                            });
                        }
                        if (sidebar.innerHTML !== html) sidebar.innerHTML = html;
                    }
                });
        }

        function fetchMessages() {
            if(!chatSession) return;
            fetch('api_support_chat.php?action=get_messages&session_id=' + chatSession)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.session && data.session.status && data.session.status !== currentSessionStatus && currentSessionStatus !== '') {
                            // Status changed dynamically!
                            currentSessionStatus = data.session.status;
                            const activeContainer = document.getElementById('activeChatInputContainer');
                            const resolvedContainer = document.getElementById('resolvedChatContainer');
                            const resolveBtn = document.getElementById('resolveBtn');
                            const reopenBtn = document.getElementById('reopenBtn');
                            
                            if (currentSessionStatus === 'open') {
                                if(activeContainer) { activeContainer.classList.remove('hidden'); activeContainer.classList.add('block'); }
                                if(resolvedContainer) { resolvedContainer.classList.remove('block'); resolvedContainer.classList.add('hidden'); }
                                if(resolveBtn) { resolveBtn.classList.remove('hidden'); resolveBtn.classList.add('flex'); }
                                if(reopenBtn) { reopenBtn.classList.remove('flex'); reopenBtn.classList.add('hidden'); }
                            } else {
                                if(activeContainer) { activeContainer.classList.remove('block'); activeContainer.classList.add('hidden'); }
                                if(resolvedContainer) { resolvedContainer.classList.remove('hidden'); resolvedContainer.classList.add('block'); }
                                if(resolveBtn) { resolveBtn.classList.remove('flex'); resolveBtn.classList.add('hidden'); }
                                if(reopenBtn) { reopenBtn.classList.remove('hidden'); reopenBtn.classList.add('flex'); }
                            }
                            
                            // Also reload just to refresh buttons if needed, OR we can just update them via JS.
                            // But wait, if they reopen it, the buttons at the top need changing too.
                            // To be perfectly smooth, we can just let it be, or we can reload the page if it's resolved? 
                            // The user requested NO reload. So we just toggle the bottom containers.
                        }
                        renderMessages(data.data);
                    }
                })
                .catch(err => console.error(err));
        }

        function formatMessage(text) {
            if(!text) return '';
            let html = escapeHtml(text);
            html = html.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-500 hover:text-blue-600 hover:underline break-all">$1</a>');
            return html;
        }

        function renderMessages(messages) {
            currentMessages = messages;
            if(!messagesDiv) return;
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div class="text-center text-apple_muted mt-10">No messages yet.</div>';
                return;
            }
            
            const currentHashes = messages.map(m => m.id).join(',');
            if (messagesDiv.dataset.lastHash !== currentHashes) {
                messagesDiv.dataset.lastHash = currentHashes;
                let html = '';
                
                messages.forEach(msg => {
                    let attachmentHtml = msg.attachment ? `<div class="mt-2 rounded-lg overflow-hidden border border-black/10"><img src="../${msg.attachment}" alt="Attachment" class="max-w-full max-h-60 object-contain cursor-pointer hover:opacity-90 transition-opacity" onclick="openLightbox('../${msg.attachment}')"></div>` : '';
                    
                    if (msg.sender_type === 'system') {
                        html += `
                        <div class="flex justify-center my-6">
                            <div class="bg-indigo-500/10 border border-indigo-500/30 text-indigo-500 px-5 py-2.5 rounded-xl text-xs font-bold text-center shadow-sm">
                                <i class="fas fa-info-circle mr-1.5"></i> ${formatMessage(msg.message)}
                            </div>
                        </div>`;
                    } else if (msg.is_mine) {
                        html += `
                        <div class="flex justify-end mb-4 group">
                            <div class="max-w-[75%]">
                                <div class="text-[10px] text-gray-500 mb-1.5 mr-2 text-right">You, ${msg.time}</div>
                                <div class="bg-primary text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words border border-primary/20">
                                    ${formatMessage(msg.message)}
                                    ${attachmentHtml}
                                </div>
                            </div>
                        </div>`;
                    } else {
                        html += `
                        <div class="flex justify-start mb-4 group">
                            <div class="w-8 h-8 rounded-full bg-black/5 border border-black/10 flex items-center justify-center shrink-0 mr-3 mt-auto mb-1 overflow-hidden">
                                <i class="fas fa-user text-xs text-gray-400"></i>
                            </div>
                            <div class="max-w-[75%]">
                                <div class="text-[10px] text-gray-500 mb-1.5 ml-2">${escapeHtml(msg.sender_name)}, ${msg.time}</div>
                                <div class="bg-white border border-black/5 text-apple_text px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm break-words">
                                    ${formatMessage(msg.message)}
                                    ${attachmentHtml}
                                </div>
                            </div>
                        </div>`;
                    }
                });
                
                messagesDiv.innerHTML = html;
                if (isScrolledToBottom) messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        }

        if (attachmentInput) {
            attachmentInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    attachmentName.textContent = this.files[0].name;
                    attachmentPreview.classList.remove('hidden');
                    attachmentPreview.classList.add('flex');
                }
            });
        }

        if (removeAttachment) {
            removeAttachment.addEventListener('click', function() {
                attachmentInput.value = '';
                attachmentPreview.classList.add('hidden');
            });
        }

        if(chatForm) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const msg = messageInput.value.trim();
                const hasAttachment = attachmentInput.files && attachmentInput.files.length > 0;
                
                if (!msg && !hasAttachment) return;
                
                // Optimistic update
                if (msg) {
                    const tempHtml = `
                    <div class="flex justify-end mb-4 group opacity-50">
                        <div class="max-w-[75%]">
                            <div class="text-[10px] text-gray-500 mb-1.5 mr-2 text-right">Sending...</div>
                            <div class="bg-primary text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words border border-primary/20">
                                ${escapeHtml(msg)}
                            </div>
                        </div>
                    </div>`;
                    messagesDiv.insertAdjacentHTML('beforeend', tempHtml);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
                
                const formData = new FormData();
                formData.append('action', 'send_message');
                formData.append('session_id', chatSession);
                formData.append('message', msg);
                if (hasAttachment) formData.append('attachment', attachmentInput.files[0]);
                
                messageInput.value = '';
                if (removeAttachment) removeAttachment.click();
                
                fetch('api_support_chat.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        isScrolledToBottom = true;
                        fetchMessages();
                    } else {
                        alert(data.message || 'Error sending message');
                    }
                });
            });
        }
        
        function resolveSession(sessionId) {
            if(confirm('Are you sure you want to resolve this session?')) {
                const formData = new FormData();
                formData.append('action', 'resolve_session');
                formData.append('session_id', sessionId);
                
                fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.reload();
                    }
                });
            }
        }
        
        function assignChat(sessionId) {
            const formData = new FormData();
            formData.append('action', 'assign_session');
            formData.append('session_id', sessionId);
            
            fetch('api_support_chat.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if(data.status === 'success') {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error assigning chat');
                }
            });
        }
        
        function reopenChat(sessionId) {
            if(confirm('Are you sure you want to reopen this session?')) {
                const formData = new FormData();
                formData.append('action', 'reopen_session');
                formData.append('session_id', sessionId);
                               fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error reopening chat');
                    }
                });
            }
        }
        
        function promptAddNote(sessionId) {
            const note = prompt("Enter your note for this chat. It will only be visible to Admins:");
            if(note !== null) {
                const formData = new FormData();
                formData.append('action', 'save_note');
                formData.append('session_id', sessionId);
                formData.append('note', note);
                
                fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error saving note');
                    }
                });
            }
        }
        
        function removeNote(sessionId) {
            if(confirm('Are you sure you want to remove this note?')) {
                const formData = new FormData();
                formData.append('action', 'save_note');
                formData.append('session_id', sessionId);
                formData.append('note', '');
                
                fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(res => res.json()).then(data => {
                    if(data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error removing note');
                    }
                });
            }
        }

        // Search and Filter functionality
        if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', () => {
                    const sessionId = chatSession;
                    
                    exportPdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Generating PDF...';
                    exportPdfBtn.disabled = true;
                    
                    // Create print container
                    const printContainer = document.createElement('div');
                    printContainer.style.backgroundColor = '#ffffff';
                    printContainer.style.color = '#000000';
                    printContainer.style.fontFamily = 'Arial, sans-serif';
                    printContainer.style.padding = '40px';
                    printContainer.style.width = '100%';
                    printContainer.style.maxWidth = '800px';
                    printContainer.style.margin = '0 auto';
                    
                    let html = `
                        <div style="border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div style="background-color: #000; border-radius: 50%; padding: 10px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <img src="/graphics/logos/hypecrews%20logo%20white.png" style="max-width: 100%; height: auto;">
                                </div>
                                <div>
                                    <h2 style="margin: 0; font-size: 24px; color: #000;">Hypecrews Support</h2>
                                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #555;">support@hypecrews.com | hypecrews.com</p>
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 14px; color: #555;">
                                <p style="margin: 0;"><strong>Session ID:</strong> #${sessionId}</p>
                                <p style="margin: 5px 0 0 0;"><strong>Exported:</strong> ${new Date().toLocaleString()}</p>
                            </div>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 20px;">
                    `;
                    
                    currentMessages.forEach(msg => {
                        const isAdmin = msg.is_mine;
                        const senderName = isAdmin ? 'You (Admin)' : msg.sender_name;
                        const bgColor = isAdmin ? '#e0e7ff' : '#f0f0f0';
                        const align = isAdmin ? 'flex-end' : 'flex-start';
                        const textAlign = isAdmin ? 'right' : 'left';
                        
                        let attachmentHtml = '';
                        if (msg.attachment) {
                            attachmentHtml = `<div style="margin-top: 10px;"><img src="../${msg.attachment}" style="max-width: 300px; max-height: 300px; border: 1px solid #ccc; border-radius: 4px;"></div>`;
                        }
                        
                        let cleanMsg = escapeHtml(msg.message).replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" style="color: #0000EE; text-decoration: underline; word-break: break-all;">$1</a>');
                        
                        html += `
                            <div style="display: flex; flex-direction: column; align-items: ${align}; width: 100%;">
                                <div style="font-size: 12px; color: #666; margin-bottom: 5px; text-align: ${textAlign}; width: 100%;">${escapeHtml(senderName)} • ${msg.time}</div>
                                <div style="background-color: ${bgColor}; padding: 15px; border-radius: 8px; max-width: 80%; border: 1px solid #ddd; color: #000; font-size: 14px; line-height: 1.5;">
                                    ${cleanMsg}
                                    ${attachmentHtml}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                    printContainer.innerHTML = html;
                    
                    const opt = {
                      margin:       10,
                      filename:     'Support_Chat_History_' + sessionId + '.pdf',
                      image:        { type: 'jpeg', quality: 0.98 },
                      html2canvas:  { scale: 2, useCORS: true, logging: false },
                      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                    };
                    
                    html2pdf().set(opt).from(printContainer).save().then(() => {
                        exportPdfBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Downloaded';
                        
                        // Tell server it was exported
                        const formData = new FormData();
                        formData.append('action', 'export_session');
                        formData.append('session_id', sessionId);
                        fetch('api_support_chat.php', {
                            method: 'POST',
                            body: formData
                        }).then(() => {
                            setTimeout(() => {
                                window.location.reload();
                            }, 3000);
                        });
                    });
                });
            }

        function escapeHtml(unsafe) {
            return (unsafe || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchQuery = this.value;
                searchTimeout = setTimeout(() => {
                    fetchThreads();
                    // Update URL silently
                    const url = new URL(window.location);
                    if (searchQuery) url.searchParams.set('search', searchQuery);
                    else url.searchParams.delete('search');
                    window.history.replaceState({}, '', url);
                }, 500);
            });
        }

        fetchThreads();
        setInterval(fetchThreads, 5000);
        
        if (chatSession) {
            fetchMessages();
            setInterval(fetchMessages, 3000);
            
            if (chatUserId) {
                fetchUserProfile();
                // Refresh profile every 60 seconds to check online status
                setInterval(fetchUserProfile, 60000);
            }
        }
        
        function fetchUserProfile() {
            fetch('api_support_chat.php?action=get_user_profile&user_id=' + chatUserId)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const profileDiv = document.getElementById('userProfileContent');
                        const u = data.data.user;
                        
                        let activeDot = u.is_active ? '<span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>' : '<span class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>';
                        let activeText = u.is_active ? '<span class="text-green-600 font-bold text-xs">Online Now</span>' : `<span class="text-gray-500 text-xs">Last seen: ${u.last_active}</span>`;
                        
                        let ordersHtml = '<p class="text-sm text-gray-500">No orders found.</p>';
                        if (data.data.orders.length > 0) {
                            ordersHtml = '<div class="space-y-2">';
                            data.data.orders.forEach(o => {
                                let statusColor = o.status === 'completed' ? 'text-green-600 bg-green-100' : (o.status === 'pending' ? 'text-yellow-600 bg-yellow-100' : 'text-blue-600 bg-blue-100');
                                ordersHtml += `
                                    <div class="border border-black/5 rounded-lg p-2 text-sm flex justify-between items-center">
                                        <div class="truncate mr-2 font-medium">#${o.id} - ${escapeHtml(o.order_title)}</div>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase ${statusColor} shrink-0">${o.status}</span>
                                    </div>
                                `;
                            });
                            ordersHtml += '</div>';
                        }
                        
                        let chatsHtml = '<p class="text-sm text-gray-500">No past chats.</p>';
                        if (data.data.past_chats.length > 0) {
                            chatsHtml = '<div class="space-y-2">';
                            data.data.past_chats.forEach(c => {
                                let statusIcon = c.status === 'open' ? '<i class="fas fa-door-open text-emerald-500"></i>' : '<i class="fas fa-lock text-gray-400"></i>';
                                chatsHtml += `
                                    <a href="?session=${c.id}" class="block border border-black/5 rounded-lg p-2 text-sm hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-bold text-primary">#${c.id}</span>
                                            <span class="text-[10px] text-gray-500">${new Date(c.created_at).toLocaleDateString()}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-700 truncate">
                                            ${statusIcon} ${escapeHtml(c.topic)}
                                        </div>
                                    </a>
                                `;
                            });
                            chatsHtml += '</div>';
                        }
                        
                        let notesHtml = '';
                        let topNotesHtml = '';
                        if (data.data.notes.length > 0) {
                            data.data.notes.forEach(n => {
                                notesHtml += `
                                    <div class="bg-yellow-50 border border-yellow-200 p-3 rounded-lg text-sm mb-2 relative group">
                                        <button onclick="deleteUserNote(${n.id})" class="absolute top-2 right-2 text-yellow-600 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <p class="text-gray-800 whitespace-pre-wrap pr-4">${escapeHtml(n.note)}</p>
                                        <div class="text-[10px] text-gray-500 mt-2 flex justify-between">
                                            <span>By ${escapeHtml(n.admin_name)}</span>
                                            <span>${new Date(n.created_at).toLocaleDateString()}</span>
                                        </div>
                                    </div>
                                `;
                                topNotesHtml += `
                                    <div class="bg-white/50 border border-yellow-300 p-2 rounded text-xs mb-2 shadow-sm">
                                        <p class="text-gray-800 font-medium whitespace-pre-wrap">${escapeHtml(n.note)}</p>
                                        <div class="text-[9px] text-gray-500 mt-1">By ${escapeHtml(n.admin_name)} • ${new Date(n.created_at).toLocaleString()}</div>
                                    </div>
                                `;
                            });
                            
                            const topBanner = document.getElementById('topAdminNotes');
                            if (topBanner) {
                                topBanner.classList.remove('hidden');
                                document.getElementById('topAdminNotesContent').innerHTML = `<div class="max-h-28 overflow-y-auto pr-2">${topNotesHtml}</div>`;
                            }
                        } else {
                            notesHtml = '<p class="text-sm text-gray-500 italic">No notes yet.</p>';
                            const topBanner = document.getElementById('topAdminNotes');
                            if (topBanner) topBanner.classList.add('hidden');
                        }
                        
                        profileDiv.innerHTML = `
                            <!-- User Header -->
                            <div class="text-center mb-6">
                                <div class="w-20 h-20 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-3 relative">
                                    ${u.name.charAt(0)}
                                    ${activeDot}
                                </div>
                                <h3 class="font-bold text-xl text-gray-800">${escapeHtml(u.name)}</h3>
                                <p class="text-sm text-gray-500 mb-2">${escapeHtml(u.email)}</p>
                                ${u.phone !== 'N/A' ? `<p class="text-xs text-gray-600"><i class="fas fa-phone-alt mr-1 text-gray-400"></i> ${escapeHtml(u.phone)}</p>` : ''}
                                ${u.company !== 'N/A' ? `<p class="text-xs text-gray-600 mt-1"><i class="fas fa-building mr-1 text-gray-400"></i> ${escapeHtml(u.company)}</p>` : ''}
                                <div class="mt-1">${activeText}</div>
                            </div>
                            
                            <!-- Location Details -->
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6 text-sm">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-8 h-8 rounded bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs text-gray-500">Location</p>
                                        <p class="font-bold text-gray-700 truncate">${escapeHtml(u.location)}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-200 text-gray-600 flex items-center justify-center shrink-0">
                                        <i class="fas fa-network-wired"></i>
                                    </div>
                                    <div class="truncate">
                                        <p class="text-xs text-gray-500">IP Address</p>
                                        <p class="font-mono font-bold text-gray-700 text-xs">${escapeHtml(u.ip_address)}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order History -->
                            <div class="mb-6">
                                <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-shopping-bag text-primary"></i> Recent Orders</h4>
                                ${ordersHtml}
                            </div>
                            
                            <!-- Past Chats -->
                            <div class="mb-6">
                                <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-history text-primary"></i> Past Tickets</h4>
                                ${chatsHtml}
                            </div>
                            
                            <!-- Admin Notes -->
                            <div class="border-t border-gray-200 pt-5">
                                <h4 class="font-bold text-gray-700 mb-3 flex items-center gap-2"><i class="fas fa-sticky-note text-yellow-500"></i> Admin Notes</h4>
                                <div class="mb-4">
                                    <textarea id="newAdminNote" rows="2" class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none bg-yellow-50/30" placeholder="Add a private note about this user..."></textarea>
                                    <button onclick="addAdminNote()" class="mt-2 w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-1.5 rounded-lg text-sm transition-colors shadow-sm">Save Note</button>
                                </div>
                                <div id="notesContainer" class="max-h-60 overflow-y-auto pr-1">
                                    ${notesHtml}
                                </div>
                            </div>
                        `;
                    }
                });
        }
        
        function addAdminNote() {
            const noteInput = document.getElementById('newAdminNote');
            const note = noteInput.value.trim();
            if (!note) return;
            
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;
            
            const formData = new FormData();
            formData.append('action', 'add_user_note');
            formData.append('user_id', chatUserId);
            formData.append('note', note);
            
            fetch('api_support_chat.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        fetchUserProfile();
                    } else {
                        alert(data.message || 'Error saving note');
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                })
                .catch(() => {
                    alert('Network error while saving note.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        }
        
        function deleteUserNote(noteId) {
            if(confirm('Are you sure you want to delete this note?')) {
                const formData = new FormData();
                formData.append('action', 'delete_user_note');
                formData.append('note_id', noteId);
                
                fetch('api_support_chat.php', { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            fetchUserProfile();
                        } else {
                            alert(data.message || 'Error deleting note');
                        }
                    });
            }
        }
        
        function toggleUserProfile() {
            const sidebar = document.getElementById('userProfileSidebar');
            const overlay = document.getElementById('userProfileOverlay');
            
            if (sidebar.classList.contains('translate-x-full')) {
                // Open
                sidebar.classList.remove('translate-x-full');
                sidebar.classList.remove('hidden'); // Ensure flex takes over if hidden was applied
                overlay.classList.remove('hidden');
            } else {
                // Close
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function openLightbox(src) {
            const lightbox = document.getElementById('imageLightbox');
            const img = document.getElementById('lightboxImage');
            img.src = src;
            lightbox.classList.remove('hidden');
            setTimeout(() => {
                lightbox.classList.remove('opacity-0');
                img.classList.remove('scale-95');
                img.classList.add('scale-100');
            }, 10);
        }

        function closeLightbox() {
            const lightbox = document.getElementById('imageLightbox');
            const img = document.getElementById('lightboxImage');
            lightbox.classList.add('opacity-0');
            img.classList.remove('scale-100');
            img.classList.add('scale-95');
            setTimeout(() => {
                lightbox.classList.add('hidden');
            }, 300);
        }
    </script>
</body>
</html>
