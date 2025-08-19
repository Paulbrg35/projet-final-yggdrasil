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
    <title>Inscription - Yggdrasil</title>

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

        /* Register Section */
        .register-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem 0;
        }

        .register-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 520px;
            padding: 2.5rem;
            text-align: center;
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

        .form-group input, .form-group select {
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

        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: var(--gold);
        }

        /* Password Strength Indicator */
        .password-requirements {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            color: var(--text-light);
            text-align: left;
            padding-left: 5px;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0.5rem 0 0;
        }

        .password-requirements li {
            margin-bottom: 0.3rem;
            position: relative;
            padding-left: 20px;
        }

        .password-requirements li::before {
            content: "❌";
            position: absolute;
            left: 0;
            top: 0;
            font-size: 0.9rem;
        }

        .password-requirements li.valid::before {
            content: "✅";
            color: green;
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

        /* MODAL (Popup) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: var(--card-bg);
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 0.5rem;
        }

        .modal-title {
            font-size: 1.5rem;
            color: var(--forest-green);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.8rem;
            color: var(--text-light);
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background-color: #f0f0f0;
            color: var(--forest-green);
        }

        .modal-body p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }

        .modal-body h3 {
            margin-top: 1.5rem;
            color: var(--forest-green);
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
            .register-card {
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
                    <a href="login.php" data-i18n="nav.login">Se connecter</a>
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

    <!-- Register Section -->
    <section class="register-section">
        <div class="container">
            <div class="register-card">
                <h1 data-i18n="register.title">Créez Votre Compte Gratuitement</h1>

                <!-- Messages d'erreur/succès -->
                <?php if (!empty($_SESSION['errors'])): ?>
                    <div style="color: red; margin: 1rem 0; padding: 0.8rem; background: #f8d7da; border-radius: 5px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                

                <form id="register-form" action="register_process.php" method="POST">
                    <div class="form-group">
                        <label for="firstname" data-i18n="register.firstname">Prénom</label>
                        <input type="text" id="firstname" name="firstname" placeholder="Jean" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['firstname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastname" data-i18n="register.lastname">Nom</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Dupont" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['lastname'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" data-i18n="register.email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" placeholder="vous@exemple.com" 
                               value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password" data-i18n="register.password">Mot de passe</label>
                        <div style="position: relative;">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="••••••••" 
                                required
                                aria-describedby="password-requirements"
                            >
                            <button 
                                type="button" 
                                id="toggle-password" 
                                style="
                                    position: absolute; 
                                    right: 10px; 
                                    top: 50%; 
                                    transform: translateY(-50%); 
                                    background: none; 
                                    border: none; 
                                    cursor: pointer; 
                                    font-size: 1.2rem;"
                                aria-label="Afficher le mot de passe"
                            >
                                🕶️
                            </button>
                        </div>
                        <div class="password-requirements" id="password-requirements">
                            <span data-i18n="register.password_req">Le mot de passe doit contenir :</span>
                            <ul>
                                <li data-i18n="register.pass_8">8 caractères minimum</li>
                                <li data-i18n="register.pass_upper">Une majuscule</li>
                                <li data-i18n="register.pass_lower">Une minuscule</li>
                                <li data-i18n="register.pass_digit">Un chiffre</li>
                                <li data-i18n="register.pass_special">Un caractère spécial (!@#$%^&*)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm" data-i18n="register.confirm">Confirmer le mot de passe</label>
                        <input type="password" id="confirm" name="confirm" placeholder="••••••••" required>
                        <div id="confirm-error" style="color: red; font-size: 0.85rem; margin-top: 0.3rem; display: none;" data-i18n="register.confirm_error">
                            Les mots de passe ne correspondent pas.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="country" data-i18n="register.country">Pays</label>
                        <select id="country" name="country" required>
                            <option value="fr" <?= ($_SESSION['form_data']['country'] ?? '') === 'fr' ? 'selected' : '' ?>>France</option>
                            <option value="be" <?= ($_SESSION['form_data']['country'] ?? '') === 'be' ? 'selected' : '' ?>>Belgique</option>
                            <option value="ch" <?= ($_SESSION['form_data']['country'] ?? '') === 'ch' ? 'selected' : '' ?>>Suisse</option>
                            <option value="ca" <?= ($_SESSION['form_data']['country'] ?? '') === 'ca' ? 'selected' : '' ?>>Canada</option>
                            <option value="other" <?= ($_SESSION['form_data']['country'] ?? '') === 'other' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="terms" required> 
                            <span data-i18n="register.terms_label">J'accepte les conditions d'utilisation</span>
                        </label>
                    </div>
                    <div class="form-group" style="margin-top: 0.5rem; font-size: 0.95rem; color: var(--text-light);">
                        <p>
                            <a href="#" class="modal-link" data-modal="terms">
                                <span data-i18n="footer.terms">Conditions d'Utilisation</span>
                            </a> 
                            &nbsp;•&nbsp;
                            <a href="#" class="modal-link" data-modal="privacy">
                                <span data-i18n="footer.privacy">Politique de Confidentialité</span>
                            </a>
                        </p>
                    </div>
                    <button type="submit" class="cta-button" data-i18n="register.submit">S'inscrire gratuitement</button>
                </form>
                <div class="login-link">
                    <span data-i18n="register.already">Déjà un compte ?</span>
                    <a href="login.php" data-i18n="register.login"> Se connecter</a>
                </div>
            </div>
        </div>
    </section>

    <!-- MODALS -->
    <div id="terms-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" data-i18n="modal.terms_title">Conditions d'Utilisation</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.terms_p1">Bienvenue sur Yggdrasil. En utilisant notre service, vous acceptez ces conditions.</p>
                <h3 data-i18n="modal.terms_1">1. Acceptation des conditions</h3>
                <p data-i18n="modal.terms_1_desc">L'utilisation de Yggdrasil implique l'acceptation de ces conditions.</p>
                <h3 data-i18n="modal.terms_2">2. Compte utilisateur</h3>
                <p data-i18n="modal.terms_2_desc">Vous êtes responsable de la sécurité de votre mot de passe.</p>
                <h3 data-i18n="modal.terms_3">3. Données familiales</h3>
                <p data-i18n="modal.terms_3_desc">Vous garantissez que les données saisies respectent la vie privée des personnes.</p>
            </div>
        </div>
    </div>

    <div id="privacy-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" data-i18n="modal.privacy_title">Politique de Confidentialité</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <p data-i18n="modal.privacy_p1">Nous respectons votre vie privée. Cette politique explique comment nous protégeons vos données.</p>
                <h3 data-i18n="modal.privacy_1">1. Collecte des données</h3>
                <p data-i18n="modal.privacy_1_desc">Nous collectons votre email, nom et données nécessaires à la création de l'arbre.</p>
                <h3 data-i18n="modal.privacy_2">2. Sécurité</h3>
                <p data-i18n="modal.privacy_2_desc">Vos données sont chiffrées et stockées conformément au RGPD.</p>
                <h3 data-i18n="modal.privacy_3">3. Partage</h3>
                <p data-i18n="modal.privacy_3_desc">Aucune donnée n'est vendue. Le partage se fait uniquement avec les membres invités.</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Yggdrasil. 
                <a href="#" class="modal-link" data-modal="privacy" data-i18n="footer.privacy">Confidentialité</a> • 
                <a href="#" class="modal-link" data-modal="terms" data-i18n="footer.terms">CGU</a>
            </p>
        </div>
    </footer>

    <!-- SCRIPTS -->
    <script>
        // === DICTIONNAIRE DE TRADUCTION ===
        const translations = {
            fr: {
                'nav.home': 'Accueil',
                'nav.login': 'Se connecter',
                'register.title': 'Créez Votre Compte Gratuitement',
                'register.firstname': 'Prénom',
                'register.lastname': 'Nom',
                'register.email': 'Adresse e-mail',
                'register.password': 'Mot de passe',
                'register.confirm': 'Confirmer le mot de passe',
                'register.country': 'Pays',
                'register.password_req': 'Le mot de passe doit contenir :',
                'register.pass_8': '8 caractères minimum',
                'register.pass_upper': 'Une majuscule',
                'register.pass_lower': 'Une minuscule',
                'register.pass_digit': 'Un chiffre',
                'register.pass_special': 'Un caractère spécial (!@#$%^&*)',
                'register.confirm_error': 'Les mots de passe ne correspondent pas.',
                'register.terms_label': 'J\'accepte les conditions d\'utilisation',
                'register.submit': 'S\'inscrire gratuitement',
                'register.already': 'Déjà un compte ?',
                'register.login': ' Se connecter',
                'footer.privacy': 'Confidentialité',
                'footer.terms': 'CGU',
                'access.dyslexia': 'Mode Dyslexie',
                'access.dark': 'Passer en mode sombre',
                'modal.terms_title': 'Conditions d\'Utilisation',
                'modal.terms_p1': 'Bienvenue sur Yggdrasil. En utilisant notre service, vous acceptez ces conditions.',
                'modal.terms_1': '1. Acceptation des conditions',
                'modal.terms_1_desc': 'L\'utilisation de Yggdrasil implique l\'acceptation de ces conditions.',
                'modal.terms_2': '2. Compte utilisateur',
                'modal.terms_2_desc': 'Vous êtes responsable de la sécurité de votre mot de passe.',
                'modal.terms_3': '3. Données familiales',
                'modal.terms_3_desc': 'Vous garantissez que les données saisies respectent la vie privée des personnes.',
                'modal.privacy_title': 'Politique de Confidentialité',
                'modal.privacy_p1': 'Nous respectons votre vie privée. Cette politique explique comment nous protégeons vos données.',
                'modal.privacy_1': '1. Collecte des données',
                'modal.privacy_1_desc': 'Nous collectons votre email, nom et données nécessaires à la création de l\'arbre.',
                'modal.privacy_2': '2. Sécurité',
                'modal.privacy_2_desc': 'Vos données sont chiffrées et stockées conformément au RGPD.',
                'modal.privacy_3': '3. Partage',
                'modal.privacy_3_desc': 'Aucune donnée n\'est vendue. Le partage se fait uniquement avec les membres invités.'
            },
            en: {
                'nav.home': 'Home',
                'nav.login': 'Sign In',
                'register.title': 'Create Your Free Account',
                'register.firstname': 'First Name',
                'register.lastname': 'Last Name',
                'register.email': 'Email Address',
                'register.password': 'Password',
                'register.confirm': 'Confirm Password',
                'register.country': 'Country',
                'register.password_req': 'Password must contain:',
                'register.pass_8': '8 characters minimum',
                'register.pass_upper': 'One uppercase letter',
                'register.pass_lower': 'One lowercase letter',
                'register.pass_digit': 'One digit',
                'register.pass_special': 'One special character (!@#$%^&*)',
                'register.confirm_error': 'Passwords do not match.',
                'register.terms_label': 'I accept the terms of use',
                'register.submit': 'Sign Up Free',
                'register.already': 'Already have an account?',
                'register.login': ' Sign in',
                'footer.privacy': 'Privacy',
                'footer.terms': 'Terms',
                'access.dyslexia': 'Dyslexia Mode',
                'access.dark': 'Switch to Dark Mode',
                'modal.terms_title': 'Terms of Service',
                'modal.terms_p1': 'Welcome to Yggdrasil. By using our service, you agree to these terms.',
                'modal.terms_1': '1. Acceptance of Terms',
                'modal.terms_2': '2. User Account',
                'modal.terms_3': '3. Family Data',
                'modal.terms_1_desc': 'Using Yggdrasil means you accept these terms.',
                'modal.terms_2_desc': 'You are responsible for the security of your password.',
                'modal.terms_3_desc': 'You guarantee that the data entered respects individuals\' privacy.',
                'modal.privacy_title': 'Privacy Policy',
                'modal.privacy_p1': 'We respect your privacy. This policy explains how we protect your data.',
                'modal.privacy_1': '1. Data Collection',
                'modal.privacy_2': '2. Security',
                'modal.privacy_3': '3. Sharing',
                'modal.privacy_1_desc': 'We collect your email, name, and data needed to build your tree.',
                'modal.privacy_2_desc': 'Your data is encrypted and stored in compliance with GDPR.',
                'modal.privacy_3_desc': 'No data is sold. Sharing is only with invited members.'
            },
            br: {
                'nav.home': 'Degemer',
                'nav.login': 'Kevreañ',
                'register.title': 'Krouit Ho Kont Amankavel',
                'register.firstname': 'Anv krenn',
                'register.lastname': 'Anv familh',
                'register.email': 'Chomlec\'h postel',
                'register.password': 'Ger-tremen',
                'register.confirm': 'Kadarnaat ar ger-tremen',
                'register.country': 'Bro',
                'register.password_req': 'Ezhomm eo ur ger-tremen gant :',
                'register.pass_8': '8 arouez minimum',
                'register.pass_upper': 'Ul luc\'hskrid uhel',
                'register.pass_lower': 'Ul luc\'hskrid bihan',
                'register.pass_digit': 'Unek niver',
                'register.pass_special': 'Unek arouez dibar (!@#$%^&*)',
                'register.confirm_error': 'Ne glot ket ar gerioù-tremen.',
                'register.terms_label': 'On aendek ar ranneloù implijout',
                'register.submit': 'Izili a-bell',
                'register.already': 'Gant ur gont dija ?',
                'register.login': ' Kevreañ',
                'footer.privacy': 'Aotre',
                'footer.terms': 'Ranneloù',
                'access.dyslexia': 'Mod Dyslexie',
                'access.dark': 'Trec\'h da gouloù tiñj',
                'modal.terms_title': 'Ranneloù Implijout',
                'modal.terms_p1': 'Degemer mat war Yggdrasil. En ur implijout al lec\'hiadur, e gemerit ar ranneloù-mañ.',
                'modal.terms_1': '1. Aendekañ ar ranneloù',
                'modal.terms_2': '2. Kont implijer',
                'modal.terms_3': '3. Titouroù teul',
                'modal.terms_1_desc': 'Implijout Yggdrasil a zo aendekañ ar ranneloù.',
                'modal.terms_2_desc': 'Ho peus fiñv da geskar ar ger-tremen.',
                'modal.terms_3_desc': 'Gouzout a ra bout peurlies ma vo enrollet ar titouroù en ur gomziant.',
                'modal.privacy_title': 'Politegerez Aotre',
                'modal.privacy_p1': 'Respectet eo ho privorez. Ar polisig-mañ a esplik kenurzh ho titouroù.',
                'modal.privacy_1': '1. Kevreañ titouroù',
                'modal.privacy_2': '2. Surenti',
                'modal.privacy_3': '3. Rannañ',
                'modal.privacy_1_desc': 'Kevreomp ho chomlec\'h, ho anv ha titouroù evit sevel ar gernezenn.',
                'modal.privacy_2_desc': 'Ho titouroù a zo enrollet en ur gomziant GDPR.',
                'modal.privacy_3_desc': 'Titour ne vo ket vendet. Ar rannañ a zo nemet gant ar re kerkent.'
            }
        };

        // Ordre des langues
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
                        el.innerHTML = translations[lang][key];
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
            document.getElementById('lang-toggle').addEventListener('click', () => {
                const currentLang = localStorage.getItem('language') || 'fr';
                const currentIndex = languages.indexOf(currentLang);
                const nextLang = languages[(currentIndex + 1) % languages.length];
                setLanguage(nextLang);
            });

            // === VALIDATION MOT DE PASSE ===
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm');
            const confirmError = document.getElementById('confirm-error');
            const requirements = document.querySelectorAll('.password-requirements li');

            const checks = {
                length: false,
                upper: false,
                lower: false,
                digit: false,
                special: false
            };

            function validatePassword() {
                const pwd = passwordInput.value;

                checks.length = pwd.length >= 8;
                checks.upper = /[A-Z]/.test(pwd);
                checks.lower = /[a-z]/.test(pwd);
                checks.digit = /\d/.test(pwd);
                checks.special = /[!@#$%^&*]/.test(pwd);

                requirements.forEach((li, index) => {
                    const keys = ['pass_8', 'pass_upper', 'pass_lower', 'pass_digit', 'pass_special'];
                    li.classList.toggle('valid', checks[keys[index]]);
                });
            }

            function validateConfirm() {
                if (confirmInput.value && confirmInput.value !== passwordInput.value) {
                    confirmError.style.display = 'block';
                } else {
                    confirmError.style.display = 'none';
                }
            }

            passwordInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', validateConfirm);

            // Pré-remplir la validation si mot de passe déjà saisi
            if (passwordInput.value) validatePassword();

            // === MODALS ===
            const termsModal = document.getElementById('terms-modal');
            const privacyModal = document.getElementById('privacy-modal');
            const closeModal = document.querySelectorAll('.modal-close');
            const modalLinks = document.querySelectorAll('.modal-link');

            modalLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalType = link.getAttribute('data-modal');
                    if (modalType === 'terms') {
                        termsModal.style.display = 'flex';
                    } else if (modalType === 'privacy') {
                        privacyModal.style.display = 'flex';
                    }
                });
            });

            closeModal.forEach(btn => {
                btn.addEventListener('click', () => {
                    termsModal.style.display = 'none';
                    privacyModal.style.display = 'none';
                });
            });

            window.addEventListener('click', (e) => {
                if (e.target === termsModal) termsModal.style.display = 'none';
                if (e.target === privacyModal) privacyModal.style.display = 'none';
            });

            // === MODE DYSLEXIE & SOMBRE ===
            const dyslexiaToggle = document.getElementById('dyslexia-toggle');
            const themeToggle = document.getElementById('theme-toggle');
            const body = document.body;

            if (localStorage.getItem('dyslexiaMode') === 'true') body.classList.add('opendyslexic');
            dyslexiaToggle.addEventListener('click', () => {
                body.classList.toggle('opendyslexic');
                localStorage.setItem('dyslexiaMode', body.classList.contains('opendyslexic') ? 'true' : 'false');
            });

            if (localStorage.getItem('theme') === 'dark') body.classList.add('dark-mode');
            themeToggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                localStorage.setItem('theme', body.classList.contains('dark-mode') ? 'dark' : 'light');
            });

            // === TOGGLE MOT DE PASSE ===
            const toggleButton = document.getElementById('toggle-password');
            let isPasswordVisible = false;

            if (toggleButton) {
                toggleButton.addEventListener('click', function () {
                    isPasswordVisible = !isPasswordVisible;
                    passwordInput.type = isPasswordVisible ? 'text' : 'password';
                    toggleButton.textContent = isPasswordVisible ? '🙈' : '👁️';
                    toggleButton.setAttribute('aria-label', 
                        isPasswordVisible ? 'Masquer le mot de passe' : 'Afficher le mot de passe'
                    );
                });
            }

            setLanguage(initialLang);
        });
    </script>
</body>
</html>