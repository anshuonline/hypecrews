<?php
require_once 'auth.php';
require_once '../config/db.php';
require_once 'components/logger.php';
$current_page = 'jobs';

$job = [
    'title' => '',
    'department' => '',
    'location' => '',
    'employment_type' => 'Full-time',
    'description' => '',
    'requirements' => ''
];
$is_edit = false;

// If editing
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $is_edit = true;
    try {
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $fetched_job = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($fetched_job) {
            $job = $fetched_job;
        } else {
            $error = "Job not found.";
            $is_edit = false;
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $department = trim($_POST['department']);
    $location = trim($_POST['location']);
    $employment_type = trim($_POST['employment_type']);
    $description = trim($_POST['description']);
    $requirements = trim($_POST['requirements']);
    
    // basic validation
    if (empty($title) || empty($description)) {
        $error = "Title and Description are required.";
        $job = $_POST; // preserve filled fields
    } else {
        try {
            if ($is_edit) {
                $stmt = $pdo->prepare("UPDATE jobs SET title=?, department=?, location=?, employment_type=?, description=?, requirements=? WHERE id=?");
                $stmt->execute([$title, $department, $location, $employment_type, $description, $requirements, $_GET['id']]);
                
                logAdminActivity($pdo, 'UPDATE_JOB', "Updated job: $title (ID: " . $_GET['id'] . ")");
                
                $success = "Job updated successfully.";
                $job = $_POST; // preserve filled fields
            } else {
                $stmt = $pdo->prepare("INSERT INTO jobs (title, department, location, employment_type, description, requirements) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $department, $location, $employment_type, $description, $requirements]);
                
                $job_id = $pdo->lastInsertId();
                logAdminActivity($pdo, 'ADD_JOB', "Added new job: $title (ID: $job_id)");
                
                $success = "Job created successfully.";
                // Clear form if added
                $job = ['title'=>'', 'department'=>'', 'location'=>'', 'employment_type'=>'Full-time', 'description'=>'', 'requirements'=>''];
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
            $job = $_POST;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Job' : 'Add Job'; ?> - Hypecrews Admin</title>
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

        /* Apple-style thin, invisible scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.15); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.25); }
        
        /* Glass panel utility */
        .glass-panel {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.04);
        }

        /* Form Inputs Apple Style */
        input[type="text"], textarea, select { 
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
    </style>
    <link rel="icon" type="image/png" href="/graphics/logos/hypecrews%20logo%20white.png">
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
        <!-- Sidebar -->
        <div class="bg-[#0f172a] h-full flex-shrink-0 z-20 shadow-xl text-white">
            <?php include 'components/sidebar.php'; ?>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative">
            
            <!-- Apple-style Glass Header -->
            <header class="glass-panel border-b border-white/60 px-10 py-6 flex justify-between items-center z-10 sticky top-0">
                <div>
                    <h1 class="text-3xl font-bold text-apple_text tracking-tight"><?php echo $is_edit ? 'Edit Job' : 'Add New Job'; ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="jobs.php" class="bg-black/5 hover:bg-black/10 text-apple_text font-semibold px-5 py-2.5 rounded-full flex items-center transition-colors shadow-sm border border-black/5">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Jobs
                    </a>
                </div>
            </header>
            
            <!-- Content -->
            <div class="flex-1 overflow-y-auto p-10 relative z-0">
                <?php if (isset($error)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-red-50/50 border-red-200 shadow-sm text-red-700 font-medium flex items-center max-w-4xl mx-auto">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="mb-8 p-4 rounded-3xl glass-panel bg-green-50/50 border-green-200 shadow-sm text-green-700 font-medium flex items-center max-w-4xl mx-auto">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <p><?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="glass-panel rounded-[2rem] p-8 md:p-12 shadow-sm flex flex-col max-w-4xl mx-auto">
                    <form method="POST" action="">
                        
                        <div class="bg-white/40 p-8 rounded-2xl border border-white/60 shadow-inner mb-8">
                            <h3 class="text-lg font-bold text-apple_text mb-6 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-briefcase text-primary mr-3"></i> Job Details
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Job Title *</label>
                                    <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required class="w-full px-4 py-3 rounded-xl border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Department</label>
                                    <input type="text" name="department" value="<?php echo htmlspecialchars($job['department']); ?>" class="w-full px-4 py-3 rounded-xl border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Location</label>
                                    <input type="text" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" placeholder="e.g. Remote, Guwahati" class="w-full px-4 py-3 rounded-xl border">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Employment Type</label>
                                    <div class="relative">
                                        <select name="employment_type" class="w-full px-4 py-3 rounded-xl border appearance-none pr-10">
                                            <option value="Full-time" <?php echo ($job['employment_type']=='Full-time') ? 'selected' : ''; ?>>Full-time</option>
                                            <option value="Part-time" <?php echo ($job['employment_type']=='Part-time') ? 'selected' : ''; ?>>Part-time</option>
                                            <option value="Contract" <?php echo ($job['employment_type']=='Contract') ? 'selected' : ''; ?>>Contract</option>
                                            <option value="Internship" <?php echo ($job['employment_type']=='Internship') ? 'selected' : ''; ?>>Internship</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-apple_muted">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/40 p-8 rounded-2xl border border-white/60 shadow-inner mb-8">
                            <h3 class="text-lg font-bold text-apple_text mb-6 flex items-center border-b border-black/5 pb-3">
                                <i class="fas fa-align-left text-primary mr-3"></i> Job Description
                            </h3>
                            <div class="mb-6">
                                <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Description *</label>
                                <textarea name="description" rows="5" required class="w-full px-4 py-3 rounded-xl border resize-y min-h-[120px]"><?php echo htmlspecialchars($job['description']); ?></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-bold text-apple_muted uppercase tracking-wider mb-2">Requirements / Qualifications</label>
                                <textarea name="requirements" rows="5" class="w-full px-4 py-3 rounded-xl border resize-y min-h-[120px]"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-black/5 flex justify-end">
                            <button type="submit" class="bg-primary hover:bg-blue-600 text-white px-10 py-4 rounded-xl font-bold shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5 w-full md:w-auto text-lg flex items-center justify-center">
                                <i class="fas <?php echo $is_edit ? 'fa-save' : 'fa-paper-plane'; ?> mr-2"></i> <?php echo $is_edit ? 'Update Job' : 'Publish Job'; ?>
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

