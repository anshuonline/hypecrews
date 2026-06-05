<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'softwares';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: softwares.php");
    exit;
}

$id = $_GET['id'];

// Fetch existing software
$stmt = $pdo->prepare("SELECT * FROM softwares WHERE id = ?");
$stmt->execute([$id]);
$software = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$software) {
    header("Location: softwares.php");
    exit;
}

// Fetch existing screenshots
$stmt = $pdo->prepare("SELECT * FROM software_screenshots WHERE software_id = ? ORDER BY display_order ASC, id ASC");
$stmt->execute([$id]);
$existing_screenshots = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $keywords = trim($_POST['keywords']);
    $version = trim($_POST['version']);
    
    // Platforms
    $platforms = isset($_POST['platforms']) ? implode(', ', $_POST['platforms']) : '';
    
    $file_type = $_POST['file_type'];
    $file_path = trim($_POST['external_link']);
    if ($file_type == 'upload') {
        $file_path = $software['file_path'];
    }
    
    // Store links
    $playstore_link = trim($_POST['playstore_link']);
    $appstore_link = trim($_POST['appstore_link']);
    $windows_store_link = trim($_POST['windows_store_link']);


    
    $error = '';
    $upload_dir = '../uploads/softwares/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if (empty($name) || empty($description)) {
        $error = "Name and Description are required.";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            // 1. Handle Logo Update
            $logo_path = $software['logo_path'];
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                    $logo_name = uniqid('logo_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_dir . $logo_name)) {
                        // Delete old
                        if (!empty($logo_path) && file_exists('../' . $logo_path)) unlink('../' . $logo_path);
                        $logo_path = 'uploads/softwares/' . $logo_name;
                    }
                }
            }

            // 2. Handle Banner Update
            $banner_path = $software['banner_path'];
            if (isset($_FILES['banner']) && $_FILES['banner']['error'] == 0) {
                $ext = strtolower(pathinfo($_FILES['banner']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $banner_name = uniqid('banner_') . '.' . $ext;
                    if (move_uploaded_file($_FILES['banner']['tmp_name'], $upload_dir . $banner_name)) {
                        // Delete old
                        if (!empty($banner_path) && file_exists('../' . $banner_path)) unlink('../' . $banner_path);
                        $banner_path = 'uploads/softwares/' . $banner_name;
                    }
                }
            }

            // 3. Handle Deleting Selected Screenshots
            if (isset($_POST['delete_screenshots']) && is_array($_POST['delete_screenshots'])) {
                foreach ($_POST['delete_screenshots'] as $ss_id) {
                    // Get path to delete file
                    $stmt_ss = $pdo->prepare("SELECT image_path FROM software_screenshots WHERE id = ? AND software_id = ?");
                    $stmt_ss->execute([$ss_id, $id]);
                    $ss_data = $stmt_ss->fetch(PDO::FETCH_ASSOC);
                    if ($ss_data) {
                        if (!empty($ss_data['image_path']) && file_exists('../' . $ss_data['image_path'])) {
                            unlink('../' . $ss_data['image_path']);
                        }
                        $pdo->prepare("DELETE FROM software_screenshots WHERE id = ?")->execute([$ss_id]);
                    }
                }
            }

            // 4. Handle Screenshot Reordering
            if (isset($_POST['screenshot_order']) && is_array($_POST['screenshot_order'])) {
                foreach ($_POST['screenshot_order'] as $ss_id => $order) {
                    $order = (int)$order;
                    $pdo->prepare("UPDATE software_screenshots SET display_order = ? WHERE id = ? AND software_id = ?")->execute([$order, $ss_id, $id]);
                }
            }

            // 5. Handle Adding New Screenshots
            if (isset($_FILES['screenshots']) && !empty($_FILES['screenshots']['name'][0])) {
                $files = $_FILES['screenshots'];
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] == 0) {
                        $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                            $ss_name = uniqid('ss_') . '.' . $ext;
                            if (move_uploaded_file($files['tmp_name'][$i], $upload_dir . $ss_name)) {
                                $ss_path = 'uploads/softwares/' . $ss_name;
                                // New screenshots get a high default display_order so they appear at the end
                                $pdo->prepare("INSERT INTO software_screenshots (software_id, image_path, display_order) VALUES (?, ?, 99)")->execute([$id, $ss_path]);
                            }
                        }
                    }
                }
            }

            // 6. Update Software Record
            $stmt = $pdo->prepare("UPDATE softwares SET name=?, description=?, keywords=?, version=?, platform=?, logo_path=?, banner_path=?, file_type=?, file_path=?, playstore_link=?, appstore_link=?, windows_store_link=? WHERE id=?");
            $stmt->execute([$name, $description, $keywords, $version, $platforms, $logo_path, $banner_path, $file_type, $file_path, $playstore_link, $appstore_link, $windows_store_link, $id]);
            
            $pdo->commit();

            logAdminActivity($pdo, 'UPDATE_SOFTWARE', "Updated software: " . $name);

            header("Location: softwares.php?msg=updated");
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
    <title>Edit Software - Hypecrews Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#6366f1', dark: '#0f172a', light: '#1e293b' } } } }
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
                <h2 class="text-2xl font-bold">Edit Software</h2>
                <a href="softwares.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg"><i class="fas fa-arrow-left mr-2"></i> Back</a>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700"><p class="text-red-300"><?php echo htmlspecialchars($error); ?></p></div>
                <?php endif; ?>
                
                <div class="bg-light rounded-xl p-6 max-w-4xl mx-auto">
                    <form method="POST" action="" enctype="multipart/form-data">
                        
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-400 mb-2">Software Name *</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($software['name']); ?>" required class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Version</label>
                                <input type="text" name="version" value="<?php echo htmlspecialchars($software['version']); ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">Description *</label>
                                <textarea name="description" rows="5" required class="w-full px-4 py-2 rounded-lg border"><?php echo htmlspecialchars($software['description']); ?></textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">SEO Keywords / Tags</label>
                                <input type="text" name="keywords" value="<?php echo htmlspecialchars($software['keywords'] ?? ''); ?>" placeholder="e.g. video editor, ai tools, hypecrews software" class="w-full px-4 py-2 rounded-lg border">
                                <p class="text-xs text-gray-500 mt-2">Comma separated tags.</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 mb-2">Supported Platforms</label>
                                <?php $platforms = explode(', ', $software['platform']); ?>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Windows" <?php echo in_array('Windows', $platforms)?'checked':''; ?>> Windows</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Mac/Apple" <?php echo in_array('Mac/Apple', $platforms)?'checked':''; ?>> Mac/Apple</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Android" <?php echo in_array('Android', $platforms)?'checked':''; ?>> Android</label>
                                    <label class="flex items-center gap-2"><input type="checkbox" name="platforms[]" value="Web" <?php echo in_array('Web', $platforms)?'checked':''; ?>> Web</label>
                                </div>
                            </div>
                        </div>

                        <!-- Media & Graphics -->
                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">Media & Graphics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="bg-gray-800 p-4 rounded-lg">
                                <label class="block text-gray-400 mb-2">Update Logo (Square Image)</label>
                                <?php if(!empty($software['logo_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($software['logo_path']); ?>" class="w-16 h-16 object-cover rounded mb-3 bg-dark">
                                <?php endif; ?>
                                <input type="file" name="logo" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                                <p class="text-xs text-gray-500 mt-2">Leave blank to keep existing logo.</p>
                            </div>
                            <div class="bg-gray-800 p-4 rounded-lg">
                                <label class="block text-gray-400 mb-2">Update Banner (Large Image)</label>
                                <?php if(!empty($software['banner_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($software['banner_path']); ?>" class="w-full h-16 object-cover rounded mb-3 bg-dark">
                                <?php endif; ?>
                                <input type="file" name="banner" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                                <p class="text-xs text-gray-500 mt-2">Leave blank to keep existing banner.</p>
                            </div>
                            
                            <div class="md:col-span-2 bg-gray-800 p-4 rounded-lg">
                                <label class="block text-gray-400 mb-4 font-semibold border-b border-gray-700 pb-2">Manage Screenshots</label>
                                
                                <?php if(!empty($existing_screenshots)): ?>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                    <?php foreach($existing_screenshots as $ss): ?>
                                    <div class="relative group rounded overflow-hidden border border-gray-600 bg-gray-900">
                                        <img src="../<?php echo htmlspecialchars($ss['image_path']); ?>" class="w-full h-24 object-cover">
                                        
                                        <!-- Reorder Input -->
                                        <div class="p-2 flex items-center justify-between">
                                            <label class="text-xs text-gray-400 flex items-center">
                                                <i class="fas fa-sort mr-1"></i> Order:
                                                <input type="number" name="screenshot_order[<?php echo $ss['id']; ?>]" value="<?php echo (int)$ss['display_order']; ?>" class="w-12 ml-2 px-1 py-1 text-xs rounded bg-dark border border-gray-600">
                                            </label>
                                        </div>

                                        <label class="absolute top-1 right-1 bg-red-600 text-white text-xs px-2 py-1 rounded cursor-pointer opacity-90 hover:opacity-100 transition-opacity shadow">
                                            <input type="checkbox" name="delete_screenshots[]" value="<?php echo $ss['id']; ?>" class="mr-1"> Delete
                                        </label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500 mb-4">No screenshots uploaded yet.</p>
                                <?php endif; ?>

                                <label class="block text-gray-400 mb-2 mt-4">Add New Screenshots (Select Multiple)</label>
                                <input type="file" name="screenshots[]" multiple accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-gray-700 file:text-white">
                            </div>
                        </div>

                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">File Delivery</h3>
                        <div class="mb-6">
                            <div class="flex gap-6 mb-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="file_type" value="google_drive" <?php echo $software['file_type']=='google_drive'?'checked':''; ?> onclick="document.getElementById('gd_input').style.display='block';"> 
                                    Google Drive / External URL
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio" name="file_type" value="upload" <?php echo $software['file_type']=='upload'?'checked':''; ?> onclick="document.getElementById('gd_input').style.display='none';"> 
                                    Direct File Upload (Keeps existing file)
                                </label>
                            </div>
                            
                            <div id="gd_input" class="bg-gray-800 p-4 rounded-lg" style="display:<?php echo $software['file_type']=='google_drive'?'block':'none'; ?>;">
                                <label class="block text-gray-400 mb-2">Google Drive File ID or Direct Link</label>
                                <input type="text" name="external_link" value="<?php echo $software['file_type']=='google_drive' ? htmlspecialchars($software['file_path']) : ''; ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                        </div>

                        <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2 mt-8">App Store Links (Optional)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 bg-gray-800 p-4 rounded-lg">
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-google-play mr-2"></i>Play Store Link</label>
                                <input type="text" name="playstore_link" value="<?php echo htmlspecialchars($software['playstore_link']); ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-apple mr-2"></i>App Store Link</label>
                                <input type="text" name="appstore_link" value="<?php echo htmlspecialchars($software['appstore_link']); ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2"><i class="fab fa-windows mr-2"></i>Windows Store Link</label>
                                <input type="text" name="windows_store_link" value="<?php echo htmlspecialchars($software['windows_store_link']); ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                        </div>
                        
                        <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-bold w-full md:w-auto mt-4">
                            Update Details & Images
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
