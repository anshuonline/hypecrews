<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch user data
try {
    $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, created_at FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching user data.";
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters long.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch();

            if ($user_data && password_verify($current_password, $user_data['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed_password, $user_id]);

                $success = "Password changed successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        } catch (PDOException $e) {
            $error = "Error changing password.";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$pageTitle = "Security — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap">
    <div class="shell shell-wide">
        <div style="margin-bottom: 30px;" data-aos="fade-up">
            <span class="eyebrow">Account security</span>
            <h1 class="dash-title" style="margin-top: 12px;">Password &amp; access.</h1>
            <p style="font-size: 13.5px; color: var(--muted); margin-top: 6px;">Keep your account protected with a strong, unique password.</p>
        </div>

        <?php if ($error): ?><div class="alert alert-error" data-aos="fade-up"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success" data-aos="fade-up"><i class="fas fa-circle-check"></i><span><?php echo htmlspecialchars($success); ?></span></div><?php endif; ?>

        <div class="grid lg:grid-cols-[260px_1fr] gap-7 items-start">
            <?php $current_user_page = 'change_password'; include 'components/user_sidebar.php'; ?>

            <div style="display: grid; gap: 22px;">
                <div class="dash-card" data-aos="fade-up">
                    <h2 style="font-size: 19px;"><i class="fas fa-lock" style="color: var(--blue); margin-right: 8px;"></i>Change password</h2>
                    <p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;">Use at least 6 characters. A mix of letters, numbers and symbols is best.</p>

                    <form method="POST" style="margin-top: 22px; max-width: 520px;">
                        <input type="hidden" name="change_password" value="1">
                        <div class="field">
                            <label class="label">Current password</label>
                            <div style="position: relative;">
                                <input type="password" id="current_password" name="current_password" required placeholder="••••••••" class="input" style="padding-right: 44px;">
                                <button type="button" data-toggle-password="current_password" aria-label="Show password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--muted-2);padding:6px;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">New password</label>
                            <div style="position: relative;">
                                <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="••••••••" class="input" style="padding-right: 44px;">
                                <button type="button" data-toggle-password="new_password" aria-label="Show password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--muted-2);padding:6px;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <div class="field">
                            <label class="label">Confirm new password</label>
                            <div style="position: relative;">
                                <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="••••••••" class="input" style="padding-right: 44px;">
                                <button type="button" data-toggle-password="confirm_password" aria-label="Show password" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:0;background:transparent;color:var(--muted-2);padding:6px;"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-top: 6px;">Update password <i class="fas fa-shield-halved"></i></button>
                    </form>
                </div>

                <div class="dash-card" data-aos="fade-up" data-aos-delay="60">
                    <h2 style="font-size: 16px;"><i class="fas fa-lightbulb" style="color: var(--blue); margin-right: 8px;"></i>Security tips</h2>
                    <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 16px;">
                        <div class="flex items-start gap-3"><div class="icon-tile icon-tile-sm"><i class="fas fa-key"></i></div><div><strong style="display:block;font-size:12.5px;color:var(--navy);">Never reuse passwords</strong><p style="font-size:11.5px;color:var(--muted);line-height:1.65;margin-top:3px;">Use a unique password for Hypecrews.</p></div></div>
                        <div class="flex items-start gap-3"><div class="icon-tile icon-tile-sm"><i class="fas fa-user-secret"></i></div><div><strong style="display:block;font-size:12.5px;color:var(--navy);">Keep it private</strong><p style="font-size:11.5px;color:var(--muted);line-height:1.65;margin-top:3px;">We’ll never ask for your password.</p></div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
</body>
</html>
