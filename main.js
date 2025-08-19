// /js/main.js
document.addEventListener('DOMContentLoaded', function () {
    // ===== ANIMATION DES STATISTIQUES =====
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length > 0) {
        const animateStats = () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = parseInt(entry.target.getAttribute('data-target'));
                        let current = 0;
                        const increment = target / 125; // ~2s
                        const counter = setInterval(() => {
                            current += increment;
                            if (current >= target) {
                                entry.target.textContent = target.toLocaleString();
                                clearInterval(counter);
                            } else {
                                entry.target.textContent = Math.floor(current).toLocaleString();
                            }
                        }, 16);
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            statNumbers.forEach(stat => observer.observe(stat));
        };
        animateStats();
    }

    // ===== SMOOTH SCROLL =====
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const headerHeight = document.querySelector('.header')?.offsetHeight || 80;
                const targetPosition = target.offsetTop - headerHeight - 20;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }
        });
    });

    // ===== PARALLAXE =====
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const parallaxElements = document.querySelectorAll('.tree-illustration');
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            parallaxElements.forEach(el => {
                el.style.transform = `translateY(${-(scrolled * 0.5)}px)`;
            });
        });
    }

    // ===== ANIMATION D'APPARITION =====
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const animatedElements = document.querySelectorAll('.feature-card, .about-text, .about-image');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(el);
        });
    }

    // ===== MENU MOBILE =====
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener('click', function () {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            navLinks.classList.toggle('active');
            this.classList.toggle('active');
            this.setAttribute('aria-expanded', !isExpanded);
            document.body.classList.toggle('menu-open', !isExpanded);
        });
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                navLinks.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');
            }
        });
    }

    console.log('🌱 Yggdrasil - Accueil chargé');
});