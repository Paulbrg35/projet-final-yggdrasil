/* ===== YGGDRASIL - SCRIPT PRINCIPAL ===== */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== VARIABLES GLOBALES =====
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const statNumbers = document.querySelectorAll('.stat-number');
    const heroTitle = document.querySelector('.hero-title');
    const toggleDarkMode = () => {
    document.body.classList.toggle('dark-mode');

    
    // ===== GESTION DU MODE SOMBRE / CLAIR =====
const themeToggle = document.getElementById('theme-toggle');

// Vérifie la préférence utilisateur au chargement
if (themeToggle) {
    const savedTheme = localStorage.getItem('theme');

    // Applique le thème sauvegardé
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggle.textContent = '☀️ Passer en mode clair';
    } else {
        themeToggle.textContent = '🌙 Passer en mode sombre';
    }

    // Ajoute l'écouteur de clic
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            themeToggle.textContent = '☀️ Passer en mode clair';
            localStorage.setItem('theme', 'dark');
        } else {
            themeToggle.textContent = '🌙 Passer en mode sombre';
            localStorage.setItem('theme', 'light');
        }

        // Optionnel : annonce pour les lecteurs d'écran
        const message = document.body.classList.contains('dark-mode')
            ? 'Mode sombre activé'
            : 'Mode clair activé';
        announceToScreenReader(message);
    });
}
};

