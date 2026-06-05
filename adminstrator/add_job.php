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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="components/sidebar.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0f172a',
                        light: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
        input, textarea, select {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #fff !important;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #6366f1 !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }
    </style>
    <link rel="icon" type="image/png" href="/Hypecrews/graphics/logos/hypecrews%20logo%20white.png">
</head>
<body class="text-white">
    <div class="flex h-screen">
        <?php include 'components/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-dark border-b border-gray-800 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold"><?php echo $is_edit ? 'Edit Job' : 'Add New Job'; ?></h2>
                    <a href="jobs.php" class="bg-gray-700 hover:bg-gray-600 px-4 py-2 rounded-lg flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Jobs
                    </a>
                </div>
            </header>
            
            <div class="flex-1 overflow-y-auto p-6">
                <?php if (isset($error)): ?>
                <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                    <p class="text-red-300"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                    <p class="text-green-300"><i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($success); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="bg-light rounded-xl p-6 max-w-3xl">
                    <form method="POST" action="">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-400 mb-2">Job Title *</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($job['title']); ?>" required class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Department</label>
                                <input type="text" name="department" value="<?php echo htmlspecialchars($job['department']); ?>" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Location</label>
                                <input type="text" name="location" value="<?php echo htmlspecialchars($job['location']); ?>" placeholder="e.g. Remote, Guwahati" class="w-full px-4 py-2 rounded-lg border">
                            </div>
                            <div>
                                <label class="block text-gray-400 mb-2">Employment Type</label>
                                <select name="employment_type" class="w-full px-4 py-2 rounded-lg border">
                                    <option value="Full-time" <?php echo ($job['employment_type']=='Full-time') ? 'selected' : ''; ?>>Full-time</option>
                                    <option value="Part-time" <?php echo ($job['employment_type']=='Part-time') ? 'selected' : ''; ?>>Part-time</option>
                                    <option value="Contract" <?php echo ($job['employment_type']=='Contract') ? 'selected' : ''; ?>>Contract</option>
                                    <option value="Internship" <?php echo ($job['employment_type']=='Internship') ? 'selected' : ''; ?>>Internship</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-400 mb-2">Job Description *</label>
                            <textarea name="description" rows="5" required class="w-full px-4 py-2 rounded-lg border"><?php echo htmlspecialchars($job['description']); ?></textarea>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-400 mb-2">Requirements / Qualifications</label>
                            <textarea name="requirements" rows="5" class="w-full px-4 py-2 rounded-lg border"><?php echo htmlspecialchars($job['requirements']); ?></textarea>
                        </div>
                        
                        <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-bold">
                            <?php echo $is_edit ? 'Update Job' : 'Publish Job'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
