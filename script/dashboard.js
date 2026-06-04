/* ═══════════════════════════════════════════════════════
   RUMAH LAUNDRY — Admin Dashboard JavaScript
   HANYA UNTUK UI: Sidebar, Animations, Scroll Effects
   TIDAK ADA CRUD, LOGIN, LOGOUT, DATABASE
   ═══════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    /* ══════════════════════════════
       ELEMENTS
       ══════════════════════════════ */
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const overlay = document.getElementById('overlay');
    const topbar = document.querySelector('.topbar');
    const profileToggle = document.getElementById('profileToggle');
    const content = document.querySelector('.content');

    const isMobile = () => window.innerWidth <= 768;

    /* ══════════════════════════════
       1. SIDEBAR TOGGLE
       ══════════════════════════════ */
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }

    /* ══════════════════════════════
       2. TOPBAR — Scroll Shadow
       ══════════════════════════════ */
    if (topbar) {
        window.addEventListener('scroll', () => {
            topbar.classList.toggle('scrolled', window.scrollY > 10);
        }, { passive: true });
    }

    /* ══════════════════════════════
       3. PROFILE DROPDOWN
       ══════════════════════════════ */
    if (profileToggle) {
        profileToggle.addEventListener('click', e => {
            e.stopPropagation();
            profileToggle.classList.toggle('open');
        });
        document.addEventListener('click', () => {
            profileToggle.classList.remove('open');
        });
    }

    /* ══════════════════════════════
       4. SCROLL ANIMATIONS
       ══════════════════════════════ */
    const animEls = document.querySelectorAll('[data-animate]');

    function triggerAnimations() {
        animEls.forEach((el, i) => {
            const delay = parseInt(el.dataset.delay || 0) + (i * 80);
            setTimeout(() => {
                el.classList.add('animated');
            }, delay);
        });
    }

    // Fire on load
    setTimeout(triggerAnimations, 100);

    /* ══════════════════════════════
       5. BUTTON RIPPLE EFFECT
       ══════════════════════════════ */
    document.addEventListener('click', e => {
        const btn = e.target.closest('.btn');
        if (!btn) return;

        const ripple = document.createElement('span');
        ripple.className = 'ripple';
        ripple.style.left = (e.clientX - btn.getBoundingClientRect().left) + 'px';
        ripple.style.top = (e.clientY - btn.getBoundingClientRect().top) + 'px';

        btn.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove());
    });

    /* ══════════════════════════════
       6. KEYBOARD SHORTCUTS
       ══════════════════════════════ */
    document.addEventListener('keydown', e => {
        // Escape close dropdown
        if (e.key === 'Escape') {
            if (profileToggle) profileToggle.classList.remove('open');
        }
    });

});
