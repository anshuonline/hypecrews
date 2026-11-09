<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

<footer class="site-footer">
    <div class="shell" style="padding-top: 76px; padding-bottom: 0;">

        <!-- CTA band -->
        <div class="card-dark" style="border-radius: 28px; padding: clamp(28px, 4vw, 52px); margin-bottom: 64px; position: relative; overflow: hidden;" data-aos="fade-up">
            <div class="arc-lines"></div>
            <div style="position: relative; z-index: 1; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 28px;">
                <div style="max-width: 620px;">
                    <span class="eyebrow eyebrow-light">Let’s build something remarkable</span>
                    <h2 style="font-size: clamp(1.9rem, 3.4vw, 2.9rem); margin: 14px 0 12px;">Your next chapter starts with a conversation.</h2>
                    <p style="font-size: 14.5px; color: rgba(255,255,255,.66); max-width: 54ch;">Tell us where you want to go. We’ll map the brand, product and growth strategy to get you there.</p>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                    <a href="/contact.php" class="btn btn-sky">Start a Project <i class="fas fa-arrow-right"></i></a>
                    <a href="/services.php" class="btn btn-outline-light">Explore Services</a>
                </div>
            </div>
        </div>

        <div class="footer-grid">
            <div>
                <img src="/graphics/logos/hypecrews%20logo%20white.png" alt="Hypecrews" style="height: 42px; width: auto; margin-bottom: 20px;">
                <p style="font-size: 13px; line-height: 1.8; max-width: 300px; color: rgba(255,255,255,.56);">
                    A premium digital brand company building identity, product and growth systems for ambitious businesses across India and beyond.
                </p>
                <div class="footer-social" style="margin-top: 22px;">
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="X"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div>
                <h4>Company</h4>
                <div class="footer-links">
                    <a href="/about.php">About us</a>
                    <a href="/careers.php">Careers</a>
                    <a href="/softwares.php">Softwares</a>
                    <a href="/contact.php">Contact</a>
                    <a href="/track_orders.php">Track order</a>
                </div>
            </div>

            <div>
                <h4>Services</h4>
                <div class="footer-links">
                    <a href="/services.php#copyright">Copyright protection</a>
                    <a href="/services.php#social">Social media</a>
                    <a href="/services.php#digital">Digital marketing</a>
                    <a href="/services.php#web">Web development</a>
                    <a href="/services.php#creative">Creative & brand</a>
                </div>
            </div>

            <div>
                <h4>Resources</h4>
                <div class="footer-links">
                    <a href="/privacy-policy.php">Privacy policy</a>
                    <a href="/terms-conditions.php">Terms & conditions</a>
                    <a href="/refund-policy.php">Refund policy</a>
                    <a href="/support_chat.php">Support desk</a>
                    <a href="/sitemap.php">Sitemap</a>
                </div>
            </div>

            <div>
                <h4>Newsletter</h4>
                <p style="font-size: 12.5px; line-height: 1.7; color: rgba(255,255,255,.52); margin-bottom: 15px;">Monthly notes on brand, product and growth. No noise.</p>
                <form id="newsletterForm" class="footer-news" novalidate>
                    <input type="email" id="newsletterEmail" placeholder="you@company.com" aria-label="Email address" required>
                    <button type="submit" aria-label="Subscribe"><i class="fas fa-paper-plane"></i></button>
                </form>
                <div id="newsletterMessage" class="mt-2 text-sm hidden"></div>
                <div style="margin-top: 20px; font-size: 12px; color: rgba(255,255,255,.5);">
                    <p><i class="fas fa-location-dot" style="color: var(--sky); margin-right: 7px;"></i>Golaghat, Assam 785621</p>
                    <p style="margin-top: 7px;"><i class="fas fa-envelope" style="color: var(--sky); margin-right: 7px;"></i><a href="mailto:info@hypecrews.com">info@hypecrews.com</a></p>
                </div>
            </div>
        </div>

        <div class="footer-watermark" aria-hidden="true">HYPECREWS</div>

        <div class="footer-bottom">
            <p>&copy; 2024–<?php echo date('Y'); ?> Hypecrews Software Private Limited. All rights reserved.</p>
            <div style="display: flex; flex-wrap: wrap; gap: 18px;">
                <a href="/privacy-policy.php">Privacy</a>
                <a href="/terms-conditions.php">Terms</a>
                <a href="/refund-policy.php">Refunds</a>
                <a href="/sitemap.php">Sitemap</a>
            </div>
        </div>
    </div>

    <!-- Newsletter thank-you dialog -->
    <div id="newsletterPopup" class="fixed inset-0 hidden items-center justify-center p-4" style="z-index: 1000; background: rgba(11, 28, 47, 0.55); backdrop-filter: blur(6px);">
        <div style="background: #fff; border-radius: 24px; padding: 40px 32px; max-width: 420px; width: 100%; text-align: center; box-shadow: var(--shadow-lg);">
            <div class="icon-tile icon-tile-lg" style="margin: 0 auto 18px; background: #eafaf3; border-color: #c9f0df; color: #15805a;">
                <i class="fas fa-check"></i>
            </div>
            <h3 style="font-size: 24px; margin-bottom: 8px;">You’re on the list</h3>
            <p id="popupMessage" style="color: var(--muted); font-size: 13.5px; margin-bottom: 22px;">Thank you for subscribing to our newsletter.</p>
            <button id="closePopup" class="btn btn-primary btn-block">Close</button>
        </div>
    </div>
</footer>

<a href="#" class="back-to-top" id="backToTop" aria-label="Back to top"><i class="fas fa-arrow-up"></i></a>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('newsletterForm');
    const emailInput = document.getElementById('newsletterEmail');
    const message = document.getElementById('newsletterMessage');
    const popup = document.getElementById('newsletterPopup');
    const popupMessage = document.getElementById('popupMessage');
    const closePopup = document.getElementById('closePopup');

    function showMessage(text, type) {
        if (!message) return;
        message.textContent = text;
        message.style.color = type === 'error' ? '#ff9b8f' : '#7ee2b8';
        message.classList.remove('hidden');
        setTimeout(() => message.classList.add('hidden'), 5000);
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = emailInput.value.trim();
            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/newsletter_subscribe.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.status === 'success') {
                            if (popupMessage) popupMessage.textContent = response.message;
                            if (popup) { popup.classList.remove('hidden'); popup.classList.add('flex'); }
                            form.classList.add('hidden');
                        } else {
                            showMessage(response.message || 'Something went wrong.', 'error');
                        }
                    } catch (err) {
                        showMessage('An error occurred. Please try again.', 'error');
                    }
                } else {
                    showMessage('An error occurred. Please try again.', 'error');
                }
            };
            xhr.onerror = function () { showMessage('Network error. Please try again.', 'error'); };
            xhr.send('email=' + encodeURIComponent(email));
        });
    }

    function hidePopup() {
        if (!popup) return;
        popup.classList.add('hidden');
        popup.classList.remove('flex');
    }
    if (closePopup) closePopup.addEventListener('click', hidePopup);
    if (popup) popup.addEventListener('click', (e) => { if (e.target === popup) hidePopup(); });
});
</script>
