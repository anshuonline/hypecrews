// JavaScript for Hypecrews website

// Navbar scroll effect
document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('navbar-scrolled');
            } else {
                header.classList.remove('navbar-scrolled');
            }
        });
    }
    
    const backToTopButton = document.getElementById('backToTop');
    
    if (backToTopButton) {
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('opacity-0', 'invisible');
                backToTopButton.classList.add('opacity-100', 'visible');
            } else {
                backToTopButton.classList.add('opacity-0', 'invisible');
                backToTopButton.classList.remove('opacity-100', 'visible');
            }
        });
        
        backToTopButton.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenuDropdown = document.getElementById('mobile-menu-dropdown');
    
    if (mobileMenuButton && mobileMenuDropdown) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenuDropdown.classList.toggle('hidden');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInsideMenu = mobileMenuButton.contains(event.target) || mobileMenuDropdown.contains(event.target);
            if (!isClickInsideMenu && !mobileMenuDropdown.classList.contains('hidden')) {
                mobileMenuDropdown.classList.add('hidden');
            }
        });
    }
    
    // Mobile services menu toggle
    const mobileServicesToggle = document.getElementById('mobile-services-toggle');
    const mobileServicesMenu = document.getElementById('mobile-services-menu');
    
    if (mobileServicesToggle && mobileServicesMenu) {
        mobileServicesToggle.addEventListener('click', function() {
            mobileServicesMenu.classList.toggle('hidden');
            const icon = this.querySelector('i');
            icon.classList.toggle('rotate-180');
        });
    }
    
    // Animation on scroll
    const animateElements = document.querySelectorAll('.animate-on-scroll');
    
    if (animateElements.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });
        
        animateElements.forEach(element => {
            observer.observe(element);
        });
    }
    
    // Form submission handling
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Basic validation
            if (!name || !email || !subject || !message) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }
            
            // In a real application, you would send this data to your server
            // For now, we'll just show a success message
            
            showMessage('Thank you for your message! We will contact you soon.', 'success');
            
            // Reset form
            contactForm.reset();
        });
    }
    
    // Add floating animation to hero elements
    const floatingElements = document.querySelectorAll('.floating');
    floatingElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.2}s`;
    });
    
    // Client slider functionality
    const slider = document.querySelector('.animate-slide');
    if (slider) {
        // Duplicate the slider content for seamless looping
        slider.innerHTML += slider.innerHTML;
        
        // Auto-slide functionality
        let autoSlideInterval;
        let isHovering = false;
        let isDragging = false;
        
        // Start auto sliding
        function startAutoSlide() {
            if (autoSlideInterval) clearInterval(autoSlideInterval);
            if (!isHovering && !isDragging) {
                autoSlideInterval = setInterval(() => {
                    if (slider.scrollLeft >= slider.scrollWidth / 2) {
                        slider.scrollLeft = 0;
                    } else {
                        slider.scrollLeft += 1;
                    }
                }, 30);
            }
        }
        
        // Stop auto sliding
        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                autoSlideInterval = null;
            }
        }
        
        // Mouse events for hover and drag
        slider.addEventListener('mouseenter', () => {
            isHovering = true;
            stopAutoSlide();
        });
        
        slider.addEventListener('mouseleave', () => {
            isHovering = false;
            startAutoSlide();
        });
        
        // Add drag functionality
        let startX;
        let scrollLeft;
        
        slider.addEventListener('mousedown', (e) => {
            isDragging = true;
            stopAutoSlide();
            startX = e.pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
            slider.style.cursor = 'grabbing';
        });
        
        slider.addEventListener('mouseup', () => {
            isDragging = false;
            slider.style.cursor = 'grab';
            startAutoSlide();
        });
        
        slider.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            e.preventDefault();
            const x = e.pageX - slider.offsetLeft;
            const walk = (x - startX) * 2; // Scroll-fast multiplier
            slider.scrollLeft = scrollLeft - walk;
        });
        
        // Touch events for mobile
        slider.addEventListener('touchstart', (e) => {
            isDragging = true;
            stopAutoSlide();
            startX = e.touches[0].pageX - slider.offsetLeft;
            scrollLeft = slider.scrollLeft;
        });
        
        slider.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            const x = e.touches[0].pageX - slider.offsetLeft;
            const walk = (x - startX) * 2; // Scroll-fast multiplier
            slider.scrollLeft = scrollLeft - walk;
        });
        
        slider.addEventListener('touchend', () => {
            isDragging = false;
            startAutoSlide();
        });
        
        // Start auto sliding initially
        startAutoSlide();
    }
    
    // Create cursor highlight element
    const cursorHighlight = document.createElement('div');
    cursorHighlight.className = 'cursor-highlight';
    document.body.appendChild(cursorHighlight);
    
    // Mouse move event to move the highlight
    document.addEventListener('mousemove', function(e) {
        cursorHighlight.style.left = e.clientX + 'px';
        cursorHighlight.style.top = e.clientY + 'px';
        cursorHighlight.classList.add('visible');
    });
    
    // Mouse leave event to hide the highlight
    document.addEventListener('mouseleave', function() {
        cursorHighlight.classList.remove('visible');
    });
    
    // Click events to animate the highlight
    document.addEventListener('mousedown', function() {
        cursorHighlight.style.width = '30px';
        cursorHighlight.style.height = '30px';
    });
    
    document.addEventListener('mouseup', function() {
        cursorHighlight.style.width = '20px';
        cursorHighlight.style.height = '20px';
    });
});

// Function to show message
function showMessage(text, type) {
    const formMessage = document.getElementById('formMessage');
    if (formMessage) {
        const alertClass = type === 'error' ? 
            'bg-red-100 border border-red-400 text-red-700' : 
            'bg-green-100 border border-green-400 text-green-700';
            
        formMessage.innerHTML = `
            <div class="${alertClass} px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">${text}</span>
            </div>
        `;
        formMessage.classList.remove('hidden');
        
        // Scroll to message
        formMessage.scrollIntoView({ behavior: 'smooth' });
        
        // Hide message after 5 seconds
        setTimeout(() => {
            formMessage.classList.add('hidden');
        }, 5000);
    }
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            const offsetTop = targetElement.offsetTop - 80; // Adjust for fixed header
            
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});