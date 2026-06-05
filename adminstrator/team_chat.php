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
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white overflow-hidden">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include 'components/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col md:flex-row h-screen">
            
            <!-- Chat Contacts Sidebar (Left) -->
            <div class="w-full md:w-80 bg-light border-r border-gray-800 flex flex-col h-1/3 md:h-full shrink-0">
                <div class="p-4 border-b border-gray-800 bg-dark shrink-0 flex items-center justify-between">
                    <h2 class="text-xl font-bold">Chats</h2>
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
            
            <!-- Chat Window (Right) -->
            <div class="flex-1 flex flex-col bg-dark relative h-2/3 md:h-full border-t md:border-t-0 border-gray-800">
                
                <!-- Chat Header -->
                <div class="p-4 bg-light border-b border-gray-800 flex items-center shadow-md shrink-0">
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
                
                <!-- Messages Area -->
                <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-4 space-y-4" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%231e293b\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
                    <!-- Messages loaded via JS -->
                    <div class="text-center text-gray-500 text-sm mt-10">Loading messages...</div>
                </div>
                
                <!-- Chat Input Area -->
                <div class="p-4 bg-light border-t border-gray-800 shrink-0">
                    <form id="chatForm" class="flex gap-2">
                        <input type="hidden" id="chatWith" value="<?php echo htmlspecialchars($chat_with); ?>">
                        <input type="text" id="messageInput" autocomplete="off" placeholder="Type your message here..." class="flex-1 bg-dark border border-gray-700 rounded-full px-6 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                        <button type="submit" class="bg-primary hover:bg-indigo-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-lg transition-transform hover:scale-105 shrink-0">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>

    <!-- AJAX Script -->
    <script>
        const chatWith = document.getElementById('chatWith').value;
        const messagesDiv = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        
        let lastMessageCount = 0;
        let isScrolledToBottom = true;

        messagesDiv.addEventListener('scroll', () => {
            // Check if user is scrolled to the bottom (with a small 10px buffer)
            isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 10;
        });

        function fetchMessages() {
            fetch('api_chat.php?chat_with=' + chatWith)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        renderMessages(data.data);
                    }
                })
                .catch(err => console.error("Error fetching messages:", err));
        }

        function renderMessages(messages) {
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div class="text-center text-gray-500 text-sm mt-10">No messages yet. Say hi!</div>';
                return;
            }
            
            // Only update DOM if new messages arrived to prevent flickering
            if (messages.length !== lastMessageCount) {
                lastMessageCount = messages.length;
                let html = '';
                
                messages.forEach(msg => {
                    const isMine = msg.is_mine;
                    const avatar = msg.profile_image 
                        ? `<img src="../${msg.profile_image}" class="w-8 h-8 rounded-full object-cover">`
                        : `<div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-xs font-bold">${msg.initial}</div>`;
                        
                    if (isMine) {
                        html += `
                        <div class="flex justify-end mb-4">
                            <div class="flex flex-col items-end max-w-[75%]">
                                <span class="text-xs text-gray-400 mb-1 mr-1">You, ${msg.time}</span>
                                <div class="bg-primary text-white p-3 rounded-2xl rounded-tr-none shadow-md break-words">
                                    ${escapeHtml(msg.message)}
                                </div>
                            </div>
                        </div>`;
                    } else {
                        html += `
                        <div class="flex justify-start mb-4">
                            <div class="mr-2 mt-auto mb-1 shrink-0">
                                ${avatar}
                            </div>
                            <div class="flex flex-col items-start max-w-[75%]">
                                <span class="text-xs text-gray-400 mb-1 ml-1">${escapeHtml(msg.username)}, ${msg.time}</span>
                                <div class="bg-gray-700 text-white p-3 rounded-2xl rounded-tl-none shadow-md break-words">
                                    ${escapeHtml(msg.message)}
                                </div>
                            </div>
                        </div>`;
                    }
                });
                
                messagesDiv.innerHTML = html;
                
                // Scroll down if they were already at the bottom
                if (isScrolledToBottom) {
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }
            }
        }

        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const msgText = messageInput.value.trim();
            if (!msgText) return;
            
            // Optimistic UI update could go here, but fetch is fast enough
            messageInput.value = '';
            
            const formData = new FormData();
            formData.append('chat_with', chatWith);
            formData.append('message', msgText);
            
            fetch('api_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    fetchMessages();
                    // Force scroll on new send
                    setTimeout(() => {
                        messagesDiv.scrollTop = messagesDiv.scrollHeight;
                    }, 100);
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
