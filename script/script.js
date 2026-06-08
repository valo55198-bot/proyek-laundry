/* ═══════════════════════════════════════════════════════
   RUMAH LAUNDRY — Interactive JavaScript
   All features: scroll, slider, tracking, animations
   ═══════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Element Cache ── */
    const navbar = document.getElementById('navbar');
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.navbar__link');
    const sections = document.querySelectorAll('section[id]');
    const trackInput = document.getElementById('trackInput');
    const trackBtn = document.getElementById('trackBtn');
    const trackLoading = document.getElementById('trackLoading');
    const trackResult = document.getElementById('trackResult');
    const trackNotFound = document.getElementById('trackNotFound');

    /* ══════════════════════════════
       1. NAVBAR — Scroll Effect
       ══════════════════════════════ */
    const handleNavScroll = () => {
        navbar.classList.toggle('scrolled', window.scrollY > 60);
    };
    window.addEventListener('scroll', handleNavScroll, { passive: true });
    handleNavScroll();

    /* ── Active Link Highlight ── */
    const highlightNav = () => {
        const scrollY = window.scrollY + 120;
        sections.forEach(section => {
            const top = section.offsetTop;
            const height = section.offsetHeight;
            const id = section.getAttribute('id');
            const link = document.querySelector(`.navbar__link[href="#${id}"]`);
            if (link) {
                if (scrollY >= top && scrollY < top + height) {
                    navLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');
                }
            }
        });
    };
    window.addEventListener('scroll', highlightNav, { passive: true });

    /* ── Smooth Scroll ── */
    function smoothScrollTo(el) {
        const navH = navbar.offsetHeight;
        const top = el.getBoundingClientRect().top + window.scrollY - navH - 20;
        window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    }

    navLinks.forEach(link => {
        link.addEventListener('click', e => {
            const href = link.getAttribute('href');
            if (href.startsWith('#')) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    smoothScrollTo(target);
                    navMenu.classList.remove('open');
                    navToggle.classList.remove('active');
                }
            }
        });
    });

    /* ── All other in-page anchor links (hero buttons, service cards, etc.) ── */
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        if (link.closest('.navbar__menu') || link.closest('.footer')) return;
        link.addEventListener('click', e => {
            const href = link.getAttribute('href');
            if (href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    smoothScrollTo(target);
                }
            }
        });
    });

    /* ── Mobile Menu Toggle ── */
    navToggle.addEventListener('click', () => {
        navMenu.classList.toggle('open');
        navToggle.classList.toggle('active');
    });

    // Close menu on outside click
    document.addEventListener('click', e => {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('open');
            navToggle.classList.remove('active');
        }
    });

    /* ══════════════════════════════
       2. SCROLL ANIMATIONS (Intersection Observer)
       ══════════════════════════════ */
    const animateElements = document.querySelectorAll('[data-animate]');
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -60px 0px'
    };

    const animObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const delay = parseInt(entry.target.dataset.delay || 0);
                setTimeout(() => {
                    entry.target.classList.add('animated');
                }, delay);
                animObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    animateElements.forEach(el => animObserver.observe(el));

    /* ══════════════════════════════
       3. PARALLAX — Hero Shapes (rAF optimized)
       ══════════════════════════════ */
    const shapes = document.querySelectorAll('.hero__shape');
    let rafParallax = null;

    function updateParallax() {
        const scrollY = window.scrollY;
        if (scrollY < window.innerHeight) {
            shapes.forEach((shape, i) => {
                const speed = [0.03, 0.05, 0.04][i] || 0.03;
                shape.style.transform = `translateY(${scrollY * speed}px)`;
            });
        }
        rafParallax = null;
    }

    window.addEventListener('scroll', () => {
        if (!rafParallax) {
            rafParallax = requestAnimationFrame(updateParallax);
        }
    }, { passive: true });

    /* ══════════════════════════════
       4. TESTIMONIAL SLIDER (Swiper.js)
       ══════════════════════════════ */
    const swiper = new Swiper('.reviews-v2__slider', {
        slidesPerView: 1,
        spaceBetween: 24,
        loop: true,
        grabCursor: true,
        speed: 800,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.reviews-v2__pagination',
            type: 'progressbar',
        },
        navigation: {
            nextEl: '.reviews-v2__btn--next',
            prevEl: '.reviews-v2__btn--prev',
        },
        breakpoints: {
            640: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1100: {
                slidesPerView: 2,
                spaceBetween: 30,
            },
            1400: {
                slidesPerView: 2.5,
                spaceBetween: 40,
            }
        }
    });

    // Pause on hover
    const swiperContainer = document.querySelector('.reviews-v2__slider');
    if (swiperContainer) {
        swiperContainer.addEventListener('mouseenter', () => swiper.autoplay.stop());
        swiperContainer.addEventListener('mouseleave', () => swiper.autoplay.start());
    }

    /* ══════════════════════════════
       5. ORDER TRACKING — Simulation
       ══════════════════════════════ */

    // Simulated order database
    const orderDatabase = {
        'RL-1234': {
            date: '10 Februari 2026',
            status: 'Diproses',
            statusClass: 'diproses',
            completedSteps: 2
        },
        'RL-5678': {
            date: '9 Februari 2026',
            status: 'Selesai',
            statusClass: 'selesai',
            completedSteps: 3
        },
        'RL-9012': {
            date: '11 Februari 2026',
            status: 'Order Diterima',
            statusClass: 'waiting',
            completedSteps: 1
        }
    };

    function resetTrackingUI() {
        trackLoading.classList.remove('show');
        trackResult.classList.remove('show');
        trackNotFound.classList.remove('show');
    }

    function showTracking(orderCode) {
        const code = orderCode.trim().toUpperCase();
        if (!code) return;

        resetTrackingUI();
        trackLoading.classList.add('show');

        // Fetch real data from backend
        fetch(`dashboard/cek_status.php?kode_order=${encodeURIComponent(code)}`)
            .then(response => response.json())
            .then(data => {
                trackLoading.classList.remove('show');

                if (data.found) {
                    // Update header
                    document.getElementById('trackOrderId').textContent = `Order #${data.kode}`;
                    document.getElementById('trackOrderDate').textContent = data.tanggal;

                    const badge = document.getElementById('trackStatusBadge');
                    badge.textContent = data.status;
                    badge.className = 'tracking__status-badge ' + (data.class || '');

                    // Update timeline steps (1-3)
                    const steps = document.querySelectorAll('.tracking__step');
                    const currentStep = data.step; // 1, 2, or 3

                    steps.forEach((step, i) => {
                        const stepNum = i + 1;
                        step.classList.remove('completed', 'active');

                        if (stepNum < currentStep) {
                            step.classList.add('completed');
                        } else if (stepNum === currentStep) {
                            if (currentStep === 3) {
                                step.classList.add('completed');
                            } else {
                                step.classList.add('active');
                            }
                        }
                    });

                    trackResult.classList.add('show');
                } else {
                    // Jika ada pesan khusus (misal: "Pesanan telah dibatalkan")
                    const notFoundTitle = trackNotFound.querySelector('h3');
                    const notFoundText = trackNotFound.querySelector('p');
                    
                    if (data.message) {
                        notFoundTitle.textContent = 'Status Pesanan';
                        notFoundText.textContent = data.message;
                    } else {
                        notFoundTitle.textContent = 'Order Tidak Ditemukan';
                        notFoundText.textContent = 'Pastikan kode order yang Anda masukkan benar. Coba lagi atau hubungi kami.';
                    }
                    
                    trackNotFound.classList.add('show');
                }
            })
            .catch(err => {
                console.error('Tracking Error:', err);
                trackLoading.classList.remove('show');
                
                const notFoundTitle = trackNotFound.querySelector('h3');
                const notFoundText = trackNotFound.querySelector('p');
                notFoundTitle.textContent = 'Koneksi Bermasalah';
                notFoundText.textContent = 'Gagal menghubungi server. Pastikan koneksi internet Anda aktif dan coba lagi.';
                
                trackNotFound.classList.add('show');
            });
    }

    trackBtn.addEventListener('click', () => showTracking(trackInput.value));
    trackInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') showTracking(trackInput.value);
    });

    /* ══════════════════════════════
       6. FOOTER LINKS — Smooth Scroll
       ══════════════════════════════ */
    document.querySelectorAll('.footer a[href^="#"]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            if (target) smoothScrollTo(target);
        });
    });

    /* ══════════════════════════════
       7. HERO — Staggered Initial Animation
       ══════════════════════════════ */
    const heroContent = document.querySelector('.hero__content[data-animate]');
    const heroVisual = document.querySelector('.hero__visual[data-animate]');

    // Stagger hero children for a professional cascade
    if (heroContent) {
        setTimeout(() => heroContent.classList.add('animated'), 150);
        const children = heroContent.children;
        for (let i = 0; i < children.length; i++) {
            children[i].style.opacity = '0';
            children[i].style.transform = 'translateY(16px)';
            children[i].style.transition = `opacity .5s cubic-bezier(.22,1,.36,1) ${150 + i * 100}ms, transform .5s cubic-bezier(.22,1,.36,1) ${150 + i * 100}ms`;
            setTimeout(() => {
                children[i].style.opacity = '1';
                children[i].style.transform = 'translateY(0)';
            }, 200 + i * 100);
        }
    }
    if (heroVisual) {
        setTimeout(() => heroVisual.classList.add('animated'), 400);
    }

    /* ══════════════════════════════════════════════════════
       9. FORM SUBMISSION ALERT (from backend redirect)
       ══════════════════════════════════════════════════════ */
    const urlParams = new URLSearchParams(window.location.search);
    const formAlert = document.getElementById('formAlert');
    if (urlParams.has('status') && formAlert) {
        const status = urlParams.get('status');
        if (status === 'success') {
            formAlert.style.display = 'flex';
            formAlert.style.alignItems = 'center';
            formAlert.style.gap = '10px';
            formAlert.style.background = '#D1FAE5';
            formAlert.style.color = '#059669';
            formAlert.style.border = '1px solid #A7F3D0';
            formAlert.innerHTML = '<i class="fas fa-check-circle"></i> Pesanan Anda berhasil dikirim! Kami akan segera menghubungi Anda.';
            formAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else if (status === 'error') {
            formAlert.style.display = 'flex';
            formAlert.style.alignItems = 'center';
            formAlert.style.gap = '10px';
            formAlert.style.background = '#FEE2E2';
            formAlert.style.color = '#DC2626';
            formAlert.style.border = '1px solid #FECACA';
            formAlert.innerHTML = '<i class="fas fa-exclamation-circle"></i> Gagal mengirim pesanan. Silakan coba lagi.';
            formAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        // Clean URL
        window.history.replaceState({}, '', window.location.pathname);
    }

    /* ══════════════════════════════
       7. LOGIN MODAL
       ══════════════════════════════ */
    const loginOverlay = document.getElementById('loginOverlay');
    const openLoginBtn = document.getElementById('openLoginModal');
    const closeLoginBtn = document.getElementById('closeLoginModal');
    const loginForm = document.getElementById('loginForm');

    if (loginOverlay && openLoginBtn) {
        // Buka modal
        openLoginBtn.addEventListener('click', () => {
            loginOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                const emailInput = document.getElementById('loginEmail');
                if (emailInput) emailInput.focus();
            }, 400);
        });

        // Tutup modal — tombol close
        if (closeLoginBtn) {
            closeLoginBtn.addEventListener('click', () => {
                loginOverlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Tutup modal — klik overlay
        loginOverlay.addEventListener('click', (e) => {
            if (e.target === loginOverlay) {
                loginOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });

        // Tutup modal — ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && loginOverlay.classList.contains('active')) {
                loginOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    /* ══════════════════════════════
       8. SWIPE TO SUBMIT BUTTON
       ══════════════════════════════ */
    const swipeContainer = document.getElementById('swipeSubmitContainer');
    const swipeThumb = document.getElementById('swipeSubmitThumb');
    const swipeText = document.getElementById('swipeSubmitText');
    const swipeIcon = document.getElementById('swipeSubmitIcon');
    const hiddenSubmitBtn = document.getElementById('hiddenSubmitBtn');
    
    if (swipeContainer && swipeThumb && hiddenSubmitBtn) {
        let isDragging = false;
        let startX = 0;
        let maxDrag = 0;
        const form = hiddenSubmitBtn.closest('form');

        const initDrag = (clientX) => {
            if (swipeContainer.classList.contains('success')) return;
            
            // Check HTML5 validity before allowing swipe
            if (form && !form.checkValidity()) {
                form.reportValidity();
                return;
            }

            isDragging = true;
            startX = clientX - (parseInt(swipeThumb.style.left) || 6);
            maxDrag = swipeContainer.offsetWidth - swipeThumb.offsetWidth - 6;
            
            swipeThumb.style.transition = 'none';
        };

        const onDrag = (clientX) => {
            if (!isDragging) return;
            let currentX = clientX - startX;
            
            if (currentX < 6) currentX = 6;
            if (currentX > maxDrag) currentX = maxDrag;
            
            swipeThumb.style.left = currentX + 'px';
            
            // Fade text out as we drag
            const opacity = 1 - (currentX / maxDrag);
            swipeText.style.opacity = Math.max(0, opacity);
        };

        const stopDrag = () => {
            if (!isDragging) return;
            isDragging = false;
            
            const currentX = parseInt(swipeThumb.style.left) || 6;
            swipeThumb.style.transition = 'left 0.3s ease, background-color 0.3s';
            
            // If dragged past 80%, trigger success
            if (currentX >= maxDrag * 0.8) {
                swipeThumb.style.left = maxDrag + 'px';
                swipeContainer.classList.add('success');
                swipeText.textContent = 'Terkirim';
                swipeText.style.opacity = 1;
                swipeIcon.className = 'fas fa-check';
                
                // Submit form after a short delay
                setTimeout(() => {
                    hiddenSubmitBtn.click();
                }, 300);
            } else {
                // Snap back
                swipeThumb.style.left = '6px';
                swipeText.style.opacity = 1;
            }
        };

        // Mouse events
        swipeThumb.addEventListener('mousedown', (e) => {
            e.preventDefault();
            initDrag(e.clientX);
        });
        document.addEventListener('mousemove', (e) => {
            if (isDragging) onDrag(e.clientX);
        });
        document.addEventListener('mouseup', stopDrag);

        // Touch events
        swipeThumb.addEventListener('touchstart', (e) => {
            initDrag(e.touches[0].clientX);
        }, { passive: true });
        document.addEventListener('touchmove', (e) => {
            if (isDragging) {
                // e.preventDefault(); // careful with passive listeners
                onDrag(e.touches[0].clientX);
            }
        }, { passive: false });
        document.addEventListener('touchend', stopDrag);
    }

    /* ══════════════════════════════
       8b. SWIPE TO WHATSAPP BUTTON
       ══════════════════════════════ */
    const swipeWaContainer = document.getElementById('swipeWaContainer');
    const swipeWaThumb = document.getElementById('swipeWaThumb');
    const swipeWaText = document.getElementById('swipeWaText');
    const swipeWaIcon = document.getElementById('swipeWaIcon');
    
    if (swipeWaContainer && swipeWaThumb) {
        let isDraggingWa = false;
        let startXWa = 0;
        let maxDragWa = 0;
        const waUrl = "https://wa.me/6285604086257";

        const initDragWa = (clientX) => {
            if (swipeWaContainer.classList.contains('success')) return;

            isDraggingWa = true;
            startXWa = clientX - (parseInt(swipeWaThumb.style.left) || 6);
            maxDragWa = swipeWaContainer.offsetWidth - swipeWaThumb.offsetWidth - 6;
            
            swipeWaThumb.style.transition = 'none';
        };

        const onDragWa = (clientX) => {
            if (!isDraggingWa) return;
            let currentX = clientX - startXWa;
            
            if (currentX < 6) currentX = 6;
            if (currentX > maxDragWa) currentX = maxDragWa;
            
            swipeWaThumb.style.left = currentX + 'px';
            
            // Fade text out as we drag
            const opacity = 1 - (currentX / maxDragWa);
            swipeWaText.style.opacity = Math.max(0, opacity);
        };

        const stopDragWa = () => {
            if (!isDraggingWa) return;
            isDraggingWa = false;
            
            const currentX = parseInt(swipeWaThumb.style.left) || 6;
            swipeWaThumb.style.transition = 'left 0.3s ease, background-color 0.3s';
            
            // If dragged past 80%, trigger success
            if (currentX >= maxDragWa * 0.8) {
                swipeWaThumb.style.left = maxDragWa + 'px';
                swipeWaContainer.classList.add('success');
                swipeWaText.textContent = 'Membuka WhatsApp...';
                swipeWaText.style.opacity = 1;
                swipeWaIcon.className = 'fas fa-check';
                
                // Open WhatsApp link in a new tab
                window.open(waUrl, '_blank');
                
                // Reset after a delay so they can click/swipe it again if needed
                setTimeout(() => {
                    swipeWaContainer.classList.remove('success');
                    swipeWaThumb.style.left = '6px';
                    swipeWaText.textContent = 'Geser untuk Chat WhatsApp';
                    swipeWaIcon.className = 'fab fa-whatsapp';
                    swipeWaText.style.opacity = 1;
                }, 2000);
            } else {
                // Snap back
                swipeWaThumb.style.left = '6px';
                swipeWaText.style.opacity = 1;
            }
        };

        // Mouse events
        swipeWaThumb.addEventListener('mousedown', (e) => {
            e.preventDefault();
            initDragWa(e.clientX);
        });
        document.addEventListener('mousemove', (e) => {
            if (isDraggingWa) onDragWa(e.clientX);
        });
        document.addEventListener('mouseup', stopDragWa);

        // Touch events
        swipeWaThumb.addEventListener('touchstart', (e) => {
            initDragWa(e.touches[0].clientX);
        }, { passive: true });
        document.addEventListener('touchmove', (e) => {
            if (isDraggingWa) {
                onDragWa(e.touches[0].clientX);
            }
        }, { passive: false });
        document.addEventListener('touchend', stopDragWa);
    }

});
