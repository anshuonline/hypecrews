<?php
$pageTitle = "Refund Policy";
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
                <h1 class="text-4xl font-bold mb-8 text-center">Refund Policy</h1>
                
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-8 shadow-2xl border border-slate-700">
                    <p class="text-gray-300 mb-6">Last Updated: <?php echo date("F d, Y"); ?></p>
                    
                    <p class="text-gray-300 mb-6">At Hypecrews, we strive to ensure complete customer satisfaction with all our services. However, we understand that circumstances may arise where a refund is necessary. This refund policy outlines the terms and conditions under which refunds may be granted.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">1. General Refund Principles</h3>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>All refund requests must be submitted in writing to our support team</li>
                        <li>Refunds are evaluated on a case-by-case basis</li>
                        <li>Processing time for refunds typically takes 5-10 business days</li>
                        <li>Approved refunds will be issued to the original payment method</li>
                        <li>Refund eligibility depends on service delivery status and terms</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">2. Service-Based Refunds</h3>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li><strong>Digital Marketing Services:</strong> Full refund available if campaign hasn't started. Partial refund for campaigns in progress based on work completed</li>
                        <li><strong>Web Development:</strong> Full refund available before project commencement. After commencement, refund based on milestone completion</li>
                        <li><strong>Content Creation:</strong> Full refund available if content hasn't been delivered. No refunds for delivered content</li>
                        <li><strong>Social Media Management:</strong> Pro-rated refund for unused months with 30-day notice</li>
                        <li><strong>Consulting Services:</strong> Full refund available if session hasn't occurred. No refunds for completed sessions</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">3. Subscription Services</h3>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Monthly subscriptions can be cancelled at any time with 30-day notice</li>
                        <li>No refunds for partial months of subscription service</li>
                        <li>Annual subscriptions offer a 30-day money-back guarantee from purchase date</li>
                        <li>Prorated refunds available for annual subscriptions cancelled after 30 days</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">4. Non-Refundable Items</h3>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Domain registration fees (managed by third parties)</li>
                        <li>Third-party software licenses</li>
                        <li>Setup fees and consultation charges</li>
                        <li>Completed custom work that meets agreed specifications</li>
                        <li>Fees for expedited services</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">5. Refund Request Process</h3>
                    <ol class="list-decimal pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Contact our support team at support@hypecrews.com with your request</li>
                        <li>Include your order number, service details, and reason for refund</li>
                        <li>Provide any supporting documentation if applicable</li>
                        <li>Our team will review your request within 3 business days</li>
                        <li>You will receive written confirmation of approval or denial</li>
                    </ol>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">6. Dispute Resolution</h3>
                    <p class="text-gray-300 mb-6">If you disagree with our refund decision, you may escalate the matter to our customer advocacy team. All disputes must be submitted in writing within 30 days of the initial decision. Our advocacy team will conduct a thorough review and provide a final determination within 10 business days.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">7. Policy Updates</h3>
                    <p class="text-gray-300 mb-6">We reserve the right to update or modify this refund policy at any time. Changes will be effective immediately upon posting on our website. Your continued use of our services after any modifications constitutes acceptance of the updated policy.</p>
                    
                    <div class="mt-10 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                        <p class="text-gray-300"><strong>Contact Us:</strong> For any questions regarding our refund policy, please contact us at <a href="mailto:support@hypecrews.com" class="text-primary hover:underline">support@hypecrews.com</a> or call us at +1 (555) 123-4567.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>