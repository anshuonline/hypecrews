<!-- Anime.js for smooth animations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<!-- Cinematic Intro Overlay -->
<div id="hc-intro-overlay" class="fixed inset-0 z-[9998] bg-gray-900/80 backdrop-blur-xl flex flex-col items-center justify-center opacity-0 pointer-events-none hidden">
    <div id="hc-intro-text-1" class="absolute text-3xl md:text-5xl font-bold text-white tracking-tight opacity-0">Welcome to Hypecrews</div>
    <div id="hc-intro-text-2" class="absolute text-3xl md:text-5xl font-bold text-white tracking-tight opacity-0 text-center px-4">We Elevate Your Digital Presence</div>
    <div id="hc-intro-text-3" class="absolute text-4xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500 opacity-0">Let's Chat!</div>
</div>

<!-- Live Chat Widget Component -->
<div id="hc-live-chat" class="fixed bottom-6 right-6 z-[9999] font-sans">
    
    <!-- Apple Style Floating Bubble -->
    <div id="hc-floating-bubble" class="absolute right-[calc(100%+16px)] bottom-2 whitespace-nowrap bg-white/95 backdrop-blur-xl text-gray-800 text-[13px] font-medium tracking-tight py-3 px-5 rounded-[20px] rounded-br-[4px] shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-gray-100 origin-bottom-right transition-all duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] scale-0 opacity-0 flex flex-col gap-1 items-start cursor-pointer hover:bg-gray-50/50 group/bubble" onclick="toggleHcChat()">
        <!-- Close button -->
        <button onclick="event.stopPropagation(); closeHcBubble()" class="absolute -top-2 -right-2 w-5 h-5 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-700 shadow-sm z-10 transition-colors opacity-0 group-hover/bubble:opacity-100">
            <i class="fas fa-times text-[9px]"></i>
        </button>
        <div class="flex items-center gap-2 mb-0.5">
            <div class="w-1.5 h-1.5 rounded-full bg-[#34c759] animate-pulse shrink-0"></div>
            <span class="font-bold text-[#007aff] text-[11px] uppercase tracking-wider">Support</span>
        </div>
        <span id="hc-bubble-text" class="transition-opacity duration-300">Hey, need help for your business? 👋</span>
    </div>

    <!-- Chat Button -->
    <button id="hc-chat-btn" onclick="toggleHcChat()" class="w-14 h-14 bg-[#007aff] hover:bg-[#005bb5] text-white rounded-full shadow-2xl shadow-[#007aff]/30 flex items-center justify-center transition-transform hover:scale-110 relative group">
        <i id="hc-chat-icon" class="fas fa-comment-dots text-2xl transition-transform duration-300"></i>
        <i id="hc-close-icon" class="fas fa-times text-2xl absolute opacity-0 scale-50 transition-all duration-300"></i>
        <span class="absolute -top-1 -right-1 flex h-4 w-4 hidden" id="hc-chat-badge">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white"></span>
        </span>
    </button>

    <!-- Chat Window -->
    <div id="hc-chat-window" class="absolute bottom-20 right-0 w-[360px] h-[550px] bg-white rounded-[24px] shadow-[0_12px_40px_rgba(0,0,0,0.12)] border border-gray-100 flex flex-col overflow-hidden transition-all duration-400 ease-[cubic-bezier(0.16,1,0.3,1)] origin-bottom-right scale-0 opacity-0 pointer-events-none">
        
        <!-- Header -->
        <div class="bg-white/90 backdrop-blur-xl border-b border-gray-100 p-4 shrink-0 relative z-10">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-900 flex items-center justify-center border border-gray-800 shrink-0 shadow-inner overflow-hidden">
                        <img src="graphics/logos/hypecrews logo white.png" alt="Hypecrews" class="w-6 h-6 object-contain">
                    </div>
                    <div>
                        <h3 class="font-semibold text-[15px] text-gray-900 leading-tight tracking-tight">Hypecrews Support</h3>
                        <p class="text-[11px] text-gray-500 flex items-center gap-1.5 font-medium mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#34c759]"></span> Typically replies instantly
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Initial Form State (Hidden if session exists) -->
        <div id="hc-chat-form-container" class="flex-1 p-6 overflow-y-auto bg-[#fafafa] hidden min-h-0">
            <p class="text-[13px] text-gray-500 mb-6 text-center">Please fill out the form below to connect with us.</p>
            <form id="hc-init-form" onsubmit="startHcChat(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Full Name <span class="text-[#ff3b30]">*</span></label>
                    <input type="text" id="hc-input-name" required class="w-full bg-white text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#007aff]/20 focus:border-[#007aff] transition-colors shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Email Address <span class="text-[#ff3b30]">*</span></label>
                    <input type="email" id="hc-input-email" required class="w-full bg-white text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#007aff]/20 focus:border-[#007aff] transition-colors shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">Phone Number</label>
                    <input type="tel" id="hc-input-phone" class="w-full bg-white text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#007aff]/20 focus:border-[#007aff] transition-colors shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5 pl-1">How can we help?</label>
                    <select id="hc-input-topic" class="w-full bg-white text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-[14px] focus:outline-none focus:ring-2 focus:ring-[#007aff]/20 focus:border-[#007aff] transition-colors shadow-sm appearance-none">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Sales & Pricing">Sales & Pricing</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Billing Issue">Billing Issue</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <button type="submit" id="hc-start-btn" class="w-full bg-[#007aff] hover:bg-[#005bb5] text-white font-semibold py-3.5 rounded-xl text-[15px] transition-colors shadow-sm mt-4">
                    Start Chat
                </button>
            </form>
        </div>

        <!-- Active Chat State -->
        <div id="hc-chat-messages-container" class="flex-1 flex flex-col hidden bg-[#fafafa] min-h-0">
            <div id="hc-messages-area" class="flex-1 overflow-y-auto p-4 space-y-3">
                <!-- Messages will be appended here via JS -->
            </div>
            
            <!-- Chat Input Area -->
            <div id="hc-chat-input-area" class="p-3 bg-white/90 backdrop-blur-xl border-t border-gray-100 shrink-0">
                <form id="hc-message-form" onsubmit="sendHcMessage(event)" class="relative flex items-center">
                    <input type="text" id="hc-message-input" placeholder="Type a message..." autocomplete="off" class="w-full bg-[#f2f2f7] border border-transparent text-gray-900 focus:bg-white rounded-full pl-4 pr-10 py-2.5 text-[14px] focus:outline-none focus:border-gray-300 transition-colors placeholder-[#8e8e93]">
                    <button type="submit" class="absolute right-1.5 w-7 h-7 bg-[#007aff] hover:bg-[#005bb5] text-white rounded-full flex items-center justify-center transition-colors shadow-sm focus:outline-none">
                        <i class="fas fa-arrow-up text-[12px]"></i>
                    </button>
                </form>
            </div>
            
            <!-- Resolved State Area -->
            <div id="hc-chat-resolved-area" class="p-4 bg-[#f2f2f7] border-t border-gray-200 hidden text-center shrink-0">
                <p class="text-[14px] font-semibold text-gray-900 mb-0.5">Chat Resolved</p>
                <p class="text-[12px] text-gray-500 mb-3">This conversation has been closed.</p>
                <button onclick="startNewChat()" class="text-[13px] font-semibold text-[#007aff] hover:text-[#005bb5] transition-colors">Start New Chat</button>
            </div>
        </div>
        
    </div>
</div>

<script>
    let hcSessionToken = localStorage.getItem('hc_chat_token');
    let hcChatPolling = null;
    let hcChatOpen = false;

    let bubbleClosed = false;

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

        // Cinematic Intro Logic
        if (!sessionStorage.getItem('hypecrewsIntroPlayed')) {
            const overlay = document.getElementById('hc-intro-overlay');
            const chatBtn = document.getElementById('hc-chat-btn');
            
            overlay.classList.remove('hidden');
            
            let tl = anime.timeline({
                easing: 'easeOutExpo',
                duration: 1000
            });
            
            tl.add({
                targets: overlay,
                opacity: [0, 1],
                duration: 800
            })
            .add({
                targets: '#hc-intro-text-1',
                translateY: [40, 0],
                opacity: [0, 1],
                duration: 1200
            })
            .add({
                targets: '#hc-intro-text-1',
                translateY: [0, -40],
                opacity: [1, 0],
                duration: 800,
                delay: 1000
            })
            .add({
                targets: '#hc-intro-text-2',
                translateY: [40, 0],
                opacity: [0, 1],
                duration: 1200
            }, '-=400')
            .add({
                targets: '#hc-intro-text-2',
                translateY: [0, -40],
                opacity: [1, 0],
                duration: 800,
                delay: 1200
            })
            .add({
                targets: '#hc-intro-text-3',
                scale: [0.8, 1],
                opacity: [0, 1],
                duration: 1500,
                easing: 'easeOutElastic(1, .8)'
            }, '-=200')
            .add({
                targets: '#hc-intro-text-3',
                scale: [1, 1.2],
                opacity: [1, 0],
                duration: 600,
                delay: 800
            })
            .add({
                targets: chatBtn,
                scale: [1, 1.3, 1.1],
                boxShadow: ['0px 0px 0px rgba(0,122,255,0)', '0px 0px 40px rgba(0,122,255,0.8)', '0px 0px 20px rgba(0,122,255,0.5)'],
                duration: 1500,
                begin: function() {
                    anime({
                        targets: chatBtn,
                        scale: [1.1, 1.2],
                        direction: 'alternate',
                        loop: true,
                        duration: 800,
                        easing: 'easeInOutSine'
                    });
                }
            })
            .add({
                targets: overlay,
                opacity: [1, 0],
                duration: 1000,
                delay: 3000,
                complete: function() {
                    overlay.classList.add('hidden');
                    sessionStorage.setItem('hypecrewsIntroPlayed', 'true');
                    anime.remove(chatBtn);
                    anime({
                        targets: chatBtn,
                        scale: 1,
                        boxShadow: '0 25px 50px -12px rgba(0, 122, 255, 0.3)',
                        duration: 500
                    });
                }
            });
        }

        // Tawk.to Style Bubble Animation (For everyone)
        let bubbleDelay = sessionStorage.getItem('hypecrewsIntroPlayed') ? 1500 : 7000;
        
        setTimeout(() => {
            const bubble = document.getElementById('hc-floating-bubble');
            const bubbleText = document.getElementById('hc-bubble-text');
            if(bubble && !hcChatOpen && !bubbleClosed) {
                bubble.classList.remove('scale-0', 'opacity-0');
                bubble.classList.add('scale-100', 'opacity-100');
                
                const messages = [
                    "Hey, need help for your business? 👋",
                    "Grow your brand with us! 🚀",
                    "Looking for marketing experts? 📈",
                    "Let's skyrocket your sales! 💸",
                    "Chat with our experts now! 💬",
                    "Have a project in mind? 💡"
                ];
                let msgIndex = 0;
                
                // Animate text continuously
                setInterval(() => {
                    if(!hcChatOpen && !bubbleClosed) {
                        bubbleText.style.opacity = '0';
                        setTimeout(() => {
                            msgIndex = (msgIndex + 1) % messages.length;
                            bubbleText.innerHTML = messages[msgIndex];
                            bubbleText.style.opacity = '1';
                        }, 300); // Wait for fade out before changing text and fading in
                    }
                }, 4000); // Change message every 4 seconds
            }
        }, bubbleDelay);
    });

    function closeHcBubble() {
        bubbleClosed = true;
        const bubble = document.getElementById('hc-floating-bubble');
        if (bubble) {
            bubble.classList.remove('scale-100', 'opacity-100');
            bubble.classList.add('scale-0', 'opacity-0', 'pointer-events-none');
        }
    }

    function toggleHcChat() {
        const windowEl = document.getElementById('hc-chat-window');
        const iconBtn = document.getElementById('hc-chat-icon');
        const closeIcon = document.getElementById('hc-close-icon');
        const badge = document.getElementById('hc-chat-badge');
        const bubble = document.getElementById('hc-floating-bubble');
        
        hcChatOpen = !hcChatOpen;
        
        if (hcChatOpen) {
            windowEl.classList.remove('scale-0', 'opacity-0', 'pointer-events-none');
            windowEl.classList.add('scale-100', 'opacity-100', 'pointer-events-auto');
            iconBtn.classList.add('scale-0', 'opacity-0');
            closeIcon.classList.remove('scale-50', 'opacity-0');
            closeIcon.classList.add('scale-100', 'opacity-100');
            badge.classList.add('hidden');
            if (bubble) bubble.classList.add('opacity-0', 'pointer-events-none');
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
            <div class="flex justify-end mb-1 animate-fade-in-up">
                <div class="max-w-[75%] bg-[#007aff] text-white rounded-[20px] px-4 py-2 text-[15px] leading-relaxed">
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
                                    <div class="text-[#8e8e93] text-[11px] font-medium px-3 py-1 text-center max-w-[80%]">
                                        ${escapeHtmlHc(m.message)}
                                    </div>
                                </div>
                            `;
                        } else if (m.is_mine) {
                            html += `
                                <div class="flex justify-end mb-1">
                                    <div class="max-w-[75%] bg-[#007aff] text-white rounded-[20px] px-4 py-2 text-[15px] leading-relaxed">
                                        ${escapeHtmlHc(m.message)}
                                    </div>
                                </div>
                            `;
                        } else {
                            html += `
                                <div class="flex justify-start mb-1 items-end gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gray-200 border border-gray-300 shrink-0 overflow-hidden flex items-center justify-center text-[10px] font-bold text-gray-500 mb-1">
                                        ${m.sender_avatar ? `<img src="${m.sender_avatar}" class="w-full h-full object-cover">` : '<i class="fas fa-headset"></i>'}
                                    </div>
                                    <div class="max-w-[75%] bg-[#e9e9eb] text-black rounded-[20px] px-4 py-2 text-[15px] leading-relaxed">
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
