<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

// Générer un token CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Yggdrasil</title>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Cormorant+Garamond:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.cdnfonts.com/css/open-dyslexic" rel="stylesheet">
    <style>
        :root {
            --forest-green: #2E5D42;
            --gold: #D4AF37;
            --bg-body: #FFF9F0;
            --text-color: #333;
            --card-bg: #FFFFFF;
            --border-color: #E0D8C8;
        }
        .dark-mode {
            --bg-body: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #222;
            --border-color: #444;
        }
        body {
            font-family: 'Lato', sans-serif;
            background: var(--bg-body);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 400px;
            margin: 10vh auto;
            padding: 2rem;
        }
        .forgot-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
        }
        .forgot-card h1 {
            color: var(--forest-green);
            font-family: 'Cormorant Garamond', serif;
        }
        .form-group {
            text-align: left;
            margin: 1.5rem 0;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
        }
        .cta-button {
            background: var(--gold);
            color: var(--forest-green);
            padding: 0.9rem;
            border: none;
            border-radius: 5px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
        }
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.8rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: left;
        }
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 0.8rem;
            border-radius: 5px;
            margin: 1rem 0;
            text-align: left;
        }
        .login-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .login-link a {
            color: var(--forest-green);
            text-decoration: none;
        }
        .opendyslexic {
            font-family: 'OpenDyslexic', sans-serif !important;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-card">
            <h1>Mot de passe oublié ?</h1>
            <p>Entrez votre email, nous vous enverrons un lien sécurisé.</p>

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

            <?php if (isset($_SESSION['reset_success'])): ?>
                <div class="success-message">
                    <?= htmlspecialchars($_SESSION['reset_success']) ?>
                </div>
                <?php unset($_SESSION['reset_success']); ?>
            <?php endif; ?>

            <form action="forgot-password-process.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="cta-button">Envoyer le lien</button>
            </form>

            <div class="login-link">
                <a href="login.php">← Retour à la connexion</a>
            </div>
        </div>
    </div>
</body>
</html>