// Bouton pour basculer le mode
document.getElementById('dark-mode-toggle').addEventListener('click', toggleDarkMode);
    
    // ===== MENU MOBILE =====
    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Toggle menu visibility
            navLinks.classList.toggle('active');
            this.classList.toggle('active');
            
            // Update aria-expanded for accessibility
            this.setAttribute('aria-expanded', !isExpanded);
            
            // Prevent body scroll when menu is open
            document.body.classList.toggle('menu-open', !isExpanded);
        });

        // Close menu when clicking on a link (mobile)
        const navItems = navLinks.querySelectorAll('a');
        navItems.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth <= 768) {
                    navLinks.classList.remove('active');
                    mobileMenuToggle.classList.remove('active');
                    mobileMenuToggle.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('menu-open');
                }
            });
        });

        // Close menu on window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                navLinks.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');
            }
        });
    }

    // ===== ANIMATION DES STATISTIQUES =====
    function animateStats() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    const duration = 2000; // 2 secondes
                    const increment = target / (duration / 16); // 60fps
                    let current = 0;

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

        statNumbers.forEach(stat => {
            observer.observe(stat);
        });
    }

    // Initialiser l'animation des stats si les éléments existent
    if (statNumbers.length > 0) {
        animateStats();
    }

    // ===== SMOOTH SCROLL POUR LES ANCRES =====
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = target.offsetTop - headerHeight - 20;
                
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===== EFFET PARALLAXE LÉGER =====
    function parallaxEffect() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.tree-illustration');
        
        parallaxElements.forEach(element => {
            const speed = 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    }

    // Appliquer l'effet parallaxe seulement si l'utilisateur n'a pas de préférence pour les animations réduites
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        window.addEventListener('scroll', throttle(parallaxEffect, 16));
    }

    // ===== LAZY LOADING DES IMAGES =====
    function lazyLoadImages() {
        const images = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    lazyLoadImages();

    // ===== ANIMATION D'APPARITION DES ÉLÉMENTS =====
    function animateOnScroll() {
        const animatedElements = document.querySelectorAll('.feature-card, .about-text, .about-image');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        animatedElements.forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            el.style.transition = 'opacity 0.8s ease, transform 0.8s ease';
            observer.observe(el);
        });
    }

    // Initialiser les animations seulement si les animations ne sont pas réduites
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        animateOnScroll();
    }

    // ===== GESTION DU FOCUS POUR L'ACCESSIBILITÉ =====
    function handleFocusVisibility() {
        let hadKeyboardEvent = true;
        
        const keyboardThrottledFunc = throttle(function() {
            hadKeyboardEvent = true;
        }, 100);

        const mouseThrottledFunc = throttle(function() {
            hadKeyboardEvent = false;
        }, 100);

        function onPointerDown() {
            mouseThrottledFunc();
        }

        function onKeyDown(e) {
            if (e.metaKey || e.altKey || e.ctrlKey) {
                return;
            }
            keyboardThrottledFunc();
        }

        function onFocus(e) {
            if (hadKeyboardEvent || e.target.matches(':focus-visible')) {
                e.target.classList.add('focus-visible');
            }
        }

        function onBlur(e) {
            e.target.classList.remove('focus-visible');
        }

        document.addEventListener('keydown', onKeyDown, true);
        document.addEventListener('mousedown', onPointerDown, true);
        document.addEventListener('focus', onFocus, true);
        document.addEventListener('blur', onBlur, true);
    }

    handleFocusVisibility();

    // ===== DÉTECTION DE LA VITESSE DE CONNEXION =====
    function adaptToConnectionSpeed() {
        if ('connection' in navigator) {
            const connection = navigator.connection;
            
            // Si la connexion est lente, désactiver les animations coûteuses
            if (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g') {
                document.body.classList.add('slow-connection');
                
                // Désactiver les animations CSS
                const style = document.createElement('style');
                style.textContent = `
                    *, *::before, *::after {
                        animation-duration: 0ms !important;
                        transition-duration: 0ms !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }
    }

    adaptToConnectionSpeed();

    // ===== GESTION DES ERREURS D'IMAGES =====
    function handleImageErrors() {
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                // Optionnel : afficher un placeholder
                const placeholder = document.createElement('div');
                placeholder.className = 'image-placeholder';
                placeholder.textContent = 'Image non disponible';
                this.parentNode.insertBefore(placeholder, this.nextSibling);
            });
        });
    }

    handleImageErrors();

    // ===== VALIDATION CÔTÉ CLIENT (si formulaires présents) =====
    function setupFormValidation() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const inputs = form.querySelectorAll('input[required], textarea[required]');
                let isValid = true;

                inputs.forEach(input => {
                    const errorMsg = input.parentNode.querySelector('.error-message');
                    
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('error');
                        
                        if (!errorMsg) {
                            const error = document.createElement('span');
                            error.className = 'error-message';
                            error.textContent = 'Ce champ est requis';
                            error.setAttribute('aria-live', 'polite');
                            input.parentNode.appendChild(error);
                        }
                    } else {
                        input.classList.remove('error');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Faire défiler vers le premier champ en erreur
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });

            // Validation en temps réel
            const inputs = form.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('error');
                    } else {
                        this.classList.remove('error');
                        const errorMsg = this.parentNode.querySelector('.error-message');
                        if (errorMsg) {
                            errorMsg.remove();
                        }
                    }
                });
            });
        });
    }

    setupFormValidation();

    // ===== AMÉLIORATION DE LA PERFORMANCE =====
    
    // Fonction de throttle pour limiter les appels de fonction
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Fonction de debounce pour retarder les appels de fonction
    function debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    // ===== ACCESSIBILITÉ AVANCÉE =====
    
    // Annonce des changements dynamiques pour les lecteurs d'écran
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;
        
        document.body.appendChild(announcement);
        
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    // Gestion des raccourcis clavier
    document.addEventListener('keydown', function(e) {
        // Alt + 1 : Aller au contenu principal
        if (e.altKey && e.key === '1') {
            e.preventDefault();
            const main = document.querySelector('main');
            if (main) {
                main.focus();
                main.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        // Alt + 2 : Aller à la navigation
        if (e.altKey && e.key === '2') {
            e.preventDefault();
            const nav = document.querySelector('.navbar');
            if (nav) {
                nav.focus();
                nav.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Échap : Fermer le menu mobile
        if (e.key === 'Escape' && navLinks && navLinks.classList.contains('active')) {
            navLinks.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            mobileMenuToggle.focus();
        }
    });

    // ===== ANALYTICS ET PERFORMANCE (Placeholder) =====
    
    // Mesure des performances de chargement
    function measurePerformance() {
        if ('performance' in window) {
            window.addEventListener('load', function() {
                setTimeout(function() {
                    const perfData = performance.timing;
                    const loadTime = perfData.loadEventEnd - perfData.navigationStart;
                    
                    console.log('Temps de chargement complet:', loadTime + 'ms');
                    
                    // Ici, vous pourriez envoyer ces données à votre service d'analytics
                    // analytics.track('page_load_time', loadTime);
                }, 100);
            });
        }
    }

    measurePerformance();

    // ===== EASTER EGG ACCESSIBLE =====
    let konamiCode = [];
    const konamiSequence = ['ArrowUp', 'ArrowUp', 'ArrowDown', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowLeft', 'ArrowRight', 'KeyB', 'KeyA'];
    
    document.addEventListener('keydown', function(e) {
        konamiCode.push(e.code);
        
        if (konamiCode.length > konamiSequence.length) {
            konamiCode.shift();
        }
        
        if (konamiCode.join('') === konamiSequence.join('')) {
            announceToScreenReader('Code secret activé ! L\'arbre Yggdrasil vous salue !');
            
            // Petit effet visuel accessible
            document.body.style.transition = 'background-color 0.5s ease';
            document.body.style.backgroundColor = '#D4AF37';
            
            setTimeout(() => {
                document.body.style.backgroundColor = '';
            }, 1000);
            
            konamiCode = [];
        }
    });

    // ===== INITIALISATION FINALE =====
    console.log('🌳 Yggdrasil chargé avec succès !');
    console.log('💡 Raccourcis accessibles : Alt+1 (contenu), Alt+2 (navigation)');
    
    // Signaler que le script est prêt
    document.body.classList.add('js-ready');
    
    // Event personnalisé pour indiquer que l'initialisation est terminée
    const readyEvent = new CustomEvent('yggdrasilReady', {
        detail: { timestamp: Date.now() }
    });
    document.dispatchEvent(readyEvent);
});

// ===== FONCTIONS UTILITAIRES GLOBALES =====

// Fonction pour gérer les cookies (RGPD)
window.YggdrasilCookies = {
    set: function(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Strict`;
    },
    
    get: function(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    },
    
    delete: function(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/`;
    }
};

// Fonction pour vérifier la prise en charge des fonctionnalités
window.YggdrasilFeatures = {
    supportsIntersectionObserver: 'IntersectionObserver' in window,
    supportsLazyLoading: 'loading' in HTMLImageElement.prototype,
    supportsCustomEvent: typeof window.CustomEvent === 'function',
    supportsES6: (() => {
        try {
            new Function("(a = 0) => a");
            return true;
        } catch (e) {
            return false;
        }
    })()
};

// ===== UTILITAIRE POUR LE RGPD (Affichage bannière) =====
function showCookieBanner() {
    if (!window.YggdrasilCookies.get('yggdrasilConsent')) {
        const banner = document.createElement('div');
        banner.className = 'cookie-banner';
        banner.innerHTML = `
            <span>
                Ce site utilise des cookies pour améliorer votre expérience. 
                <a href="/mentions-legales.html" target="_blank" rel="noopener">En savoir plus</a>
            </span>
            <button class="btn btn-primary accept-cookies">Accepter</button>
        `;
        document.body.appendChild(banner);

        banner.querySelector('.accept-cookies').addEventListener('click', function() {
            window.YggdrasilCookies.set('yggdrasilConsent', 'true', 365);
            banner.remove();
        });
    }
}

document.addEventListener('yggdrasilReady', showCookieBanner);

// ===== FIN