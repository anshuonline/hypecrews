<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $country = trim($_POST['country']);
    $age = intval($_POST['age']);
    $company_name = trim($_POST['company_name']);
    $company_website = trim($_POST['company_website']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username)) $errors[] = "Username is required";
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($mobile)) $errors[] = "Mobile number is required";
    if (empty($country)) $errors[] = "Country is required";
    if (empty($age) || $age <= 0) $errors[] = "Valid age is required";
    if (empty($password)) $errors[] = "Password is required";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = "Username or email already exists";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    if (empty($errors)) {
        try {
            $hashed_password = md5($password);

            $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $username,
                $first_name,
                $last_name,
                $email,
                $mobile,
                $country,
                $age,
                $company_name ?: null,
                $company_website ?: null,
                $hashed_password
            ]);

            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;

            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
$pageTitle = "Create Account — Hypecrews";
$pageDescription = "Create your Hypecrews account. Sign up to access premium digital services, project management and our creative agency dashboard.";
$robots = "noindex, nofollow";
$canonical = "https://hypecrews.com/register.php";
include 'components/head.php';
?>
<style>
    .auth-input-wrap { position: relative; }
    .auth-input-wrap > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--muted-2); font-size: 13px; pointer-events: none; }
    .auth-input-wrap .input { padding-left: 42px; }
    .auth-input-wrap .eye { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: var(--muted-2); padding: 6px; }
    .auth-visual::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at 20% 10%, rgba(96,193,229,.2), transparent 40%), radial-gradient(circle at 90% 85%, rgba(0,90,156,.4), transparent 45%); }
    .auth-form-side { align-items: flex-start; overflow-y: auto; }
