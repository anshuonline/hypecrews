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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066cc',
                        apple_bg: '#f5f5f7',
                        apple_text: '#1d1d1f',
                        apple_muted: '#86868b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #f5f5f7; font-family: 'Inter', sans-serif; color: #1d1d1f; }
        .glass-card {
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .apple-input {
            background-color: #f5f5f7;
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 14px;
            color: #1d1d1f;
            transition: all 0.2s ease;
        }
        .apple-input:focus {
            background-color: #ffffff;
            border-color: #0066cc;
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
            outline: none;
        }
        .upload-area {
            border: 2px dashed rgba(0,0,0,0.1);
            border-radius: 16px;
            background-color: #fafafa;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #0066cc;
            background-color: #f0f7ff;
        }
        
        /* Custom UI Checkboxes/Radio */
        .platform-checkbox:checked + div {
            background-color: #0066cc;
            color: white;
            border-color: #0066cc;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }
        .radio-card:checked + div {
            border-color: #0066cc;
            background-color: #f8fbff;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.1);
        }
        .radio-card:checked + div .icon-main {
            color: #0066cc;
        }
        .radio-card:checked + div .radio-indicator {
            border-color: #0066cc;
            background-color: #0066cc;
        }
        .radio-card:checked + div .radio-indicator::after {
            content: '';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 8px; height: 8px;
            background: white;
            border-radius: 50%;
        }
        .radio-indicator {
            width: 20px; height: 20px;
            border: 2px solid #d1d1d6;
            border-radius: 50%;
            position: relative;
            transition: all 0.2s;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d1d6; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #86868b; }
        
        .header-blur {
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(245, 245, 247, 0.8);
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">
    
    <!-- Sidebar -->
    <div class="h-full flex-shrink-0 z-50">
        <?php include 'components/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col relative w-full min-w-0">
        
        <!-- Header -->
        <header class="header-blur border-b border-black/5 p-6 flex justify-between items-center z-40 absolute top-0 w-full">
            <div>
                <h1 class="text-2xl font-bold text-apple_text tracking-tight">Add New Software</h1>
                <p class="text-xs text-apple_muted font-medium mt-1 uppercase tracking-wider">Create a new product listing</p>
            </div>
            <a href="softwares.php" class="bg-white border border-black/10 hover:bg-gray-50 text-apple_text font-semibold px-5 py-2.5 rounded-full shadow-sm transition-all flex items-center text-sm">
                <i class="fas fa-arrow-left mr-2"></i> Cancel
            </a>
        </header>
        
        <!-- Form Area -->
        <div class="flex-1 overflow-y-auto pt-28 pb-24 px-6 md:px-12 bg-apple_bg relative scroll-smooth">
            <div class="max-w-6xl mx-auto">
                
                <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 flex items-center gap-3 shadow-sm">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-500 shrink-0">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-red-800">Error</h4>
                        <p class="text-sm text-red-600 font-medium"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" enctype="multipart/form-data" id="addSoftwareForm" class="flex flex-col lg:flex-row gap-8">
                    
                    <!-- Left Column: Main Content (70%) -->
                    <div class="flex-[2] space-y-8">
                        
                        <!-- 1. App Details Card -->
                        <div class="glass-card p-8">
                            <h3 class="text-lg font-bold text-apple_text mb-6 flex items-center">
                                <span class="w-8 h-8 rounded-full bg-blue-50 text-primary flex items-center justify-center mr-3 text-sm"><i class="fas fa-info"></i></span>
                                Basic Information
                            </h3>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Software Name <span class="text-red-500">*</span></label>
                                    <input type="text" name="name" required class="apple-input w-full px-4 py-3 text-base font-semibold" placeholder="e.g. Hypecrews Studio">
                                </div>
                                
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Description <span class="text-red-500">*</span></label>
                                    <textarea name="description" rows="5" required class="apple-input w-full px-4 py-3 text-sm leading-relaxed" placeholder="Write a comprehensive description about the software's features and benefits..."></textarea>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">SEO Keywords / Tags</label>
                                        <input type="text" name="keywords" class="apple-input w-full px-4 py-3 text-sm" placeholder="video editor, tools, utility">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Supported Platforms</label>
                                        <div class="flex flex-wrap gap-2">
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="platforms[]" value="Windows" class="platform-checkbox sr-only" checked>
                                                <div class="px-4 py-2 rounded-xl border border-black/10 text-apple_text bg-white text-sm font-medium transition-all hover:bg-gray-50 flex items-center gap-1.5">
                                                    <i class="fab fa-windows"></i> Windows
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="platforms[]" value="Mac/Apple" class="platform-checkbox sr-only">
                                                <div class="px-4 py-2 rounded-xl border border-black/10 text-apple_text bg-white text-sm font-medium transition-all hover:bg-gray-50 flex items-center gap-1.5">
                                                    <i class="fab fa-apple"></i> Mac
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="platforms[]" value="Android" class="platform-checkbox sr-only">
                                                <div class="px-4 py-2 rounded-xl border border-black/10 text-apple_text bg-white text-sm font-medium transition-all hover:bg-gray-50 flex items-center gap-1.5">
                                                    <i class="fab fa-android"></i> Android
                                                </div>
                                            </label>
                                            <label class="cursor-pointer">
                                                <input type="checkbox" name="platforms[]" value="Web" class="platform-checkbox sr-only">
                                                <div class="px-4 py-2 rounded-xl border border-black/10 text-apple_text bg-white text-sm font-medium transition-all hover:bg-gray-50 flex items-center gap-1.5">
                                                    <i class="fas fa-globe"></i> Web
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Graphics & Media Card -->
                        <div class="glass-card p-8">
                            <h3 class="text-lg font-bold text-apple_text mb-6 flex items-center">
                                <span class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mr-3 text-sm"><i class="fas fa-image"></i></span>
                                Media & Graphics
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">App Icon (512x512)</label>
                                    <div class="relative group cursor-pointer upload-area h-32 flex flex-col items-center justify-center" id="iconUploadBox">
                                        <input type="file" name="logo" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewSingleImage(this, 'iconPreview', 'iconUploadBox')">
                                        <div class="text-center transition-opacity" id="iconPreviewContainer">
                                            <div class="w-10 h-10 bg-black/5 rounded-full flex items-center justify-center mx-auto mb-2 text-apple_muted group-hover:text-primary transition-colors"><i class="fas fa-cloud-upload-alt"></i></div>
                                            <span class="text-sm font-medium text-apple_text">Upload Icon</span>
                                        </div>
                                        <img id="iconPreview" src="" class="absolute inset-0 w-full h-full object-contain p-2 hidden rounded-2xl z-0">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Feature Banner (1024x500)</label>
                                    <div class="relative group cursor-pointer upload-area h-32 flex flex-col items-center justify-center" id="bannerUploadBox">
                                        <input type="file" name="banner" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewSingleImage(this, 'bannerPreview', 'bannerUploadBox')">
                                        <div class="text-center transition-opacity" id="bannerPreviewContainer">
                                            <div class="w-10 h-10 bg-black/5 rounded-full flex items-center justify-center mx-auto mb-2 text-apple_muted group-hover:text-primary transition-colors"><i class="fas fa-image"></i></div>
                                            <span class="text-sm font-medium text-apple_text">Upload Banner</span>
                                        </div>
                                        <img id="bannerPreview" src="" class="absolute inset-0 w-full h-full object-cover p-1 hidden rounded-xl z-0 opacity-80">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Screenshots (Multiple)</label>
                                <div class="relative group cursor-pointer upload-area h-40 flex flex-col items-center justify-center" id="ssUploadBox">
                                    <input type="file" name="screenshots[]" multiple accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="previewScreenshots(this)">
                                    <div class="text-center transition-opacity" id="ssPreviewContainer">
                                        <div class="w-12 h-12 bg-black/5 rounded-full flex items-center justify-center mx-auto mb-3 text-apple_muted group-hover:text-primary transition-colors text-xl"><i class="fas fa-images"></i></div>
                                        <span class="text-sm font-bold text-apple_text block mb-1">Drag & Drop Screenshots</span>
                                        <span class="text-xs text-apple_muted">JPEG or PNG up to 8MB</span>
                                    </div>
                                </div>
                                <!-- Screenshot thumbnails will appear here -->
                                <div id="screenshotsPreviewRow" class="flex gap-3 overflow-x-auto mt-4 py-2 hidden"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Settings (30%) -->
                    <div class="flex-1 space-y-8">
                        
                        <!-- 3. Publishing Settings -->
                        <div class="glass-card p-6">
                            <h3 class="text-sm font-bold text-apple_text uppercase tracking-wider border-b border-black/5 pb-3 mb-5 flex items-center">
                                <i class="fas fa-cog text-apple_muted mr-2"></i> Settings
                            </h3>
                            
                            <div class="space-y-5">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Current Version</label>
                                    <input type="text" name="version" placeholder="1.0.0" class="apple-input w-full px-4 py-3 text-sm font-mono bg-black/5">
                                </div>
                            </div>
                        </div>

                        <!-- 4. Delivery Method -->
                        <div class="glass-card p-6">
                            <h3 class="text-sm font-bold text-apple_text uppercase tracking-wider border-b border-black/5 pb-3 mb-5 flex items-center">
                                <i class="fas fa-download text-apple_muted mr-2"></i> Delivery Method
                            </h3>
                            
                            <div class="space-y-4">
                                <label class="block cursor-pointer relative">
                                    <input type="radio" name="file_type" value="google_drive" checked class="radio-card sr-only" onclick="document.getElementById('gd_input').style.display='block'; document.getElementById('up_input').style.display='none';">
                                    <div class="p-4 rounded-xl border-2 border-black/5 bg-white transition-all flex items-center gap-4">
                                        <div class="radio-indicator"></div>
                                        <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0 icon-main"><i class="fas fa-link"></i></div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-apple_text">External URL</h4>
                                            <p class="text-xs text-apple_muted mt-0.5">Google Drive, OneDrive</p>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="block cursor-pointer relative">
                                    <input type="radio" name="file_type" value="upload" class="radio-card sr-only" onclick="document.getElementById('gd_input').style.display='none'; document.getElementById('up_input').style.display='block';">
                                    <div class="p-4 rounded-xl border-2 border-black/5 bg-white transition-all flex items-center gap-4">
                                        <div class="radio-indicator"></div>
                                        <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 icon-main"><i class="fas fa-cloud-upload-alt"></i></div>
                                        <div class="flex-1">
                                            <h4 class="text-sm font-bold text-apple_text">Direct Upload</h4>
                                            <p class="text-xs text-apple_muted mt-0.5">Host on this server</p>
                                        </div>
                                    </div>
                                </label>

                                <div id="gd_input" class="pt-2 animate-fade-in">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Download Link URL</label>
                                    <input type="url" name="external_link" placeholder="https://..." class="apple-input w-full px-4 py-3 text-sm">
                                </div>
                                
                                <div id="up_input" class="pt-2 hidden animate-fade-in">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Upload File (.zip, .exe)</label>
                                    <div class="relative group cursor-pointer upload-area h-16 flex items-center justify-center">
                                        <input type="file" name="software_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="document.getElementById('fileNameLabel').innerText = this.files[0]?.name || 'Select File'">
                                        <div class="flex items-center text-apple_text text-sm font-medium">
                                            <i class="fas fa-file-archive text-apple_muted mr-2"></i>
                                            <span id="fileNameLabel">Choose File...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. App Stores -->
                        <div class="glass-card p-6">
                            <h3 class="text-sm font-bold text-apple_text uppercase tracking-wider border-b border-black/5 pb-3 mb-5 flex items-center">
                                <i class="fas fa-store text-apple_muted mr-2"></i> App Stores
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-apple mr-1.5 text-black"></i> App Store</label>
                                    <input type="url" name="appstore_link" placeholder="https://apps.apple.com/..." class="apple-input w-full px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-google-play mr-1.5 text-emerald-500"></i> Google Play</label>
                                    <input type="url" name="playstore_link" placeholder="https://play.google.com/..." class="apple-input w-full px-4 py-2.5 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-windows mr-1.5 text-blue-500"></i> Windows Store</label>
                                    <input type="url" name="windows_store_link" placeholder="https://apps.microsoft.com/..." class="apple-input w-full px-4 py-2.5 text-sm">
                                </div>
                            </div>
                        </div>

                    </div>
                    
                </form>
            </div>
        </div>
        
        <!-- Sticky Footer -->
        <div class="absolute bottom-0 w-full glass-card rounded-none rounded-t-[32px] p-4 px-8 border-t border-black/5 flex justify-end items-center z-40 bg-white/80 backdrop-blur-xl">
            <p class="text-xs text-apple_muted font-medium mr-auto hidden sm:block">Please ensure all required fields are filled before saving.</p>
            <button type="button" onclick="window.location.href='softwares.php'" class="px-6 py-3 text-apple_muted hover:text-apple_text font-bold text-sm mr-2 transition-colors">Discard</button>
            <button type="button" onclick="document.getElementById('addSoftwareForm').submit()" class="bg-primary hover:bg-blue-600 text-white px-8 py-3 rounded-full font-bold shadow-lg shadow-blue-500/30 transition-all transform hover:scale-105 flex items-center gap-2 text-sm">
                <i class="fas fa-cloud-upload-alt"></i> Publish Software
            </button>
        </div>

    </div>

    <!-- Scripts for Image Preview -->
    <script>
        function previewSingleImage(input, imgId, containerId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById(imgId);
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    document.getElementById(containerId).querySelector('div').style.opacity = '0';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewScreenshots(input) {
            const container = document.getElementById('screenshotsPreviewRow');
            const mainBox = document.getElementById('ssPreviewContainer');
            
            if (input.files && input.files.length > 0) {
                container.innerHTML = '';
                container.classList.remove('hidden');
                mainBox.style.display = 'none'; // hide the drag drop text to make room
                
                for(let i=0; i<input.files.length; i++) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'w-24 h-32 shrink-0 rounded-lg overflow-hidden border border-black/10 shadow-sm relative';
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        }
        
        // Mobile Sidebar Toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
