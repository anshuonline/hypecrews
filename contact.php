<?php
session_start();
$pageTitle = "Contact Us — Hypecrews";
$pageDescription = "Contact Hypecrews for digital marketing, web development, copyright protection and more. Reach us at info@hypecrews.com or call +91 361 324 3276. Golaghat, Assam.";
$pageKeywords = "contact hypecrews, digital agency contact, get in touch, hypecrews support, hypecrews email, hypecrews phone, digital services inquiry";
$canonical = "https://hypecrews.com/contact.php";
$jsonLd = '{
  "@context": "https://schema.org", "@type": "ContactPage", "name": "Contact Hypecrews", "url": "https://hypecrews.com/contact.php",
  "mainEntity": { "@type": "Organization", "name": "Hypecrews", "url": "https://hypecrews.com", "email": "info@hypecrews.com", "telephone": "+913613243276", "address": { "@type": "PostalAddress", "addressLocality": "Golaghat", "addressRegion": "Assam", "postalCode": "785621", "addressCountry": "IN" } }
}';
include 'components/head.php';
?>
<body class="antialiased">
<?php include 'components/nav.php'; ?>

<!-- ============================================================ HERO -->
<section class="page-hero-sm">
    <div class="hero-mesh"></div>
    <div class="grid-lines"></div>
    <div class="orb orb-1" style="width: 280px; height: 280px; top: 0; right: -4%;"></div>
    <div class="shell shell-wide relative z-1">
        <div class="section-head section-head-center" style="margin-bottom: 0;" data-aos="fade-up">
            <div class="chip" style="margin-bottom: 18px;"><span style="width:7px;height:7px;border-radius:99px;background:#1a9f6e;box-shadow:0 0 0 4px rgba(26,159,110,.13);display:inline-block;"></span> We’re online and ready</div>
            <h1 class="display-1">Let’s build something<br><span class="gradient-text">incredible together.</span></h1>
            <p class="lead">Have a question or a project in mind? Tell us where you want to go and we’ll come back within one business day with a clear next step.</p>
        </div>
    </div>
</section>

<!-- ========================================================== CONTACT -->
<section class="section" style="padding-top: 56px; background: var(--mist);">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-[1.15fr_.85fr] gap-8 items-start">
            <!-- Form -->
            <div class="card" style="padding: clamp(24px, 3.6vw, 42px);" data-aos="fade-right">
                <span class="eyebrow">Send a message</span>
                <h2 style="font-size: clamp(24px, 3vw, 32px); margin-top: 14px;">Tell us about your project.</h2>
                <p style="font-size: 13px; color: var(--muted); margin-top: 8px;">Fields marked <span style="color:#b14537;">*</span> are required.</p>

                <form id="contactForm" novalidate style="margin-top: 26px;">
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="field" style="margin-bottom: 0;">
                            <label for="name" class="label">Full name <span style="color:#b14537;">*</span></label>
                            <input type="text" id="name" name="name" class="input" placeholder="John Doe" required>
                        </div>
                        <div class="field" style="margin-bottom: 0;">
                            <label for="email" class="label">Email address <span style="color:#b14537;">*</span></label>
                            <input type="email" id="email" name="email" class="input" placeholder="john@example.com" required>
                        </div>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4" style="margin-top: 18px;">
                        <div class="field" style="margin-bottom: 0;">
                            <label for="phone" class="label">Phone number</label>
                            <input type="text" id="phone" name="phone" class="input" placeholder="9876543210" maxlength="15" inputmode="numeric">
                        </div>
                        <div class="field" style="margin-bottom: 0;">
                            <label for="subject" class="label">Subject <span style="color:#b14537;">*</span></label>
                            <input type="text" id="subject" name="subject" class="input" placeholder="How can we help?" required>
                        </div>
                    </div>
                    <div class="field" style="margin-top: 18px;">
                        <label for="service" class="label">Service interest <span style="color:#b14537;">*</span></label>
                        <select id="service" name="service" class="select" required>
                            <option value="" disabled selected>Select a service</option>
                            <option value="copyright">Copyright Protection &amp; Removal</option>
                            <option value="social">Social Media Management</option>
                            <option value="marketing">Digital Marketing</option>
                            <option value="web">Web &amp; Development</option>
                            <option value="recovery">Recovery &amp; Support</option>
                            <option value="video">Video Production</option>
                            <option value="seo">Marketing &amp; SEO</option>
                            <option value="creative">Creative &amp; Production</option>
                            <option value="partner">Become a Partner</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="field" style="margin-top: 18px;">
                        <label for="message" class="label">Message <span style="color:#b14537;">*</span></label>
                        <textarea id="message" name="message" rows="5" class="textarea" placeholder="Tell us about your goals, timeline and budget…" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg btn-block">Send message <i class="fas fa-paper-plane"></i></button>
                    <p style="font-size: 11px; color: var(--muted-2); text-align: center; margin-top: 12px;"><i class="fas fa-lock" style="margin-right: 5px;"></i>Your details stay private. No spam, ever.</p>
                </form>
                <div id="formMessage" class="mt-4 hidden" aria-live="polite"></div>
            </div>

            <!-- Info -->
            <div style="display: grid; gap: 16px;" data-aos="fade-left" data-aos-delay="80">
                <div class="card card-pad" style="display: flex; gap: 16px; align-items: flex-start; padding: 24px;">
                    <div class="icon-tile"><i class="fas fa-location-dot"></i></div>
                    <div><h3 style="font-size: 16px;">Headquarters</h3><p style="font-size: 13px; color: var(--muted); margin-top: 5px; line-height: 1.7;">Golaghat, Assam 785621<br>India</p></div>
                </div>
                <div class="card card-pad" style="display: flex; gap: 16px; align-items: flex-start; padding: 24px;">
                    <div class="icon-tile"><i class="fas fa-phone"></i></div>
                    <div><h3 style="font-size: 16px;">Call us directly</h3><p style="font-size: 13px; color: var(--muted); margin-top: 5px; line-height: 1.7;"><a href="tel:+913613243276" style="color: var(--blue); font-weight: 700;">+91 361 324 3276</a><br><span style="font-size: 10px; text-transform: uppercase; letter-spacing: .12em; font-weight: 800; color: var(--muted-2);">Available 24/7</span></p></div>
                </div>
                <div class="card card-pad" style="display: flex; gap: 16px; align-items: flex-start; padding: 24px;">
                    <div class="icon-tile"><i class="fas fa-envelope"></i></div>
                    <div><h3 style="font-size: 16px;">Email support</h3><p style="font-size: 13px; color: var(--muted); margin-top: 5px; line-height: 1.9;"><a href="mailto:info@hypecrews.com" style="color: var(--blue); font-weight: 700;">info@hypecrews.com</a><br><a href="mailto:support@hypecrews.com" style="color: var(--blue); font-weight: 700;">support@hypecrews.com</a></p></div>
                </div>
                <div class="card-dark" style="padding: 26px; position: relative; overflow: hidden;">
                    <div class="arc-lines"></div>
                    <div class="relative z-1">
                        <h3 style="font-size: 17px; color: #fff;">Prefer to talk first?</h3>
                        <p style="font-size: 12.5px; color: rgba(255,255,255,.62); margin-top: 7px; line-height: 1.7;">Book a 30-minute strategy call. No pitch, just useful clarity on your next move.</p>
                        <a href="tel:+913613243276" class="btn btn-sky btn-sm" style="margin-top: 16px;"><i class="fas fa-phone"></i> Book a call</a>
                    </div>
                </div>
                <div class="card card-pad" style="padding: 24px;">
                    <h3 style="font-size: 14px;">Connect with us</h3>
                    <div class="flex gap-2" style="margin-top: 14px;">
                        <a href="#" class="icon-tile icon-tile-sm" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="icon-tile icon-tile-sm" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="icon-tile icon-tile-sm" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="icon-tile icon-tile-sm" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========================================================== FAQ -->
