<?php
require_once 'auth.php';
require_once '../config/db.php';
$current_page = 'support_chats';

$admin_id = $_SESSION['admin_id'];

// Fetch threads (users who have sent/received messages)
try {
    $stmt = $pdo->prepare("
        SELECT u.id, u.username, u.first_name, u.last_name, u.profile_image, 
        (SELECT message FROM support_chats WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_message,
        (SELECT created_at FROM support_chats WHERE user_id = u.id ORDER BY created_at DESC LIMIT 1) as last_activity,
        (SELECT COUNT(*) FROM support_chats WHERE user_id = u.id AND sender_type = 'user' AND is_read = 0) as unread_count
        FROM users u
        WHERE EXISTS (SELECT 1 FROM support_chats WHERE user_id = u.id)
        ORDER BY last_activity DESC
    ");
    $stmt->execute();
    $threads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$chat_with = isset($_GET['user']) ? $_GET['user'] : null;
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
                        <input type="text" placeholder="Search users..." class="w-full bg-white/50 border border-white rounded-2xl pl-11 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all">
                    </div>
                </div>
                
                <div class="flex-1 overflow-y-auto chat-scroll px-3 py-2 space-y-1">
                    <?php if (empty($threads)): ?>
                        <div class="text-center p-6 text-apple_muted">No support chats yet.</div>
                    <?php else: ?>
                        <?php foreach($threads as $t): ?>
                            <a href="?user=<?php echo $t['id']; ?>" class="flex items-center p-3 rounded-2xl transition-all duration-300 <?php echo $chat_with == $t['id'] ? 'bg-primary/10 border border-primary/20 shadow-sm' : 'hover:bg-white/50 border border-transparent'; ?>">
                                <div class="w-12 h-12 rounded-full bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold text-lg mr-3 shrink-0 relative">
                                    <?php if ($t['profile_image']): ?>
                                        <img src="../<?php echo htmlspecialchars($t['profile_image']); ?>" class="w-full h-full rounded-full object-cover">
                                    <?php else: ?>
                                        <?php echo substr(htmlspecialchars($t['first_name']), 0, 1); ?>
                                    <?php endif; ?>
                                    
                                    <?php if ($t['unread_count'] > 0): ?>
                                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white"><?php echo $t['unread_count']; ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-baseline mb-0.5">
                                        <p class="font-bold text-[15px] truncate text-apple_text"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name']); ?></p>
                                        <span class="text-[10px] font-medium text-apple_muted shrink-0"><?php echo date('M d', strtotime($t['last_activity'])); ?></span>
                                    </div>
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
                        <p class="text-lg font-medium">Select a user to view support chat.</p>
                    </div>
                <?php else: ?>
                    <?php
                        // Get current user info
                        $u_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $u_stmt->execute([$chat_with]);
                        $chat_user = $u_stmt->fetch();
                    ?>
                    <!-- Chat Header -->
                    <div class="px-6 py-4 glass-panel border-b border-black/5 flex items-center shadow-sm shrink-0 z-10 rounded-none">
                        <a href="support_chats.php" class="md:hidden mr-4 w-10 h-10 rounded-full bg-black/5 flex items-center justify-center text-apple_text">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center font-bold mr-3 shrink-0">
                                <?php if ($chat_user['profile_image']): ?>
                                    <img src="../<?php echo htmlspecialchars($chat_user['profile_image']); ?>" class="w-full h-full rounded-full object-cover">
                                <?php else: ?>
                                    <?php echo substr(htmlspecialchars($chat_user['first_name']), 0, 1); ?>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold leading-tight"><?php echo htmlspecialchars($chat_user['first_name'] . ' ' . $chat_user['last_name']); ?></h2>
                                <p class="text-[11px] font-semibold text-apple_muted">@<?php echo htmlspecialchars($chat_user['username']); ?> • <?php echo htmlspecialchars($chat_user['email']); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages Area -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-6 space-y-4 relative z-0">
                        <div class="text-center mt-10"><i class="fas fa-spinner fa-spin text-primary text-2xl"></i></div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="p-4 md:p-6 glass-panel border-t border-black/5 shrink-0 z-10 rounded-none pb-6">
                        <form id="chatForm" class="flex gap-3">
                            <input type="hidden" id="chatWith" value="<?php echo htmlspecialchars($chat_with); ?>">
                            <input type="text" id="messageInput" autocomplete="off" placeholder="Type reply as Admin..." class="flex-1 bg-white border border-black/10 rounded-full px-5 py-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 shadow-sm transition-all font-medium">
                            <button type="submit" class="bg-primary hover:bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-md transition-transform hover:scale-105 shrink-0">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php if ($chat_with): ?>
    <script>
        const chatWith = document.getElementById('chatWith').value;
        const messagesDiv = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        
        let isScrolledToBottom = true;
        messagesDiv.addEventListener('scroll', () => {
            isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 20;
        });

        function fetchMessages() {
            fetch('api_support_chat.php?action=get_messages&user_id=' + chatWith)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMessages(data.data);
                    }
                })
                .catch(err => console.error("Error fetching messages:", err));
        }

        function renderMessages(messages) {
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div class="text-center text-apple_muted mt-10">No messages yet.</div>';
                return;
            }
            
            const currentHashes = messages.map(m => m.id).join(',');
            if (messagesDiv.dataset.lastHash !== currentHashes) {
                messagesDiv.dataset.lastHash = currentHashes;
                let html = '';
                
                messages.forEach(msg => {
                    if (msg.is_mine) {
                        // Admin sent it
                        html += `
                        <div class="flex justify-end mb-4 group">
                            <div class="max-w-[75%]">
                                <div class="text-[10px] text-gray-500 mb-1.5 mr-2 text-right">You, ${msg.time}</div>
                                <div class="bg-primary text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words border border-primary/20">
                                    ${escapeHtml(msg.message)}
                                </div>
                            </div>
                        </div>`;
                    } else {
                        // User sent it
                        html += `
                        <div class="flex justify-start mb-4 group">
                            <div class="w-8 h-8 rounded-full bg-black/5 border border-black/10 flex items-center justify-center shrink-0 mr-3 mt-auto mb-1 overflow-hidden">
                                <i class="fas fa-user text-xs text-gray-400"></i>
                            </div>
                            <div class="max-w-[75%]">
                                <div class="text-[10px] text-gray-500 mb-1.5 ml-2">${escapeHtml(msg.sender_name)}, ${msg.time}</div>
                                <div class="bg-white border border-black/5 text-apple_text px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm break-words">
                                    ${escapeHtml(msg.message)}
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

        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const msg = messageInput.value.trim();
            if (!msg) return;
            
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('user_id', chatWith);
            formData.append('message', msg);
            
            messageInput.value = '';
            
            fetch('api_support_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    isScrolledToBottom = true;
                    fetchMessages();
                }
            });
        });

        function escapeHtml(unsafe) {
            return (unsafe || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        }

        fetchMessages();
        setInterval(fetchMessages, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
