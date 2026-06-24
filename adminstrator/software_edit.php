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
    
    $upload_dir = '../uploads/softwares/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    if ($file_type == 'upload') {
        if (isset($_FILES['software_file']) && $_FILES['software_file']['error'] == 0) {
            $ext = strtolower(pathinfo($_FILES['software_file']['name'], PATHINFO_EXTENSION));
            $new_file_name = uniqid('app_') . '.' . $ext;
            if (move_uploaded_file($_FILES['software_file']['tmp_name'], $upload_dir . $new_file_name)) {
                // Delete old file if it was an uploaded file
                if (!empty($software['file_path']) && $software['file_type'] == 'upload' && file_exists('../' . $software['file_path'])) {
                    unlink('../' . $software['file_path']);
                }
                $file_path = 'uploads/softwares/' . $new_file_name;
            } else {
                $error = "Failed to upload new software file.";
                $file_path = $software['file_path'];
            }
        } else {
            // Keep the old file
            $file_path = $software['file_path'];
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0066cc', // Apple Blue
                        apple_text: '#1d1d1f', // Apple Dark text
                        apple_muted: '#86868b', // Apple Muted text
                    },
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'Inter', 'Segoe UI', 'Roboto', 'sans-serif']
                    },
                    boxShadow: {
                        'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f5f5f7; 
            font-family: -apple-system, BlinkMacSystemFont, 'Inter', 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            position: relative;
        }
        
        /* The colorful blurred background that makes the glassmorphism visible */
        .glass-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 0;
            background: #f5f5f9;
            overflow: hidden;
            pointer-events: none;
        }
        
        .glass-bg::before, .glass-bg::after, .glass-blob {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
        }
        
        .glass-bg::before {
            background: #dbeafe; /* Light blue */
            width: 600px;
            height: 600px;
            top: -100px;
            right: -100px;
        }
        
        .glass-bg::after {
            background: #f3e8ff; /* Light purple */
            width: 500px;
            height: 500px;
            bottom: -100px;
            left: 10%;
        }
        
        .glass-blob {
            background: #e0f2fe; /* Sky blue */
            width: 400px;
            height: 400px;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Glass panel utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }

        /* Form Inputs Apple Style */
        input[type="text"], input[type="number"], textarea, select { 
            background-color: rgba(255, 255, 255, 0.8) !important; 
            border-color: rgba(0, 0, 0, 0.1) !important; 
            color: #1d1d1f !important; 
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02) inset;
        }
        input:focus, textarea:focus, select:focus { 
            border-color: #0066cc !important; 
            outline: none; 
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.15) !important; 
            background-color: #ffffff !important;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
    </style>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-YCMZ1CPN6G"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-YCMZ1CPN6G');
