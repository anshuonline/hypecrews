<?php
$current_user_page = isset($current_user_page) ? $current_user_page : 'profile';

$unread_stmt = $pdo->prepare("SELECT COUNT(*) FROM support_chats c JOIN support_sessions s ON c.session_id = s.id WHERE s.user_id = ? AND c.sender_type = 'admin' AND c.is_read = 0");
$unread_stmt->execute([$_SESSION['user_id']]);
$support_unread = $unread_stmt->fetchColumn();
?>

<aside class="side-card">
    <div class="side-user">
        <div class="side-avatar"><?php echo strtoupper(substr(htmlspecialchars($user['first_name']), 0, 1)); ?></div>
        <div style="overflow: hidden;">
            <strong style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
            <small style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">@<?php echo htmlspecialchars($user['username']); ?></small>
        </div>
    </div>

    <div class="side-label">Workspace</div>
    <a href="/profile.php" class="side-link <?php echo $current_user_page === 'profile' ? 'is-active' : ''; ?>">
        <i class="fas fa-user-circle"></i> Profile settings
    </a>
    <a href="/track_orders.php" class="side-link <?php echo $current_user_page === 'orders' ? 'is-active' : ''; ?>">
        <i class="fas fa-box"></i> My orders
    </a>
    <a href="/change_password.php" class="side-link <?php echo $current_user_page === 'change_password' ? 'is-active' : ''; ?>">
        <i class="fas fa-lock"></i> Security
    </a>
    <a href="/support_chat.php" class="side-link <?php echo $current_user_page === 'support_chat' ? 'is-active' : ''; ?>">
        <i class="fas fa-comments"></i> Support chat
        <?php if ($support_unread > 0): ?><span class="count"><?php echo (int) $support_unread; ?></span><?php endif; ?>
    </a>

    <div class="side-label" style="margin-top: 22px;">Quick links</div>
    <a href="/services.php" class="side-link"><i class="fas fa-layer-group"></i> Services</a>
    <a href="/contact.php" class="side-link"><i class="fas fa-headset"></i> Talk to us</a>

    <div style="border-top: 1px solid var(--line); margin-top: 18px; padding-top: 12px;">
        <a href="/logout.php" class="side-link is-danger"><i class="fas fa-arrow-right-from-bracket"></i> Sign out</a>
    </div>
</aside>
