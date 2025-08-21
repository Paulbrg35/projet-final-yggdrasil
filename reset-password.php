<?php
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Lien invalide.");
}

$stmt = getDatabase()->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    die("Lien invalide ou expiré.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Réinitialiser le mot de passe</title>
    <style>
        body { font-family: 'Lato', sans-serif; background: #FFF9F0; text-align: center; padding: 10vh 20px; }
        .reset-card { max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        input, button { width: 100%; padding: 0.8rem; margin: 0.5rem 0; }
        button { background: #D4AF37; color: #2E5D42; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="reset-card">
        <h2>Réinitialiser votre mot de passe</h2>

        <?php if (isset($_SESSION['reset_errors'])): ?>
            <div class="error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($_SESSION['reset_errors'] as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['reset_errors']); ?>
        <?php endif; ?>

        <form action="reset-password-process.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm" placeholder="Confirmer le mot de passe" required>
            <button type="submit">Réinitialiser</button>
        </form>

        <a href="login.php">← Retour à la connexion</a>
    </div>
</body>
</html>