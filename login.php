<?php
// Activer le mode debug en développement
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Démarrer la session (UNE SEULE FOIS)
session_start();

// Rediriger si déjà connecté
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Inclure la config
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

// Variable pour stocker les erreurs
$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation des champs
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Rechercher l'utilisateur
            $stmt = getDatabase()->prepare("SELECT id, firstname, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            // Vérifier le mot de passe
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['firstname'] = $user['firstname'];

                // "Se souvenir de moi"
                if (isset($_POST['remember'])) {
                    $token = bin2hex(random_bytes(32));
                    $expires = time() + (30 * 24 * 60 * 60); // 30 jours

                    $stmt = getDatabase()->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                    $stmt->execute([$token, $user['id']]);

                    setcookie('remember', $token, $expires, '/', '', false, true);
                }

                // Rediriger vers le tableau de bord
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        } catch (Exception $e) {
            error_log("Erreur connexion : " . $e->getMessage());
            $error = "Erreur serveur. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="darkreader-disable" content="true">
    <meta name="color-scheme" content="light">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Yggdrasil</title>

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

        /* Login Section */
        .login-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 2rem 0;
        }

        .login-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            text-align: center;
        }

        .login-card h1 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            color: var(--forest-green);
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

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        .remember-forgot a {
            color: var(--forest-green);
            text-decoration: none;
        }

        .remember-forgot a:hover {
            text-decoration: underline;
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

        .signup-link {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .signup-link a {
            color: var(--forest-green);
            font-weight: bold;
            text-decoration: none;
        }

        .signup-link a:hover {
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
            .login-card {
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
                    <a href="register.php" data-i18n="nav.register">S'inscrire</a>
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

    <!-- Section de connexion -->
    <section class="login-section">
        <div class="container">
            <div class="login-card">
                <h1 data-i18n="login.title">Connexion à votre compte</h1>

                <!-- Message d'erreur -->
                <?php if ($error): ?>
                    <div class="error-message">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email" data-i18n="login.email">Adresse e-mail</label>
                        <input type="email" id="email" name="email" placeholder="vous@exemple.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password" data-i18n="login.password">Mot de passe</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    <div class="remember-forgot">
                        <label>
                            <input type="checkbox" name="remember"> <span data-i18n="login.remember">Se souvenir de moi</span>
                        </label>
                        <a href="forgot-password.php" data-i18n="login.forgot">Mot de passe oublié ?</a>
                    </div>
                    <button type="submit" class="cta-button" data-i18n="login.submit">Se connecter</button>
                </form>
                <div class="signup-link">
                    <span data-i18n="login.noaccount">Pas encore de compte ?</span>
                    <a href="register.php" data-i18n="login.register"> S'inscrire gratuitement</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2025 Yggdrasil. 
                <a href="privacy.html" data-i18n="footer.privacy">Confidentialité</a> • 
                <a href="terms.html" data-i18n="footer.terms">CGU</a>
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // === DICTIONNAIRE DE TRADUCTION ===
        const translations = {
            fr: {
                'nav.home': 'Accueil',
                'nav.register': 'S\'inscrire',
                'access.dyslexia': 'Mode Dyslexie',
                'access.dark': 'Passer en mode sombre',
                'login.title': 'Connexion à votre compte',
                'login.email': 'Adresse e-mail',
                'login.password': 'Mot de passe',
                'login.remember': 'Se souvenir de moi',
                'login.forgot': 'Mot de passe oublié ?',
                'login.submit': 'Se connecter',
                'login.noaccount': 'Pas encore de compte ?',
                'login.register': ' S\'inscrire gratuitement',
                'footer.privacy': 'Confidentialité',
                'footer.terms': 'CGU'
            },
            en: {
                'nav.home': 'Home',
                'nav.register': 'Sign Up',
                'access.dyslexia': 'Dyslexia Mode',
                'access.dark': 'Switch to Dark Mode',
                'login.title': 'Sign In to Your Account',
                'login.email': 'Email Address',
                'login.password': 'Password',
                'login.remember': 'Remember me',
                'login.forgot': 'Forgot password?',
                'login.submit': 'Sign In',
                'login.noaccount': 'Don\'t have an account?',
                'login.register': ' Sign up for free',
                'footer.privacy': 'Privacy',
                'footer.terms': 'Terms'
            },
            br: {
                'nav.home': 'Degemer',
                'nav.register': 'Izili',
                'access.dyslexia': 'Mod Dyslexie',
                'access.dark': 'Trec\'h da gouloù tiñj',
                'login.title': 'Kevreañ da hoc\'h kont',
                'login.email': 'Chomlec\'h postel',
                'login.password': 'Ger-tremen',
                'login.remember': 'Souvenit oc\'h eus va',
                'login.forgot': 'Ker-tremen forgetet ?',
                'login.submit': 'Kevreañ',
                'login.noaccount': 'N\'hoc\'h ket gant ur gont ?',
                'login.register': ' Izili a-bell',
                'footer.privacy': 'Privacy',
                'footer.terms': 'Ranneloù'
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
    </script>
</body>
</html>