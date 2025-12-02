<?php
$pageTitle = "Page Not Found - Hypecrews";
http_response_code(404);
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
</head>
<body class="bg-dark text-white">
    <?php include 'components/nav.php'; ?>

    <!-- 404 Section -->
    <section class="pt-32 pb-16 min-h-screen flex items-center bg-gradient-to-r from-dark to-[#0f172a]">
        <div class="container mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="text-9xl font-bold text-primary mb-6">404</div>
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Oops! Page Not Found</h1>
                <p class="text-xl text-gray-400 mb-10">The page you're looking for doesn't exist or has been moved.</p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="index.php" class="bg-gradient-to-r from-primary to-secondary hover:from-indigo-600 hover:to-purple-700 text-white font-bold py-3 px-8 rounded-lg transition">Go Home</a>
                    <a href="contact.php" class="border-2 border-white text-white hover:bg-white hover:text-dark font-bold py-3 px-8 rounded-lg transition">Contact Us</a>
                </div>
                
                <div class="mt-16">
                    <h2 class="text-2xl font-bold mb-8">Popular Pages</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                        <a href="index.php#services" class="bg-dark p-6 rounded-xl shadow-lg hover:shadow-xl transition border border-gray-800">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-cogs text-primary"></i>
                            </div>
                            <h3 class="font-bold">Our Services</h3>
                        </a>
                        <a href="about.php" class="bg-dark p-6 rounded-xl shadow-lg hover:shadow-xl transition border border-gray-800">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-friends text-primary"></i>
                            </div>
                            <h3 class="font-bold">About Us</h3>
                        </a>
                        <a href="contact.php" class="bg-dark p-6 rounded-xl shadow-lg hover:shadow-xl transition border border-gray-800">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <h3 class="font-bold">Contact</h3>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>