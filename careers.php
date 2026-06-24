<?php
session_start();
$pageTitle = "Careers - Hypecrews";
require_once 'config/db.php';

$success = '';
$error = '';

// Handle job application submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_job'])) {
    $job_id = $_POST['job_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $cover_letter = trim($_POST['cover_letter']);
    
    // File upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $allowed = ['pdf', 'doc', 'docx'];
        $filename = $_FILES['resume']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            // Check file size (max 5MB)
            if ($_FILES['resume']['size'] <= 5242880) {
                $new_filename = uniqid('resume_') . '.' . $ext;
                $upload_dir = 'uploads/resumes/';
                
                // create dir if not exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $destination = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['resume']['tmp_name'], $destination)) {
                    $resume_path = $destination;
                } else {
                    $error = "Failed to upload resume.";
                }
            } else {
                $error = "Resume size must be less than 5MB.";
            }
        } else {
            $error = "Invalid file format. Only PDF, DOC, and DOCX are allowed.";
        }
    } else {
        $error = "Resume is required.";
    }
    
    // Validate mandatory fields
    if (empty($phone)) {
        $error = "Phone number is required.";
    }
    if (empty($cover_letter)) {
        $error = "Cover letter is required.";
    }
    
    if (empty($error)) {
        try {
            // Check if already applied with this email
            $stmt = $pdo->prepare("SELECT id FROM job_applications WHERE job_id = ? AND email = ?");
            $stmt->execute([$job_id, $email]);
            if ($stmt->fetch()) {
                $error = "You have already applied for this job with this email address.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO job_applications (job_id, applicant_name, email, phone, resume_path, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$job_id, $name, $email, $phone, $resume_path, $cover_letter]);
                $success = "Your application has been submitted successfully!";
                
                // Set cookie for 30 days to mark as applied
                setcookie("applied_job_" . $job_id, "1", time() + (86400 * 30), "/");
                // Manually populate it for the current request so the UI updates immediately
                $_COOKIE["applied_job_" . $job_id] = "1";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch active jobs
try {
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE status = 'active' ORDER BY created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $jobs = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Explore career opportunities at Hypecrews. Join our creative digital agency team in Golaghat, Assam and build your future with us.">
    <meta name="keywords" content="hypecrews careers, jobs digital agency, work at hypecrews, digital agency jobs, creative careers, hypecrews jobs">
    <link rel="canonical" href="https://hypecrews.com/careers.php">
    <!-- Open Graph Tags -->
    <meta property="og:title" content="Careers - Hypecrews | Join Our Creative Team">
    <meta property="og:description" content="Explore career opportunities at Hypecrews. Join our creative digital agency team and build your future with us.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://hypecrews.com/careers.php">
    <meta property="og:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <meta property="og:site_name" content="Hypecrews">
    <!-- Twitter Card Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Careers - Hypecrews | Join Our Creative Team">
    <meta name="twitter:description" content="Explore career opportunities at Hypecrews. Join our creative digital agency team and build your future with us.">
    <meta name="twitter:image" content="https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png">
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebPage",
        "name": "Careers - Hypecrews",
        "description": "Explore career opportunities at Hypecrews. Join our creative digital agency team in Golaghat, Assam and build your future with us.",
        "url": "https://hypecrews.com/careers.php",
        "publisher": {
            "@type": "Organization",
            "name": "Hypecrews",
            "logo": {
                "@type": "ImageObject",
                "url": "https://hypecrews.com/graphics/logos/hypecrews%20logo%20white.png"
            },
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Golaghat",
                "addressRegion": "Assam",
                "addressCountry": "IN"
            }
        }
    }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        dark: '#0B0F19',
                        light: '#1e293b'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        heading: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0B0F19;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.05) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.05) 0, transparent 50%);
            color: #f8fafc;
        }

        .glass-card {
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(99, 102, 241, 0.3);
            box-shadow: 0 10px 40px rgba(99, 102, 241, 0.15);
            transform: translateY(-4px);
        }

        .reveal-up {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s cubic-bezier(0.5, 0, 0, 1);
        }

        .reveal-up.active {
            opacity: 1;
            transform: translateY(0);
        }
        
        .modal {
            display: none;
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }
        .modal.active {
            display: flex;
        }
        
        /* Modal animation */
        .modal-content {
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .modal.active .modal-content {
            transform: scale(1);
            opacity: 1;
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
<body class="text-white antialiased selection:bg-primary selection:text-white">
    <?php include 'components/nav.php'; ?>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 relative overflow-hidden">
        <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[120px] animate-[pulse_8s_infinite_alternate]"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-900/20 rounded-full blur-[120px] animate-[pulse_10s_infinite_alternate-reverse]"></div>
        </div>
        
        <div class="container mx-auto px-6 lg:px-8 text-center relative z-10 reveal-up">
            <div class="inline-flex items-center justify-center mb-8 px-4 py-2 border border-white/10 rounded-full bg-white/5 backdrop-blur-sm">
                <span class="w-2 h-2 rounded-full bg-secondary mr-2 animate-pulse"></span>
                <span class="text-sm font-medium tracking-wider uppercase text-gray-300">Join the Crew</span>
            </div>
            
            <h1 class="font-heading text-5xl md:text-7xl font-bold mb-8 tracking-tight leading-tight">
                Build Your Future <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-primary">With Hypecrews</span>
            </h1>
            
            <p class="text-xl max-w-3xl mx-auto text-slate-400 mb-12 font-light leading-relaxed">
                We're always looking for talented individuals to join our growing team. Explore our open positions and elevate your career to the next level.
            </p>
        </div>
    </section>

    <!-- Jobs Section -->
    <section class="py-16 relative z-10 min-h-[50vh]">
        <div class="container mx-auto px-6 lg:px-8">
            
            <?php if (!empty($error)): ?>
            <div class="max-w-4xl mx-auto mb-10 bg-red-900/20 border border-red-500/50 backdrop-blur-md text-red-200 px-6 py-4 rounded-2xl flex items-start shadow-lg reveal-up">
                <i class="fas fa-exclamation-circle text-red-400 text-xl mt-0.5 mr-4 shrink-0"></i>
                <p class="font-medium"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
            <div class="max-w-4xl mx-auto mb-10 bg-emerald-900/20 border border-emerald-500/50 backdrop-blur-md text-emerald-200 px-6 py-4 rounded-2xl flex items-start shadow-lg reveal-up">
                <i class="fas fa-check-circle text-emerald-400 text-xl mt-0.5 mr-4 shrink-0"></i>
                <p class="font-medium"><?php echo htmlspecialchars($success); ?></p>
            </div>
            <?php endif; ?>

            <div class="max-w-4xl mx-auto space-y-8">
                <?php if (empty($jobs)): ?>
                    <div class="glass-card p-16 rounded-3xl text-center reveal-up border-dashed border-2 border-white/10">
                        <div class="w-20 h-20 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                            <i class="fas fa-briefcase text-3xl text-slate-500"></i>
                        </div>
                        <h3 class="font-heading text-2xl font-bold mb-3 text-white">No Open Positions</h3>
                        <p class="text-slate-400 font-light">We currently don't have any open positions. Please check back later!</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $delay = 0;
                    foreach ($jobs as $job): 
                    ?>
                    <div class="glass-card rounded-3xl p-8 lg:p-10 reveal-up" style="transition-delay: <?php echo $delay; ?>ms;">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 pb-8 border-b border-white/10">
                            <div>
                                <h2 class="font-heading text-3xl font-bold text-white mb-4"><?php echo htmlspecialchars($job['title']); ?></h2>
                                <div class="flex flex-wrap gap-4">
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-sm text-indigo-300 font-medium">
                                        <i class="fas fa-building mr-2"></i><?php echo htmlspecialchars($job['department']); ?>
                                    </span>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/20 text-sm text-purple-300 font-medium">
                                        <i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-sm text-emerald-300 font-medium">
                                        <i class="fas fa-clock mr-2"></i><?php echo htmlspecialchars($job['employment_type']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php $hasApplied = isset($_COOKIE['applied_job_' . $job['id']]); ?>
                            <?php if ($hasApplied): ?>
                                <button disabled class="mt-6 md:mt-0 bg-white/5 border border-white/10 text-slate-400 px-8 py-3 rounded-xl font-bold cursor-not-allowed flex items-center shrink-0">
                                    <i class="fas fa-check-circle mr-2"></i> Applied
                                </button>
                            <?php else: ?>
                                <button onclick="openApplyModal(<?php echo $job['id']; ?>, '<?php echo addslashes($job['title']); ?>')" class="mt-6 md:mt-0 bg-white text-dark hover:bg-gray-200 px-8 py-3 rounded-xl font-bold transition-all duration-300 shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:shadow-[0_0_30px_rgba(255,255,255,0.4)] flex items-center shrink-0">
                                    Apply Now <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-slate-300 font-light">
                            <div>
                                <h3 class="font-heading font-bold text-xl text-white mb-4 flex items-center">
                                    <i class="fas fa-align-left text-primary mr-3"></i> Description
                                </h3>
                                <p class="whitespace-pre-wrap leading-relaxed text-slate-400"><?php echo htmlspecialchars($job['description']); ?></p>
                            </div>
                            <?php if (!empty($job['requirements'])): ?>
                            <div>
                                <h3 class="font-heading font-bold text-xl text-white mb-4 flex items-center">
                                    <i class="fas fa-list-check text-secondary mr-3"></i> Requirements
                                </h3>
                                <p class="whitespace-pre-wrap leading-relaxed text-slate-400"><?php echo htmlspecialchars($job['requirements']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                    $delay += 100;
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Application Modal -->
    <div id="applyModal" class="modal fixed inset-0 z-[100] items-center justify-center p-4">
        <div class="modal-content glass-card border border-white/10 rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col relative overflow-hidden shadow-[0_0_50px_rgba(0,0,0,0.5)]">
            <!-- decorative background -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full blur-[60px] pointer-events-none"></div>
            
            <div class="p-6 md:p-8 border-b border-white/10 flex justify-between items-center relative z-10 bg-dark/40">
                <h3 class="font-heading text-2xl font-bold text-white" id="modalTitle">Apply for Job</h3>
                <button onclick="closeApplyModal()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-slate-400 hover:text-white hover:bg-white/10 transition-colors border border-white/5">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6 md:p-8 overflow-y-auto custom-scrollbar relative z-10">
                <form action="careers.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="apply_job" value="1">
                    <input type="hidden" name="job_id" id="modalJobId" value="">
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Full Name <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required class="w-full bg-[#0B0F19]/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors shadow-inner">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Email Address <span class="text-red-400">*</span></label>
                            <input type="email" name="email" required class="w-full bg-[#0B0F19]/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors shadow-inner">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-2">Phone Number <span class="text-red-400">*</span></label>
                            <input type="tel" name="phone" required class="w-full bg-[#0B0F19]/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors shadow-inner placeholder:text-slate-600" placeholder="+91 9876543210">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Resume / CV <span class="text-red-400">*</span></label>
                        <div class="relative">
                            <input type="file" name="resume" accept=".pdf,.doc,.docx" required class="w-full bg-[#0B0F19]/80 border border-white/10 rounded-xl px-4 py-3 text-slate-300 focus:border-primary focus:outline-none transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/20 file:text-primary hover:file:bg-primary/30 cursor-pointer">
                        </div>
                        <p class="text-xs text-slate-500 mt-2 font-medium"><i class="fas fa-info-circle mr-1"></i> Accepted formats: PDF, DOC, DOCX. Max size: 5MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Cover Letter <span class="text-red-400">*</span></label>
                        <textarea name="cover_letter" rows="5" required class="w-full bg-[#0B0F19]/80 border border-white/10 rounded-xl px-4 py-3 text-white focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none transition-colors shadow-inner resize-y placeholder:text-slate-600" placeholder="Tell us why you're a great fit for this role..."></textarea>
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-600 text-white font-bold py-4 rounded-xl transition-all duration-300 shadow-[0_0_20px_rgba(99,102,241,0.3)] hover:shadow-[0_0_30px_rgba(99,102,241,0.5)] transform hover:-translate-y-1">
                            Submit Application <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script>
        function openApplyModal(jobId, jobTitle) {
            document.getElementById('modalJobId').value = jobId;
            document.getElementById('modalTitle').innerText = 'Apply: ' + jobTitle;
            const modal = document.getElementById('applyModal');
            modal.classList.add('active');
            // Small delay to allow display block to apply before animating opacity/transform
            setTimeout(() => {
                modal.querySelector('.modal-content').style.opacity = '1';
                modal.querySelector('.modal-content').style.transform = 'scale(1)';
            }, 10);
            document.body.style.overflow = 'hidden';
        }
        
        function closeApplyModal() {
            const modal = document.getElementById('applyModal');
            modal.querySelector('.modal-content').style.opacity = '0';
            modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }, 300);
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('applyModal');
            if (event.target == modal) {
                closeApplyModal();
            }
        }
        
        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', () => {
            const observerOptions = {
                root: null,
                rootMargin: '0px',
                threshold: 0.15
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    }
                });
            }, observerOptions);

            const revealElements = document.querySelectorAll('.reveal-up');
            revealElements.forEach(el => observer.observe(el));
        });
    </script>
    <script src="js/main.js"></script>
</body>
</html>

