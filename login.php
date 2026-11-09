<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT id, username, first_name, last_name, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            // Use MD5 to verify password
            if ($user && $user['password'] === md5($password)) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                header("Location: profile.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Login error. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
$pageTitle = "Client Login — Hypecrews";
$pageDescription = "Log in to your Hypecrews account. Access your dashboard, manage projects and connect with our digital agency services.";
$robots = "noindex, nofollow";
$canonical = "https://hypecrews.com/login.php";
include 'components/head.php';
?>
<style>
    .auth-input-wrap { position: relative; }
    .auth-input-wrap > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--muted-2); font-size: 13px; pointer-events: none; }
    .auth-input-wrap .input { padding-left: 42px; }
    .auth-input-wrap .eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: var(--muted-2); padding: 6px; }
    .auth-visual { border-radius: 0; }
    .auth-visual::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at 20% 10%, rgba(96,193,229,.2), transparent 40%), radial-gradient(circle at 90% 85%, rgba(0,90,156,.4), transparent 45%); }
</style>
<body class="antialiased">
<div class="auth-wrap">
    <!-- Visual -->
    <div class="auth-visual">
        <div class="relative z-1">
            <a href="/index.php" aria-label="Hypecrews home"><img src="/graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews" style="height: 42px; width: auto;"></a>
        </div>

        <div class="relative z-1" style="max-width: 480px; margin: 40px 0;">
            <span class="eyebrow eyebrow-light">Client portal</span>
            <h1 style="font-size: clamp(2.1rem, 3.6vw, 3.1rem); margin-top: 18px;">Your brand,<br><span class="gradient-text-light">in one place.</span></h1>
            <p style="font-size: 14px; color: rgba(255,255,255,.66); line-height: 1.85; margin-top: 18px;">Track projects, download files, manage support conversations and stay close to every milestone — all from your Hypecrews dashboard.</p>

            <div style="display: grid; gap: 12px; margin-top: 30px;">
                <div style="display: flex; gap: 12px; align-items: center; padding: 14px 16px; border-radius: 14px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);">
                    <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.14); border-color: rgba(96,193,229,.28); color: var(--sky);"><i class="fas fa-shield-halved"></i></span>
                    <div><strong style="display:block;font-size:12px;color:#fff;">Secure by design</strong><small style="font-size:10.5px;color:rgba(255,255,255,.55);">Your account and assets are protected.</small></div>
                </div>
                <div style="display: flex; gap: 12px; align-items: center; padding: 14px 16px; border-radius: 14px; background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.1);">
                    <span class="icon-tile icon-tile-sm" style="background: rgba(96,193,229,.14); border-color: rgba(96,193,229,.28); color: var(--sky);"><i class="fas fa-chart-line"></i></span>
                    <div><strong style="display:block;font-size:12px;color:#fff;">Live project visibility</strong><small style="font-size:10.5px;color:rgba(255,255,255,.55);">Every update, file and status in real time.</small></div>
                </div>
            </div>
        </div>

        <div class="relative z-1" style="font-size: 11.5px; color: rgba(255,255,255,.42); display: flex; flex-wrap: wrap; gap: 14px;">
            <a href="/privacy-policy.php" style="color: rgba(255,255,255,.55);">Privacy</a>
            <a href="/terms-conditions.php" style="color: rgba(255,255,255,.55);">Terms</a>
            <a href="/contact.php" style="color: rgba(255,255,255,.55);">Need help?</a>
        </div>
    </div>

    <!-- Form -->
    <div class="auth-form-side">
        <div class="auth-card">
            <a href="/index.php" class="text-sm" style="display: inline-flex; align-items: center; gap: 8px; color: var(--muted); margin-bottom: 26px;"><i class="fas fa-arrow-left"></i> Back to home</a>
            <span class="eyebrow">Welcome back</span>
            <h1 style="font-size: clamp(1.9rem, 3vw, 2.5rem); margin-top: 14px;">Sign in to your account.</h1>
            <p style="font-size: 13.5px; color: var(--muted); margin-top: 10px;">Access your dashboard to continue where you left off.</p>

            <?php if ($error): ?>
            <div class="alert alert-error" style="margin-top: 22px;"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div>
            <?php endif; ?>
            <?php if ($success): ?>
            <div class="alert alert-success" style="margin-top: 22px;"><i class="fas fa-circle-check"></i><span><?php echo htmlspecialchars($success); ?></span></div>
            <?php endif; ?>

            <form method="POST" style="margin-top: 24px;">
                <input type="hidden" name="login" value="1">
                <div class="field">
                    <label class="label" for="username">Username or email</label>
                    <div class="auth-input-wrap">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" class="input" placeholder="Enter your username" required autocomplete="username">
                    </div>
                </div>
                <div class="field">
                    <div class="flex items-center justify-between" style="margin-bottom: 7px;">
                        <label class="label" for="password" style="margin-bottom: 0;">Password</label>
                        <a href="#" style="font-size: 11px; font-weight: 700; color: var(--blue);">Forgot password?</a>
                    </div>
                    <div class="auth-input-wrap">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" class="input" placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="eye" data-toggle-password="password" aria-label="Show password"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="flex items-center gap-2" style="margin-bottom: 22px;">
                    <input type="checkbox" id="remember-me" name="remember-me" style="width: 15px; height: 15px; accent-color: var(--blue);">
                    <label for="remember-me" style="font-size: 12.5px; color: var(--muted);">Keep me logged in</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in <i class="fas fa-arrow-right"></i></button>
            </form>

            <p style="text-align: center; font-size: 13px; color: var(--muted); margin-top: 22px;">Don’t have an account? <a href="register.php" style="font-weight: 800; color: var(--blue);">Create one now</a></p>
            <p style="text-align: center; font-size: 11px; color: var(--muted-2); margin-top: 26px;"><i class="fas fa-lock" style="margin-right: 5px;"></i>Secured with encrypted connection</p>
        </div>
    </div>
</div>
</body>
</html>
