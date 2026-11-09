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

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $mobile_number = trim($_POST['mobile_number']);
    $country = trim($_POST['country']);
    $age = (int)$_POST['age'];
    $company_name = trim($_POST['company_name']);
    $company_website = trim($_POST['company_website']);

    if (empty($first_name) || empty($last_name) || empty($email) ||
        empty($mobile_number) || empty($country) || empty($age)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $user_id]);
            if ($stmt->fetch()) {
                $error = "Email already used by another account.";
            } else {
                $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile_number = ?, country = ?, age = ?, company_name = ?, company_website = ? WHERE id = ?");
                $stmt->execute([$first_name, $last_name, $email, $mobile_number, $country, $age, $company_name, $company_website, $user_id]);

                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $success = "Profile updated successfully!";

                $stmt = $pdo->prepare("SELECT username, first_name, last_name, email, mobile_number, country, age, company_name, company_website, created_at FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user = $stmt->fetch();
            }
        } catch (PDOException $e) {
            $error = "Error updating profile.";
        }
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
$pageTitle = "My Profile — Hypecrews";
$robots = "noindex, nofollow";
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<main class="dash-wrap">
    <div class="shell shell-wide">
        <div class="flex flex-wrap items-end justify-between gap-5" style="margin-bottom: 30px;" data-aos="fade-up">
            <div>
                <span class="eyebrow">Client portal</span>
                <h1 class="dash-title" style="margin-top: 12px;">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>.</h1>
                <p style="font-size: 13.5px; color: var(--muted); margin-top: 6px;">Manage your identity, security and workspace from one place.</p>
            </div>
            <a href="/track_orders.php" class="btn btn-primary">Track my orders <i class="fas fa-arrow-right"></i></a>
        </div>

        <?php if ($error): ?><div class="alert alert-error" data-aos="fade-up"><i class="fas fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error); ?></span></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success" data-aos="fade-up"><i class="fas fa-circle-check"></i><span><?php echo htmlspecialchars($success); ?></span></div><?php endif; ?>

        <div class="grid lg:grid-cols-[260px_1fr] gap-7 items-start">
            <?php $current_user_page = 'profile'; include 'components/user_sidebar.php'; ?>

            <div style="display: grid; gap: 22px;">
                <!-- Snapshot -->
                <div class="dash-card" data-aos="fade-up">
                    <div class="flex flex-wrap items-center gap-5">
                        <div class="side-avatar" style="width: 64px; height: 64px; font-size: 24px; background: linear-gradient(140deg, var(--navy), var(--blue)); color: #fff;"><?php echo strtoupper(substr(htmlspecialchars($user['first_name']), 0, 1)); ?></div>
                        <div style="flex: 1; min-width: 180px;">
                            <h2 style="font-size: 20px; margin: 0;"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
                            <p style="font-size: 12.5px; color: var(--muted); margin-top: 3px;">@<?php echo htmlspecialchars($user['username']); ?> · <?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-green"><i class="fas fa-circle" style="font-size: 5px;"></i> Active client</span>
                            <p style="font-size: 10.5px; color: var(--muted-2); margin-top: 7px;">Member since <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Edit profile -->
                <div class="dash-card" data-aos="fade-up" data-aos-delay="60">
                    <div class="flex items-center justify-between" style="margin-bottom: 4px;">
                        <h2 style="font-size: 19px; margin: 0;"><i class="fas fa-id-card" style="color: var(--blue); margin-right: 8px;"></i>Personal details</h2>
                        <a href="/change_password.php" class="link-arrow" style="font-size: 10px;">Security <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <p style="font-size: 12.5px; color: var(--muted); margin-top: 5px;">Keep your contact and company information up to date.</p>

                    <form method="POST" style="margin-top: 22px;">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="field" style="margin-bottom: 0;"><label class="label">First name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required class="input"></div>
                            <div class="field" style="margin-bottom: 0;"><label class="label">Last name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required class="input"></div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 16px;">
                            <div class="field" style="margin-bottom: 0;"><label class="label">Email address</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="input"></div>
                            <div class="field" style="margin-bottom: 0;"><label class="label">Mobile number</label><input type="tel" name="mobile_number" value="<?php echo htmlspecialchars($user['mobile_number']); ?>" required class="input"></div>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-4" style="margin-top: 16px;">
                            <div class="field" style="margin-bottom: 0;"><label class="label">Age</label><input type="number" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required min="13" max="120" class="input"></div>
                            <div class="field" style="margin-bottom: 0; grid-column: span 2;"><label class="label">Country</label><input type="text" name="country" value="<?php echo htmlspecialchars($user['country']); ?>" required class="input"></div>
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 16px;">
                            <div class="field" style="margin-bottom: 0;"><label class="label">Company <span style="color:var(--muted-2);font-weight:600;">(optional)</span></label><input type="text" name="company_name" value="<?php echo htmlspecialchars($user['company_name']); ?>" placeholder="Leave blank if none" class="input"></div>
                            <div class="field" style="margin-bottom: 0;"><label class="label">Company website <span style="color:var(--muted-2);font-weight:600;">(optional)</span></label><input type="url" name="company_website" value="<?php echo htmlspecialchars($user['company_website']); ?>" placeholder="https://" class="input"></div>
                        </div>
                        <div class="flex justify-end" style="margin-top: 24px;">
                            <button type="submit" class="btn btn-primary">Save changes <i class="fas fa-check"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'components/footer.php'; ?>
</body>
</html>
