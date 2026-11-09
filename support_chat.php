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
    $stmt = $pdo->prepare("SELECT s.*, a.username as assigned_admin_name FROM support_sessions s LEFT JOIN administrators a ON s.assigned_admin_id = a.id WHERE s.user_id = ? AND s.status = 'open' ORDER BY s.updated_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $active_session = $stmt->fetch();

    // Check for resolved session to show export/timer
    $stmt = $pdo->prepare("SELECT s.*, a.username as assigned_admin_name FROM support_sessions s LEFT JOIN administrators a ON s.assigned_admin_id = a.id WHERE s.user_id = ? AND s.status = 'resolved' ORDER BY s.updated_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $resolved_session = $stmt->fetch();

    if(!$active_session && $resolved_session) {
        $active_session = $resolved_session; // Allow viewing it
    }

    $time_remaining = -1;
    if ($active_session && $active_session['exported_at']) {
        $exported_time = strtotime($active_session['exported_at']);
        $time_remaining = max(0, 3600 - (time() - $exported_time));
        if ($time_remaining <= 0) {
            $pdo->prepare("DELETE FROM support_sessions WHERE id = ?")->execute([$active_session['id']]);
            header("Location: support_chat.php");
            exit;
        }
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$pageTitle = "Support Desk — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<style>
    .support-shell { display: grid; grid-template-columns: 260px minmax(0, 1fr); gap: 26px; align-items: start; }
    .support-sidebar { display: block; }
    .chat-panel {
        background: #fff; border: 1px solid var(--line); border-radius: 24px;
        display: flex; flex-direction: column; overflow: hidden;
        height: calc(100vh - 220px); min-height: 580px;
        box-shadow: var(--shadow-sm);
        width: 100%;
    }
    .chat-panel-head {
        padding: 18px 22px; border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #ffffff, #fbfdff);
        display: flex; align-items: center; justify-content: space-between; gap: 14px;
    }
    .chat-body {
        flex: 1; overflow-y: auto; padding: 26px 24px;
        background:
            radial-gradient(circle at 100% 0%, rgba(96,193,229,.08), transparent 42%),
            linear-gradient(180deg, #f8fbfe, #ffffff 60%);
        scroll-behavior: smooth;
    }
    .chat-body::-webkit-scrollbar { width: 7px; }
    .chat-body::-webkit-scrollbar-track { background: transparent; }
    .chat-body::-webkit-scrollbar-thumb { background: #cfe3ef; border-radius: 10px; }
    .chat-body::-webkit-scrollbar-thumb:hover { background: #a9cde0; }
    .chat-foot { border-top: 1px solid var(--line); background: #fbfdff; padding: 16px 20px; flex-shrink: 0; }
    .chat-input {
        width: 100%; border: 1px solid var(--line); background: #fff; color: var(--ink);
        border-radius: 999px; padding: 13px 50px 13px 20px; font-size: 13.5px; outline: none;
        transition: border-color .25s ease, box-shadow .25s ease;
    }
    .chat-input::placeholder { color: var(--muted-2); }
    .chat-input:focus { border-color: var(--sky); box-shadow: 0 0 0 4px rgba(96,193,229,.14); }
    .chat-send {
        width: 46px; height: 46px; flex-shrink: 0; border: 0; border-radius: 999px;
        background: linear-gradient(140deg, var(--blue), var(--navy)); color: #fff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 22px rgba(0,90,156,.28); transition: transform .25s ease;
    }
    .chat-send:hover { transform: translateY(-2px) scale(1.03); }
    .msg-row { display: flex; margin-bottom: 16px; }
    .msg-row.is-mine { justify-content: flex-end; }
    .msg-row.is-system { justify-content: center; margin: 22px 0; }
    .msg-meta { font-size: 10px; font-weight: 700; letter-spacing: .04em; color: var(--muted-2); margin-bottom: 6px; }
    .msg-meta.is-right { text-align: right; margin-right: 4px; }
    .msg-bubble {
        max-width: 78%; padding: 13px 17px; font-size: 13.5px; line-height: 1.75;
        border-radius: 18px; word-break: break-word; white-space: pre-line;
    }
    .msg-bubble.is-mine {
        background: linear-gradient(145deg, var(--blue), #004a80); color: #fff;
        border-bottom-right-radius: 5px; box-shadow: 0 10px 24px rgba(0,90,156,.2);
    }
    .msg-bubble.is-admin {
        background: #fff; color: var(--ink); border: 1px solid var(--line);
        border-bottom-left-radius: 5px; box-shadow: var(--shadow-xs);
    }
    .msg-bubble.is-system {
        background: #eaf8fd; color: var(--blue); border: 1px solid #cdeef9;
        font-size: 12px; font-weight: 700; padding: 10px 16px; border-radius: 12px; max-width: 90%;
    }
    .msg-avatar {
        width: 34px; height: 34px; flex-shrink: 0; border-radius: 999px; overflow: hidden;
        background: linear-gradient(140deg, var(--navy), var(--blue)); color: #fff;
        display: flex; align-items: center; justify-content: center; font-size: 13px;
        margin-right: 10px; align-self: flex-end; margin-bottom: 4px;
    }
    .msg-avatar img { width: 100%; height: 100%; object-fit: cover; }
    .msg-attach { margin-top: 10px; border-radius: 12px; overflow: hidden; border: 1px solid var(--line); }
    .msg-attach img { max-width: 100%; max-height: 240px; object-fit: contain; cursor: zoom-in; display: block; }
    .topic-select {
        width: 100%; appearance: none; border: 1px solid var(--line); border-radius: 12px;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%235d7788' stroke-width='2.4' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E") no-repeat right 15px center;
        padding: 12px 42px 12px 15px; font-size: 13.5px; color: var(--ink); outline: none;
    }
    .topic-select:focus { border-color: var(--sky); box-shadow: 0 0 0 4px rgba(96,193,229,.14); }
    .urgency-opt { position: relative; cursor: pointer; }
    .urgency-opt input { position: absolute; opacity: 0; pointer-events: none; }
    .urgency-opt span {
        display: block; text-align: center; padding: 10px; border-radius: 11px;
        border: 1px solid var(--line); background: #fff; font-size: 12px; font-weight: 800;
        color: var(--muted); transition: all .22s ease;
    }
    .urgency-opt input:checked + span { background: #eaf8fd; border-color: var(--sky); color: var(--blue); box-shadow: 0 0 0 3px rgba(96,193,229,.12); }
    .urgency-opt input[value="urgent"]:checked + span { background: #fdeeee; border-color: #f2b8b8; color: #b4453f; box-shadow: 0 0 0 3px rgba(180,69,63,.08); }
    .urgency-opt input[value="low"]:checked + span { background: #eef8f2; border-color: #b9e3c9; color: #15805a; box-shadow: 0 0 0 3px rgba(21,128,90,.07); }
    .lightbox {
        position: fixed; inset: 0; z-index: 1200; display: none; align-items: center; justify-content: center;
        background: rgba(5, 14, 25, .93); backdrop-filter: blur(8px); padding: 24px; opacity: 0; transition: opacity .25s ease;
    }
    .lightbox.is-open { display: flex; opacity: 1; }
    .lightbox img { max-width: 92vw; max-height: 88vh; border-radius: 16px; box-shadow: 0 40px 100px rgba(0,0,0,.6); }
    .typing-dots { display: inline-flex; gap: 4px; align-items: center; }
    .typing-dots span { width: 6px; height: 6px; border-radius: 99px; background: var(--sky); animation: chatPulse 1.2s infinite ease-in-out; }
    .typing-dots span:nth-child(2) { animation-delay: .18s; }
    .typing-dots span:nth-child(3) { animation-delay: .36s; }
    @keyframes chatPulse { 0%, 80%, 100% { opacity: .25; transform: translateY(0); } 40% { opacity: 1; transform: translateY(-3px); } }
    @media (max-width: 1024px) {
        .support-sidebar { display: none; }
        .support-shell { grid-template-columns: minmax(0, 1fr); }
        .chat-panel { height: calc(100vh - 190px); min-height: 520px; }
    }
    @media (max-width: 640px) {
        .chat-panel { height: calc(100vh - 150px); min-height: 480px; border-radius: 18px; }
        .chat-body { padding: 18px 14px; }
        .msg-bubble { max-width: 88%; }
    }
</style>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap" style="padding-bottom: 34px;">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-5" style="margin-bottom: 24px;" data-aos="fade-up">
            <div>
                <span class="eyebrow">Support desk</span>
                <h1 class="dash-title" style="margin-top: 12px;">We're here to help.</h1>
                <p style="font-size: 13.5px; color: var(--muted); margin-top: 6px;">A direct line to the Hypecrews team — replies usually land within a few minutes.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="badge badge-green"><span style="width:5px;height:5px;border-radius:99px;background:currentColor;display:inline-block;"></span> Team online</span>
                <a href="/contact.php" class="btn btn-outline btn-sm"><i class="fas fa-envelope"></i> Email us</a>
            </div>
        </div>

        <div class="support-shell">
            <!-- Sidebar -->
            <div class="support-sidebar">
                <?php
                $current_user_page = 'support_chat';
                include 'components/user_sidebar.php';
                ?>
            </div>

            <!-- Chat Interface / New Session Form -->
            <div class="chat-panel" data-aos="fade-up">

                <?php if (!$active_session): ?>
                    <!-- New Session Form -->
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: clamp(24px, 4vw, 48px); text-align: center; overflow-y: auto;">
                        <div class="icon-tile icon-tile-lg" style="margin-bottom: 18px;"><i class="fas fa-headset"></i></div>
                        <h2 style="font-size: clamp(1.5rem, 2.6vw, 2rem);">Start a new conversation.</h2>
                        <p style="font-size: 13.5px; color: var(--muted); max-width: 430px; margin-top: 9px; line-height: 1.8;">Tell us what this is about so we can route you to the right specialist. No ticket forms, no waiting on hold.</p>

                        <form method="POST" style="width: 100%; max-width: 480px; margin-top: 26px; text-align: left; background: #fbfdff; border: 1px solid var(--line); border-radius: 20px; padding: clamp(20px, 3vw, 28px);">
                            <input type="hidden" name="create_session" value="1">

                            <div class="field">
                                <label class="label" for="topic">What is this regarding?</label>
                                <select name="topic" id="topic" required class="topic-select">
                                    <option value="" disabled selected>Select a topic…</option>
                                    <option value="Copyright Protection">Copyright Protection</option>
                                    <option value="Social Media">Social Media</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                    <option value="Web & Development">Web &amp; Development</option>
                                    <option value="Recovery & Support">Recovery &amp; Support</option>
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="field" style="margin-bottom: 22px;">
                                <label class="label">Urgency level</label>
                                <div class="grid grid-cols-3 gap-2.5">
                                    <label class="urgency-opt">
                                        <input type="radio" name="urgency" value="low">
                                        <span>Low</span>
                                    </label>
                                    <label class="urgency-opt">
                                        <input type="radio" name="urgency" value="normal" checked>
                                        <span>Normal</span>
                                    </label>
                                    <label class="urgency-opt">
                                        <input type="radio" name="urgency" value="urgent">
                                        <span>Urgent</span>
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block">Start conversation <i class="fas fa-arrow-right"></i></button>

                            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2" style="margin-top: 16px; font-size: 10.5px; color: var(--muted-2);">
                                <span><i class="fas fa-lock" style="margin-right: 4px;"></i> Private &amp; secure</span>
                                <span><i class="fas fa-bolt" style="margin-right: 4px;"></i> Avg. reply 4 min</span>
                                <span><i class="fas fa-clock" style="margin-right: 4px;"></i> 24/7 coverage</span>
                            </div>
                        </form>
                    </div>

                <?php else: ?>
                    <!-- Chat Header -->
                    <div class="chat-panel-head">
                        <div class="flex items-center gap-3" style="min-width: 0;">
                            <div class="side-avatar" style="width: 44px; height: 44px; font-size: 16px; background: linear-gradient(140deg, var(--navy), var(--blue)); color: #fff; flex-shrink: 0;"><i class="fas fa-headset"></i></div>
                            <div style="min-width: 0;">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 style="font-size: 15px; margin: 0;">Hypecrews Support</h3>
                                    <span class="chip" style="font-size: 10px; padding: 3px 8px;">#<?php echo $active_session['id']; ?></span>
                                    <?php if ($active_session['assigned_admin_name']): ?>
                                        <span class="badge badge-sky" style="font-size: 9.5px;"><i class="fas fa-user-shield" style="margin-right: 3px;"></i><?php echo htmlspecialchars($active_session['assigned_admin_name']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="flex flex-wrap items-center gap-x-2 gap-y-1" style="font-size: 11px; color: var(--muted); margin-top: 4px;">
                                    <span class="flex items-center gap-1.5"><span style="width:6px;height:6px;border-radius:99px;background:#16a06a;display:inline-block;"></span> Live</span>
                                    <span style="color:var(--line);">•</span>
                                    <span><?php echo htmlspecialchars($active_session['topic']); ?></span>
                                    <span style="color:var(--line);">•</span>
                                    <span style="font-weight: 800; text-transform: uppercase; letter-spacing: .05em; color: <?php echo $active_session['urgency'] === 'urgent' ? '#b4453f' : ($active_session['urgency'] === 'normal' ? '#15805a' : 'var(--blue)'); ?>;"><?php echo htmlspecialchars(ucfirst($active_session['urgency'])); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="hidden sm:flex items-center gap-2">
                            <span class="badge <?php echo $active_session['status'] === 'open' ? 'badge-green' : 'badge-slate'; ?>"><?php echo ucfirst($active_session['status']); ?></span>
                        </div>
                    </div>

                    <!-- Chat Messages Area -->
                    <div id="chatMessages" class="chat-body" data-session-id="<?php echo $active_session['id']; ?>">
                        <div style="text-align: center; padding: 40px 0; color: var(--muted-2);">
                            <div class="typing-dots" style="justify-content: center; margin-bottom: 14px;"><span></span><span></span><span></span></div>
                            <p style="font-size: 12px; font-weight: 700;">Loading your conversation…</p>
                        </div>
                    </div>

                    <?php if($active_session['status'] === 'open'): ?>
                    <!-- Chat Input -->
                    <div class="chat-foot">
                        <form id="chatForm" class="flex items-end gap-3">
                            <input type="hidden" id="sessionId" value="<?php echo $active_session['id']; ?>">
                            <div style="position: relative; flex: 1;">
                                <input type="text" id="messageInput" autocomplete="off" placeholder="Type your message…" class="chat-input">
                                <label style="position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:var(--muted-2);padding:7px;" title="Attach an image">
                                    <i class="fas fa-paperclip"></i>
                                    <input type="file" id="attachmentInput" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                                </label>
                            </div>
                            <button type="submit" class="chat-send" aria-label="Send message"><i class="fas fa-paper-plane"></i></button>
                        </form>
                        <div id="attachmentPreview" class="hidden" style="margin-top: 12px; font-size: 12px; color: var(--blue); font-weight: 700; align-items: center; gap: 8px;">
                            <span class="chip" style="font-size: 10.5px;"><i class="fas fa-image"></i> <span id="attachmentName"></span></span>
                            <button type="button" id="removeAttachment" aria-label="Remove attachment" style="color: #b4453f; background: transparent; border: 0; padding: 4px;"><i class="fas fa-times"></i></button>
                        </div>
                        <p style="font-size: 10.5px; color: var(--muted-2); margin-top: 10px; text-align: center;">Press Enter to send · Images up to 2MB · <span style="font-weight: 800;">End-to-end private</span></p>
                    </div>
                    <?php else: ?>
                    <!-- Export Area -->
                    <div class="chat-foot" style="padding: clamp(24px, 3vw, 38px); text-align: center;">
                        <div class="icon-tile" style="margin: 0 auto 16px; background: #eaf8fd; border-color: #cdeef9; color: var(--blue);"><i class="fas fa-circle-check"></i></div>
                        <h4 style="font-size: 17px;">This conversation has been resolved.</h4>

                        <?php if ($active_session['user_exported'] != 1): ?>
                            <p style="font-size: 12.5px; color: var(--muted); margin-top: 7px; max-width: 440px; margin-inline: auto; line-height: 1.75;">Export the full chat history as a PDF for your records. After exporting, it will be permanently deleted after 1 hour.</p>
                            <button id="exportPdfBtn" class="btn btn-primary" style="margin-top: 18px;">
                                <i class="fas fa-file-pdf"></i> Export chat to PDF
                            </button>
                        <?php endif; ?>

                        <?php if ($time_remaining >= 0): ?>
                            <div style="margin-top: 20px; padding: 16px; border: 1px solid #f5dede; background: #fdf6f6; border-radius: 14px; max-width: 360px; margin-inline: auto;">
                                <p style="font-size: 11.5px; color: #b4453f; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 6px;">
                                    <i class="fas fa-triangle-exclamation"></i> Chat deletes permanently in
                                </p>
                                <div id="countdownTimer" style="font-family: var(--font-head); font-size: 30px; font-weight: 800; color: var(--navy); letter-spacing: .12em; margin-top: 6px;">--:--</div>
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

                        <div class="flex flex-wrap items-center justify-center gap-3" style="margin-top: 20px;">
                            <button onclick="reopenSession()" class="btn btn-outline btn-sm"><i class="fas fa-rotate-left"></i> Reopen chat</button>
                            <button onclick="startNewChat()" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Start new chat</button>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Image Lightbox -->
<div id="imageLightbox" class="lightbox" onclick="if(event.target === this) closeLightbox()">
    <button onclick="closeLightbox()" aria-label="Close preview" style="position: absolute; top: 20px; right: 24px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.18); color: #fff; width: 42px; height: 42px; border-radius: 999px; cursor: pointer; font-size: 16px;">
        <i class="fas fa-times"></i>
    </button>
    <img id="lightboxImage" src="" alt="Zoomed attachment">
</div>

<script>
    function openLightbox(url) {
        const lb = document.getElementById('imageLightbox');
        const img = document.getElementById('lightboxImage');
        if (!lb || !img) return;
        img.src = url;
        lb.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lb = document.getElementById('imageLightbox');
        const img = document.getElementById('lightboxImage');
        if (!lb) return;
        lb.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });

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
            let html = escapeHtml(text);
            html = html.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" style="color:#005A9C;font-weight:700;text-decoration:underline;word-break:break-all;">$1</a>');
            return html;
        }

        function renderMessages(messages) {
            currentMessages = messages;
            if(!messagesDiv) return;
            if (messages.length === 0) {
                messagesDiv.innerHTML = '<div style="text-align:center;padding:40px 16px;color:var(--muted);"><div class="icon-tile" style="margin:0 auto 14px;"><i class="fas fa-comment-dots"></i></div><p style="font-size:13px;font-weight:700;color:var(--navy);">No messages yet</p><p style="font-size:12px;margin-top:4px;">Send a message below to start the conversation.</p></div>';
                return;
            }

            const currentHashes = messages.map(m => m.id).join(',');
            if (messagesDiv.dataset.lastHash !== currentHashes) {
                messagesDiv.dataset.lastHash = currentHashes;
                let html = '';

                messages.forEach(msg => {
                    let attachmentHtml = '';
                    if (msg.attachment) {
                        attachmentHtml = `<div class="msg-attach"><img src="${msg.attachment}" alt="Attachment" onclick="openLightbox('${msg.attachment}')"></div>`;
                    }

                    if (msg.sender_type === 'system') {
                        html += `
                        <div class="msg-row is-system">
                            <div class="msg-bubble is-system"><i class="fas fa-circle-info" style="margin-right:6px;"></i>${formatMessage(msg.message)}</div>
                        </div>`;
                    } else if (msg.is_mine) {
                        html += `
                        <div class="msg-row is-mine">
                            <div style="max-width: 78%; display: flex; flex-direction: column; align-items: flex-end;">
                                <div class="msg-meta is-right">${msg.time} · You</div>
                                <div class="msg-bubble is-mine">${formatMessage(msg.message)}${attachmentHtml}</div>
                            </div>
                        </div>`;
                    } else {
                        const avatarHtml = msg.sender_avatar
                            ? `<img src="${msg.sender_avatar}" alt="">`
                            : `<i class="fas fa-user-shield"></i>`;

                        html += `
                        <div class="msg-row">
                            <div class="msg-avatar">${avatarHtml}</div>
                            <div style="max-width: 78%; min-width: 0;">
                                <div class="msg-meta">${escapeHtml(msg.sender_name)} (Admin) · ${msg.time}</div>
                                <div class="msg-bubble is-admin">${formatMessage(msg.message)}${attachmentHtml}</div>
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
                    attachmentPreview.style.display = 'flex';
                }
            });
        }

        if (removeAttachment) {
            removeAttachment.addEventListener('click', function() {
                attachmentInput.value = '';
                attachmentPreview.classList.add('hidden');
                attachmentPreview.style.display = 'none';
            });
        }

        if (chatForm) {
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const msg = messageInput.value.trim();
                const hasAttachment = attachmentInput.files && attachmentInput.files.length > 0;

                if (!msg && !hasAttachment) return;

                if (msg) {
                    const tempHtml = `
                    <div class="msg-row is-mine" style="opacity:.55;">
                        <div style="max-width: 78%; display: flex; flex-direction: column; align-items: flex-end;">
                            <div class="msg-meta is-right">Sending…</div>
                            <div class="msg-bubble is-mine">${escapeHtml(msg)}</div>
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

                exportPdfBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF…';
                exportPdfBtn.disabled = true;

                const printContainer = document.createElement('div');
                printContainer.style.backgroundColor = '#ffffff';
                printContainer.style.color = '#000000';
                printContainer.style.fontFamily = 'Arial, sans-serif';
                printContainer.style.padding = '40px';
                printContainer.style.width = '100%';
                printContainer.style.maxWidth = '800px';
                printContainer.style.margin = '0 auto';

                let html = `
                    <div style="border-bottom: 2px solid #1B365D; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background-color: #1B365D; border-radius: 50%; padding: 10px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <img src="/graphics/logos/hypecrews%20logo%20white.png" style="max-width: 100%; height: auto;">
                            </div>
                            <div>
                                <h2 style="margin: 0; font-size: 24px; color: #1B365D;">Hypecrews Support</h2>
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
                    const bgColor = isUser ? '#eaf4fb' : '#f4f9fc';
                    const align = isUser ? 'flex-end' : 'flex-start';
                    const textAlign = isUser ? 'right' : 'left';

                    let attachmentHtml = '';
                    if (msg.attachment) {
                        attachmentHtml = `<div style="margin-top: 10px;"><img src="${msg.attachment}" style="max-width: 300px; max-height: 300px; border: 1px solid #ccc; border-radius: 4px;"></div>`;
                    }

                    let cleanMsg = escapeHtml(msg.message).replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" style="color: #005A9C; text-decoration: underline; word-break: break-all;">$1</a>');

                    html += `
                        <div style="display: flex; flex-direction: column; align-items: ${align}; width: 100%;">
                            <div style="font-size: 12px; color: #666; margin-bottom: 5px; text-align: ${textAlign}; width: 100%;">${escapeHtml(senderName)} • ${msg.time}</div>
                            <div style="background-color: ${bgColor}; padding: 15px; border-radius: 8px; max-width: 80%; border: 1px solid #dde8ef; color: #000; font-size: 14px; line-height: 1.5;">
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
                    exportPdfBtn.innerHTML = '<i class="fas fa-check"></i> Downloaded';

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

        fetchMessages();
        setInterval(fetchMessages, 3000);
    });
</script>
</body>
</html>
