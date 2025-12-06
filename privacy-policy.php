<?php
$pageTitle = "Privacy Policy";
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
                <h1 class="text-4xl font-bold mb-8 text-center">Privacy Policy</h1>
                
                <div class="bg-slate-800/50 backdrop-blur-sm rounded-xl p-8 shadow-2xl border border-slate-700">
                    <p class="text-gray-300 mb-6">Last Updated: <?php echo date("F d, Y"); ?></p>
                    
                    <p class="text-gray-300 mb-6">Hypecrews is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our services. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the site or use our services.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">1. Information We Collect</h3>
                    <h4 class="text-lg font-bold mb-3 text-gray-200">Personal Information</h4>
                    <p class="text-gray-300 mb-4">We may collect personally identifiable information that you voluntarily provide to us when you:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>Register for an account on our website</li>
                        <li>Place an order or purchase our services</li>
                        <li>Subscribe to our newsletter</li>
                        <li>Contact us via email or our contact forms</li>
                        <li>Participate in surveys, promotions, or contests</li>
                    </ul>
                    
                    <h4 class="text-lg font-bold mb-3 text-gray-200">Usage Data</h4>
                    <p class="text-gray-300 mb-4">We may automatically collect certain information when you visit, use, or navigate our website:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>IP address and browser type</li>
                        <li>Device information and operating system</li>
                        <li>Pages visited and time spent on our website</li>
                        <li>Referring website and exit pages</li>
                        <li>Click patterns and site interaction data</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">2. How We Use Your Information</h3>
                    <p class="text-gray-300 mb-4">We may use the information we collect for various purposes:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li>To provide, maintain, and improve our services</li>
                        <li>To process transactions and send transactional communications</li>
                        <li>To send periodic emails about your account or orders</li>
                        <li>To personalize your experience and deliver content relevant to your interests</li>
                        <li>To monitor and analyze usage and trends to improve our website</li>
                        <li>To detect, prevent, and address technical issues or security breaches</li>
                        <li>To comply with legal obligations and protect our rights</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">3. Information Sharing and Disclosure</h3>
                    <p class="text-gray-300 mb-4">We may share your information in the following situations:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li><strong>Service Providers:</strong> With third-party vendors who perform services on our behalf</li>
                        <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                        <li><strong>Legal Requirements:</strong> When required by law or to protect our rights and safety</li>
                        <li><strong>Consent:</strong> With your explicit consent or at your direction</li>
                        <li><strong>Affiliates:</strong> With our affiliated companies for business purposes</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">4. Data Security</h3>
                    <p class="text-gray-300 mb-6">We implement appropriate technical and organizational security measures to protect your personal information. These measures include encryption, secure server connections, access controls, and regular security assessments. However, no method of transmission over the Internet or electronic storage is 100% secure, and we cannot guarantee absolute security.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">5. Data Retention</h3>
                    <p class="text-gray-300 mb-6">We retain your personal information for as long as necessary to fulfill the purposes outlined in this privacy policy, unless a longer retention period is required or permitted by law. When we no longer need your information, we will securely delete or anonymize it.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">6. Your Rights</h3>
                    <p class="text-gray-300 mb-4">Depending on your location, you may have certain rights regarding your personal information:</p>
                    <ul class="list-disc pl-6 text-gray-300 mb-6 space-y-2">
                        <li><strong>Right to Access:</strong> Request copies of your personal information</li>
                        <li><strong>Right to Rectification:</strong> Correct inaccurate personal information</li>
                        <li><strong>Right to Erasure:</strong> Request deletion of your personal information</li>
                        <li><strong>Right to Restrict Processing:</strong> Limit how we use your personal information</li>
                        <li><strong>Right to Data Portability:</strong> Obtain and reuse your information for your own purposes</li>
                        <li><strong>Right to Object:</strong> Object to processing based on legitimate interests</li>
                    </ul>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">7. Cookies and Tracking Technologies</h3>
                    <p class="text-gray-300 mb-6">We use cookies and similar tracking technologies to track activity on our website and store certain information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">8. Third-Party Websites</h3>
                    <p class="text-gray-300 mb-6">Our website may contain links to third-party websites that are not operated by us. If you click on a third-party link, you will be directed to that third party's site. We strongly advise you to review the Privacy Policy of every site you visit. We have no control over and assume no responsibility for the content, privacy policies, or practices of any third-party sites or services.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">9. Children's Privacy</h3>
                    <p class="text-gray-300 mb-6">Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children under 18. If you are a parent or guardian and you are aware that your child has provided us with personal information, please contact us so we can take necessary actions.</p>
                    
                    <h3 class="text-xl font-bold mb-4 mt-8 text-white">10. Changes to This Privacy Policy</h3>
                    <p class="text-gray-300 mb-6">We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
                    
                    <div class="mt-10 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
                        <p class="text-gray-300"><strong>Contact Us:</strong> If you have any questions about this Privacy Policy, please contact us at <a href="mailto:privacy@hypecrews.com" class="text-primary hover:underline">privacy@hypecrews.com</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>