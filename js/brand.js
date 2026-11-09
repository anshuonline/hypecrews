/* ============================================================
   HYPECREWS — INTERACTIONS
   ============================================================ */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    initAOS();
    initHeader();
    initDrawer();
    initCounters();
    initSwipers();
    initAccordions();
    initPasswordToggles();
    initScrollProgress();
    initBackTop();
    initActiveNav();
    initTilt();
    initSmoothAnchors();
  });

  /* ------------------------------------------------------------- AOS */
  function initAOS() {
    if (window.AOS) {
      window.AOS.init({ duration: 700, easing: 'ease-out-cubic', once: true, offset: 60 });
    }
  }

  /* ---------------------------------------------------------- Header */
  function initHeader() {
    var shell = document.querySelector('.nav-shell, .site-header');
    if (!shell) return;
    var onScroll = function () {
      shell.classList.toggle('is-scrolled', window.scrollY > 24);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  /* ---------------------------------------------------------- Drawer */
  function initDrawer() {
    var burger = document.getElementById('navBurger') || document.getElementById('mobileMenuBtn');
    var drawer = document.getElementById('navDrawer') || document.getElementById('mobileDrawer');
    var close = document.getElementById('navDrawerClose') || drawer && drawer.querySelector('[data-drawer-close]');
    if (!burger || !drawer) return;

    function open() {
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      burger.setAttribute('aria-expanded', 'true');
    }
    function hide() {
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      burger.setAttribute('aria-expanded', 'false');
    }
    burger.addEventListener('click', open);
    if (close) close.addEventListener('click', hide);
    drawer.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', hide);
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') hide();
    });
  }

  /* --------------------------------------------------------- Counters */
  function initCounters() {
    var els = document.querySelectorAll('[data-count]');
    if (!els.length) return;

    var animate = function (el) {
      var target = parseFloat(el.dataset.count);
      if (isNaN(target)) return;
      var decimals = (el.dataset.count.split('.')[1] || '').length;
      var suffix = el.dataset.suffix || '';
      var duration = 1400;
      var start = performance.now();
      var step = function (now) {
        var p = Math.min((now - start) / duration, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = (target * eased).toFixed(decimals) + suffix;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = target.toFixed(decimals) + suffix;
      };
      requestAnimationFrame(step);
    };

    if (!('IntersectionObserver' in window)) {
      els.forEach(animate);
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animate(entry.target);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });
    els.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------- Swipers */
  function initSwipers() {
    if (!window.Swiper) return;
    document.querySelectorAll('.swiper').forEach(function (el) {
      var type = el.dataset.swiper || 'default';
      var opts = {
        slidesPerView: 1,
        spaceBetween: 18,
        loop: el.querySelectorAll('.swiper-slide').length > 2,
        pagination: el.querySelector('.swiper-pagination') ? { el: el.querySelector('.swiper-pagination'), clickable: true } : undefined,
        navigation: {
          nextEl: el.querySelector('.swiper-button-next'),
          prevEl: el.querySelector('.swiper-button-prev')
        },
        breakpoints: {
          640: { slidesPerView: type === 'testimonials' ? 1 : 2 },
          1024: { slidesPerView: type === 'testimonials' ? 2 : 3 }
        }
      };
      if (type === 'testimonials') {
        opts.autoplay = { delay: 5200, disableOnInteraction: false };
        opts.loop = true;
      }
      if (type === 'screenshots') {
        opts.breakpoints = { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } };
      }
      new window.Swiper(el, opts);
    });
  }

  /* ------------------------------------------------------- Accordions */
  function initAccordions() {
    document.querySelectorAll('.accordion').forEach(function (acc) {
      if (acc.getAttribute('data-open') === 'true') {
        var initialPanel = acc.querySelector('.accordion-panel');
        if (initialPanel) {
          acc.classList.add('is-open');
          initialPanel.style.maxHeight = initialPanel.scrollHeight + 'px';
        }
      }
      acc.querySelectorAll('.acc-btn, .accordion-trigger').forEach(function (btn) {
        btn.addEventListener('click', function () {
          var item = btn.closest('.acc-item') || btn.closest('.accordion');
          var panel = item.querySelector('.acc-panel, .accordion-panel');
          var isOpen = item.classList.contains('is-open');
          var siblings = item.parentElement ? item.parentElement.querySelectorAll('.acc-item, .accordion') : [];
          siblings.forEach(function (other) {
            other.classList.remove('is-open');
            var p = other.querySelector('.acc-panel, .accordion-panel');
            if (p) p.style.maxHeight = null;
          });
          if (!isOpen && panel) {
            item.classList.add('is-open');
            panel.style.maxHeight = panel.scrollHeight + 'px';
          }
        });
      });
    });
  }

  /* -------------------------------------------------- Password toggles */
  function initPasswordToggles() {
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var target = document.getElementById(btn.dataset.togglePassword);
        if (!target) return;
        var showing = target.type === 'text';
        target.type = showing ? 'password' : 'text';
        var icon = btn.querySelector('i');
        if (icon) icon.className = showing ? 'fas fa-eye' : 'fas fa-eye-slash';
      });
    });
  }

  /* ----------------------------------------------------- Scroll bar */
  function initScrollProgress() {
    var bar = document.querySelector('.scroll-progress');
    if (!bar) return;
    var update = function () {
      var h = document.documentElement.scrollHeight - window.innerHeight;
      bar.style.width = (h > 0 ? (window.scrollY / h) * 100 : 0) + '%';
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
  }

  /* -------------------------------------------------------- Back top */
  function initBackTop() {
    var btn = document.querySelector('.back-top, .back-to-top');
    if (!btn) return;
    var update = function () {
      btn.classList.toggle('is-visible', window.scrollY > 500);
    };
    update();
    window.addEventListener('scroll', update, { passive: true });
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  /* ------------------------------------------------------ Active nav */
  function initActiveNav() {
    var path = window.location.pathname.split('/').pop() || 'index.php';
    document.querySelectorAll('.nav-link, .side-link, .drawer-link, .mobile-nav-link').forEach(function (link) {
      var href = link.getAttribute('href') || '';
      var file = href.split('#')[0].split('/').pop();
      if (file && file === path) link.classList.add('is-active');
    });
  }

  /* ------------------------------------------------------------ Tilt */
  function initTilt() {
    if (window.matchMedia('(hover: none)').matches) return;
    document.querySelectorAll('[data-tilt]').forEach(function (el) {
      var strength = parseFloat(el.dataset.tilt) || 6;
      el.addEventListener('mousemove', function (e) {
        var r = el.getBoundingClientRect();
        var x = (e.clientX - r.left) / r.width - 0.5;
        var y = (e.clientY - r.top) / r.height - 0.5;
        el.style.transform = 'perspective(900px) rotateX(' + (-y * strength) + 'deg) rotateY(' + (x * strength) + 'deg)';
      });
      el.addEventListener('mouseleave', function () {
        el.style.transform = '';
      });
    });
  }

  /* --------------------------------------------------- Smooth anchors */
  function initSmoothAnchors() {
    document.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var id = a.getAttribute('href');
        if (!id || id === '#') return;
        var target = document.querySelector(id);
        if (!target) return;
        e.preventDefault();
        var top = target.getBoundingClientRect().top + window.scrollY - 96;
        window.scrollTo({ top: top, behavior: 'smooth' });
        history.replaceState(null, '', id);
      });
    });
  }
})();
