<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="darkreader-disable" content="true">
    <meta name="color-scheme" content="light">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Yggdrasil</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&family=Lato:wght@400;700&family=Cormorant+Garamond:wght@700&display=swap" rel="stylesheet">
    
    <!-- OpenDyslexic -->
    <link href="https://fonts.cdnfonts.com/css/open-dyslexic" rel="stylesheet">

    <style>
        :root {
            --forest-green: #2E5D42;
            --gold: #D4AF37;
            --cream: #F8F5F0;
            --light-cream: #FFF9F0;
            --text-dark: #333;
            --text-light: #666;
            --border-color: #E0D8C8;

            --bg-body: #FFF9F0;
            --bg-section: #FFF9F0;
            --header-bg: #2E5D42;
            --text-color: #333333;
            --card-bg: #FFFFFF;
            --footer-bg: #2E5D42;
            --shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .dark-mode {
            --bg-body: #1a1a1a;
            --bg-section: #2d2d2d;
            --header-bg: #1e3d2a;
            --text-color: #e0e0e0;
            --card-bg: #222;
            --border-color: #444;
            --footer-bg: #1e3d2a;
        }

        body {
            font-family: 'Lato', Arial, sans-serif;
            color: var(--text-color);
            background-color: var(--bg-body);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            transition: all 0.4s ease;
        }

        h1, h2, h3 {
            font-family: 'Cormorant Garamond', Georgia, serif;
            color: var(--forest-green);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            background-color: var(--header-bg);
            color: white;
            padding: 1rem 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: var(--gold);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--forest-green);
            font-weight: bold;
            font-size: 1.2rem;
        }

        .logo-text {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.5rem;
            font-weight: bold;
            color: white;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 1.5rem;
            font-weight: 500;
        }

        /* Accessibility Controls */
        .accessibility-controls {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .dyslexia-button {
            background-color: var(--forest-green);
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .dyslexia-button:hover {
            background-color: var(--gold);
            transform: translateY(-2px);
        }

        .dyslexia-button.active {
            background-color: var(--gold);
            color: var(--forest-green);
        }

        /* Language Toggle Button */
        #lang-toggle {
            background-color: var(--forest-green);
            color: white;
            padding: 0.6rem 1rem;
            font-size: 0.9rem;
        }

        /* FAQ Section */
        .faq-section {
            padding: 5rem 0;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .faq-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .faq-header p {
            font-size: 1.2rem;
            color: var(--text-light);
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            margin-bottom: 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .faq-question {
            background-color: var(--card-bg);
            padding: 1rem 1.5rem;
            font-weight: 500;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .faq-question:hover {
            background-color: var(--light-cream);
        }

        .faq-question::after {
            content: '+';
            font-size: 1.5rem;
            color: var(--gold);
            transition: transform 0.3s;
        }

        .faq-item.active .faq-question::after {
            content: '−';
        }

        .faq-answer {
            padding: 0 1.5rem;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, padding 0.4s ease;
            background-color: var(--card-bg);
        }

        .faq-item.active .faq-answer {
            padding: 1rem 1.5rem;
            max-height: 500px;
        }

        .faq-answer p {
            margin: 0.5rem 0;
            color: var(--text-dark);
        }

        /* Footer */
        footer {
            background-color: var(--footer-bg);
            color: white;
            padding: 3rem 0 2rem;
            margin-top: 3rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-column h3 {
            color: var(--gold);
            margin-bottom: 1.2rem;
            font-size: 1.1rem;
        }

        .footer-column ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .footer-column ul li {
            margin-bottom: 0.6rem;
        }

        .footer-column a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-column a:hover {
            color: var(--gold);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            opacity: 0.7;
        }

        /* OpenDyslexic Font */
        .opendyslexic {
            font-family: 'OpenDyslexic', 'Open Sans', 'Lato', sans-serif !important;
            line-height: 1.8;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            nav {
                margin-top: 0.5rem;
            }
            .faq-header h1 {
                font-size: 2rem;
            }
            .faq-header p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- Load OpenDyslexic early -->
    <script>
        function loadOpenDyslexicFont() {
            if (!document.getElementById('opendyslexic-font')) {
                const link = document.createElement('link');
                link.id = 'opendyslexic-font';
                link.rel = 'stylesheet';
                link.href = 'https://fonts.cdnfonts.com/css/open-dyslexic';
                document.head.appendChild(link);
            }
        }
        loadOpenDyslexicFont();
    </script>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="img/logo.png" class="logo-icon">
                        <img src="img/logo.png" alt="Yggdrasil Logo" style="width: 40px; height: 40px;">
                    </a>
                    <div class="logo-text">Yggdrasil</div>
                </div>
                <nav>
                    <a href="index.html" data-i18n="nav.home">Accueil</a>
                    <a href="dashboard.html" data-i18n="nav.dashboard">Tableau de bord</a>
                    <a href="contact.php" data-i18n="nav.contact">Contact</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Accessibility Controls -->
    <div class="container">
        <div class="accessibility-controls">
            <button id="dyslexia-toggle" class="dyslexia-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 4zm0 3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5z"/>
                    <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                </svg>
                <span data-i18n="access.dyslexia">Mode Dyslexie</span>
            </button>
            <button id="theme-toggle" class="dyslexia-button">
                🌙 <span data-i18n="access.dark">Passer en mode sombre</span>
            </button>
            <button id="lang-toggle" class="dyslexia-button">
                🌍 <span id="lang-text">FR → EN → BR</span>
            </button>
        </div>
    </div>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-header">
                <h1 data-i18n="faq.title">Questions Fréquentes</h1>
                <p data-i18n="faq.subtitle">Tout ce que vous devez savoir sur Yggdrasil, la création d’arbre généalogique, la sécurité et la collaboration familiale.</p>
            </div>

            <div class="faq-container" id="faq-list">
                <!-- Les questions seront injectées ici via JS -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3 data-i18n="footer.support">Support</h3>
                    <ul>
                        <li><a href="faq.php" data-i18n="footer.faq">FAQ</a></li>
                        <li><a href="tutorials.html" data-i18n="footer.tutorials">Tutoriels</a></li>
                        <li><a href="contact.php" data-i18n="footer.contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 data-i18n="footer.legal">Légal</h3>
                    <ul>
                        <li><a href="#" class="modal-link" data-modal="terms">CGU</a></li>
                        <li><a href="#" class="modal-link" data-modal="privacy">Confidentialité</a></li>
                        <li><a href="#" class="modal-link" data-modal="gdpr">RGPD</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 data-i18n="footer.tools">Outils</h3>
                    <ul>
                        <li><a href="export.php" data-i18n="footer.export">Exporter l'arbre</a></li>
                        <li><a href="invite.html" data-i18n="footer.invite">Inviter la famille</a></li>
                        <li><a href="gallery.html" data-i18n="footer.gallery">Galerie photos</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Yggdrasil. <span data-i18n="footer.allrights">Tous droits réservés.</span></p>
            </div>
        </div>
    </footer>

    <!-- MODALS -->
    <div id="terms-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" data-i18n="modal.terms_title">Conditions d'Utilisation</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.terms_p1">En utilisant Yggdrasil, vous acceptez nos conditions.</p>
            </div>
        </div>
    </div>

    <div id="privacy-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" data-i18n="modal.privacy_title">Politique de Confidentialité</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.privacy_p1">Nous protégeons vos données conformément au RGPD.</p>
            </div>
        </div>
    </div>

    <div id="gdpr-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Conformité RGPD</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p>Yggdrasil respecte pleinement le RGPD. Vous avez le droit d'accéder, corriger ou supprimer vos données à tout moment.</p>
            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        // === DICTIONNAIRE DE TRADUCTION ===
        const translations = {
            fr: {
                'nav.home': 'Accueil',
                'nav.dashboard': 'Tableau de bord',
                'nav.contact': 'Contact',
                'access.dyslexia': 'Mode Dyslexie',
                'access.dark': 'Passer en mode sombre',
                'footer.faq': 'FAQ',
                'footer.tutorials': 'Tutoriels',
                'footer.contact': 'Contact',
                'footer.legal': 'Légal',
                'footer.export': 'Exporter l\'arbre',
                'footer.invite': 'Inviter la famille',
                'footer.gallery': 'Galerie photos',
                'footer.allrights': 'Tous droits réservés.',
                'modal.terms_title': 'Conditions d\'Utilisation',
                'modal.terms_p1': 'En utilisant Yggdrasil, vous acceptez nos conditions.',
                'modal.privacy_title': 'Politique de Confidentialité',
                'modal.privacy_p1': 'Nous protégeons vos données conformément au RGPD.',
                'faq.title': 'Questions Fréquentes',
                'faq.subtitle': 'Tout ce que vous devez savoir sur Yggdrasil, la création d’arbre généalogique, la sécurité et la collaboration familiale.',
                'faq.q1': 'Qu\'est-ce qu\'Yggdrasil ?',
                'faq.a1': 'Yggdrasil est une plateforme intuitive pour créer, explorer et partager votre arbre généalogique. Vous pouvez ajouter des membres, des photos, des dates et collaborer avec votre famille.',
                'faq.q2': 'Est-ce gratuit ?',
                'faq.a2': 'Oui, l\'inscription et l\'utilisation de base sont 100% gratuites. Une version premium propose des fonctionnalités étendues (export PDF, historique complet, etc.).',
                'faq.q3': 'Mes données sont-elles sécurisées ?',
                'faq.a3': 'Oui. Vos données sont chiffrées et stockées conformément au RGPD. Aucune donnée n\'est vendue ou partagée avec des tiers.',
                'faq.q4': 'Puis-je inviter ma famille ?',
                'faq.a4': 'Absolument ! Vous pouvez inviter des membres de votre famille par email. Vous choisissez leurs droits : lecture seule ou édition.',
                'faq.q5': 'Puis-je exporter mon arbre ?',
                'faq.a5': 'Oui, vous pouvez exporter votre arbre au format PDF, GEDCOM ou image depuis votre tableau de bord.',
                'faq.q6': 'Comment supprimer mon compte ?',
                'faq.a6': 'Vous pouvez supprimer votre compte à tout moment depuis la section "Profil" > "Supprimer le compte". Toutes vos données seront effacées.',
                'faq.q7': 'Prenez-vous en charge le breton ?',
                'faq.a7': 'Oui ! Yggdrasil est multilingue, y compris en breton (br), pour préserver les racines culturelles de nos utilisateurs.',
                'faq.q8': 'Puis-je ajouter des photos ?',
                'faq.a8': 'Bien sûr ! Vous pouvez enrichir chaque membre de votre arbre avec des photos, des légendes et des dates.',
                'faq.q9': 'Comment récupérer mon mot de passe ?',
                'faq.a9': 'Cliquez sur "Mot de passe oublié" sur la page de connexion. Un lien de réinitialisation sera envoyé à votre email.',
                'faq.q10': 'Puis-je utiliser Yggdrasil sur mobile ?',
                'faq.a10': 'Oui, Yggdrasil est entièrement responsive et fonctionne sur smartphone, tablette et ordinateur.'
            },
            en: {
                'nav.home': 'Home',
                'nav.dashboard': 'Dashboard',
                'nav.contact': 'Contact',
                'access.dyslexia': 'Dyslexia Mode',
                'access.dark': 'Switch to Dark Mode',
                'footer.faq': 'FAQ',
                'footer.tutorials': 'Tutorials',
                'footer.contact': 'Contact',
                'footer.legal': 'Legal',
                'footer.export': 'Export Tree',
                'footer.invite': 'Invite Family',
                'footer.gallery': 'Photo Gallery',
                'footer.allrights': 'All rights reserved.',
                'modal.terms_title': 'Terms of Service',
                'modal.terms_p1': 'By using Yggdrasil, you agree to our terms.',
                'modal.privacy_title': 'Privacy Policy',
                'modal.privacy_p1': 'We protect your data in compliance with GDPR.',
                'faq.title': 'Frequently Asked Questions',
                'faq.subtitle': 'Everything you need to know about Yggdrasil, family trees, security, and collaboration.',
                'faq.q1': 'What is Yggdrasil?',
                'faq.a1': 'Yggdrasil is an intuitive platform to create, explore, and share your family tree. Add members, photos, dates, and collaborate with your family.',
                'faq.q2': 'Is it free?',
                'faq.a2': 'Yes, registration and basic use are 100% free. A premium version offers extended features (PDF export, full history, etc.).',
                'faq.q3': 'Are my data secure?',
                'faq.a3': 'Yes. Your data is encrypted and stored in compliance with GDPR. No data is sold or shared with third parties.',
                'faq.q4': 'Can I invite my family?',
                'faq.a4': 'Absolutely! You can invite family members by email. You choose their permissions: view-only or edit.',
                'faq.q5': 'Can I export my tree?',
                'faq.a5': 'Yes, you can export your tree as PDF, GEDCOM, or image from your dashboard.',
                'faq.q6': 'How do I delete my account?',
                'faq.a6': 'You can delete your account at any time from "Profile" > "Delete Account". All your data will be erased.',
                'faq.q7': 'Do you support Breton?',
                'faq.a7': 'Yes! Yggdrasil is multilingual, including Breton (br), to preserve our users\' cultural roots.',
                'faq.q8': 'Can I add photos?',
                'faq.a8': 'Of course! You can enrich each family member with photos, captions, and dates.',
                'faq.q9': 'How do I recover my password?',
                'faq.a9': 'Click "Forgot password" on the login page. A reset link will be sent to your email.',
                'faq.q10': 'Can I use Yggdrasil on mobile?',
                'faq.a10': 'Yes, Yggdrasil is fully responsive and works on smartphones, tablets, and computers.'
            },
            br: {
                'nav.home': 'Degemer',
                'nav.dashboard': 'Taolenn-wez',
                'nav.contact': 'Darempred',
                'access.dyslexia': 'Mod Dyslexie',
                'access.dark': 'Trec\'h da gouloù tiñj',
                'footer.faq': 'FAQ',
                'footer.tutorials': 'Tutoalioù',
                'footer.contact': 'Darempred',
                'footer.legal': 'Lezenn',
                'footer.export': 'Ezporzh ar gernezenn',
                'footer.invite': 'Kerkent ho teuli',
                'footer.gallery': 'Kened skeudennoù',
                'footer.allrights': 'An holl gwirioù rezervet.',
                'modal.terms_title': 'Ranneloù Implijout',
                'modal.terms_p1': 'En ur implijout Yggdrasil, e gemerit ar ranneloù.',
                'modal.privacy_title': 'Politegerez Aotre',
                'modal.privacy_p1': 'Gouzout a ra bout ho titouroù en ur gomziant GDPR.',
                'faq.title': 'Goulennoù Raok Elaouet',
                'faq.subtitle': 'An holl a raok gouzout a-bout Yggdrasil, ar gernezenn genealogel, an aotre hag ar c\'hendeuz.',
                'faq.q1': 'Petra eo Yggdrasil ?',
                'faq.a1': 'Yggdrasil a zo un al lec\'hiadur skaragadus evit sevel, skoazellañ ha rannañ ho kernezenn genealogel. Ouzhpennit tud, skeudennoù, deizioù ha kendeuzit gant ho teuli.',
                'faq.q2': 'Ha gratis ?',
                'faq.a2': 'Ya, an izili ha implij oberour a zo 100% amankavel. Un elfenn premium a zegas perakroù estreget (ezporzh PDF, istor, hag all).',
                'faq.q3': 'Ho titouroù a zo surentet ?',
                'faq.a3': 'Ya. Ho titouroù a zo enrollet en ur gomziant GDPR. Titour ne vo ket vendet pe rannet gant trede.',
                'faq.q4': 'Posut a raib kerkent ma teuli ?',
                'faq.a4': 'Absoluj! C\'hellit kerkent ar membroù eus ho teuli dre bostel. C\'hellit kouzout o gwir : lenn hepken pe modifikadur.',
                'faq.q5': 'Posut a raib ezporzh ma kernezenn ?',
                'faq.a5': 'Ya, c\'hellit ezporzh ho kernezenn e format PDF, GEDCOM pe skeudenn eus ho taolenn-wez.',
                'faq.q6': 'Petra eo dilemel ma gont ?',
                'faq.a6': 'C\'hellit dilemel ho kont war-un-tik eus "Profil" > "Dilemel an kont". An holl ho titouroù a vo lamet.',
                'faq.q7': 'Ho po implijet ar brezhoneg ?',
                'faq.a7': 'Ya! Yggdrasil a zo liesyezhel, gant ar brezhoneg (br), evit gwareziñ gwreiz ar vugale.',
                'faq.q8': 'Posut a raib ouzhpennañ skeudennoù ?',
                'faq.a8': 'Evit seguro! C\'hellit pennañ pep hini eus ho teuli gant skeudennoù, diskribladoù ha deizioù.',
                'faq.q9': 'Petra eo adkavout ma ger-tremen ?',
                'faq.a9': 'Klikit war "Ger-tremen kerret" war ar bajenn kevreañ. Ur c\'hlik evit adsevel a vo kaset war ho chomlec\'h.',
                'faq.q10': 'Posut a raib implijout Yggdrasil war mobili ?',
                'faq.a10': 'Ya, Yggdrasil a zo skaragadus ha labour war mobili, tablet hag urzhiataer.'
            }
        };

        // Langues
        const languages = ['fr', 'en', 'br'];
        const langNames = {
            fr: 'FR → EN → BR',
            en: 'EN → BR → FR',
            br: 'BR → FR → EN'
        };

        // Appliquer la langue
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

        // Charger au démarrage
        document.addEventListener('DOMContentLoaded', function () {
            const savedLang = localStorage.getItem('language');
            const initialLang = languages.includes(savedLang) ? savedLang : 'fr';
            setLanguage(initialLang);

            // Bascule langue
            document.getElementById('lang-toggle')?.addEventListener('click', () => {
                const currentLang = localStorage.getItem('language') || 'fr';
                const nextLang = languages[(languages.indexOf(currentLang) + 1) % languages.length];
                setLanguage(nextLang);
            });

            // === FAQ ACCORDION ===
            const faqContainer = document.getElementById('faq-list');
            const totalQuestions = 10;

            for (let i = 1; i <= totalQuestions; i++) {
                const questionKey = `faq.q${i}`;
                const answerKey = `faq.a${i}`;

                const item = document.createElement('div');
                item.className = 'faq-item';
                item.innerHTML = `
                    <button class="faq-question" aria-expanded="false">
                        <span data-i18n="${questionKey}">${translations[initialLang][questionKey]}</span>
                    </button>
                    <div class="faq-answer">
                        <p data-i18n="${answerKey}">${translations[initialLang][answerKey]}</p>
                    </div>
                `;
                faqContainer.appendChild(item);

                // Gestion du clic
                const question = item.querySelector('.faq-question');
                question.addEventListener('click', () => {
                    const isActive = item.classList.contains('active');
                    // Fermer tous les autres
                    document.querySelectorAll('.faq-item').forEach(el => {
                        el.classList.remove('active');
                    });
                    // Ouvrir celui-ci
                    if (!isActive) {
                        item.classList.add('active');
                    }
                });
            }

            // === MODE DYSLEXIE & SOMBRE ===
            const body = document.body;

            if (localStorage.getItem('dyslexiaMode') === 'true') body.classList.add('opendyslexic');
            document.getElementById('dyslexia-toggle')?.addEventListener('click', () => {
                body.classList.toggle('opendyslexic');
                localStorage.setItem('dyslexiaMode', body.classList.contains('opendyslexic') ? 'true' : 'false');
            });

            if (localStorage.getItem('theme') === 'dark') body.classList.add('dark-mode');
            document.getElementById('theme-toggle')?.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
            });

            // === MODALS ===
            const termsModal = document.getElementById('terms-modal');
            const privacyModal = document.getElementById('privacy-modal');
            const gdprModal = document.getElementById('gdpr-modal');
            const closeModal = document.querySelectorAll('.modal-close');
            const modalLinks = document.querySelectorAll('.modal-link');

            modalLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalType = link.getAttribute('data-modal');
                    if (modalType === 'terms') termsModal.style.display = 'flex';
                    if (modalType === 'privacy') privacyModal.style.display = 'flex';
                    if (modalType === 'gdpr') gdprModal.style.display = 'flex';
                });
            });

            closeModal.forEach(btn => {
                btn.addEventListener('click', () => {
                    termsModal.style.display = 'none';
                    privacyModal.style.display = 'none';
                    gdprModal.style.display = 'none';
                });
            });

            window.addEventListener('click', (e) => {
                if (e.target === termsModal) termsModal.style.display = 'none';
                if (e.target === privacyModal) privacyModal.style.display = 'none';
                if (e.target === gdprModal) gdprModal.style.display = 'none';
            });
        });
    </script>
</body>
</html>