<section class="section" style="background: #fff;">
    <div class="shell shell-wide">
        <div class="grid lg:grid-cols-[.8fr_1.2fr] gap-14 items-start">
            <div data-aos="fade-right">
                <span class="eyebrow">FAQ</span>
                <h2 class="display-2" style="margin-top: 16px;">Frequently asked<br><span class="gradient-text">questions.</span></h2>
                <p class="lead" style="margin-top: 18px;">Find answers to common questions about our services and process. Still stuck? We’re one message away.</p>
            </div>
            <div data-aos="fade-left">
                <?php
                $faqs = [
                    ['q' => 'How long does a typical project take to complete?', 'a' => 'Project timelines vary depending on scope and complexity. Simple websites can be completed in 2–4 weeks, while larger projects may take 2–6 months. We provide detailed timelines during our initial consultation.'],
                    ['q' => 'Do you offer ongoing support after project completion?', 'a' => 'Yes, we offer various maintenance and support packages to ensure your digital assets continue to perform optimally. Our team is always available for updates, improvements and troubleshooting.'],
                    ['q' => 'What information should I prepare before contacting you?', 'a' => 'To help us understand your needs better, please prepare information about your business goals, target audience, timeline and budget. Examples of websites or designs you like can also be helpful.'],
                    ['q' => 'How do you ensure the security of my data and intellectual property?', 'a' => 'We take data security seriously and employ industry-standard encryption, secure development practices and confidentiality agreements. All client data is stored securely, and we never share your information without explicit permission.'],
                ];
                foreach ($faqs as $i => $faq): ?>
                <div class="accordion" data-open="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                    <button type="button" class="accordion-trigger"><?php echo $faq['q']; ?><i class="fas fa-plus"></i></button>
                    <div class="accordion-panel"><div class="accordion-panel-inner"><?php echo $faq['a']; ?></div></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Phone: digits only */
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('keypress', function (e) {
            if (e.which < 48 || e.which > 57) e.preventDefault();
        });
        phoneInput.addEventListener('paste', function (e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            this.value = paste.replace(/[^0-9]/g, '');
        });
    }

    /* Form submission */
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    function showMessage(text, type) {
        if (!formMessage) return;
        formMessage.classList.remove('hidden');
        formMessage.innerHTML = '<div class="alert ' + (type === 'error' ? 'alert-error' : 'alert-success') + '"><i class="fas ' + (type === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check') + '"></i><span>' + text + '</span></div>';
        formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(function () { if (type !== 'error') formMessage.classList.add('hidden'); }, 6000);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();

            if (!name || !email || !subject || !message) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }

            const btn = form.querySelector('button[type="submit"]');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Sending…';

            fetch('/contact-handler.php', { method: 'POST', body: new FormData(form) })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status === 'success') {
                        showMessage(res.message || 'Thank you for your message. We will contact you soon.', 'success');
                        form.reset();
                    } else {
                        showMessage(res.message || 'Something went wrong. Please try again.', 'error');
                    }
                })
                .catch(function () {
                    showMessage('Thank you for your message! We will contact you soon.', 'success');
                    form.reset();
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = original;
                });
        });
    }
});
</script>

<?php include 'components/footer.php'; ?>
<?php include 'components/guest_chat.php'; ?>
</body>
</html>
