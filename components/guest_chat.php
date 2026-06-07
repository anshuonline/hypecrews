<!-- Live Chat Widget Component -->
<div id="hc-live-chat" class="fixed bottom-6 left-6 z-[9999] font-sans">
    
    <!-- Chat Button -->
    <button id="hc-chat-btn" onclick="toggleHcChat()" class="w-14 h-14 bg-purple-600 hover:bg-purple-700 text-white rounded-full shadow-2xl shadow-purple-600/30 flex items-center justify-center transition-transform hover:scale-110 relative group">
        <i id="hc-chat-icon" class="fas fa-comment-dots text-2xl transition-transform duration-300"></i>
        <i id="hc-close-icon" class="fas fa-times text-2xl absolute opacity-0 scale-50 transition-all duration-300"></i>
        <span class="absolute -top-1 -right-1 flex h-4 w-4 hidden" id="hc-chat-badge">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white"></span>
        </span>
        <!-- Tooltip -->
        <span class="absolute left-full ml-4 whitespace-nowrap bg-gray-900 text-white text-xs font-bold py-1 px-3 rounded shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            Live Chat Support
            <div class="absolute right-full top-1/2 -mt-1 w-0 h-0 border-4 border-transparent border-r-gray-900"></div>
        </span>
    </button>

    <!-- Chat Window -->
    <div id="hc-chat-window" class="absolute bottom-20 left-0 w-[350px] h-[500px] bg-white rounded-2xl shadow-2xl shadow-purple-900/20 border border-gray-100 flex flex-col overflow-hidden transition-all duration-300 origin-bottom-left scale-0 opacity-0 pointer-events-none">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 p-4 text-white shrink-0 shadow-md relative z-10">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center backdrop-blur text-xl font-bold border border-white/30 shadow-inner">
                        HC
                    </div>
                    <div>
                        <h3 class="font-bold text-lg leading-tight">Hypecrews Support</h3>
                        <p class="text-xs text-purple-100 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse border border-green-200"></span> We typically reply instantly
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Initial Form State (Hidden if session exists) -->
        <div id="hc-chat-form-container" class="flex-1 p-6 overflow-y-auto bg-gray-50/50 hidden">
            <p class="text-sm text-gray-600 mb-5 leading-relaxed">Please fill out the form below to start chatting with our support team.</p>
            <form id="hc-init-form" onsubmit="startHcChat(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="hc-input-name" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-shadow shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" id="hc-input-email" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-shadow shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="hc-input-phone" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-shadow shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">How can we help?</label>
                    <select id="hc-input-topic" class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-shadow shadow-sm appearance-none">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Sales & Pricing">Sales & Pricing</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Billing Issue">Billing Issue</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" id="hc-start-btn" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 rounded-xl text-sm transition-all shadow-lg hover:shadow-xl mt-2 flex items-center justify-center gap-2">
                    <span>Start Chat</span> <i class="fas fa-paper-plane text-xs"></i>
                </button>
            </form>
        </div>

        <!-- Active Chat State -->
        <div id="hc-chat-messages-container" class="flex-1 flex flex-col hidden bg-gray-50/30">
            <div id="hc-messages-area" class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Messages will be appended here via JS -->
            </div>
            
            <!-- Chat Input Area -->
            <div id="hc-chat-input-area" class="p-3 bg-white border-t border-gray-100 shadow-[0_-10px_20px_-10px_rgba(0,0,0,0.05)]">
                <form id="hc-message-form" onsubmit="sendHcMessage(event)" class="relative flex items-center">
                    <input type="text" id="hc-message-input" placeholder="Type your message..." autocomplete="off" class="w-full bg-gray-100 border-transparent focus:bg-white rounded-full pl-5 pr-12 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all">
                    <button type="submit" class="absolute right-2 w-8 h-8 bg-purple-600 hover:bg-purple-700 text-white rounded-full flex items-center justify-center transition-colors shadow-sm focus:outline-none">
                        <i class="fas fa-paper-plane text-[10px]"></i>
                    </button>
                </form>
            </div>
            
            <!-- Resolved State Area -->
            <div id="hc-chat-resolved-area" class="p-4 bg-gray-50 border-t border-gray-200 hidden text-center">
                <p class="text-sm font-bold text-gray-700 mb-1">Chat Resolved</p>
                <p class="text-xs text-gray-500 mb-3">This conversation has been closed.</p>
                <button onclick="startNewChat()" class="text-xs font-bold text-purple-600 hover:text-purple-800 bg-purple-50 hover:bg-purple-100 py-1.5 px-4 rounded-full transition-colors border border-purple-200">Start New Chat</button>
            </div>
        </div>
        
    </div>
