require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'softwares';
$current_page = 'softwares';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $keywords = trim($_POST['keywords']);
    $version = trim($_POST['version']);
    
    // Platforms logic (comma separated)
    $platforms = isset($_POST['platforms']) ? implode(', ', $_POST['platforms']) : '';
    
    $file_type = $_POST['file_type'];
    $file_path = trim($_POST['external_link']);
    
    // Store links
    $playstore_link = trim($_POST['playstore_link']);
    $appstore_link = trim($_POST['appstore_link']);
    $windows_store_link = trim($_POST['windows_store_link']);

    // Pricing logic
    $is_paid = isset($_POST['is_paid']) ? (int)$_POST['is_paid'] : 0;
    $price = $is_paid ? (float)$_POST['price'] : 0.00;
    $payment_link = $is_paid && isset($_POST['payment_link']) ? trim($_POST['payment_link']) : '';

    $logo_path = '';
    $banner_path = '';
    
    $error = '';
    $upload_dir = '../uploads/softwares/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    // Handle Logo Upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
            $logo_name = uniqid('logo_') . '.' . $ext;
            if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
                $logo_path = 'uploads/softwares/' . $logo_name;
            }
        }
    }

    // Handle Banner Upload
    if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $banner_name = uniqid('banner_') . '.' . $ext;
            if (move_uploaded_file($_FILES['banner']['tmp_name'], $upload_dir . $banner_name)) {
                $banner_path = 'uploads/softwares/' . $banner_name;
            }
        }
    }

    // Handle Software File Upload (if file_type is upload)
    if ($file_type == 'upload' && isset($_FILES['software_file']) && $_FILES['software_file']['error'] == 0) {
        $ext = strtolower(pathinfo($_FILES['software_file']['name'], PATHINFO_EXTENSION));
        $file_name = uniqid('app_') . '.' . $ext;
        if (move_uploaded_file($_FILES['software_file']['tmp_name'], $upload_dir . $file_name)) {
            $file_path = 'uploads/softwares/' . $file_name;
        } else {
            $error = "Failed to upload software file. Check server limits.";
        }
    }

    if (empty($name) || empty($description)) {
        $error = "Name and Description are required.";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();
            
            // Insert Software
            $stmt = $pdo->prepare("INSERT INTO softwares (name, description, keywords, version, platform, logo_path, banner_path, file_type, file_path, playstore_link, appstore_link, windows_store_link, is_paid, price, payment_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $keywords, $version, $platforms, $logo_path, $banner_path, $file_type, $file_path, $playstore_link, $appstore_link, $windows_store_link, $is_paid, $price, $payment_link]);
            
            $software_id = $pdo->lastInsertId();
            
            // Handle Screenshots Upload
            if (isset($_FILES['screenshots']) && !empty($_FILES['screenshots']['name'][0])) {
                $files = $_FILES['screenshots'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] == 0) {
                        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $ss_name = uniqid('ss_') . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $ss_name)) {
                                $ss_path = 'uploads/softwares/' . $ss_name;
                                $pdo->prepare("INSERT INTO software_screenshots (software_id, image_path, display_order) VALUES (?, ?, ?)")->execute([$software_id, $ss_path, $i]);
                            }
                        }
                    }
                }
            }
            
            $pdo->commit();

            logAdminActivity($pdo, 'ADD_SOFTWARE', "Added new software: " . $name);

            header("Location: softwares.php?msg=added");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Software - Hypecrews Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#6366f1', dark: '#0f172a', light: '#1e293b' }
                }
            }
        }
    </script>
    <style>
        body { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); font-family: 'Inter', sans-serif; min-height: 100vh; }
        input[type="text"], input[type="number"], textarea, select { background-color: #0f172a !important; border-color: #334155 !important; color: #fff !important; }
        input:focus, textarea:focus, select:focus { border-color: #6366f1 !important; outline: none; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2); }
    </style>
</head>
<body class="text-white">
    <div class="flex h-screen">
        <?php include 'components/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-dark border-b border-gray-800 p-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold">Add New Software</h2>
                <a href="softwares.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg"><i class="fas fa-arrow-left mr-2"></i> Back</a>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700"><p class="text-red-300"><?php echo htmlspecialchars($error); ?></p></div>
                <?php endif; ?>
                
                <div class="bg-light rounded-xl p-6 max-w-4xl mx-auto">
                    <form method="POST" action="" enctype="multipart/form-data">
                        
                        <!-- Basic Info -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-400 mb-2">Software Name *</label>
                                <input type="text" name="name" required class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Version</label>
                                <input type="text" name="version" placeholder="e.g. 1.0.0" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">Description *</label>
                                <textarea name="description" rows="5" required class="w-full px-4 py-2 rounded-lg border"></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">SEO Keywords / Tags</label>
                                <input type="text" name="keywords" placeholder="e.g. video editor, ai tools, hypecrews software" class="w-full px-4 py-2 rounded-lg border">
                                <p class="text-xs text-gray-500 mt-2">Comma separated tags. These help with SEO and categorization.</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">Supported Platforms</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Windows" checked> Windows</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Mac/Apple"> Mac/Apple</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Android"> Android</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Web"> Web</label>
                                </div>
                            </div>
                        </div>

                        <!-- Media -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">Media & Graphics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-400 mb-2">Logo (Square Image)</label>
                                <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Banner (Large Image)</label>
                                <input type="file" name="banner" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">Screenshots (Select Multiple)</label>
                                <input type="file" name="screenshots[]" multiple accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                            </div>
                        </div>

                        <!-- Pricing -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">Pricing</h3>
                        <div class="mb-6">
                            <div class="flex gap-6 mb-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="is_paid" value="0" checked onclick="document.getElementById('price_input').style.display='none';"> 
                                    Free
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="is_paid" value="1" onclick="document.getElementById('price_input').style.display='block';"> 
                                    Paid
                                </label>
                            </div>
                            
                            <div id="price_input" style="display:none;" class="bg-gray-800 p-4 rounded-lg w-full">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-400 mb-2">Price (₹)</label>
                                        <input type="number" step="0.01" name="price" placeholder="e.g. 499.00" class="w-full px-4 py-2 rounded-lg border">
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 mb-2">Payment Link</label>
                                        <input type="text" name="payment_link" placeholder="e.g. https://razorpay.me/..." class="w-full px-4 py-2 rounded-lg border">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Delivery -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">File Delivery</h3>
                        <div class="mb-6">
                            <label class="block text-gray-400 mb-2">How should users download this?</label>
                            <div class="flex gap-6 mb-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="file_type" value="google_drive" checked onclick="document.getElementById('gd_input').style.display='block'; document.getElementById('up_input').style.display='none';"> 
                                    Google Drive / External URL (Recommended)
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="file_type" value="upload" onclick="document.getElementById('gd_input').style.display='none'; document.getElementById('up_input').style.display='block';"> 
                                    Direct File Upload
                                </label>
                            </div>
                            
                            <div id="gd_input" class="bg-gray-800 p-4 rounded-lg">
                                <label class="block text-gray-400 mb-2">Google Drive File ID or Direct Link</label>
                                <input type="text" name="external_link" placeholder="e.g. 1A2B3C4D5E6F7G8H9I0J or https://mega.nz/..." class="w-full px-4 py-2 rounded-lg border">
                                <p class="text-xs text-gray-500 mt-2">If using Google Drive, just paste the File ID or the full view link.</p>
                            </div>
                            
                            <div id="up_input" class="bg-gray-800 p-4 rounded-lg" style="display:none;">
                                <label class="block text-gray-400 mb-2">Upload Software File (.exe, .zip, .apk)</label>
                                <input type="file" name="software_file" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                                <p class="text-xs text-red-400 mt-2"><i class="fas fa-exclamation-triangle"></i> Max upload size depends on your server's php.ini settings.</p>
                            </div>
                        </div>

                        <!-- Store Links -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">App Store Links (Optional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 bg-gray-800 p-4 rounded-lg">
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-google-play mr-2"></i>Play Store Link</label>
                                <input type="text" name="playstore_link" placeholder="https://play.google.com/..." class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-apple mr-2"></i>App Store Link</label>
                                <input type="text" name="appstore_link" placeholder="https://apps.apple.com/..." class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-windows mr-2"></i>Windows Store Link</label>
                                <input type="text" name="windows_store_link" placeholder="https://apps.microsoft.com/..." class="w-full px-4 py-2 rounded-lg border">
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-bold w-full md:w-auto mt-4">
                            Publish Software
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
