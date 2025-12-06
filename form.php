<?php
$pageTitle = "Music Audition Form - Hypecrews";

// Initialize variables
$duplicateError = "";
$submissionSuccess = false;
$submittedName = "";
$submissionError = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    // Include database connection
    require_once 'config/db.php';
    
    // Get form data
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $age = $_POST['age'];
    $address = $_POST['address'];
    $musicType = $_POST['musicType'];
    $experience = $_POST['experience'];
    $instruments = $_POST['instruments'];
    $youtubeLink = $_POST['videoLink'];
    
    // Check for duplicate email, phone, or YouTube link
    try {
        $checkStmt = $pdo->prepare("SELECT id FROM audition_submissions WHERE email = ? OR phone = ? OR youtube_link = ?");
        $checkStmt->execute([$email, $phone, $youtubeLink]);
        $duplicate = $checkStmt->fetch();
        
        if ($duplicate) {
            $duplicateError = "An audition has already been submitted with this email, phone number, or YouTube link.";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'duplicate', 'message' => $duplicateError]);
                exit;
            }
        } else {
            // Handle photo upload
            $photoPath = "";
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                $uploadDir = 'uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileType = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileType;
                $targetPath = $uploadDir . $fileName;
                
                // Check file size (4MB limit)
                if ($_FILES['photo']['size'] <= 4 * 1024 * 1024) {
                    // Check file type
                    $allowedTypes = ['jpg', 'jpeg', 'png'];
                    if (in_array(strtolower($fileType), $allowedTypes)) {
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                            $photoPath = $targetPath;
                        }
                    }
                }
            }
            
            // Insert data into database
            $stmt = $pdo->prepare("INSERT INTO audition_submissions (full_name, email, phone, age, address, music_type, experience, instruments, photo_path, youtube_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullName, $email, $phone, $age, $address, $musicType, $experience, $instruments, $photoPath, $youtubeLink]);
            $submissionSuccess = true;
            $submittedName = $fullName;
            
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'name' => $fullName]);
                exit;
            }
        }
    } catch (PDOException $e) {
        $submissionError = "Database error: " . $e->getMessage();
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $submissionError]);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
        .file-upload-label {
            transition: all 0.3s ease;
        }
        
        .file-upload-label:hover {
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
        }
        
        .youtube-link-input:focus {
            border-color: #ff0000;
            box-shadow: 0 0 0 3px rgba(255, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(30, 41, 59, 0.9)), url('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .form-container {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            background: rgba(30, 41, 59, 0.85);
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            padding-bottom: 1rem;
        }
        
        .step:not(:last-child):after {
            content: '';
            position: absolute;
            top: 14px;
            left: 50%;
            right: -50%;
            height: 2px;
            background: #4b5563;
            z-index: 1;
        }
        
        .step.active:after {
            background: #6366f1;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #4b5563;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .step.active .step-circle {
            background: #6366f1;
        }
        
        .step.completed .step-circle {
            background: #10b981;
        }
        
        .thank-you-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .thank-you-popup.active {
            opacity: 1;
            visibility: visible;
        }
        
        .photo-preview {
            display: none;
            max-width: 100%;
            max-height: 300px;
            margin-top: 1rem;
            border-radius: 0.5rem;
        }
        
        @media (max-width: 768px) {
            body {
                background: linear-gradient(rgba(15, 23, 42, 0.9), rgba(30, 41, 59, 0.95)), url('https://images.unsplash.com/photo-1511671782779-c97d3d27a1d4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
                background-size: cover;
                background-position: center;
            }
            
            .step:not(:last-child):after {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-dark text-white">
    <!-- Audition Form Section -->
    <section class="py-8">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <div class="text-center mb-8">
                    <h1 class="text-3xl md:text-4xl font-bold mb-4">Music <span class="text-primary">Audition</span> Form</h1>
                    <p class="text-gray-300 max-w-2xl mx-auto">Fill out this form to submit your audition for our music program. Please ensure all details are accurate before submitting.</p>
                </div>
                
                <div class="bg-light rounded-2xl shadow-xl p-6 md:p-8 form-container">
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step active" data-step="1">
                            <div class="step-circle">1</div>
                            <div class="text-sm">Personal Info</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-circle">2</div>
                            <div class="text-sm">Music Info</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-circle">3</div>
                            <div class="text-sm">Photo</div>
                        </div>
                        <div class="step" data-step="4">
                            <div class="step-circle">4</div>
                            <div class="text-sm">YouTube</div>
                        </div>
                    </div>
                    
                    <form id="auditionForm" class="space-y-6" enctype="multipart/form-data" method="POST">
                        <!-- Personal Information Section -->
                        <div class="form-section active" id="section-1">
                            <div class="border-b border-gray-700 pb-6">
                                <h2 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fas fa-user mr-3 text-primary"></i> Personal Information
                                </h2>
                                
                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="fullName" class="block text-gray-300 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                                            <input type="text" id="fullName" name="fullName" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="John Doe" required>
                                        </div>
                                        
                                        <div>
                                            <label for="email" class="block text-gray-300 font-medium mb-2">Email Address <span class="text-red-500">*</span></label>
                                            <input type="email" id="email" name="email" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="john@example.com" required>
                                        </div>
                                        
                                        <div>
                                            <label for="phone" class="block text-gray-300 font-medium mb-2">Phone Number <span class="text-red-500">*</span></label>
                                            <input type="text" id="phone" name="phone" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="9876543210" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15">
                                        </div>
                                        
                                        <div>
                                            <label for="age" class="block text-gray-300 font-medium mb-2">Age <span class="text-red-500">*</span></label>
                                            <input type="number" id="age" name="age" min="13" max="100" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="25" required>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="address" class="block text-gray-300 font-medium mb-2">Full Address (Indian Address) <span class="text-red-500">*</span></label>
                                        <textarea id="address" name="address" rows="3" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="Enter your full address including city, state, and pincode" required></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-end pt-4">
                                <button type="button" class="next-step bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-next="2">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Music Information Section -->
                        <div class="form-section" id="section-2">
                            <div class="border-b border-gray-700 pb-6">
                                <h2 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fas fa-music mr-3 text-primary"></i> Music Information
                                </h2>
                                
                                <div class="space-y-6">
                                    <div>
                                        <label for="musicType" class="block text-gray-300 font-medium mb-2">Music Type <span class="text-red-500">*</span></label>
                                        <select id="musicType" name="musicType" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" required>
                                            <option value="" class="bg-dark">Select a music type</option>
                                            <option value="pop" class="bg-dark">Pop</option>
                                            <option value="rock" class="bg-dark">Rock</option>
                                            <option value="hiphop" class="bg-dark">Hip Hop/Rap</option>
                                            <option value="jazz" class="bg-dark">Jazz</option>
                                            <option value="classical" class="bg-dark">Classical</option>
                                            <option value="electronic" class="bg-dark">Electronic/Dance</option>
                                            <option value="country" class="bg-dark">Country</option>
                                            <option value="rnb" class="bg-dark">R&B/Soul</option>
                                            <option value="reggae" class="bg-dark">Reggae</option>
                                            <option value="folk" class="bg-dark">Folk</option>
                                            <option value="blues" class="bg-dark">Blues</option>
                                            <option value="other" class="bg-dark">Other</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="experience" class="block text-gray-300 font-medium mb-2">Musical Experience (Years) <span class="text-red-500">*</span></label>
                                        <input type="number" id="experience" name="experience" min="0" max="100" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="5" required>
                                    </div>
                                    
                                    <div>
                                        <label for="instruments" class="block text-gray-300 font-medium mb-2">Instruments Played</label>
                                        <input type="text" id="instruments" name="instruments" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary text-white backdrop-blur-sm" placeholder="Guitar, Piano, Drums">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex justify-between pt-4">
                                <button type="button" class="prev-step bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-prev="1">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="next-step bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-next="3">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Photo Upload Section -->
                        <div class="form-section" id="section-3">
                            <div class="border-b border-gray-700 pb-6">
                                <h2 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fas fa-camera mr-3 text-primary"></i> Profile Photo
                                </h2>
                                
                                <div>
                                    <label class="block text-gray-300 font-medium mb-2">Upload Photo <span class="text-red-500">*</span></label>
                                    <div class="flex items-center justify-center w-full">
                                        <label for="photo" class="flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-600 rounded-lg cursor-pointer bg-dark/50 hover:bg-light/50 transition-all duration-300 file-upload-label backdrop-blur-sm">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                                <p class="mb-2 text-sm text-gray-300"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-gray-400">PNG, JPG, JPEG (MAX. 4MB)</p>
                                            </div>
                                            <input id="photo" name="photo" type="file" class="hidden" accept="image/*" required />
                                        </label>
                                    </div>
                                    <img id="photoPreview" class="photo-preview" src="" alt="Photo Preview">
                                    <p class="mt-2 text-sm text-gray-400">Maximum file size: 4MB</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between pt-4">
                                <button type="button" class="prev-step bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-prev="2">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="button" class="next-step bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-next="4">
                                    Next <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- YouTube Video Section -->
                        <div class="form-section" id="section-4">
                            <div class="border-b border-gray-700 pb-6">
                                <h2 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fab fa-youtube mr-3 text-red-500"></i> Audition Video
                                </h2>
                                
                                <div>
                                    <label for="videoLink" class="block text-gray-300 font-medium mb-2">YouTube Video Link <span class="text-red-500">*</span></label>
                                    <input type="url" id="videoLink" name="videoLink" class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 youtube-link-input text-white backdrop-blur-sm" placeholder="https://www.youtube.com/watch?v=..." required>
                                    <p class="mt-2 text-sm text-gray-400">Please upload your audition video to YouTube and paste the link here. Only YouTube links are accepted.</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between pt-4">
                                <button type="button" class="prev-step bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300" data-prev="3">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous
                                </button>
                                <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300">
                                    Submit Audition
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Success Message -->
                    <div id="successMessage" class="hidden mt-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-500 text-2xl mr-3"></i>
                            <div>
                                <h3 class="font-bold text-lg">Audition Submitted Successfully!</h3>
                                <p class="text-green-300">Thank you for your submission. We'll review your audition and contact you soon.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Error Message -->
                    <div id="errorMessage" class="hidden mt-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                            <div>
                                <h3 class="font-bold text-lg">Submission Error</h3>
                                <p id="errorMessageText" class="text-red-300">Please check your form and try again.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Duplicate Error Message -->
                    <?php if (!empty($duplicateError)): ?>
                    <div class="mt-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 text-2xl mr-3"></i>
                            <div>
                                <h3 class="font-bold text-lg">Registration Already Exists</h3>
                                <p class="text-red-300"><?php echo htmlspecialchars($duplicateError); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="text-center mt-8 text-gray-400 text-sm">
                    <p>© 2023 Hypecrews. All rights reserved.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Thank You Popup -->
    <div class="thank-you-popup" id="thankYouPopup">
        <div class="bg-dark rounded-2xl shadow-xl p-8 max-w-md w-full mx-4 text-center">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
            <h2 class="text-2xl font-bold mb-2">Thank You!</h2>
            <p class="text-gray-300 mb-6">Thank you for submitting your audition, <span id="userName" class="text-primary font-bold"></span>!</p>
            <p class="text-gray-400 mb-6">We have received your submission and will review your audition shortly.</p>
            <button id="closePopup" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg transition-all duration-300">
                Close
            </button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Form navigation
            const nextButtons = document.querySelectorAll('.next-step');
            const prevButtons = document.querySelectorAll('.prev-step');
            const formSections = document.querySelectorAll('.form-section');
            const steps = document.querySelectorAll('.step');
            const thankYouPopup = document.getElementById('thankYouPopup');
            const closePopup = document.getElementById('closePopup');
            const userName = document.getElementById('userName');
            const photoInput = document.getElementById('photo');
            const photoPreview = document.getElementById('photoPreview');
            
            // Photo preview functionality
            photoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        photoPreview.src = e.target.result;
                        photoPreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    photoPreview.style.display = 'none';
                }
            });
            
            // Phone number validation - Allow only digits
            document.getElementById('phone').addEventListener('keypress', function(e) {
                // Allow only digits (0-9)
                if (e.which < 48 || e.which > 57) {
                    e.preventDefault();
                }
            });
            
            // Also prevent pasting non-numeric content
            document.getElementById('phone').addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = paste.replace(/[^0-9]/g, '');
                this.value = numericValue;
            });
            
            // Next button click
            nextButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentStep = parseInt(this.closest('.form-section').id.split('-')[1]);
                    const nextStep = parseInt(this.getAttribute('data-next'));
                    
                    // Validate current section before moving to next
                    if (validateSection(currentStep)) {
                        showSection(nextStep);
                        updateStepIndicator(nextStep);
                    }
                });
            });
            
            // Previous button click
            prevButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const currentStep = parseInt(this.closest('.form-section').id.split('-')[1]);
                    const prevStep = parseInt(this.getAttribute('data-prev'));
                    
                    showSection(prevStep);
                    updateStepIndicator(prevStep);
                });
            });
            
            // Close popup
            closePopup.addEventListener('click', function() {
                thankYouPopup.classList.remove('active');
            });
            
            // Validate form section
            function validateSection(step) {
                const section = document.getElementById(`section-${step}`);
                const inputs = section.querySelectorAll('input[required], select[required], textarea[required]');
                
                for (let input of inputs) {
                    if (!input.value.trim()) {
                        showError(`Please fill in all required fields in this section.`);
                        input.focus();
                        return false;
                    }
                    
                    // Special validation for email
                    if (input.type === 'email' && !isValidEmail(input.value)) {
                        showError('Please enter a valid email address.');
                        input.focus();
                        return false;
                    }
                    
                    // Special validation for phone number
                    if (input.name === 'phone' && !/^[0-9]+$/.test(input.value)) {
                        showError('Phone number should contain only digits.');
                        input.focus();
                        return false;
                    }
                    
                    // Special validation for YouTube link
                    if (input.name === 'videoLink' && !isValidYouTubeUrl(input.value)) {
                        showError('Please enter a valid YouTube video link.');
                        input.focus();
                        return false;
                    }
                }
                
                // Special validation for photo
                if (step === 3) {
                    const photoInput = document.getElementById('photo');
                    if (photoInput.files.length === 0) {
                        showError('Please upload a photo.');
                        return false;
                    }
                    
                    const file = photoInput.files[0];
                    const fileSize = file.size / 1024 / 1024; // in MB
                    if (fileSize > 4) {
                        showError('Photo size must be less than 4MB.');
                        return false;
                    }
                    
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type.toLowerCase())) {
                        showError('Please upload a valid image file (JPEG, JPG, or PNG).');
                        return false;
                    }
                }
                
                return true;
            }
            
            // Show specific section
            function showSection(step) {
                formSections.forEach(section => {
                    section.classList.remove('active');
                });
                document.getElementById(`section-${step}`).classList.add('active');
            }
            
            // Update step indicator
            function updateStepIndicator(step) {
                steps.forEach((stepEl, index) => {
                    const stepNum = index + 1;
                    stepEl.classList.remove('active', 'completed');
                    
                    if (stepNum < step) {
                        stepEl.classList.add('completed');
                    } else if (stepNum === step) {
                        stepEl.classList.add('active');
                    }
                });
            }
            
            // Form submission
            const form = document.getElementById('auditionForm');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validate last section
                if (!validateSection(4)) {
                    return;
                }
                
                // Show loading state
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
                submitButton.disabled = true;
                
                // Submit form via AJAX to avoid page refresh
                const formData = new FormData(this);
                
                fetch('', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'duplicate') {
                        // Show duplicate error message
                        showError(data.message);
                    } else if (data.status === 'success') {
                        // Show thank you popup with user's name
                        userName.textContent = data.name;
                        thankYouPopup.classList.add('active');
                        
                        // Reset form
                        form.reset();
                        photoPreview.style.display = 'none';
                        
                        // Reset to first section
                        showSection(1);
                        updateStepIndicator(1);
                    } else if (data.status === 'error') {
                        // Show general error message
                        showError(data.message || 'An error occurred while submitting the form. Please try again.');
                    }
                    
                    // Restore submit button
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('An error occurred while submitting the form. Please try again.');
                    // Restore submit button
                    submitButton.innerHTML = originalText;
                    submitButton.disabled = false;
                });
            });
            
            // Helper functions
            function isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }
            
            function isValidYouTubeUrl(url) {
                const regExp = /^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/;
                return regExp.test(url);
            }
            
            function showError(message) {
                const errorMessage = document.getElementById('errorMessage');
                const errorMessageText = document.getElementById('errorMessageText');
                errorMessageText.textContent = message;
                errorMessage.classList.remove('hidden');
                
                // Scroll to error message
                errorMessage.scrollIntoView({ behavior: 'smooth' });
                
                // Hide error after 5 seconds
                setTimeout(() => {
                    errorMessage.classList.add('hidden');
                }, 5000);
            }
        });
    </script>
</body>
</html>