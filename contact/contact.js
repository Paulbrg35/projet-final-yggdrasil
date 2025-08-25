// === DICTIONNAIRE DE TRADUCTION ===
const translations = {
    fr: {
        'nav.home': 'Accueil',
        'nav.faq': 'FAQ',
        'contact.title': 'Nous Contacter',
        'contact.subtitle': 'Une question ? Une suggestion ? Nous sommes là pour vous aider.',
        'contact.name': 'Votre nom',
        'contact.email': 'Votre email',
        'contact.subject': 'Sujet',
        'contact.message': 'Message',
        'contact.send': 'Envoyer le message',
        'contact.info': 'Informations de contact',
        'contact.follow': 'Suivez-nous',
        'footer.privacy': 'Confidentialité',
        'footer.terms': 'CGU'
    },
    en: {
        'nav.home': 'Home',
        'nav.faq': 'FAQ',
        'contact.title': 'Contact Us',
        'contact.subtitle': 'Any question? A suggestion? We are here to help.',
        'contact.name': 'Your name',
        'contact.email': 'Your email',
        'contact.subject': 'Subject',
        'contact.message': 'Message',
        'contact.send': 'Send message',
        'contact.info': 'Contact Information',
        'contact.follow': 'Follow us',
        'footer.privacy': 'Privacy',
        'footer.terms': 'Terms'
    },
    br: {
        'nav.home': 'Degemer',
        'nav.faq': 'FAQ',
        'contact.title': 'Kevreañ ganeomp',
        'contact.subtitle': 'Unan eus a goulenn? Un doare? Oc’h war eus hepken.',
        'contact.name': 'Ho anvet',
        'contact.email': 'Ho chomlec\'h postel',
        'contact.subject': 'Danvez',
        'contact.message': 'Kemennadenn',
        'contact.send': 'Kas ar gemennadenn',
        'contact.info': 'Titouroù kevreañ',
        'contact.follow': 'Heuliañ ac\'hanomp',
        'footer.privacy': 'Privacy',
        'footer.terms': 'Terms'
    }
};

const languages = ['fr', 'en', 'br'];
const langNames = {
    fr: 'FR → EN → BR',
    en: 'EN → BR → FR',
    br: 'BR → FR → EN'
};

function setLanguage(lang) {
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (translations[lang] && translations[lang][key]) {
            if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                el.setAttribute('placeholder', translations[lang][key]);
            } else {
                el.textContent = translations[lang][key];
            }
        }
    });
    document.getElementById('lang-text').textContent = langNames[lang];
    document.documentElement.lang = lang;
    localStorage.setItem('language', lang);
}

document.addEventListener('DOMContentLoaded', function () {
    const savedLang = localStorage.getItem('language') || 'fr';
    setLanguage(savedLang);

    document.getElementById('lang-toggle').addEventListener('click', function () {
        const currentLang = localStorage.getItem('language') || 'fr';
        const currentIndex = languages.indexOf(currentLang);
        const nextLang = languages[(currentIndex + 1) % 3];
        setLanguage(nextLang);
    });

    // === MODE DYSLEXIE ===
    const dyslexiaToggle = document.getElementById('dyslexia-toggle');
    const body = document.body;
    const savedDyslexia = localStorage.getItem('dyslexiaMode') === 'true';

    if (savedDyslexia) {
        body.classList.add('opendyslexic');
    }

    dyslexiaToggle.addEventListener('click', () => {
        body.classList.toggle('opendyslexic');
        localStorage.setItem('dyslexiaMode', body.classList.contains('opendyslexic') ? 'true' : 'false');
    });

    // === MODE SOMBRE ===
    const themeToggle = document.getElementById('theme-toggle');
    const savedTheme = localStorage.getItem('theme') || 'light';

    if (savedTheme === 'dark') {
        body.classList.add('dark-mode');
    }

    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
    });
});