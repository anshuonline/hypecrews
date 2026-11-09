<?php
$pageTitle = "Terms & Conditions — Hypecrews";
$pageDescription = "Read Hypecrews' Terms & Conditions. Understand the rules, user responsibilities, payment terms, and policies governing our digital marketing and web services.";
$pageKeywords = "Hypecrews terms and conditions, terms of service, user agreement, digital services policy, Hypecrews legal, service terms";
$canonical = "https://hypecrews.com/terms-conditions.php";
$jsonLd = '{
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "Terms & Conditions",
    "description": "Terms and conditions governing the use of Hypecrews digital marketing, web development, and creative services.",
    "url": "https://hypecrews.com/terms-conditions.php",
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
    },
    "inLanguage": "en"
}';
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<section class="page-hero-sm" style="background: linear-gradient(145deg, var(--navy-deep), var(--navy) 65%, #0b2f52); position: relative; overflow: hidden;">
    <div class="arc-lines"></div>
    <div class="shell shell-wide relative z-1">
        <div class="max-w-3xl" data-aos="fade-up">
            <span class="chip chip-light" style="font-size: 11px;"><i class="fas fa-file-contract"></i> Legal</span>
            <h1 class="display-1" style="color: #fff; margin-top: 20px; font-size: clamp(2.2rem, 4.4vw, 3.4rem);">Terms &amp; Conditions</h1>
            <p style="font-size: 14px; color: rgba(255,255,255,.62); margin-top: 16px; line-height: 1.85; max-width: 620px;">The clear, fair agreement behind every engagement — what you can expect from us, and what we ask of you.</p>
            <div class="flex flex-wrap items-center gap-2" style="margin-top: 22px;">
                <span class="chip chip-light" style="font-size: 11px;"><i class="far fa-calendar"></i> Last updated <?php echo date("F d, Y"); ?></span>
                <span class="chip chip-light" style="font-size: 11px;"><i class="fas fa-scale-balanced"></i> Plain-language legal</span>
            </div>
        </div>
    </div>
</section>

