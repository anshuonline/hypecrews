<?php
$pageTitle = "Terms & Conditions";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Hypecrews</title>
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

    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white min-h-screen pt-24 pb-16">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-4xl font-bold mb-8 text-center">Terms & Conditions</h1>
                
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-8 shadow-2xl border border-slate-700">
                    <p class="text-gray-300 mb-6">Last Updated: <?php echo date("F d, Y"); ?></p>
                    
                    <p class="text-gray-300 mb-6">Welcome to Hypecrews. These terms and conditions outline the rules and regulations for the use of Hypecrews' services and website. By accessing this website or using our services, we assume you accept these terms and conditions. Do not continue to use Hypecrews if you do not agree to all the terms and conditions stated here.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">1. Acceptance of Terms</h3>
                    <p class="text-gray-300 mb-4">By engaging our services, you acknowledge that you have read, understood, and agreed to be bound by these terms and conditions. If you do not agree to these terms, you should not use our services. These terms constitute a legally binding agreement between you and Hypecrews.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">2. Services Description</h3>
                    <p class="text-gray-300 mb-4">Hypecrews provides a range of digital services including but not limited to:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Digital marketing and advertising services</li>
                        <li>Web design and development solutions</li>
                        <li>Social media management and content creation</li>
                        <li>Copyright protection and content removal services</li>
                        <li>Business support and consulting services</li>
                        <li>Video and music production services</li>
                        <li>Artist management and PR services</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">3. User Responsibilities</h3>
                    <p class="text-gray-300 mb-4">As a user of our services, you agree to:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Provide accurate and complete information when registering or placing orders</li>
                        <li>Maintain the confidentiality of your account credentials</li>
                        <li>Notify us immediately of any unauthorized use of your account</li>
                        <li>Comply with all applicable laws and regulations</li>
                        <li>Respect intellectual property rights of others</li>
                        <li>Not engage in any activity that could harm our systems or reputation</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">4. Intellectual Property Rights</h3>
                    <p class="text-gray-300 mb-4">Unless otherwise stated, Hypecrews and/or its licensors own the intellectual property rights for all material on this website and in our services. All intellectual property rights are reserved. You may view and/or print pages for your personal use subject to restrictions set in these terms and conditions.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">5. Payment Terms</h3>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>All prices are quoted in USD unless otherwise specified</li>
                        <li>Payment is due according to the agreed payment schedule</li>
                        <li>Late payments may incur interest charges at 1.5% per month</li>
                        <li>Services may be suspended for overdue accounts</li>
                        <li>Credit card payments are processed securely through our payment partners</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">6. Limitation of Liability</h3>
                    <p class="text-gray-300 mb-4">Hypecrews shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Your use or inability to use our services</li>
                        <li>Any unauthorized access to or alteration of your transmissions</li>
                        <li>Any interruption or cessation of transmission to or from our services</li>
                        <li>Any bugs, viruses, or similar issues transmitted through our services</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">7. Warranty Disclaimer</h3>
                    <p class="text-gray-300 mb-6">Our services are provided "as is" and "as available" without any warranties of any kind, either express or implied. Hypecrews disclaims all warranties, including but not limited to merchantability, fitness for a particular purpose, and non-infringement. We do not warrant that our services will be uninterrupted, timely, secure, or error-free.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">8. Termination</h3>
                    <p class="text-gray-300 mb-6">We may terminate or suspend your access to our services immediately, without prior notice, for any reason whatsoever, including without limitation if you breach the terms. Upon termination, your right to use our services will cease immediately. All provisions of the terms which by their nature should survive termination shall survive termination.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">9. Governing Law</h3>
                    <p class="text-gray-300 mb-6">These terms shall be governed and construed in accordance with the laws of the State of Delaware, United States, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these terms will not be considered a waiver of those rights.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">10. Changes to Terms</h3>
                    <p class="text-gray-300 mb-6">We reserve the right, at our sole discretion, to modify or replace these terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>
                    
                    <div class="mt-10 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                        <p class="text-gray-300"><strong>Contact Us:</strong> If you have any questions about these Terms & Conditions, please contact us at <a href="mailto:legal@hypecrews.com" class="text-primary hover:underline">legal@hypecrews.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>