</script>
</head>
<body class="text-apple_text">

    <!-- Abstract blurred colorful background -->
    <div class="glass-bg">
        <div class="glass-blob"></div>
    </div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <?php include 'components/sidebar.php'; ?>
        
        <div class="flex-1 flex flex-col overflow-hidden relative">
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <h2 class="text-3xl font-bold text-apple_text tracking-tight">Edit Software</h2>
                <a href="softwares.php" class="bg-black/5 hover:bg-black/10 text-apple_text font-semibold px-5 py-2.5 rounded-full flex items-center transition-colors shadow-sm border border-black/5">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </header>
            
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (!empty($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center max-w-5xl mx-auto">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="glass-panel rounded-[2rem] p-8 md:p-12 shadow-sm max-w-5xl mx-auto">
                    <form method="POST" action="" enctype="multipart/form-data">
                        
                        <!-- Basic Information -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center">
                                <i class="fas fa-info-circle text-primary mr-3"></i>Basic Information
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white/40 rounded-2xl border border-white/60 shadow-inner">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Software Name *</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($software['name']); ?>" required class="w-full px-4 py-3 rounded-xl border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Version</label>
                                    <input type="text" name="version" value="<?php echo htmlspecialchars($software['version']); ?>" class="w-full px-4 py-3 rounded-xl border">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Description *</label>
                                    <textarea name="description" rows="5" required class="w-full px-4 py-3 rounded-xl border"><?php echo htmlspecialchars($software['description']); ?></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">SEO Keywords / Tags</label>
                                    <input type="text" name="keywords" value="<?php echo htmlspecialchars($software['keywords'] ?? ''); ?>" placeholder="e.g. video editor, ai tools, hypecrews software" class="w-full px-4 py-3 rounded-xl border">
                                    <p class="text-[11px] font-semibold text-apple_muted mt-1.5 ml-1">Comma separated tags.</p>
                                </div>
                                <div class="md:col-span-2 pt-2">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-3">Supported Platforms</label>
                                    <?php $platforms = explode(', ', $software['platform']); ?>
                                    <div class="flex flex-wrap gap-4">
                                        <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-lg border border-black/5 shadow-sm hover:border-primary/50 transition-colors">
                                            <input type="checkbox" name="platforms[]" value="Windows" class="w-4 h-4 text-primary rounded border-black/10" <?php echo in_array('Windows', $platforms)?'checked':''; ?>> 
                                            <span class="font-medium">Windows</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-lg border border-black/5 shadow-sm hover:border-primary/50 transition-colors">
                                            <input type="checkbox" name="platforms[]" value="Mac/Apple" class="w-4 h-4 text-primary rounded border-black/10" <?php echo in_array('Mac/Apple', $platforms)?'checked':''; ?>> 
                                            <span class="font-medium">Mac/Apple</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-lg border border-black/5 shadow-sm hover:border-primary/50 transition-colors">
                                            <input type="checkbox" name="platforms[]" value="Android" class="w-4 h-4 text-primary rounded border-black/10" <?php echo in_array('Android', $platforms)?'checked':''; ?>> 
                                            <span class="font-medium">Android</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-lg border border-black/5 shadow-sm hover:border-primary/50 transition-colors">
                                            <input type="checkbox" name="platforms[]" value="Web" class="w-4 h-4 text-primary rounded border-black/10" <?php echo in_array('Web', $platforms)?'checked':''; ?>> 
                                            <span class="font-medium">Web</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Media & Graphics -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center">
                                <i class="fas fa-images text-primary mr-3"></i>Media & Graphics
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-white/40 p-6 rounded-2xl border border-white/60 shadow-inner">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-4">Update Logo (Square Image)</label>
                                    <?php if(!empty($software['logo_path'])): ?>
                                        <div class="mb-4">
                                            <img src="../<?php echo htmlspecialchars($software['logo_path']); ?>" class="w-20 h-20 object-cover rounded-[1rem] shadow-sm border border-black/5 bg-white">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-apple_text font-medium file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:bg-primary/10 file:text-primary file:font-bold hover:file:bg-primary/20 transition-colors cursor-pointer">
                                    <p class="text-[11px] font-semibold text-apple_muted mt-2 ml-1">Leave blank to keep existing logo.</p>
                                </div>
                                
                                <div class="bg-white/40 p-6 rounded-2xl border border-white/60 shadow-inner">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-4">Update Banner (Large Image)</label>
                                    <?php if(!empty($software['banner_path'])): ?>
                                        <div class="mb-4">
                                            <img src="../<?php echo htmlspecialchars($software['banner_path']); ?>" class="w-full h-20 object-cover rounded-[1rem] shadow-sm border border-black/5 bg-white">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="banner" accept="image/*" class="w-full text-sm text-apple_text font-medium file:mr-4 file:py-2.5 file:px-5 file:rounded-full file:border-0 file:bg-primary/10 file:text-primary file:font-bold hover:file:bg-primary/20 transition-colors cursor-pointer">
                                    <p class="text-[11px] font-semibold text-apple_muted mt-2 ml-1">Leave blank to keep existing banner.</p>
                                </div>
                                
                                <div class="md:col-span-2 bg-white/40 p-6 rounded-2xl border border-white/60 shadow-inner">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-4 pb-2 border-b border-black/5">Manage Screenshots</label>
                                    
                                    <?php if(!empty($existing_screenshots)): ?>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                                        <?php foreach($existing_screenshots as $ss): ?>
                                        <div class="relative group rounded-xl overflow-hidden border border-black/10 bg-white shadow-sm hover:shadow-md transition-shadow">
                                            <img src="../<?php echo htmlspecialchars($ss['image_path']); ?>" class="w-full h-28 object-cover">
                                            
                                            <!-- Reorder Input -->
                                            <div class="p-3 bg-white/90 backdrop-blur-sm border-t border-black/5 flex items-center justify-between">
                                                <label class="text-[10px] font-bold text-apple_muted uppercase tracking-wider flex items-center">
                                                    <i class="fas fa-sort mr-1"></i> Order:
                                                    <input type="number" name="screenshot_order[<?php echo $ss['id']; ?>]" value="<?php echo (int)$ss['display_order']; ?>" class="w-12 ml-2 px-1.5 py-1 text-sm rounded bg-white border border-black/10 focus:border-primary focus:ring-1 focus:ring-primary shadow-inner text-center">
                                                </label>
                                            </div>

                                            <label class="absolute top-2 right-2 bg-white/90 text-red-500 text-xs font-bold px-2 py-1.5 rounded-lg cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity shadow-md border border-red-100 flex items-center">
                                                <input type="checkbox" name="delete_screenshots[]" value="<?php echo $ss['id']; ?>" class="mr-1.5 w-3 h-3 text-red-500 rounded focus:ring-red-500"> Delete
                                            </label>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php else: ?>
                                        <div class="bg-white/50 rounded-xl p-6 text-center border border-black/5 mb-6">
                                            <i class="fas fa-images text-2xl text-apple_muted mb-2"></i>
                                            <p class="text-sm font-medium text-apple_muted">No screenshots uploaded yet.</p>
                                        </div>
                                    <?php endif; ?>

                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-3">Add New Screenshots (Select Multiple)</label>
                                    <div class="bg-white/60 border border-dashed border-primary/30 rounded-xl p-6 text-center">
                                        <input type="file" name="screenshots[]" multiple accept="image/*" class="w-full text-sm text-apple_text font-medium file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:bg-primary file:text-white file:font-bold hover:file:bg-blue-600 transition-colors cursor-pointer mx-auto block max-w-md">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- File Delivery -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center">
                                <i class="fas fa-cloud-download-alt text-primary mr-3"></i>File Delivery
                            </h3>
                            <div class="bg-white/40 p-6 rounded-2xl border border-white/60 shadow-inner">
                                <div class="flex flex-col sm:flex-row gap-4 mb-6 p-1 bg-black/5 rounded-xl inline-flex">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="file_type" value="google_drive" class="peer hidden" <?php echo $software['file_type']=='google_drive'?'checked':''; ?> onclick="document.getElementById('gd_input').style.display='block'; document.getElementById('upload_input').style.display='none';"> 
                                        <div class="px-5 py-2.5 rounded-lg text-sm font-bold text-apple_muted transition-all peer-checked:bg-white peer-checked:text-apple_text peer-checked:shadow-sm">
                                            <i class="fab fa-google-drive mr-1.5 text-blue-500"></i> External URL
                                        </div>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="file_type" value="upload" class="peer hidden" <?php echo $software['file_type']=='upload'?'checked':''; ?> onclick="document.getElementById('gd_input').style.display='none'; document.getElementById('upload_input').style.display='block';"> 
                                        <div class="px-5 py-2.5 rounded-lg text-sm font-bold text-apple_muted transition-all peer-checked:bg-white peer-checked:text-apple_text peer-checked:shadow-sm">
                                            <i class="fas fa-upload mr-1.5 text-indigo-500"></i> Direct Upload
                                        </div>
                                    </label>
                                </div>
                                
                                <div id="gd_input" class="bg-white/50 p-5 rounded-xl border border-black/5 shadow-sm" style="display:<?php echo $software['file_type']=='google_drive'?'block':'none'; ?>;">
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Google Drive File ID or Direct Link</label>
                                    <input type="text" name="external_link" value="<?php echo $software['file_type']=='google_drive' ? htmlspecialchars($software['file_path']) : ''; ?>" class="w-full px-4 py-3 rounded-xl border">
                                </div>

                                <div id="upload_input" class="bg-white/50 p-5 rounded-xl border border-black/5 shadow-sm" style="display:<?php echo $software['file_type']=='upload'?'block':'none'; ?>;">
                                    <?php if($software['file_type']=='upload' && !empty($software['file_path'])): ?>
                                        <div class="mb-5 p-4 bg-white rounded-xl border border-primary/20 shadow-sm flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary mr-4">
                                                    <i class="fas fa-file-archive"></i>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-bold text-apple_muted uppercase tracking-wider mb-0.5">Current File on Server:</p>
                                                    <p class="text-apple_text font-bold text-sm break-all"><?php echo basename($software['file_path']); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-3">Upload New File to Replace Current (.exe, .zip, .apk)</label>
                                    <div class="bg-white border border-dashed border-black/10 rounded-xl p-5 text-center">
                                        <input type="file" name="software_file" class="w-full text-sm text-apple_text font-medium file:mr-4 file:py-2.5 file:px-6 file:rounded-full file:border-0 file:bg-black/5 file:text-apple_text file:font-bold hover:file:bg-black/10 transition-colors cursor-pointer mx-auto block max-w-md">
                                    </div>
                                    <div class="mt-4 flex items-start text-xs font-semibold text-amber-600 bg-amber-50 p-3 rounded-lg border border-amber-100">
                                        <i class="fas fa-exclamation-triangle mt-0.5 mr-2"></i>
                                        <p>Leave blank to keep the existing file. Uploading a new file will permanently delete the old one.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- App Store Links -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-apple_text mb-6 flex items-center">
                                <i class="fas fa-link text-primary mr-3"></i>App Store Links <span class="text-xs font-bold text-apple_muted bg-white px-2 py-0.5 rounded-full ml-3 border border-black/5 shadow-sm">Optional</span>
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-white/40 rounded-2xl border border-white/60 shadow-inner">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-google-play mr-2 text-green-500 text-base"></i>Play Store Link</label>
                                    <input type="text" name="playstore_link" value="<?php echo htmlspecialchars($software['playstore_link']); ?>" class="w-full px-4 py-3 rounded-xl border" placeholder="https://play.google.com/...">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-apple mr-2 text-gray-800 text-base"></i>App Store Link</label>
                                    <input type="text" name="appstore_link" value="<?php echo htmlspecialchars($software['appstore_link']); ?>" class="w-full px-4 py-3 rounded-xl border" placeholder="https://apps.apple.com/...">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2 flex items-center"><i class="fab fa-windows mr-2 text-blue-500 text-base"></i>Windows Store Link</label>
                                    <input type="text" name="windows_store_link" value="<?php echo htmlspecialchars($software['windows_store_link']); ?>" class="w-full px-4 py-3 rounded-xl border" placeholder="https://apps.microsoft.com/...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-black/5 flex justify-end">
                            <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-10 py-4 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 w-full md:w-auto text-lg flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Update Details & Images
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="mt-12 text-center text-sm font-medium text-apple_muted mb-6">
                    &copy; <?php echo date('Y'); ?> Hypecrews. All rights reserved.
                </div>
            </div>
        </div>
    </div>
</body>
</html>

