<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

$token = $_GET['token'] ?? '';
if (empty($token)) {
    die("Lien invalide.");
}

$stmt = $pdo->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset || strtotime($reset['expires_at']) < time()) {
    die("Lien expiré ou invalide.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Réinitialiser le mot de passe</title>
    <style>
        body { font-family: 'Lato', sans-serif; text-align: center; margin: 10vh auto; max-width: 400px; }
        input { width: 100%; padding: 0.8rem; margin: 0.5rem 0; }
        button { background: #D4AF37; color: #2E5D42; padding: 1rem; width: 100%; }
    </style>
</head>
<body>
    <h2>Réinitialiser votre mot de passe</h2>
    <form action="reset-password-process.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        <input type="password" name="confirm" placeholder="Confirmer" required>
        <button type="submit">Réinitialiser</button>
    </form>
    <a href="login.php">← Retour à la connexion</a>
</body>
</html>