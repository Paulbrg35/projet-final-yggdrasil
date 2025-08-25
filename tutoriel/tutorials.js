// === TRADUCTIONS FR / EN / BR ===
const translations = {
    fr: {
        title: "Guide d'utilisation",
        subtitle: "Apprenez à créer et gérer votre arbre généalogique",
        welcome: { 
            title: "Bienvenue sur votre plateforme généalogique !", 
            text: "Ce guide vous accompagnera pas à pas pour découvrir toutes les fonctionnalités et créer votre premier arbre généalogique." 
        },
        access: { 
            dyslexia: "Dyslexie", 
            normal: "Normal", 
            dark: "Sombre", 
            light: "Clair" 
        },
        nav: { 
            prev: "Précédent", 
            next: "Suivant", 
            home: "Accueil" 
        }
    },
    en: {
        title: "User Guide",
        subtitle: "Learn how to create and manage your family tree",
        welcome: { 
            title: "Welcome to your genealogy platform!", 
            text: "This guide will walk you through all the features and help you create your first family tree." 
        },
        access: { 
            dyslexia: "Dyslexia", 
            normal: "Normal", 
            dark: "Dark", 
            light: "Light" 
        },
        nav: { 
            prev: "Previous", 
            next: "Next", 
            home: "Home" 
        }
    },
    br: {
        title: "Kemener ar Gontrol",
        subtitle: "Deskiñ penaos krouiñ ha merañ ho kerzh genealojel",
        welcome: { 
            title: "Degemer mat war ho lec'hienn genealojel !", 
            text: "Ar c'hemener-mañ a'z aiskiñ dre ar pep fonctionnalite ha sikour ac'hanoc'h da grouiñ ho kerzh genealojel kentañ." 
        },
        access: { 
            dyslexia: "Dysleksie", 
            normal: "Leavel", 
            dark: "Du", 
            light: "Gwer" 
        },
        nav: { 
            prev: "Kent", 
            next: "Da-heul", 
            home: "Degemer" 
        }
    }
};

// === VARIABLES GLOBALES ===
let currentLang = 'fr';
let currentStep = 0;
const totalSteps = 5;
const languages = ['fr', 'en', 'br'];
const langNames = { fr: 'FR', en: 'EN', br: 'BR' };

// === FONCTIONS DE TRADUCTION ===
/**
 * Traduit tous les éléments avec l'attribut data-i18n
 * @param {string} lang - Code de langue
 */
function translatePage(lang) {
    const elements = document.querySelectorAll('[data-i18n]');
    elements.forEach(el => {
        const keys = el.getAttribute('data-i18n').split('.');
        let text = translations[lang];
        
        keys.forEach(k => {
            if (text && text[k] !== undefined) {
                text = text[k];
            }
        });
        
        if (text && typeof text === 'string') {
            el.textContent = text;
        }
    });
    
    // Mettre à jour l'indicateur de langue
    const langText = document.getElementById('lang-text');
    if (langText) {
        langText.textContent = languages.map(l => langNames[l]).join(' → ');
    }
}

// === GESTION DES ÉTAPES ===
/**
 * Affiche une étape spécifique du tutoriel
 * @param {number} step - Numéro de l'étape (0 = accueil)
 */
function showStep(step) {
    // Masquer tous les éléments
    const welcome = document.getElementById('welcome');
    if (welcome) welcome.classList.add('hidden');
    
    for (let i = 1; i <= totalSteps; i++) {
        const stepElement = document.getElementById(`step${i}`);
        if (stepElement) stepElement.classList.add('hidden');
    }

    // Afficher l'élément approprié
    if (step === 0) {
        if (welcome) welcome.classList.remove('hidden');
    } else if (step >= 1 && step <= totalSteps) {
        const stepElement = document.getElementById(`step${step}`);
        if (stepElement) stepElement.classList.remove('hidden');
    }

    // Mettre à jour les variables et UI
    currentStep = step;
    updateNavigationButtons();
    updateProgressBar();
}

/**
 * Met à jour l'état des boutons de navigation
 */
function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    if (prevBtn) {
        prevBtn.disabled = currentStep === 0;
    }
    
    if (nextBtn) {
        nextBtn.disabled = currentStep === totalSteps;
        
        // Changer le texte du bouton selon l'étape
        const nextSpan = nextBtn.querySelector('span[data-i18n="nav.next"]');
        if (currentStep === totalSteps) {
            nextBtn.innerHTML = '🎉 Terminé !';
        } else {
            nextBtn.innerHTML = `<span data-i18n="nav.next">${translations[currentLang].nav.next}</span> ➡️`;
        }
    }
}

/**
 * Met à jour la barre de progression
 */
function updateProgressBar() {
    const progressBar = document.getElementById('progressBar');
    if (progressBar) {
        const progress = (currentStep / totalSteps) * 100;
        progressBar.style.width = `${progress}%`;
    }
}