</style>
<body class="antialiased">
<div class="auth-wrap">
    <!-- Visual -->
    <div class="auth-visual">
        <div class="relative z-1">
            <a href="/index.php" aria-label="Hypecrews home"><img src="/graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews" style="height: 42px; width: auto;"></a>
        </div>

        <div class="relative z-1" style="max-width: 480px; margin: 40px 0;">
            <span class="eyebrow eyebrow-light">Join Hypecrews</span>
            <h1 style="font-size: clamp(2.1rem, 3.6vw, 3.1rem); margin-top: 18px;">One account.<br><span class="gradient-text-light">Your whole brand.</span></h1>
            <p style="font-size: 14px; color: rgba(255,255,255,.66); line-height: 1.85; margin-top: 18px;">Create your client account to track orders, manage projects, share files and talk directly with the team building your brand.</p>

            <div style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 32px;">
                <div><strong style="display:block;font-family:var(--font-head);font-size:24px;color:#fff;">1,000+</strong><small style="font-size:11px;color:rgba(255,255,255,.5);font-weight:700;">Projects delivered</small></div>
                <div><strong style="display:block;font-family:var(--font-head);font-size:24px;color:#fff;">750+</strong><small style="font-size:11px;color:rgba(255,255,255,.5);font-weight:700;">Brands served</small></div>
                <div><strong style="display:block;font-family:var(--font-head);font-size:24px;color:#fff;">4.9/5</strong><small style="font-size:11px;color:rgba(255,255,255,.5);font-weight:700;">Client rating</small></div>
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
        <div class="auth-card" style="max-width: 520px; padding: 20px 0;">
            <a href="/index.php" class="text-sm" style="display: inline-flex; align-items: center; gap: 8px; color: var(--muted); margin-bottom: 22px;"><i class="fas fa-arrow-left"></i> Back to home</a>
            <span class="eyebrow">Create account</span>
            <h1 style="font-size: clamp(1.8rem, 2.8vw, 2.3rem); margin-top: 14px;">Start your next chapter.</h1>
            <p style="font-size: 13.5px; color: var(--muted); margin-top: 9px;">A few details and you’re in. It takes less than two minutes.</p>

            <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="margin-top: 22px; flex-direction: column; gap: 6px;">
                <div class="flex items-center gap-2" style="font-weight: 800;"><i class="fas fa-circle-exclamation"></i> Please fix the following</div>
                <ul style="display: grid; gap: 4px; padding-left: 20px; list-style: disc;">
                    <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" style="margin-top: 22px;">
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">First name</label>
                        <div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="first_name" value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>" required class="input" placeholder="John"></div>
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Last name</label>
                        <div class="auth-input-wrap"><i class="fas fa-user"></i><input type="text" name="last_name" value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>" required class="input" placeholder="Doe"></div>
                    </div>
                </div>
                <div class="field" style="margin-top: 16px;">
                    <label class="label">Username</label>
                    <div class="auth-input-wrap"><i class="fas fa-at"></i><input type="text" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required class="input" placeholder="johndoe123"></div>
                </div>
                <div class="field">
                    <label class="label">Email address</label>
                    <div class="auth-input-wrap"><i class="fas fa-envelope"></i><input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required class="input" placeholder="john@example.com"></div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Mobile number</label>
                        <div class="auth-input-wrap"><i class="fas fa-phone"></i><input type="tel" name="mobile" value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>" required class="input" placeholder="+91 9876543210"></div>
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Country</label>
                        <div class="auth-input-wrap"><i class="fas fa-globe"></i><input type="text" name="country" value="<?php echo isset($_POST['country']) ? htmlspecialchars($_POST['country']) : ''; ?>" required class="input" placeholder="India"></div>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 16px;">
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Age</label>
                        <div class="auth-input-wrap"><i class="fas fa-cake-candles"></i><input type="number" name="age" min="1" max="120" value="<?php echo isset($_POST['age']) ? intval($_POST['age']) : ''; ?>" required class="input" placeholder="25"></div>
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Company <span style="color:var(--muted-2);font-weight:600;">(optional)</span></label>
                        <div class="auth-input-wrap"><i class="fas fa-building"></i><input type="text" name="company_name" value="<?php echo isset($_POST['company_name']) ? htmlspecialchars($_POST['company_name']) : ''; ?>" class="input" placeholder="Tech Innovations Inc."></div>
                    </div>
                </div>
                <div class="field" style="margin-top: 16px;">
                    <label class="label">Company website <span style="color:var(--muted-2);font-weight:600;">(optional)</span></label>
                    <div class="auth-input-wrap"><i class="fas fa-link"></i><input type="url" name="company_website" value="<?php echo isset($_POST['company_website']) ? htmlspecialchars($_POST['company_website']) : ''; ?>" class="input" placeholder="https://example.com"></div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Password</label>
                        <div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" id="regPassword" name="password" required class="input" placeholder="Minimum 6 characters"><button type="button" class="eye" data-toggle-password="regPassword" aria-label="Show password"><i class="fas fa-eye"></i></button></div>
                    </div>
                    <div class="field" style="margin-bottom: 0;">
                        <label class="label">Confirm password</label>
                        <div class="auth-input-wrap"><i class="fas fa-lock"></i><input type="password" id="regConfirm" name="confirm_password" required class="input" placeholder="Confirm your password"><button type="button" class="eye" data-toggle-password="regConfirm" aria-label="Show password"><i class="fas fa-eye"></i></button></div>
                    </div>
                </div>
                <div class="flex items-start gap-2" style="margin: 20px 0;">
                    <input type="checkbox" id="terms" required style="width: 15px; height: 15px; margin-top: 3px; accent-color: var(--blue);">
                    <label for="terms" style="font-size: 12px; color: var(--muted); line-height: 1.6;">I agree to the <a href="/terms-conditions.php" style="color: var(--blue); font-weight: 700;">Terms &amp; Conditions</a> and <a href="/privacy-policy.php" style="color: var(--blue); font-weight: 700;">Privacy Policy</a>.</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">Create account <i class="fas fa-arrow-right"></i></button>
            </form>

            <p style="text-align: center; font-size: 13px; color: var(--muted); margin-top: 20px;">Already have an account? <a href="login.php" style="font-weight: 800; color: var(--blue);">Sign in</a></p>
            <p style="text-align: center; font-size: 11px; color: var(--muted-2); margin-top: 18px;"><i class="fas fa-lock" style="margin-right: 5px;"></i>Your information stays private and secure.</p>
        </div>
    </div>
</div>
</body>
</html>