<section class="section-sm" style="background: var(--mist);">
    <div class="shell shell-wide">
        <div class="article-layout">
            <aside class="article-toc" data-aos="fade-up">
                <h3>On this page</h3>
                <a href="#acceptance">1. Acceptance of terms</a>
                <a href="#services">2. Services description</a>
                <a href="#responsibilities">3. User responsibilities</a>
                <a href="#ip">4. Intellectual property</a>
                <a href="#payment">5. Payment terms</a>
                <a href="#liability">6. Limitation of liability</a>
                <a href="#warranty">7. Warranty disclaimer</a>
                <a href="#termination">8. Termination</a>
                <a href="#law">9. Governing law</a>
                <a href="#changes">10. Changes to terms</a>
            </aside>

            <div class="article-body" data-aos="fade-up">
                <p style="font-size: 12px; color: var(--muted-2); font-weight: 700;">Last Updated: <?php echo date("F d, Y"); ?></p>

                <p>Welcome to Hypecrews. These terms and conditions outline the rules and regulations for the use of Hypecrews' services and website. By accessing this website or using our services, we assume you accept these terms and conditions. Do not continue to use Hypecrews if you do not agree to all the terms and conditions stated here.</p>

                <h2 id="acceptance" style="font-size: 21px;">1. Acceptance of Terms</h2>
                <p>By engaging our services, you acknowledge that you have read, understood, and agreed to be bound by these terms and conditions. If you do not agree to these terms, you should not use our services. These terms constitute a legally binding agreement between you and Hypecrews.</p>

                <h2 id="services" style="font-size: 21px;">2. Services Description</h2>
                <p>Hypecrews provides a range of digital services including but not limited to:</p>
                <ul>
                    <li>Digital marketing and advertising services</li>
                    <li>Web design and development solutions</li>
                    <li>Social media management and content creation</li>
                    <li>Copyright protection and content removal services</li>
                    <li>Business support and consulting services</li>
                    <li>Video and music production services</li>
                    <li>Artist management and PR services</li>
                </ul>

                <h2 id="responsibilities" style="font-size: 21px;">3. User Responsibilities</h2>
                <p>As a user of our services, you agree to:</p>
                <ul>
                    <li>Provide accurate and complete information when registering or placing orders</li>
                    <li>Maintain the confidentiality of your account credentials</li>
                    <li>Notify us immediately of any unauthorized use of your account</li>
                    <li>Comply with all applicable laws and regulations</li>
                    <li>Respect intellectual property rights of others</li>
                    <li>Not engage in any activity that could harm our systems or reputation</li>
                </ul>

                <h2 id="ip" style="font-size: 21px;">4. Intellectual Property Rights</h2>
                <p>Unless otherwise stated, Hypecrews and/or its licensors own the intellectual property rights for all material on this website and in our services. All intellectual property rights are reserved. You may view and/or print pages for your personal use subject to restrictions set in these terms and conditions.</p>

                <h2 id="payment" style="font-size: 21px;">5. Payment Terms</h2>
                <ul>
                    <li>All prices are quoted in INR (Indian Rupee) and USD (US Dollar) depending on the client location, unless otherwise specified</li>
                    <li>Payment is due according to the agreed payment schedule</li>
                    <li>Late payments may incur interest charges at 1.5% per month</li>
                    <li>Services may be suspended for overdue accounts</li>
                    <li>Credit card payments are processed securely through our payment partners</li>
                </ul>

                <h2 id="liability" style="font-size: 21px;">6. Limitation of Liability</h2>
                <p>Hypecrews shall not be liable for any indirect, incidental, special, consequential, or punitive damages, including but not limited to loss of profits, data, use, goodwill, or other intangible losses, resulting from:</p>
                <ul>
                    <li>Your use or inability to use our services</li>
                    <li>Any unauthorized access to or alteration of your transmissions</li>
                    <li>Any interruption or cessation of transmission to or from our services</li>
                    <li>Any bugs, viruses, or similar issues transmitted through our services</li>
                </ul>

                <h2 id="warranty" style="font-size: 21px;">7. Warranty Disclaimer</h2>
                <p>Our services are provided "as is" and "as available" without any warranties of any kind, either express or implied. Hypecrews disclaims all warranties, including but not limited to merchantability, fitness for a particular purpose, and non-infringement. We do not warrant that our services will be uninterrupted, timely, secure, or error-free.</p>

                <h2 id="termination" style="font-size: 21px;">8. Termination</h2>
                <p>We may terminate or suspend your access to our services immediately, without prior notice, for any reason whatsoever, including without limitation if you breach the terms. Upon termination, your right to use our services will cease immediately. All provisions of the terms which by their nature should survive termination shall survive termination.</p>

                <h2 id="law" style="font-size: 21px;">9. Governing Law</h2>
                <p>These terms shall be governed and construed in accordance with the laws of the State of Delaware, United States, without regard to its conflict of law provisions. Our failure to enforce any right or provision of these terms will not be considered a waiver of those rights.</p>

                <h2 id="changes" style="font-size: 21px;">10. Changes to Terms</h2>
                <p>We reserve the right, at our sole discretion, to modify or replace these terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.</p>

                <div class="card" style="margin-top: 30px; padding: 22px; background: var(--mist);">
                    <p style="margin: 0; font-size: 13.5px; color: var(--muted);"><strong style="color: var(--navy);">Contact us:</strong> If you have any questions about these Terms &amp; Conditions, please contact us at <a href="mailto:legal@hypecrews.com">legal@hypecrews.com</a>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-sm" style="background: #fff; border-top: 1px solid var(--line);">
    <div class="shell shell-wide">
        <div class="card-dark" style="padding: clamp(26px, 4vw, 44px); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 20px; position: relative; overflow: hidden;" data-aos="fade-up">
            <div class="arc-lines"></div>
            <div class="relative z-1">
                <h2 style="font-size: clamp(1.4rem, 2.4vw, 1.9rem); color: #fff;">Ready to work together?</h2>
                <p style="font-size: 13px; color: rgba(255,255,255,.62); margin-top: 7px;">Start with a conversation — no commitment, no pressure.</p>
            </div>
            <div class="flex flex-wrap gap-3 relative z-1">
                <a href="/contact.php" class="btn btn-sky">Start a project <i class="fas fa-arrow-right"></i></a>
                <a href="/privacy-policy.php" class="btn btn-ghost-light">Privacy policy</a>
            </div>
        </div>
    </div>
</section>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