/**
 * Va à une étape spécifique
 * @param {number} step - Numéro de l'étape
 */
function goToStep(step) {
    if (step >= 0 && step <= totalSteps) {
        showStep(step);
    }
}

/**
 * Va à l'étape suivante
 */
function nextStep() {
    if (currentStep < totalSteps) {
        showStep(currentStep + 1);
    }
}

/**
 * Va à l'étape précédente
 */
function previousStep() {
    if (currentStep > 0) {
        showStep(currentStep - 1);
    }
}

/**
 * Retourne à l'accueil
 */
function goToWelcome() {
    showStep(0);
}

// === GESTION DES MODES D'ACCESSIBILITÉ ===
/**
 * Initialise les contrôles d'accessibilité
 */
function initAccessibilityControls() {
    // Mode dyslexie
    const dyslexiaToggle = document.getElementById('dyslexia-toggle');
    if (dyslexiaToggle) {
        dyslexiaToggle.addEventListener('click', toggleDyslexiaMode);
    }

    // Mode sombre
    const themeToggle = document.getElementById('theme-toggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleThemeMode);
    }

    // Changement de langue
    const langToggle = document.getElementById('lang-toggle');
    if (langToggle) {
        langToggle.addEventListener('click', toggleLanguage);
    }
}

/**
 * Bascule le mode dyslexie
 */
function toggleDyslexiaMode() {
    const body = document.body;
    const isActive = body.classList.toggle('dyslexic-mode');
    const button = document.getElementById('dyslexia-toggle');
    const span = button?.querySelector('span');

    if (isActive) {
        button?.classList.add('active');
        if (span) {
            span.setAttribute('data-i18n', 'access.normal');
            span.textContent = translations[currentLang].access.normal;
        }
    } else {
        button?.classList.remove('active');
        if (span) {
            span.setAttribute('data-i18n', 'access.dyslexia');
            span.textContent = translations[currentLang].access.dyslexia;
        }
    }
    
    // Sauvegarder la préférence
    savePreference('dyslexicMode', isActive);
}

/**
 * Bascule le mode sombre
 */
function toggleThemeMode() {
    const body = document.body;
    const isActive = body.classList.toggle('dark-mode');
    const button = document.getElementById('theme-toggle');
    const span = button?.querySelector('span');

    if (isActive) {
        button?.classList.add('active');
        if (span) {
            span.setAttribute('data-i18n', 'access.light');
            span.textContent = translations[currentLang].access.light;
        }
    } else {
        button?.classList.remove('active');
        if (span) {
            span.setAttribute('data-i18n', 'access.dark');
            span.textContent = translations[currentLang].access.dark;
        }
    }
    
    // Sauvegarder la préférence
    savePreference('darkMode', isActive);
}

/**
 * Bascule la langue
 */
function toggleLanguage() {
    const currentIndex = languages.indexOf(currentLang);
    currentLang = languages[(currentIndex + 1) % languages.length];
    
    translatePage(currentLang);
    updateNavigationButtons();
    updateAccessibilityLabels();
    
    // Sauvegarder la préférence
    savePreference('language', currentLang);
}

/**
 * Met à jour les labels d'accessibilité selon la langue
 */
function updateAccessibilityLabels() {
    const dyslexiaBtn = document.getElementById('dyslexia-toggle');
    const themeBtn = document.getElementById('theme-toggle');
    
    if (dyslexiaBtn) {
        const span = dyslexiaBtn.querySelector('span');
        const isActive = dyslexiaBtn.classList.contains('active');
        if (span) {
            span.textContent = isActive ? 
                translations[currentLang].access.normal : 
                translations[currentLang].access.dyslexia;
        }
    }
    
    if (themeBtn) {
        const span = themeBtn.querySelector('span');
        const isActive = themeBtn.classList.contains('active');
        if (span) {
            span.textContent = isActive ? 
                translations[currentLang].access.light : 
                translations[currentLang].access.dark;
        }
    }
}

// === GESTION DES PRÉFÉRENCES ===
/**
 * Sauvegarde une préférence dans le localStorage
 * @param {string} key - Clé de la préférence
 * @param {any} value - Valeur à sauvegarder
 */
function savePreference(key, value) {
    try {
        localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
        console.warn('Impossible de sauvegarder les préférences:', error);
    }
}

/**
 * Charge une préférence depuis le localStorage
 * @param {string} key - Clé de la préférence
 * @param {any} defaultValue - Valeur par défaut
 * @returns {any} Valeur de la préférence
 */
function loadPreference(key, defaultValue) {
    try {
        const stored = localStorage.getItem(key);
        return stored ? JSON.parse(stored) : defaultValue;
    } catch (error) {
        console.warn('Impossible de charger les préférences:', error);
        return defaultValue;
    }
}

