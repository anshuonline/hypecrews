<?php
session_start();
require_once 'config/db.php';

// Get service name from URL parameter
$service_name = isset($_GET['service']) ? htmlspecialchars($_GET['service']) : 'Service Inquiry';

$error = '';
$success = '';

// Check if user is logged in
$user_logged_in = isset($_SESSION['user_id']);
$name = '';
$email = '';
$phone = '';
$company = '';

// If user is logged in, pre-fill their information
if ($user_logged_in) {
    try {
        $stmt = $pdo->prepare("SELECT first_name, last_name, email, mobile_number, company_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $name = $user['first_name'] . ' ' . $user['last_name'];
            $email = $user['email'];
            $phone = $user['mobile_number'];
            $company = $user['company_name'];
        }
    } catch (PDOException $e) {
        $error = "Error fetching user information: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_name = isset($_POST['service_name']) ? htmlspecialchars($_POST['service_name']) : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $company = isset($_POST['company']) ? trim($_POST['company']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validation
    if (empty($name)) {
        $error = 'Name is required';
    } elseif (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (empty($message)) {
        $error = 'Message is required';
    } else {
        try {
            // Insert the query into database
            $stmt = $pdo->prepare("INSERT INTO service_queries (service_name, name, email, phone, company, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$service_name, $name, $email, $phone, $company, $message]);
            
            $success = 'Your query has been submitted successfully! We will get back to you soon.';
            
            // Clear form fields after successful submission
            $name = '';
            $email = '';
            $phone = '';
            $company = '';
            $message = '';
        } catch (PDOException $e) {
            $error = "Error submitting query: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Query - Hypecrews</title>
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
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <a href="services.php" class="inline-flex items-center text-primary hover:text-indigo-300 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Services
                </a>
                <h1 class="text-3xl font-bold">Service Query</h1>
                <p class="text-gray-400">Interested in "<?php echo $service_name; ?>"? Please fill out the form below and we'll get back to you soon.</p>
            </div>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-red-300"><?php echo $error; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-green-300"><?php echo $success; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="bg-light rounded-xl shadow-lg p-6">
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="service_name" value="<?php echo $service_name; ?>">
                    
                    <div>
                        <label for="service" class="block text-sm font-medium text-gray-300 mb-2">Service</label>
                        <input 
                            type="text" 
                            id="service" 
                            class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                            value="<?php echo $service_name; ?>" 
                            readonly>
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Enter your full name"
                            value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="Enter your email address"
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                            <input 
                                type="text" 
                                id="phone" 
                                name="phone" 
                                class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                                placeholder="9876543210"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                maxlength="15"
                                value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                    </div>
                    
                    <div>
                        <label for="company" class="block text-sm font-medium text-gray-300 mb-2">Company (Optional)</label>
                        <input 
                            type="text" 
                            id="company" 
                            name="company" 
                            class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Enter your company name"
                            value="<?php echo htmlspecialchars($company); ?>">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-300 mb-2">Message *</label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="5"
                            required
                            class="w-full px-4 py-3 bg-dark/50 border border-gray-700 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-primary"
                            placeholder="Please provide details about your requirements..."><?php echo htmlspecialchars(isset($_POST['message']) ? $_POST['message'] : ''); ?></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="window.location.href='services.php'" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg">
                            Cancel
                        </button>
                        <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg">
                            Submit Query
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        // Phone number validation - Allow only digits
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('phone');
            
            phoneInput.addEventListener('keypress', function(e) {
                // Allow only digits (0-9)
                if (e.which < 48 || e.which > 57) {
                    e.preventDefault();
                }
            });
            
            // Also prevent pasting non-numeric content
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = paste.replace(/[^0-9]/g, '');
                this.value = numericValue;
            });
        });
    </script>
</body>
</html>