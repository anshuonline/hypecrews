<?php
session_start();
require_once 'config/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if (!$order_id) {
    header("Location: track_orders.php");
    exit();
}

// Get order details (ensure it belongs to the logged-in user and review is requested)
try {
    $stmt = $pdo->prepare("SELECT o.*, u.username, u.first_name, u.last_name FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ? AND o.user_id = ? AND o.review_requested = 1 AND o.status = 'completed'");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header("Location: track_orders.php");
        exit();
    }
} catch (PDOException $e) {
    $error = "Error fetching order: " . $e->getMessage();
}

// Check if user has already submitted a review for this order
try {
    $stmt = $pdo->prepare("SELECT * FROM order_reviews WHERE order_id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $existing_review = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing_review) {
        $review_submitted = true;
        $rating = $existing_review['rating'];
        $review_text = $existing_review['review'];
    } else {
        $review_submitted = false;
        $rating = 5;
        $review_text = '';
    }
} catch (PDOException $e) {
    $error = "Error checking existing review: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$review_submitted) {
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $review_text = isset($_POST['review']) ? trim($_POST['review']) : '';
    
    // Validation
    if ($rating < 1 || $rating > 5) {
        $error = 'Rating must be between 1 and 5';
    } else {
        try {
            // Insert the review
            $stmt = $pdo->prepare("INSERT INTO order_reviews (order_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $user_id, $rating, $review_text]);
            
            $success = "Review submitted successfully!";
            $review_submitted = true;
        } catch (PDOException $e) {
            $error = "Error submitting review: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review - Hypecrews</title>
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
        
        .rating-stars {
            color: #cbd5e1;
        }
        
        .rating-stars .star-active {
            color: #fbbf24;
        }
    </style>
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="mb-6">
                <a href="track_orders.php" class="inline-flex items-center text-primary hover:text-indigo-300 mb-4">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Orders
                </a>
                <h1 class="text-3xl font-bold">Submit Review</h1>
                <p class="text-gray-400">Order #<?php echo $order['id']; ?> • <?php echo htmlspecialchars($order['order_title']); ?></p>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="mb-6 p-4 rounded-lg bg-red-900/50 border border-red-700 backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-red-300"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (isset($success)): ?>
            <div class="mb-6 p-4 rounded-lg bg-green-900/50 border border-green-700 backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                    <div>
                        <p class="text-green-300"><?php echo htmlspecialchars($success); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Information -->
                <div class="lg:col-span-2">
                    <div class="bg-light rounded-xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold mb-4">Order Information</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm">Description</p>
                                <p class="mt-1"><?php echo nl2br(htmlspecialchars($order['order_description'])); ?></p>
                            </div>
                            
                            <?php if ($order['tracking_id']): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Tracking ID</p>
                                <p class="font-mono mt-1"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div>
                                <p class="text-gray-400 text-sm">Status</p>
                                <p class="mt-1 font-medium text-green-500">Completed</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Review Form -->
                    <div class="bg-light rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold mb-4">
                            <?php if ($review_submitted): ?>
                                Your Review
                            <?php else: ?>
                                Submit Your Review
                            <?php endif; ?>
                        </h2>
                        
                        <?php if ($review_submitted): ?>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm">Rating</p>
                                <div class="mt-1">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo ($i <= $rating) ? 'text-yellow-500' : 'text-gray-400'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <?php if (!empty($review_text)): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Review</p>
                                <p class="mt-1"><?php echo nl2br(htmlspecialchars($review_text)); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mt-6">
                                <p class="text-green-500 font-medium">Thank you for your review!</p>
                                <a href="track_orders.php" class="mt-2 inline-block text-primary hover:text-indigo-300">
                                    <i class="fas fa-arrow-left mr-2"></i> Back to Orders
                                </a>
                            </div>
                        </div>
                        <?php else: ?>
                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Rating *</label>
                                <div class="rating-stars flex text-2xl">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <button type="button" class="star <?php echo ($i <= $rating) ? 'star-active' : ''; ?>" data-rating="<?php echo $i; ?>">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" id="rating" name="rating" value="<?php echo $rating; ?>">
                            </div>
                            
                            <div>
                                <label for="review" class="block text-sm font-medium text-gray-300 mb-2">Review (Optional)</label>
                                <textarea 
                                    id="review" 
                                    name="review" 
                                    rows="4"
                                    class="w-full input-field bg-dark/50 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                                    placeholder="Share your experience with this service..."><?php echo htmlspecialchars($review_text); ?></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-4">
                                <button type="button" onclick="window.location.href='track_orders.php'" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                                    Cancel
                                </button>
                                <button type="submit" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-2 px-6 rounded-lg">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div>
                    <div class="bg-light rounded-xl shadow-lg p-6 sticky top-24">
                        <h2 class="text-xl font-bold mb-4">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-400 text-sm">Order ID</p>
                                <p class="font-mono">#<?php echo $order['id']; ?></p>
                            </div>
                            
                            <div>
                                <p class="text-gray-400 text-sm">Created</p>
                                <p><?php echo date('M j, Y g:i A', strtotime($order['created_at'])); ?></p>
                            </div>
                            
                            <div>
                                <p class="text-gray-400 text-sm">Last Updated</p>
                                <p><?php echo date('M j, Y g:i A', strtotime($order['updated_at'])); ?></p>
                            </div>
                            
                            <?php if ($order['tracking_id']): ?>
                            <div>
                                <p class="text-gray-400 text-sm">Tracking ID</p>
                                <p class="font-mono"><?php echo htmlspecialchars($order['tracking_id']); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
    <script>
        // Rating stars interaction
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.rating-stars .star');
            const ratingInput = document.getElementById('rating');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    
                    // Update star visuals
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('star-active');
                        } else {
                            s.classList.remove('star-active');
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>