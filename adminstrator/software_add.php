<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'softwares';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $keywords = trim($_POST['keywords'] ?? '');
    $version = trim($_POST['version'] ?? '');
    
    // Platforms logic (comma separated)
    $platforms = isset($_POST['platforms']) ? implode(', ', $_POST['platforms']) : '';
    
    $file_type = $_POST['file_type'] ?? 'google_drive';
    $file_path = trim($_POST['external_link'] ?? '');
    
    // Store links
    $playstore_link = trim($_POST['playstore_link'] ?? '');
    $appstore_link = trim($_POST['appstore_link'] ?? '');
    $windows_store_link = trim($_POST['windows_store_link'] ?? '');

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
    if ($file_type == 'upload' && isset($_FILES['software_file'])) {
        if ($_FILES['software_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['software_file']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid('app_') . '.' . $ext;
            if (move_uploaded_file($_FILES['software_file']['tmp_name'], $upload_dir . $file_name)) {
                $file_path = 'uploads/softwares/' . $file_name;
            } else {
                $error = "Failed to move uploaded software file. Check directory permissions.";
            }
        } elseif ($_FILES['software_file']['error'] != UPLOAD_ERR_NO_FILE) {
            $error = "Software file upload failed. Error code: " . $_FILES['software_file']['error'];
        }
    }

    if (empty($name) || empty($description)) {
        $error = "Name and Description are required.";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();
            
            // Insert Software
            $stmt = $pdo->prepare("INSERT INTO softwares (name, description, keywords, version, platform, logo_path, banner_path, file_type, file_path, playstore_link, appstore_link, windows_store_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $keywords, $version, $platforms, $logo_path, $banner_path, $file_type, $file_path, $playstore_link, $appstore_link, $windows_store_link]);
            
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
            
            <div class="flex-1 overflow-y-auto bg-[#0f172a] relative pb-24">
                <div class="max-w-5xl mx-auto p-6">
                    <?php if (!empty($error)): ?>
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/50 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        <p class="text-red-400 font-medium"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="addSoftwareForm">
                        
                        <!-- 1. App Details Card -->
                        <div class="bg-[#1e293b] border border-slate-700/50 rounded-2xl p-8 mb-6 shadow-lg shadow-black/20">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center text-xl">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-100">App details</h3>
                                    <p class="text-sm text-slate-400">Basic information about your software.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-300">Software Name <span class="text-red-400">*</span></label>
                                    <input type="text" name="name" required class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-100 transition-all placeholder-slate-500" placeholder="Enter app name">
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-300">Short description <span class="text-red-400">*</span></label>
                                    <textarea name="description" rows="4" required class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-100 transition-all placeholder-slate-500" placeholder="Briefly describe what your software does"></textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300">Version</label>
                                    <input type="text" name="version" placeholder="1.0.0" class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-100 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300">Tags / Keywords</label>
                                    <input type="text" name="keywords" placeholder="e.g. video editor, tools" class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-100 transition-all">
                                </div>
                                <div class="space-y-3 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-300">Supported Platforms</label>
                                    <div class="flex flex-wrap gap-3">
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="platforms[]" value="Windows" class="peer sr-only" checked>
                                            <div class="px-5 py-2.5 rounded-full border border-slate-600 text-slate-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-all hover:bg-slate-700 flex items-center gap-2">
                                                <i class="fab fa-windows"></i> Windows
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="platforms[]" value="Mac/Apple" class="peer sr-only">
                                            <div class="px-5 py-2.5 rounded-full border border-slate-600 text-slate-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-all hover:bg-slate-700 flex items-center gap-2">
                                                <i class="fab fa-apple"></i> Mac
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="platforms[]" value="Android" class="peer sr-only">
                                            <div class="px-5 py-2.5 rounded-full border border-slate-600 text-slate-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-all hover:bg-slate-700 flex items-center gap-2">
                                                <i class="fab fa-android"></i> Android
                                            </div>
                                        </label>
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="platforms[]" value="Web" class="peer sr-only">
                                            <div class="px-5 py-2.5 rounded-full border border-slate-600 text-slate-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white transition-all hover:bg-slate-700 flex items-center gap-2">
                                                <i class="fas fa-globe"></i> Web
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Graphics Card -->
                        <div class="bg-[#1e293b] border border-slate-700/50 rounded-2xl p-8 mb-6 shadow-lg shadow-black/20">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl">
                                    <i class="fas fa-image"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-100">Graphics</h3>
                                    <p class="text-sm text-slate-400">App icon, feature graphic, and screenshots.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300">App Icon (512x512)</label>
                                    <div class="relative group cursor-pointer">
                                        <input type="file" name="logo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="w-full h-32 border-2 border-dashed border-slate-600 rounded-xl bg-[#0f172a] flex flex-col items-center justify-center text-slate-400 group-hover:border-emerald-500 group-hover:text-emerald-400 transition-all">
                                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                                            <span class="text-sm">Upload Icon</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300">Feature Graphic (1024x500)</label>
                                    <div class="relative group cursor-pointer">
                                        <input type="file" name="banner" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="w-full h-32 border-2 border-dashed border-slate-600 rounded-xl bg-[#0f172a] flex flex-col items-center justify-center text-slate-400 group-hover:border-emerald-500 group-hover:text-emerald-400 transition-all">
                                            <i class="fas fa-cloud-upload-alt text-2xl mb-2"></i>
                                            <span class="text-sm">Upload Banner</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2 md:col-span-2">
                                    <label class="block text-sm font-semibold text-slate-300">Screenshots (Select multiple)</label>
                                    <div class="relative group cursor-pointer">
                                        <input type="file" name="screenshots[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="w-full h-40 border-2 border-dashed border-slate-600 rounded-xl bg-[#0f172a] flex flex-col items-center justify-center text-slate-400 group-hover:border-emerald-500 group-hover:text-emerald-400 transition-all">
                                            <i class="fas fa-images text-3xl mb-2"></i>
                                            <span class="text-sm font-medium">Drag & drop or browse to upload screenshots</span>
                                            <span class="text-xs text-slate-500 mt-1">JPEG or PNG, max 8MB per image</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. App Release / Delivery -->
                        <div class="bg-[#1e293b] border border-slate-700/50 rounded-2xl p-8 mb-6 shadow-lg shadow-black/20">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center text-xl">
                                    <i class="fas fa-box-open"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-100">App Release</h3>
                                    <p class="text-sm text-slate-400">Setup how users will download the software file.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="file_type" value="google_drive" checked class="peer sr-only" onclick="document.getElementById('gd_input').style.display='block'; document.getElementById('up_input').style.display='none';">
                                        <div class="p-4 rounded-xl border-2 border-slate-700 bg-[#0f172a] text-slate-300 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 transition-all flex items-center gap-3">
                                            <i class="fab fa-google-drive text-2xl text-slate-500 peer-checked:text-purple-400"></i>
                                            <div>
                                                <h4 class="font-bold">External URL</h4>
                                                <p class="text-xs text-slate-500">Google Drive, Mega, etc.</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="flex-1 cursor-pointer">
                                        <input type="radio" name="file_type" value="upload" class="peer sr-only" onclick="document.getElementById('gd_input').style.display='none'; document.getElementById('up_input').style.display='block';">
                                        <div class="p-4 rounded-xl border-2 border-slate-700 bg-[#0f172a] text-slate-300 peer-checked:border-purple-500 peer-checked:bg-purple-500/10 transition-all flex items-center gap-3">
                                            <i class="fas fa-file-upload text-2xl text-slate-500 peer-checked:text-purple-400"></i>
                                            <div>
                                                <h4 class="font-bold">Direct Upload</h4>
                                                <p class="text-xs text-slate-500">Host on this server</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <div id="gd_input" class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300">Download URL</label>
                                    <input type="text" name="external_link" placeholder="https://" class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 text-slate-100 transition-all">
                                </div>
                                
                                <div id="up_input" class="space-y-2" style="display:none;">
                                    <label class="block text-sm font-semibold text-slate-300">Software File (.zip, .exe, .apk)</label>
                                    <div class="relative group cursor-pointer">
                                        <input type="file" name="software_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="w-full h-20 border-2 border-dashed border-slate-600 rounded-xl bg-[#0f172a] flex items-center justify-center text-slate-400 group-hover:border-purple-500 group-hover:text-purple-400 transition-all">
                                            <i class="fas fa-file-archive text-xl mr-3"></i>
                                            <span class="text-sm">Select file to upload</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Store Presence -->
                        <div class="bg-[#1e293b] border border-slate-700/50 rounded-2xl p-8 mb-6 shadow-lg shadow-black/20">
                            <div class="flex items-center gap-3 mb-6 border-b border-slate-700/50 pb-4">
                                <div class="w-10 h-10 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center text-xl">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-slate-100">Store Presence</h3>
                                    <p class="text-sm text-slate-400">Links to official app stores (Optional).</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300"><i class="fab fa-google-play mr-1 text-green-400"></i> Google Play</label>
                                    <input type="text" name="playstore_link" placeholder="https://play.google.com/..." class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-slate-100 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300"><i class="fab fa-apple mr-1 text-slate-300"></i> App Store</label>
                                    <input type="text" name="appstore_link" placeholder="https://apps.apple.com/..." class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-slate-100 transition-all">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-slate-300"><i class="fab fa-windows mr-1 text-blue-400"></i> MS Store</label>
                                    <input type="text" name="windows_store_link" placeholder="https://apps.microsoft.com/..." class="w-full px-4 py-3 rounded-xl bg-[#0f172a] border border-slate-700 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 text-slate-100 transition-all">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sticky Footer Action Bar -->
                        <div class="fixed bottom-0 left-0 md:left-64 right-0 bg-[#0f172a]/90 backdrop-blur-md border-t border-slate-800 p-4 px-6 md:px-10 flex justify-end z-30 shadow-[0_-10px_30px_rgba(0,0,0,0.5)]">
                            <button type="button" onclick="window.location.href='softwares.php'" class="px-6 py-2.5 text-slate-300 hover:text-white font-medium mr-4">Discard</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-blue-600/20 transition-all flex items-center gap-2">
                                <i class="fas fa-check"></i> Save & Publish
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