</div>

<script>
    let hcSessionToken = localStorage.getItem('hc_chat_token');
    let hcChatPolling = null;
    let hcChatOpen = false;

    // Initialize UI on load
    document.addEventListener('DOMContentLoaded', () => {
        if (hcSessionToken) {
            document.getElementById('hc-chat-form-container').classList.add('hidden');
            document.getElementById('hc-chat-messages-container').classList.remove('hidden');
            document.getElementById('hc-chat-messages-container').classList.add('flex');
            pollHcMessages();
            hcChatPolling = setInterval(pollHcMessages, 3000);
        } else {
            document.getElementById('hc-chat-form-container').classList.remove('hidden');
            document.getElementById('hc-chat-messages-container').classList.add('hidden');
            document.getElementById('hc-chat-messages-container').classList.remove('flex');
        }
    });

    function toggleHcChat() {
        const windowEl = document.getElementById('hc-chat-window');
        const iconBtn = document.getElementById('hc-chat-icon');
        const closeIcon = document.getElementById('hc-close-icon');
        const badge = document.getElementById('hc-chat-badge');
        
        hcChatOpen = !hcChatOpen;
        
        if (hcChatOpen) {
            windowEl.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
            windowEl.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            iconBtn.classList.add('scale-0', 'opacity-0');
            closeIcon.classList.remove('scale-50', 'opacity-0');
            closeIcon.classList.add('scale-100', 'opacity-100');
            badge.classList.add('hidden');
            if (hcSessionToken) scrollToBottomHc();
        } else {
            windowEl.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
            windowEl.classList.remove('scale-100', 'opacity-100', 'pointer-events-auto');
            iconBtn.classList.remove('scale-0', 'opacity-0');
            closeIcon.classList.add('scale-50', 'opacity-0');
            closeIcon.classList.remove('scale-100', 'opacity-100');
        }
    }

    function startHcChat(e) {
        e.preventDefault();
        
        const btn = document.getElementById('hc-start-btn');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Starting...';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'create_session');
        formData.append('name', document.getElementById('hc-input-name').value);
        formData.append('email', document.getElementById('hc-input-email').value);
        formData.append('phone', document.getElementById('hc-input-phone').value);
        formData.append('topic', document.getElementById('hc-input-topic').value);
        
        fetch('api_guest_chat.php', {
            method: 'POST',
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                hcSessionToken = data.token;
                localStorage.setItem('hc_chat_token', hcSessionToken);
                
                document.getElementById('hc-chat-form-container').classList.add('hidden');
                document.getElementById('hc-chat-messages-container').classList.remove('hidden');
                document.getElementById('hc-chat-messages-container').classList.add('flex');
                
                pollHcMessages();
                hcChatPolling = setInterval(pollHcMessages, 3000);
            } else {
                alert(data.message || 'Error starting chat');
                btn.innerHTML = '<span>Start Chat</span> <i class="fas fa-paper-plane text-xs"></i>';
                btn.disabled = false;
            }
        }).catch(err => {
            alert('Connection error');
            btn.innerHTML = '<span>Start Chat</span> <i class="fas fa-paper-plane text-xs"></i>';
            btn.disabled = false;
        });
    }

    function sendHcMessage(e) {
        e.preventDefault();
        const input = document.getElementById('hc-message-input');
        const message = input.value.trim();
        if (!message || !hcSessionToken) return;
        
        input.value = '';
        
        // Optimistic UI Append
        const msgHtml = `
            <div class="flex justify-end animate-fade-in-up">
                <div class="max-w-[85%] bg-purple-600 text-white rounded-2xl rounded-tr-sm px-4 py-2 text-sm shadow-md">
                    ${escapeHtmlHc(message)}
                </div>
            </div>
        `;
        document.getElementById('hc-messages-area').insertAdjacentHTML('beforeend', msgHtml);
        scrollToBottomHc();

        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('message', message);
        
        fetch('api_guest_chat.php', {
            method: 'POST',
            headers: {
                'X-Session-Token': hcSessionToken
            },
            body: formData
        }).then(res => res.json()).then(data => {
            if (data.status !== 'success') {
                console.error('Failed to send message:', data.message);
            }
            pollHcMessages();
        });
    }

    let lastMessageCount = 0;
    function pollHcMessages() {
        if (!hcSessionToken) return;
        
        fetch('api_guest_chat.php?action=get_messages', {
            headers: {
                'X-Session-Token': hcSessionToken
            }
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                
                // Check Session Status
                const inputArea = document.getElementById('hc-chat-input-area');
                const resolvedArea = document.getElementById('hc-chat-resolved-area');
                if (data.session && data.session.status === 'resolved') {
                    inputArea.classList.add('hidden');
                    resolvedArea.classList.remove('hidden');
                    clearInterval(hcChatPolling);
                } else {
                    inputArea.classList.remove('hidden');
                    resolvedArea.classList.add('hidden');
                }

                // Render Messages
                if (data.data && data.data.length > 0) {
                    const area = document.getElementById('hc-messages-area');
                    let html = '';
                    
                    data.data.forEach((m, idx) => {
                        if (m.sender_type === 'system') {
                            html += `
                                <div class="flex justify-center my-4 animate-fade-in">
                                    <div class="bg-gray-100 text-gray-500 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider text-center max-w-[80%]">
                                        ${escapeHtmlHc(m.message)}
                                    </div>
                                </div>
                            `;
                        } else if (m.is_mine) {
                            html += `
                                <div class="flex justify-end mb-2">
                                    <div class="max-w-[85%] bg-purple-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 text-[13px] shadow-md shadow-purple-900/10 leading-relaxed">
                                        ${escapeHtmlHc(m.message)}
                                    </div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="flex justify-start mb-2 items-end gap-2">
                                    <div class="w-6 h-6 rounded-full bg-gray-200 border border-gray-300 shrink-0 overflow-hidden flex items-center justify-center text-[10px] font-bold text-gray-500">
                                        ${m.sender_avatar ? `<img src="uploads/admins/${m.sender_avatar}" class="w-full h-full object-cover">` : '<i class="fas fa-headset"></i>'}
                                    </div>
                                    <div class="max-w-[75%] bg-white border border-gray-100 text-gray-800 rounded-2xl rounded-tl-sm px-4 py-2.5 text-[13px] shadow-sm leading-relaxed">
                                        ${escapeHtmlHc(m.message)}
                                    </div>
                                </div>
                            `;
                        }
                    });
                    
                    area.innerHTML = html;
                    
                    if (data.data.length > lastMessageCount) {
                        scrollToBottomHc();
                        if (!hcChatOpen) {
                            document.getElementById('hc-chat-badge').classList.remove('hidden');
                            // Optional sound effect could go here
                        }
                        lastMessageCount = data.data.length;
                    }
                }
            } else if (data.status === 'error' && data.message === 'Invalid session') {
                startNewChat();
            }
        });
    }

    function scrollToBottomHc() {
        const area = document.getElementById('hc-messages-area');
        if (area) {
            area.scrollTop = area.scrollHeight;
        }
    }

    function startNewChat() {
        localStorage.removeItem('hc_chat_token');
        hcSessionToken = null;
        if (hcChatPolling) clearInterval(hcChatPolling);
        
        document.getElementById('hc-chat-form-container').classList.remove('hidden');
        document.getElementById('hc-chat-messages-container').classList.add('hidden');
        document.getElementById('hc-chat-messages-container').classList.remove('flex');
        document.getElementById('hc-messages-area').innerHTML = '';
        lastMessageCount = 0;
    }

    function escapeHtmlHc(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
    
    /* Custom Scrollbar for Chat */
    #hc-messages-area::-webkit-scrollbar { width: 5px; }
    #hc-messages-area::-webkit-scrollbar-track { background: transparent; }
    #hc-messages-area::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    #hc-messages-area:hover::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); }
</style>
