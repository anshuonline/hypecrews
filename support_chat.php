<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, mobile_number, country, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error");
}
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Chat - Peak Experience</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0B0F19',
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0B0F19; color: #f8fafc; overflow-x: hidden; height: 100vh; display: flex; flex-direction: column; }
        .ambient-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
            background: radial-gradient(circle at 15% 50%, rgba(99, 102, 241, 0.05), transparent 25%),
                        radial-gradient(circle at 85% 30%, rgba(139, 92, 246, 0.05), transparent 25%);
            pointer-events: none;
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }
        
        /* Chat scrollbar */
        .chat-scroll::-webkit-scrollbar { width: 6px; }
        .chat-scroll::-webkit-scrollbar-track { background: transparent; }
        .chat-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .chat-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
        
        .chat-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            transition: all 0.3s ease;
        }
        .chat-input:focus {
            background: rgba(0, 0, 0, 0.2);
            border-color: #8b5cf6;
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.3);
            outline: none;
        }
    </style>
</head>
<body class="antialiased selection:bg-secondary selection:text-white">
    <div class="ambient-bg"></div>
    <?php include 'components/nav.php'; ?>
    
    <div class="pt-28 pb-8 flex-1 relative z-10 flex flex-col h-full overflow-hidden">
        <div class="container mx-auto px-4 lg:px-8 max-w-6xl h-full flex flex-col">
            
            <div class="flex justify-between items-end mb-6 shrink-0">
                <div>
                    <h1 class="font-heading text-3xl md:text-4xl font-black mb-1 text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Support Desk</h1>
                    <p class="text-gray-400 font-light text-sm">Direct line to the Peak Experience team.</p>
                </div>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-6 flex-1 overflow-hidden min-h-[500px]">
                
                <!-- Sidebar Menu -->
                <div class="w-full lg:w-1/4 xl:w-1/5 shrink-0 hidden lg:flex flex-col h-full">
                    <?php 
                    $current_user_page = 'support_chat';
                    include 'components/user_sidebar.php'; 
                    ?>
                </div>
                
                <!-- Chat Interface -->
                <div class="w-full lg:w-3/4 xl:w-4/5 flex-1 flex flex-col glass-card rounded-3xl overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full blur-[60px] pointer-events-none"></div>
                    
                    <!-- Chat Header -->
                    <div class="p-5 border-b border-white/5 flex items-center justify-between bg-black/20 shrink-0 z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center text-secondary shadow-[0_0_15px_rgba(139,92,246,0.2)]">
                                <i class="fas fa-headset text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-white text-lg">Admin Support</h3>
                                <p class="text-xs text-green-400 font-medium flex items-center gap-1.5"><i class="fas fa-circle text-[8px]"></i> We usually reply within minutes</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Messages Area -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-6 space-y-6 z-10">
                        <div class="text-center text-gray-500 font-medium text-sm mt-10">
                            <i class="fas fa-circle-notch fa-spin text-secondary text-2xl mb-3"></i><br>
                            Loading messages...
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="p-5 border-t border-white/5 bg-black/20 shrink-0 z-10">
                        <form id="chatForm" class="flex gap-3">
                            <input type="text" id="messageInput" autocomplete="off" placeholder="Type your message here..." class="flex-1 chat-input rounded-full px-6 py-3.5 text-sm font-medium">
                            <button type="submit" class="bg-secondary hover:bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-[0_0_15px_rgba(139,92,246,0.4)] transition-transform hover:scale-105 shrink-0">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const messagesDiv = document.getElementById('chatMessages');
            const chatForm = document.getElementById('chatForm');
            const messageInput = document.getElementById('messageInput');
            
            let isScrolledToBottom = true;

            messagesDiv.addEventListener('scroll', () => {
                isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 20;
            });

            function fetchMessages() {
                fetch('api_support_chat.php')
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
                    messagesDiv.innerHTML = '<div class="text-center text-gray-500 font-medium text-sm mt-10"><i class="fas fa-hand-sparkles text-3xl mb-3 opacity-50"></i><br>No messages yet. Send a message to start chatting!</div>';
                    return;
                }
                
                const currentHashes = messages.map(m => m.id).join(',');
                if (messagesDiv.dataset.lastHash !== currentHashes) {
                    messagesDiv.dataset.lastHash = currentHashes;
                    let html = '';
                    
                    messages.forEach(msg => {
                        if (msg.is_mine) {
                            html += `
                            <div class="flex justify-end mb-4 group">
                                <div class="max-w-[80%]">
                                    <div class="text-[10px] text-gray-500 mb-1.5 mr-2 text-right">${msg.time}</div>
                                    <div class="bg-primary/20 border border-primary/30 text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words">
                                        ${escapeHtml(msg.message)}
                                    </div>
                                </div>
                            </div>`;
                        } else {
                            const avatarHtml = msg.sender_avatar 
                                ? `<img src="${msg.sender_avatar}" class="w-full h-full object-cover">`
                                : `<i class="fas fa-user-shield"></i>`;
                                
                            html += `
                            <div class="flex justify-start mb-4 group">
                                <div class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center shrink-0 mr-3 mt-auto mb-1 overflow-hidden">
                                    ${avatarHtml}
                                </div>
                                <div class="max-w-[80%]">
                                    <div class="text-[10px] text-gray-500 mb-1.5 ml-2">${msg.sender_name} (Admin), ${msg.time}</div>
                                    <div class="bg-white/5 border border-white/10 text-gray-200 px-5 py-3 rounded-2xl rounded-tl-sm shadow-sm break-words">
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

            // Initial fetch and interval
            fetchMessages();
            setInterval(fetchMessages, 3000);
        });
    </script>
</body>
</html>
