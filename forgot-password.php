<?php
session_start();
// Autoriser l'inclusion de config.php
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

// Générer un token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Yggdrasil</title>

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
            color-scheme: light;
        }

        .dark-mode {
            --bg-body: #1a1a1a;
            --bg-section: #2d2d2d;
            --header-bg: #1e3d2a;
            --text-color: #ffffff;
            --card-bg: #222;
            --border-color: #444;
            --footer-bg: #1e3d2a;
            --shadow: 0 5px 15px rgba(0,0,0,0.3);
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

        /* Forgot Password Section */
        .forgot-password-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem 0;
        }

        .forgot-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            text-align: center;
        }

        .forgot-card h1 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--forest-green);
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-family: 'Lato', sans-serif;
            font-size: 1rem;
            background-color: var(--bg-body);
            color: var(--text-color);
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--gold);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
        }

        .cta-button {
            background-color: var(--gold);
            color: var(--forest-green);
            padding: 0.9rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .cta-button:hover {
            background-color: white;
            color: var(--forest-green);
            transform: translateY(-2px);
        }

        .login-link {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .login-link a {
            color: var(--forest-green);
            font-weight: bold;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Footer */
        footer {
            background-color: var(--footer-bg);
            color: white;
            padding: 2rem 0;
            margin-top: auto;
            text-align: center;
            font-size: 0.9rem;
        }

        footer a {
            color: var(--gold);
            text-decoration: none;
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
            .forgot-card {
                padding: 2rem;
                margin: 1rem;
            }
            .accessibility-controls {
                flex-direction: column;
                align-items: center;
            }
        }

        /* OpenDyslexic Font */
        .opendyslexic {
            font-family: 'OpenDyslexic', 'Open Sans', 'Lato', sans-serif !important;
            line-height: 1.8;
        }
    </style>
</head>
<body>

    <!-- Charger OpenDyslexic -->
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
                    <a href="login.php" data-i18n="nav.login">Connexion</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contrôles d'accessibilité -->
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

    <!-- Section Mot de passe oublié -->
    <section class="forgot-password-section">
        <div class="container">
            <div class="forgot-card">
                <h1 data-i18n="forgot.title">Mot de passe oublié ?</h1>
                <p data-i18n="forgot.subtitle">Entrez votre email, nous vous enverrons un lien pour le réinitialiser.</p>

                <!-- Message d'erreur -->
                <?php if (isset($_SESSION['reset_errors'])): ?>
                    <div class="error-message">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['reset_errors']); ?>
                <?php endif; ?>

                <!-- Message de succès -->
                <?php if (isset($_SESSION['reset_success'])): ?>
                    <div class="success-message">
                        <?= htmlspecialchars($_SESSION['reset_success']) ?>
                    </div>
                    <?php unset($_SESSION['reset_success']); ?>
                <?php endif; ?>

                <!-- Formulaire -->
                <form action="forgot-password-process.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                    <div class="form-group">
                        <label for="email" data-i18n="forgot.email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" placeholder="vous@exemple.com" required>
                    </div>
                    <button type="submit" class="cta-button" data-i18n="forgot.submit">Envoyer le lien</button>
                </form>

                <div class="login-link">
                    <a href="login.php" data-i18n="forgot.back">← Retour à la connexion</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Yggdrasil. 
                <a href="#" onclick="openModal('privacy-modal')" data-i18n="footer.privacy">Confidentialité</a> • 
                <a href="#" onclick="openModal('terms-modal')" data-i18n="footer.terms">CGU</a>
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // === DICTIONNAIRE DE TRADUCTION ===
        const translations = {
            fr: {
                'nav.home': 'Accueil',
                'nav.login': 'Connexion',
                'access.dyslexia': 'Mode Dyslexie',
                'access.dark': 'Passer en mode sombre',
                'forgot.title': 'Mot de passe oublié ?',
                'forgot.subtitle': 'Entrez votre email, nous vous enverrons un lien pour le réinitialiser.',
                'forgot.email': 'Adresse e-mail',
                'forgot.submit': 'Envoyer le lien',
                'forgot.back': '← Retour à la connexion',
                'footer.privacy': 'Confidentialité',
                'footer.terms': 'CGU'
            },
            en: {
                'nav.home': 'Home',
                'nav.login': 'Login',
                'access.dyslexia': 'Dyslexia Mode',
                'access.dark': 'Switch to Dark Mode',
                'forgot.title': 'Forgot your password?',
                'forgot.subtitle': 'Enter your email, we will send you a reset link.',
                'forgot.email': 'Email Address',
                'forgot.submit': 'Send Reset Link',
                'forgot.back': '← Back to Login',
                'footer.privacy': 'Privacy',
                'footer.terms': 'Terms'
            },
            br: {
                'nav.home': 'Degemer',
                'nav.login': 'Kevreañ',
                'access.dyslexia': 'Mod Dyslexie',
                'access.dark': 'Trec\'h da gouloù tiñj',
                'forgot.title': 'Ger-tremen forgetet ?',
                'forgot.subtitle': 'Roit ho chomlec\'h postel, e vo kaset ur c\'hemenañ evit adsañset anezhañ.',
                'forgot.email': 'Chomlec\'h postel',
                'forgot.submit': 'Kas ar gemenñ',
                'forgot.back': '← Distreiñ d\'ar gementadur',
                'footer.privacy': 'Prevezded',
                'footer.terms': 'Amplegadoù'
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

            function updateDyslexiaButton() {
                const lang = localStorage.getItem('language') || 'fr';
                const text = body.classList.contains('opendyslexic')
                    ? translations[lang]['access.dyslexia'] + ' (activé)'
                    : translations[lang]['access.dyslexia'];
                dyslexiaToggle.classList.toggle('active', body.classList.contains('opendyslexic'));
                dyslexiaToggle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 4zm0 3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5z"/>
                        <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                    </svg>
                    <span>${text}</span>
                `;
            }

            dyslexiaToggle.addEventListener('click', () => {
                body.classList.toggle('opendyslexic');
                localStorage.setItem('dyslexiaMode', body.classList.contains('opendyslexic') ? 'true' : 'false');
                updateDyslexiaButton();
            });
            updateDyslexiaButton();

            // === MODE SOMBRE ===
            const themeToggle = document.getElementById('theme-toggle');
            const savedTheme = localStorage.getItem('theme') || 'light';

            if (savedTheme === 'dark') {
                body.classList.add('dark-mode');
            }

            function updateThemeButton() {
                const lang = localStorage.getItem('language') || 'fr';
                const isDark = body.classList.contains('dark-mode');
                const text = isDark 
                    ? translations[lang]['access.dark'].replace('sombre', 'clair') 
                    : translations[lang]['access.dark'];
                themeToggle.innerHTML = (isDark ? '☀️ ' : '🌙 ') + `<span>${text}</span>`;
            }

            themeToggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
                updateThemeButton();
            });
            updateThemeButton();
        });

        // === MODALS (réutilisé depuis index.html) ===
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
        }
        document.querySelectorAll('.modal-close').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });
        window.addEventListener('click', e => {
            document.querySelectorAll('.modal').forEach(modal => {
                if (e.target === modal) modal.style.display = 'none';
            });
        });
    </script>

    <!-- Modals Légaux -->
    <div id="terms-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 data-i18n="modal.terms_title">Conditions Générales</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.terms_p1">Bienvenue sur Yggdrasil. En utilisant notre service, vous acceptez ces conditions.</p>
            </div>
        </div>
    </div>

    <div id="privacy-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 data-i18n="modal.privacy_title">Politique de Confidentialité</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.privacy_p1">Nous respectons votre vie privée. Vos données sont sécurisées et ne seront jamais partagées.</p>
            </div>
        </div>
    </div>
</body>
</html>