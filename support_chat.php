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

    // Handle new session creation
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_session'])) {
        $topic = trim($_POST['topic']);
        $urgency = trim($_POST['urgency']);
        if (!empty($topic) && in_array($urgency, ['low', 'normal', 'urgent'])) {
            $stmt = $pdo->prepare("INSERT INTO support_sessions (user_id, topic, urgency) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $topic, $urgency]);
            header("Location: support_chat.php");
            exit();
        }
    }

    // Auto delete exported sessions older than 1 hour
    $pdo->exec("DELETE FROM support_sessions WHERE exported_at IS NOT NULL AND exported_at < NOW() - INTERVAL 1 HOUR");

    // Check for active session
    $stmt = $pdo->prepare("SELECT s.*, a.username as assigned_admin_name FROM support_sessions s LEFT JOIN administrators a ON s.assigned_admin_id = a.id WHERE s.user_id = ? AND s.status = 'open' ORDER BY s.created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $active_session = $stmt->fetch();

    // Check for resolved session to show export/timer
    $stmt = $pdo->prepare("SELECT s.*, a.username as assigned_admin_name FROM support_sessions s LEFT JOIN administrators a ON s.assigned_admin_id = a.id WHERE s.user_id = ? AND s.status = 'resolved' ORDER BY s.created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $resolved_session = $stmt->fetch();
    
    if(!$active_session && $resolved_session) {
        $active_session = $resolved_session; // Allow viewing it
    }

    $time_remaining = -1;
    if ($active_session && $active_session['exported_at']) {
        $exported_time = strtotime($active_session['exported_at']);
        $time_remaining = max(0, 3600 - (time() - $exported_time));
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
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
                
                <!-- Chat Interface / New Session Form -->
                <div class="w-full lg:w-3/4 xl:w-4/5 flex-1 flex flex-col glass-card rounded-3xl overflow-hidden relative">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-secondary/10 rounded-full blur-[60px] pointer-events-none"></div>
                    
                    <?php if (!$active_session): ?>
                        <!-- New Session Form -->
                        <div class="flex-1 flex flex-col items-center justify-center p-8 z-10 text-center">
                            <div class="w-20 h-20 rounded-full bg-secondary/20 flex items-center justify-center text-secondary mb-6 shadow-[0_0_30px_rgba(139,92,246,0.3)]">
                                <i class="fas fa-ticket-alt text-3xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-2 font-heading">Start a New Conversation</h2>
                            <p class="text-gray-400 mb-8 max-w-md">Please provide some details about your inquiry so we can route you to the right expert.</p>
                            
                            <form method="POST" class="w-full max-w-md bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm text-left">
                                <input type="hidden" name="create_session" value="1">
                                
                                <div class="mb-5">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">What is this regarding?</label>
                                    <select name="topic" required class="w-full chat-input rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-secondary/50 appearance-none bg-[#151b2b]">
                                        <option value="" disabled selected>Select a topic...</option>
                                        <option value="Copyright Protection">Copyright Protection</option>
                                        <option value="Social Media">Social Media</option>
                                        <option value="Digital Marketing">Digital Marketing</option>
                                        <option value="Web & Development">Web & Development</option>
                                        <option value="Recovery & Support">Recovery & Support</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-300 mb-2">Urgency Level</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="urgency" value="low" class="peer sr-only">
                                            <div class="text-center py-2 px-3 rounded-lg border border-white/10 bg-white/5 text-gray-400 text-xs font-bold transition-all peer-checked:bg-blue-500/20 peer-checked:text-blue-400 peer-checked:border-blue-500/50 hover:bg-white/10">Low</div>
                                        </label>
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="urgency" value="normal" checked class="peer sr-only">
                                            <div class="text-center py-2 px-3 rounded-lg border border-white/10 bg-white/5 text-gray-400 text-xs font-bold transition-all peer-checked:bg-emerald-500/20 peer-checked:text-emerald-400 peer-checked:border-emerald-500/50 hover:bg-white/10">Normal</div>
                                        </label>
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="urgency" value="urgent" class="peer sr-only">
                                            <div class="text-center py-2 px-3 rounded-lg border border-white/10 bg-white/5 text-gray-400 text-xs font-bold transition-all peer-checked:bg-red-500/20 peer-checked:text-red-400 peer-checked:border-red-500/50 hover:bg-white/10">Urgent</div>
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-primary to-secondary text-white font-bold hover:shadow-[0_0_20px_rgba(139,92,246,0.4)] hover:scale-[1.02] transition-all">
                                    Initiate Chat <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </button>
                            </form>
                        </div>
                        
                    <?php else: ?>
                        <!-- Chat Header -->
                        <div class="p-5 border-b border-white/5 flex items-center justify-between bg-black/20 shrink-0 z-10">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-full bg-secondary/20 flex items-center justify-center text-secondary shadow-[0_0_15px_rgba(139,92,246,0.2)]">
                                    <i class="fas fa-headset text-xl"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-white text-lg">Admin Support <span class="text-xs bg-white/10 px-2 py-0.5 rounded text-gray-300">#<?php echo $active_session['id']; ?></span></h3>
                                        <?php if ($active_session['assigned_admin_name']): ?>
                                            <span class="text-[10px] bg-purple-500/20 text-purple-300 border border-purple-500/30 px-2 py-0.5 rounded-full font-bold flex items-center gap-1"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($active_session['assigned_admin_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-xs text-green-400 font-medium flex items-center gap-1.5 mt-0.5">
                                        <i class="fas fa-circle text-[8px]"></i> 
                                        <?php echo htmlspecialchars($active_session['topic']); ?> • 
                                        <span class="uppercase <?php echo $active_session['urgency'] === 'urgent' ? 'text-red-400' : ($active_session['urgency'] === 'normal' ? 'text-emerald-400' : 'text-blue-400'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($active_session['urgency'])); ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chat Messages Area -->
                        <div id="chatMessages" class="flex-1 overflow-y-auto chat-scroll p-6 space-y-6 z-10" data-session-id="<?php echo $active_session['id']; ?>">
                            <div class="text-center text-gray-500 font-medium text-sm mt-10">
                                <i class="fas fa-circle-notch fa-spin text-secondary text-2xl mb-3"></i><br>
                                Loading messages...
                            </div>
                        </div>
                        
                        <?php if($active_session['status'] === 'open'): ?>
                        <!-- Chat Input -->
                        <div class="p-5 border-t border-white/5 bg-black/20 shrink-0 z-10">
                            <form id="chatForm" class="flex gap-3">
                                <input type="hidden" id="sessionId" value="<?php echo $active_session['id']; ?>">
                                <div class="relative flex-1">
                                    <input type="text" id="messageInput" autocomplete="off" placeholder="Type your message here..." class="w-full chat-input rounded-full pl-6 pr-12 py-3.5 text-sm font-medium">
                                    <label class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-secondary transition-colors p-2">
                                        <i class="fas fa-paperclip"></i>
                                        <input type="file" id="attachmentInput" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                                    </label>
                                </div>
                                <button type="submit" class="bg-secondary hover:bg-purple-600 text-white rounded-full w-12 h-12 flex items-center justify-center shadow-[0_0_15px_rgba(139,92,246,0.4)] transition-transform hover:scale-105 shrink-0">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </form>
                            <div id="attachmentPreview" class="hidden mt-3 text-sm text-green-400 font-medium items-center gap-2">
                                <i class="fas fa-image"></i> <span id="attachmentName"></span>
                                <button type="button" id="removeAttachment" class="text-red-400 hover:text-red-300 ml-2"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Export Area -->
                        <div class="p-8 border-t border-white/5 bg-black/20 shrink-0 z-10 text-center">
                            <h4 class="text-lg font-bold text-white mb-2">This session has been resolved.</h4>
                            
                            <?php if ($active_session['user_exported'] != 1): ?>
                                <p class="text-gray-400 text-sm mb-6">You can export the chat history as a PDF. After exporting, the history will be permanently deleted after 1 hour.</p>
                                <button id="exportPdfBtn" class="bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-bold py-3 px-6 rounded-xl shadow-[0_0_20px_rgba(16,185,129,0.3)] transition-transform hover:scale-[1.02] mb-6">
                                    <i class="fas fa-file-pdf mr-2"></i> Export Chat to PDF
                                </button>
                            <?php endif; ?>

                            <?php if ($time_remaining >= 0): ?>
                                <p class="text-red-400 text-sm mb-4 font-bold flex items-center justify-center gap-2">
                                    <i class="fas fa-check-circle text-emerald-400"></i> Chat will be permanently deleted in:
                                </p>
                                <div class="text-4xl font-black font-heading text-white tracking-widest mb-6" id="countdownTimer">
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
                            
                            <div class="flex items-center justify-center gap-4 mt-2">
                                <button onclick="reopenSession()" class="bg-indigo-500/20 hover:bg-indigo-500 text-indigo-400 hover:text-white font-bold py-3 px-6 rounded-xl transition-all border border-indigo-500/30 shadow-[0_0_15px_rgba(99,102,241,0.2)]">
                                    <i class="fas fa-undo-alt mr-2"></i> Reopen Chat
                                </button>
                                <button onclick="startNewChat()" class="bg-primary hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-xl shadow-[0_0_15px_rgba(139,92,246,0.4)] transition-transform hover:scale-105">
                                    <i class="fas fa-plus-circle mr-2"></i> Start New Chat
                                </button>
                            </div>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
            let currentSessionStatus = '<?php echo $active_session ? htmlspecialchars($active_session['status']) : ''; ?>';

            if(messagesDiv) {
                messagesDiv.addEventListener('scroll', () => {
                    isScrolledToBottom = messagesDiv.scrollHeight - messagesDiv.clientHeight <= messagesDiv.scrollTop + 20;
                });
            }

            function fetchMessages() {
                const sessionId = messagesDiv ? messagesDiv.dataset.sessionId : '';
                const url = sessionId ? `api_support_chat.php?session_id=${sessionId}` : 'api_support_chat.php';
                
                fetch(url)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            if (data.session && data.session.status && data.session.status !== currentSessionStatus && currentSessionStatus !== '') {
                                window.location.reload();
                                return;
                            }
                            renderMessages(data.data);
                        }
                    })
                    .catch(err => console.error("Error fetching messages:", err));
            }

            function formatMessage(text) {
                if(!text) return '';
                // Escape HTML first
                let html = escapeHtml(text);
                // Make links clickable
                html = html.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-400 hover:text-blue-300 hover:underline break-all">$1</a>');
                return html;
            }

            function renderMessages(messages) {
                currentMessages = messages;
                if(!messagesDiv) return;
                if (messages.length === 0) {
                    messagesDiv.innerHTML = '<div class="text-center text-gray-500 font-medium text-sm mt-10"><i class="fas fa-hand-sparkles text-3xl mb-3 opacity-50"></i><br>No messages yet. Send a message to start chatting!</div>';
                    return;
                }
                
                const currentHashes = messages.map(m => m.id).join(',');
                if (messagesDiv.dataset.lastHash !== currentHashes) {
                    messagesDiv.dataset.lastHash = currentHashes;
                    let html = '';
                    
                    messages.forEach(msg => {
                        let attachmentHtml = '';
                        if (msg.attachment) {
                            attachmentHtml = `<div class="mt-2 rounded-lg overflow-hidden border border-white/10"><img src="${msg.attachment}" alt="Attachment" class="max-w-full max-h-60 object-contain"></div>`;
                        }
                        
                        if (msg.sender_type === 'system') {
                            html += `
                            <div class="flex justify-center my-6">
                                <div class="bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 px-5 py-2.5 rounded-xl text-xs font-bold text-center shadow-sm">
                                    <i class="fas fa-info-circle mr-1.5"></i> ${formatMessage(msg.message)}
                                </div>
                            </div>`;
                        } else if (msg.is_mine) {
                            html += `
                            <div class="flex justify-end mb-4 group">
                                <div class="max-w-[80%]">
                                    <div class="text-[10px] text-gray-500 mb-1.5 mr-2 text-right">${msg.time}</div>
                                    <div class="bg-primary/20 border border-primary/30 text-white px-5 py-3 rounded-2xl rounded-tr-sm shadow-sm break-words">
                                        ${formatMessage(msg.message)}
                                        ${attachmentHtml}
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
                                        ${formatMessage(msg.message)}
                                        ${attachmentHtml}
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

            if (attachmentInput) {
                attachmentInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        if(this.files[0].size > 2 * 1024 * 1024) {
                            alert('Image size must be less than 2MB');
                            this.value = '';
                            return;
                        }
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
                    attachmentPreview.classList.remove('flex');
                });
            }

            if (chatForm) {
                chatForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const msg = messageInput.value.trim();
                    const hasAttachment = attachmentInput.files && attachmentInput.files.length > 0;
                    
                    if (!msg && !hasAttachment) return;
                    
                    // Optimistic update (text only)
                    if (msg) {
                        const tempHtml = `
                        <div class="flex justify-end mb-4 group opacity-50">
                            <div class="max-w-[85%]">
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
                    formData.append('message', msg);
                    if (hasAttachment) {
                        formData.append('attachment', attachmentInput.files[0]);
                    }
                    
                    messageInput.value = '';
                    if (removeAttachment) removeAttachment.click();
                    
                    fetch('api_support_chat.php', {
                            method: 'POST',
                            body: formData
                        })
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
            
            if (exportPdfBtn) {
                exportPdfBtn.addEventListener('click', () => {
                    const sessionId = messagesDiv.dataset.sessionId;
                    
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
                        const isUser = msg.is_mine;
                        const senderName = isUser ? 'You' : msg.sender_name + ' (Admin)';
                        const bgColor = isUser ? '#f0f0f0' : '#e0e7ff';
                        const align = isUser ? 'flex-end' : 'flex-start';
                        const textAlign = isUser ? 'right' : 'left';
                        
                        let attachmentHtml = '';
                        if (msg.attachment) {
                            attachmentHtml = `<div style="margin-top: 10px;"><img src="${msg.attachment}" style="max-width: 300px; max-height: 300px; border: 1px solid #ccc; border-radius: 4px;"></div>`;
                        }
                        
                        // We use a simple replacement for formatMessage that avoids adding tailwind classes to links
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

            window.startNewChat = function() {
                const messagesDiv = document.getElementById('chatMessages');
                const sessionId = messagesDiv ? messagesDiv.dataset.sessionId : '';
                if (!sessionId) return;
                
                const formData = new FormData();
                formData.append('action', 'dismiss_session');
                formData.append('session_id', sessionId);
                
                fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(() => {
                    window.location.reload();
                });
            }
            
            window.reopenSession = function() {
                const messagesDiv = document.getElementById('chatMessages');
                const sessionId = messagesDiv ? messagesDiv.dataset.sessionId : '';
                if (!sessionId) return;
                
                const formData = new FormData();
                formData.append('action', 'reopen_session');
                formData.append('session_id', sessionId);
                
                fetch('api_support_chat.php', {
                    method: 'POST',
                    body: formData
                }).then(() => {
                    window.location.reload();
                });
            }

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