/**
 * Charge toutes les préférences sauvegardées
 */
function loadPreferences() {
    // Charger la langue
    const savedLang = loadPreference('language', 'fr');
    if (languages.includes(savedLang)) {
        currentLang = savedLang;
    }

    // Charger le mode sombre
    const darkMode = loadPreference('darkMode', false);
    if (darkMode) {
        document.body.classList.add('dark-mode');
        const themeBtn = document.getElementById('theme-toggle');
        const span = themeBtn?.querySelector('span');
        
        if (themeBtn) themeBtn.classList.add('active');
        if (span) {
            span.textContent = translations[currentLang].access.light;
            span.setAttribute('data-i18n', 'access.light');
        }
    }

    // Charger le mode dyslexie
    const dyslexicMode = loadPreference('dyslexicMode', false);
    if (dyslexicMode) {
        document.body.classList.add('dyslexic-mode');
        const dyslexiaBtn = document.getElementById('dyslexia-toggle');
        const span = dyslexiaBtn?.querySelector('span');
        
        if (dyslexiaBtn) dyslexiaBtn.classList.add('active');
        if (span) {
            span.textContent = translations[currentLang].access.normal;
            span.setAttribute('data-i18n', 'access.normal');
        }
    }

    // Appliquer la traduction
    translatePage(currentLang);
}

// === FONCTIONS DE SIMULATION ===
/**
 * Simule un clic sur le nœud utilisateur
 */
function simulateClick() {
    const node = document.getElementById('user-node');
    if (node) {
        node.style.transform = 'scale(1.3)';
        setTimeout(() => {
            node.style.transform = 'scale(1)';
        }, 200);
    }
}

/**
 * Simule l'ajout d'un membre de la famille
 * @param {string} type - Type de relation (parent, child, spouse, sibling)
 */
function addFamily(type) {
    const messages = {
        parent: '👨‍👩‍👦 Vous ajouteriez un parent à votre arbre',
        child: '👶 Vous ajouteriez un enfant à votre arbre',
        spouse: '💑 Vous ajouteriez votre conjoint(e)',
        sibling: '👫 Vous ajouteriez un frère ou une sœur'
    };
    
    const message = messages[type] || '👥 Vous ajouteriez un membre de la famille';
    alert(`💡 ${message}`);
}

// === GESTION DES ÉVÉNEMENTS ===
/**
 * Ajoute les gestionnaires d'événements pour le clavier
 */
function initKeyboardNavigation() {
    document.addEventListener('keydown', (event) => {
        // Navigation avec les touches fléchées
        if (event.key === 'ArrowLeft' && currentStep > 0) {
            event.preventDefault();
            previousStep();
        } else if (event.key === 'ArrowRight' && currentStep < totalSteps) {
            event.preventDefault();
            nextStep();
        } else if (event.key === 'Home') {
            event.preventDefault();
            goToWelcome();
        }
        
        // Raccourcis pour l'accessibilité
        if (event.ctrlKey || event.metaKey) {
            switch (event.key) {
                case 'd':
                    event.preventDefault();
                    toggleDyslexiaMode();
                    break;
                case 'm':
                    event.preventDefault();
                    toggleThemeMode();
                    break;
                case 'l':
                    event.preventDefault();
                    toggleLanguage();
                    break;
            }
        }
    });
}

// === FONCTION POUR OUVRIR LES MODALS ===
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Fermer tous les modals
document.querySelectorAll('.modal-close').forEach(btn => {
    btn.addEventListener('click', function() {
        const modal = this.closest('.modal');
        if (modal) modal.style.display = 'none';
    });
});

// Fermer en cliquant en dehors
window.addEventListener('click', function(e) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

/**
 * Gère le redimensionnement de la fenêtre
 */
function handleResize() {
    // Ajuster les éléments si nécessaire
    updateProgressBar();
}

// === INITIALISATION ===
/**
 * Initialise l'application
 */
function init() {
    try {
        // Charger les préférences
        loadPreferences();
        
        // Initialiser les contrôles
        initAccessibilityControls();
        initKeyboardNavigation();
        
        // Afficher l'écran d'accueil
        showStep(0);
        
        // Gestionnaire de redimensionnement
        window.addEventListener('resize', handleResize);
        
        console.log('Tutoriel généalogique initialisé avec succès');
    } catch (error) {
        console.error('Erreur lors de l\'initialisation:', error);
    }
}

// === LANCEMENT DE L'APPLICATION ===
document.addEventListener('DOMContentLoaded', init);

// Exposer certaines fonctions globalement pour les appels depuis HTML
window.simulateClick = simulateClick;
window.addFamily = addFamily;
window.nextStep = nextStep;
window.previousStep = previousStep;
window.goToWelcome = goToWelcome;
window.goToStep = goToStep;