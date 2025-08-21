<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';
/** @var PDO $pdo */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

$token = $_GET['token'] ?? '';

// Si pas de token
if (empty($token)) {
    $_SESSION['reset_error'] = "Lien invalide ou expiré.";
    header("Location: forgot-password.php");
    exit();
}

// Vérifier si le token existe
try {
    $stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        $_SESSION['reset_error'] = "Lien invalide ou expiré.";
        header("Location: forgot-password.php");
        exit();
    }

    // Vérifier l'expiration
    if (strtotime($reset['expires_at']) < time()) {
        $_SESSION['reset_error'] = "Ce lien a expiré.";
        // Supprimer le token expiré
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);
        header("Location: forgot-password.php");
        exit();
    }
} catch (Exception $e) {
    error_log("Erreur vérification token : " . $e->getMessage());
    $_SESSION['reset_error'] = "Erreur serveur.";
    header("Location: forgot-password.php");
    exit();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $errors = [];

    // Validation du mot de passe
    if (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une majuscule.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins une minuscule.";
    }
    if (!preg_match('/\d/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un chiffre.";
    }
    if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
        $errors[] = "Le mot de passe doit contenir au moins un caractère spécial.";
    }
    if ($password !== $confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        try {
            // Commencer une transaction
            $pdo->beginTransaction();

            // Hacher le nouveau mot de passe
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            // Mettre à jour l'utilisateur
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $result = $stmt->execute([$hashed, $reset['email']]);

            if (!$result) {
                throw new Exception("Échec de la mise à jour du mot de passe");
            }

            // Supprimer TOUS les tokens de réinitialisation pour cet email
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$reset['email']]);

            // Valider la transaction
            $pdo->commit();

            // Message de succès
            $_SESSION['login_success'] = "Votre mot de passe a été mis à jour avec succès. Vous pouvez maintenant vous connecter.";
            header("Location: login.php");
            exit();

        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $pdo->rollback();
            error_log("Erreur mise à jour mot de passe : " . $e->getMessage());
            $errors[] = "Une erreur est survenue. Veuillez réessayer.";
        }
    }

    // En cas d'erreur, stocker les erreurs en session
    if (!empty($errors)) {
        $_SESSION['reset_errors'] = $errors;
        header("Location: reset-password.php?token=" . urlencode($token));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Yggdrasil</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&family=Lato:wght@400;700&family=Cormorant+Garamond:wght@700&display=swap" rel="stylesheet">

    <style>
        :root {
            --forest-green: #2E5D42;
            --gold: #D4AF37;
            --cream: #F8F5F0;
            --text-dark: #333;
            --border-color: #E0D8C8;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #FFF9F0;
            color: #333;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 420px;
            margin: 5rem auto;
            padding: 2.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: var(--forest-green);
            font-family: 'Cormorant Garamond', serif;
            margin-top: 0;
            margin-bottom: 1.5rem;
        }

        .requirements {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            text-align: left;
            font-size: 0.9rem;
        }

        .requirements h4 {
            margin: 0 0 0.5rem 0;
            color: var(--forest-green);
        }

        .requirements ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .form-group {
            text-align: left;
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        input[type="password"] {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
        }

        .error-list {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            text-align: left;
            font-size: 0.95rem;
            border-left: 4px solid #dc3545;
        }

        .error-list ul {
            margin: 0;
            padding-left: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }

        button {
            background-color: var(--gold);
            color: var(--forest-green);
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        button:hover {
            background-color: #c49b2a;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        button:active {
            transform: translateY(0);
        }

        .login-link {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            color: #666;
        }

        .login-link a {
            color: var(--forest-green);
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--gold);
        }

        /* Indicateur de force du mot de passe */
        .password-strength {
            margin-top: 0.5rem;
            height: 4px;
            background: #eee;
            border-radius: 2px;
            overflow: hidden;
        }

        .strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        .strength-weak { background: #dc3545; width: 33%; }
        .strength-medium { background: #ffc107; width: 66%; }
        .strength-strong { background: #28a745; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Réinitialiser votre mot de passe</h2>

        <!-- Exigences du mot de passe -->
        <div class="requirements">
            <h4>Exigences du mot de passe :</h4>
            <ul>
                <li>Au moins 8 caractères</li>
                <li>Une lettre majuscule</li>
                <li>Une lettre minuscule</li>
                <li>Un chiffre</li>
                <li>Un caractère spécial (!@#$%^&*...)</li>
            </ul>
        </div>

        <!-- Messages d'erreur -->
        <?php if (isset($_SESSION['reset_errors'])): ?>
            <div class="error-list">
                <ul>
                    <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['reset_errors']); ?>
        <?php endif; ?>

        <!-- Formulaire -->
        <form method="POST">
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                <div class="password-strength">
                    <div class="strength-bar" id="strengthBar"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm">Confirmer le mot de passe</label>
                <input type="password" id="confirm" name="confirm" placeholder="••••••••" required>
            </div>

            <button type="submit">Réinitialiser le mot de passe</button>
        </form>

        <div class="login-link">
            <a href="login.php">← Retour à la connexion</a>
        </div>
    </div>

    <script>
        // Indicateur de force du mot de passe
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('strengthBar');
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength++;

            strengthBar.className = 'strength-bar';
            if (strength <= 2) {
                strengthBar.classList.add('strength-weak');
            } else if (strength <= 4) {
                strengthBar.classList.add('strength-medium');
            } else {
                strengthBar.classList.add('strength-strong');
            }
        });
    </script>
</body>
</html>