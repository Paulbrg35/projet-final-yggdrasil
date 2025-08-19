<?php
session_start();
// Définir la constante pour autoriser l'inclusion de config.php
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

// Fonction pour générer un token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Générer le token CSRF pour le formulaire
$csrf_token = generateCSRFToken();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Réinitialisation de mot de passe pour Yggdrasil">
    <meta name="robots" content="noindex, nofollow">
    <title>Mot de passe oublié - Yggdrasil</title>

    <!-- Headers de sécurité -->
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">

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
            --error-bg: #f8d7da;
            --error-color: #721c24;
            --success-bg: #d4edda;
            --success-color: #155724;
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
            --error-bg: #2d1b1b;
            --error-color: #ff6b6b;
            --success-bg: #1b2d1b;
            --success-color: #4caf50;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', Arial, sans-serif;
            color: var(--text-color);
            background-color: var(--bg-body);
            margin: 0;
            padding: 0;
            line-height: 1.6;
            transition: all 0.4s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            text-decoration: none;
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
            overflow: hidden;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: var(--gold);
        }

        /* Accessibility Controls */
        .accessibility-controls {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .accessibility-button {
            background-color: var(--forest-green);
            color: white;
            padding: 0.6rem 1.2rem;
            border: none;
            border-radius: 5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .accessibility-button:hover {
            background-color: var(--gold);
            transform: translateY(-2px);
        }

        .accessibility-button.active {
            background-color: var(--gold);
            color: var(--forest-green);
        }

        /* Main Content */
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
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
            border: 1px solid var(--border-color);
        }

        .forgot-card h1 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--forest-green);
        }

        .forgot-card .subtitle {
            margin-bottom: 2rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-color);
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-family: 'Lato', sans-serif;
            font-size: 1rem;
            background-color: var(--bg-body);
            color: var(--text-color);
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .form-group input:invalid {
            border-color: #e74c3c;
        }

        .error-message {
            background: var(--error-bg);
            color: var(--error-color);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
            border-left: 4px solid #dc3545;
        }

        .error-message ul {
            margin: 0;
            padding-left: 20px;
        }

        .success-message {
            background: var(--success-bg);
            color: var(--success-color);
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: left;
            border-left: 4px solid #28a745;
        }

        .cta-button {
            background-color: var(--gold);
            color: var(--forest-green);
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Lato', sans-serif;
        }

        .cta-button:hover:not(:disabled) {
            background-color: #B8941F;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .cta-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .login-link {
            margin-top: 2rem;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .login-link a {
            color: var(--forest-green);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--gold);
            text-decoration: underline;
        }

        /* Security Notice */
        .security-notice {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 6px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
            color: #1565c0;
            text-align: left;
        }

        .dark-mode .security-notice {
            background: #1a237e;
            border-color: #3f51b5;
            color: #e3f2fd;
        }

        /* Footer */
        footer {
            background-color: var(--footer-bg);
            color: white;
            padding: 2rem 0;
            text-align: center;
            font-size: 0.9rem;
        }

        footer a {
            color: var(--gold);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: white;
            text-decoration: underline;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg);
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background-color: var(--forest-green);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-header h2 {
            margin: 0;
            color: white;
            font-size: 1.3rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 3px;
            transition: background-color 0.3s ease;
        }

        .modal-close:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .modal-body {
            padding: 1.5rem;
            color: var(--text-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            nav {
                margin-top: 0.5rem;
            }
            
            nav a {
                margin: 0 0.75rem;
            }
            
            .forgot-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .accessibility-controls {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }
            
            .modal-content {
                width: 95%;
                margin: 1rem;
            }
        }

        @media (max-width: 480px) {
            .forgot-card {
                padding: 1.5rem 1rem;
            }
            
            .container {
                padding: 0 10px;
            }
        }

        /* OpenDyslexic Font */
        .opendyslexic {
            font-family: 'OpenDyslexic', 'Open Sans', 'Lato', sans-serif !important;
            line-height: 1.8;
        }

        .opendyslexic h1, .opendyslexic h2, .opendyslexic h3 {
            font-family: 'OpenDyslexic', 'Cormorant Garamond', serif !important;
        }

        /* Loading State */
        .loading {
            position: relative;
            overflow: hidden;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        /* Focus Styles for Accessibility */
        button:focus, input:focus, a:focus {
            outline: 2px solid var(--gold);
            outline-offset: 2px;
        }

        /* Skip Link for Screen Readers */
        .skip-link {
            position: absolute;
            top: -40px;
            left: 6px;
            background: var(--forest-green);
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 1000;
        }

        .skip-link:focus {
            top: 6px;
        }
    </style>
</head>
<body>
    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="skip-link">Aller au contenu principal</a>

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
                <a href="index.html" class="logo">
                    <div class="logo-icon">
                        <img src="img/logo.png" alt="Logo Yggdrasil" onerror="this.style.display='none'; this.parentNode.textContent='Y';">
                    </div>
                    <div class="logo-text">Yggdrasil</div>
                </a>
                <nav role="navigation">
                    <a href="index.html" data-i18n="nav.home">Accueil</a>
                    <a href="login.php" data-i18n="nav.login">Connexion</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Contrôles d'accessibilité -->
    <div class="container">
        <div class="accessibility-controls" role="toolbar" aria-label="Options d'accessibilité">
            <button id="dyslexia-toggle" class="accessibility-button" aria-pressed="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                    <path d="M4 4a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 4zm0 3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5z"/>
                    <path d="M2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2zm13 2v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1z"/>
                </svg>
                <span data-i18n="access.dyslexia">Mode Dyslexie</span>
            </button>
            <button id="theme-toggle" class="accessibility-button" aria-pressed="false">
                <span aria-hidden="true">🌙</span>
                <span data-i18n="access.dark">Passer en mode sombre</span>
            </button>
            <button id="lang-toggle" class="accessibility-button">
                <span aria-hidden="true">🌍</span>
                <span id="lang-text">FR → EN → BR</span>
            </button>
        </div>
    </div>

    <!-- Section Mot de passe oublié -->
    <main id="main-content">
        <div class="container">
            <div class="forgot-card">
                <h1 data-i18n="forgot.title">Mot de passe oublié ?</h1>
                <p class="subtitle" data-i18n="forgot.subtitle">Entrez votre email, nous vous enverrons un lien pour le réinitialiser.</p>

                <!-- Message d'erreur -->
                <?php if (isset($_SESSION['reset_errors'])): ?>
                    <div class="error-message" role="alert">
                        <?php if (count($_SESSION['reset_errors']) > 1): ?>
                            <ul>
                                <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <?= htmlspecialchars($_SESSION['reset_errors'][0], ENT_QUOTES, 'UTF-8') ?>
                        <?php endif; ?>
                    </div>
                    <?php unset($_SESSION['reset_errors']); ?>
                <?php endif; ?>

                <!-- Message de succès -->
                <?php if (isset($_SESSION['reset_success'])): ?>
                    <div class="success-message" role="alert">
                        <?= htmlspecialchars($_SESSION['reset_success'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php unset($_SESSION['reset_success']); ?>
                <?php endif; ?>

                <!-- Formulaire -->
                <form action="forgot-password-process.php" method="POST" id="forgot-form" novalidate>
                    <!-- Token CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                    
                    <div class="form-group">
                        <label for="email" data-i18n="forgot.email">Adresse e-mail</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="vous@exemple.com" 
                            required 
                            autocomplete="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                            aria-describedby="email-help"
                        >
                        <div id="email-help" class="sr-only">Entrez l'adresse email associée à votre compte</div>
                    </div>
                    
                    <button type="submit" class="cta-button" id="submit-btn" data-i18n="forgot.submit">
                        Envoyer le lien
                    </button>
                </form>

                <div class="security-notice">
                    <strong>🔒 Note de sécurité :</strong><br>
                    Pour votre protection, nous affichons toujours le même message, que l'email existe ou non dans notre système.
                </div>

                <div class="login-link">
                    <a href="login.php" data-i18n="forgot.back">← Retour à la connexion</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Yggdrasil. 
                <a href="#" onclick="openModal('privacy-modal')" data-i18n="footer.privacy">Confidentialité</a> • 
                <a href="#" onclick="openModal('terms-modal')" data-i18n="footer.terms">CGU</a>
            </p>
        </div>
    </footer>

    <!-- Modals -->
    <div id="terms-modal" class="modal" role="dialog" aria-labelledby="terms-title" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="terms-title" data-i18n="modal.terms_title">Conditions Générales d'Utilisation</h2>
                <button class="modal-close" aria-label="Fermer la modal">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.terms_p1">Bienvenue sur Yggdrasil. En utilisant notre service, vous acceptez ces conditions d'utilisation.</p>
                <p>Ces conditions définissent les règles d'utilisation de la plateforme Yggdrasil et s'appliquent à tous les utilisateurs.</p>
            </div>
        </div>
    </div>

    <div id="privacy-modal" class="modal" role="dialog" aria-labelledby="privacy-title" aria-hidden="true">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="privacy-title" data-i18n="modal.privacy_title">Politique de Confidentialité</h2>
                <button class="modal-close" aria-label="Fermer la modal">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.privacy_p1">Nous respectons votre vie privée. Vos données personnelles sont sécurisées et ne seront jamais partagées sans votre consentement.</p>
                <p>Nous collectons uniquement les données nécessaires au fonctionnement du service et les protégeons selon les standards les plus élevés.</p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // === CONFIGURATION ET UTILITAIRES ===
        const CONFIG = {
            STORAGE_PREFIX: 'yggdrasil_',
            ANIMATION_DURATION: 300
        };

        // Stockage local avec fallback
        const Storage = {
            get: function(key, defaultValue = null) {
                try {
                    if (typeof localStorage !== 'undefined') {
                        const value = localStorage.getItem(CONFIG.STORAGE_PREFIX + key);
                        return value !== null ? value : defaultValue;
                    }
                } catch (e) {
                    console.warn('localStorage non disponible:', e);
                }
                return this.memory[key] || defaultValue;
            },
            set: function(key, value) {
                try {
                    if (typeof localStorage !== 'undefined') {
                        localStorage.setItem(CONFIG.STORAGE_PREFIX + key, value);
                        return;
                    }
                } catch (e) {
                    console.warn('localStorage non disponible:', e);
                }
                this.memory[key] = value;
            },
            memory: {}
        };

        // === DICTIONNAIRE DE TRADUCTION ===
        const translations = {
            fr: {
                'nav.home': 'Accueil',
                'nav.login': 'Connexion',
                'access.dyslexia': 'Mode Dyslexie',
                'access.dark': 'Passer en mode sombre',
                'access.light': 'Passer en mode clair',
                'forgot.title': 'Mot de passe oublié ?',
                'forgot.subtitle': 'Entrez votre email, nous vous enverrons un lien pour le réinitialiser.',
                'forgot.email': 'Adresse e-mail',
                'forgot.submit': 'Envoyer le lien',
                'forgot.back': '← Retour à la connexion',
                'footer.privacy': 'Confidentialité',
                'footer.terms': 'CGU',
                'modal.terms_title': 'Conditions Générales d\'Utilisation',
                'modal.terms_p1': 'Bienvenue sur Yggdrasil. En utilisant notre service, vous acceptez ces conditions d\'utilisation.',
                'modal.privacy_title': 'Politique de Confidentialité',
                'modal.privacy_p1': 'Nous respectons votre vie privée. Vos données personnelles sont sécurisées et ne seront jamais partagées sans votre consentement.'
            },
            en: {
                'nav.home': 'Home',
                'nav.login': 'Login',
                'access.dyslexia': 'Dyslexia Mode',
                'access.dark': 'Switch to Dark Mode',
                'access.light': 'Switch to Light Mode',
                'forgot.title': 'Forgot your password?',
                'forgot.subtitle': 'Enter your email, we will send you a reset link.',
                'forgot.email': 'Email Address',
                'forgot.submit': 'Send Reset Link',
                'forgot.back': '← Back to Login',
                'footer.privacy': 'Privacy',
                'footer.terms': 'Terms',
                'modal.terms_title': 'Terms of Service',
                'modal.terms_p1': 'Welcome to Yggdrasil. By using our service, you agree to these terms of use.',
                'modal.privacy_title': 'Privacy Policy',
                'modal.privacy_p1': 'We respect your privacy. Your personal data is secured and will never be shared without your consent.'
            },
            br: {
                'nav.home': 'Degemer',
                'nav.login': 'Kevreañ',
                'access.dyslexia': 'Mod Dyslexie',
                'access.dark': 'Trec\'h da gouloù tiñv',
                'access.light': 'Trec\'h da gouloù sklaer',
                'forgot.title': 'Ger-tremen disoñjet?',
                'forgot.subtitle': 'Roit ho chomlec\'h postel, e vo kaset ur c\'hemenañ evit adsañset anezhañ.',
                'forgot.email': 'Chomlec\'h postel',
                'forgot.submit': 'Kas ar gemenañ',
                'forgot.back': '← Distreiñ d\'ar gementadur',
                'footer.privacy': 'Prevezded',
                'footer.terms': 'Amplegadoù',
                'modal.terms_title': 'Amplegadoù Arver',
                'modal.terms_p1': 'Degemer mat war Yggdrasil. En arver hon servij e asantit d\'an amplegadoù-mañ.',
                'modal.privacy_title': 'Reolennoù Prevezded',
                'modal.privacy_p1': 'Doujañ a reomp ho puhez prevez. Ho roadennoù zo diarvar ha ne vint morse rannet hep ho asant.'
            }
        };

        const languages = ['fr', 'en', 'br'];
        const langNames = {
            fr: 'FR → EN → BR',
            en: 'EN → BR → FR',
            br: 'BR → FR → EN'
        };

        // === SYSTÈME DE TRADUCTION ===
        function setLanguage(lang) {
            if (!translations[lang]) {
                console.warn('Langue non supportée:', lang);
                return;
            }

            document.querySelectorAll('[data-i18n]').forEach(el => {
                const key = el.getAttribute('data-i18n');
                if (translations[lang][key]) {
                    if (el.tagName === 'INPUT' && el.type !== 'submit') {
                        el.setAttribute('placeholder', translations[lang][key]);
                    } else {
                        el.textContent = translations[lang][key];
                    }
                }
            });

            // Mettre à jour les éléments spéciaux
            const langText = document.getElementById('lang-text');
            if (langText) langText.textContent = langNames[lang];
            
            document.documentElement.lang = lang;
            Storage.set('language', lang);
            
            // Mettre à jour les boutons avec état
            updateDyslexiaButton();
            updateThemeButton();
        }

        // === GESTION DU MODE DYSLEXIE ===
        function updateDyslexiaButton() {
            const dyslexiaToggle = document.getElementById('dyslexia-toggle');
            const isActive = document.body.classList.contains('opendyslexic');
            const lang = Storage.get('language', 'fr');
            
            if (dyslexiaToggle) {
                dyslexiaToggle.classList.toggle('active', isActive);
                dyslexiaToggle.setAttribute('aria-pressed', isActive);
                
                const baseText = translations[lang]['access.dyslexia'];
                const statusText = isActive ? ' (activé)' : '';
                dyslexiaToggle.querySelector('span').textContent = baseText + statusText;
            }
        }

        function toggleDyslexia() {
            document.body.classList.toggle('opendyslexic');
            const isActive = document.body.classList.contains('opendyslexic');
            Storage.set('dyslexiaMode', isActive ? 'true' : 'false');
            updateDyslexiaButton();
        }

        // === GESTION DU THÈME SOMBRE ===
        function updateThemeButton() {
            const themeToggle = document.getElementById('theme-toggle');
            const isDark = document.body.classList.contains('dark-mode');
            const lang = Storage.get('language', 'fr');
            
            if (themeToggle) {
                themeToggle.classList.toggle('active', isDark);
                themeToggle.setAttribute('aria-pressed', isDark);
                
                const textKey = isDark ? 'access.light' : 'access.dark';
                const icon = isDark ? '☀️' : '🌙';
                
                themeToggle.innerHTML = `
                    <span aria-hidden="true">${icon}</span>
                    <span data-i18n="${textKey}">${translations[lang][textKey]}</span>
                `;
            }
        }

        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            Storage.set('theme', isDark ? 'dark' : 'light');
            updateThemeButton();
        }

        // === GESTION DES MODALS ===
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
                // Focus sur le premier élément focusable
                const focusable = modal.querySelector('button, input, a, [tabindex]');
                if (focusable) focusable.focus();
            }
        }

        function closeModal(modal) {
            modal.style.display = 'none';
            modal.setAttribute('aria-hidden', 'true');
        }

        // === VALIDATION DU FORMULAIRE ===
        function validateForm(form) {
            const email = form.querySelector('#email');
            const submitBtn = form.querySelector('#submit-btn');
            
            function updateSubmitState() {
                const isValid = email.value.trim() && email.checkValidity();
                submitBtn.disabled = !isValid;
            }
            
            email.addEventListener('input', updateSubmitState);
            email.addEventListener('blur', updateSubmitState);
            updateSubmitState();
        }

        // === INITIALISATION ===
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Charger les préférences sauvegardées
                const savedLang = Storage.get('language', 'fr');
                const savedTheme = Storage.get('theme', 'light');
                const savedDyslexia = Storage.get('dyslexiaMode', 'false');

                // Appliquer les préférences
                setLanguage(savedLang);
                
                if (savedTheme === 'dark') {
                    document.body.classList.add('dark-mode');
                }
                
                if (savedDyslexia === 'true') {
                    document.body.classList.add('opendyslexic');
                }

                // Mettre à jour l'UI
                updateDyslexiaButton();
                updateThemeButton();

                // Event Listeners
                const langToggle = document.getElementById('lang-toggle');
                const dyslexiaToggle = document.getElementById('dyslexia-toggle');
                const themeToggle = document.getElementById('theme-toggle');
                const forgotForm = document.getElementById('forgot-form');

                if (langToggle) {
                    langToggle.addEventListener('click', function() {
                        const currentLang = Storage.get('language', 'fr');
                        const currentIndex = languages.indexOf(currentLang);
                        const nextLang = languages[(currentIndex + 1) % languages.length];
                        setLanguage(nextLang);
                    });
                }

                if (dyslexiaToggle) {
                    dyslexiaToggle.addEventListener('click', toggleDyslexia);
                }

                if (themeToggle) {
                    themeToggle.addEventListener('click', toggleTheme);
                }

                // Validation du formulaire
                if (forgotForm) {
                    validateForm(forgotForm);
                    
                    // Animation de soumission
                    forgotForm.addEventListener('submit', function(e) {
                        const submitBtn = this.querySelector('#submit-btn');
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        submitBtn.textContent = 'Envoi en cours...';
                    });
                }

                // Gestion des modals
                document.querySelectorAll('.modal-close').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const modal = this.closest('.modal');
                        closeModal(modal);
                    });
                });

                // Fermer modal en cliquant à l'extérieur
                window.addEventListener('click', function(e) {
                    if (e.target.classList.contains('modal')) {
                        closeModal(e.target);
                    }
                });

                // Fermer modal avec Escape
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        document.querySelectorAll('.modal[style*="flex"]').forEach(closeModal);
                    }
                });

                // Focus automatique sur le champ email si pas de message
                const emailField = document.getElementById('email');
                const hasMessages = document.querySelector('.error-message, .success-message');
                if (emailField && !hasMessages) {
                    emailField.focus();
                }

            } catch (error) {
                console.error('Erreur lors de l\'initialisation:', error);
            }
        });

        // Fonction globale pour les modals (appelée depuis onclick)
        window.openModal = openModal;

        // === CLASSE POUR LECTEURS D'ÉCRAN ===
        const style = document.createElement('style');
        style.textContent = `
            .sr-only {
                position: absolute;
                width: 1px;
                height: 1px;
                padding: 0;
                margin: -1px;
                overflow: hidden;
                clip: rect(0, 0, 0, 0);
                white-space: nowrap;
                border: 0;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>