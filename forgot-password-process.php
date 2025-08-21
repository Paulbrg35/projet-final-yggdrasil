<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: forgot-password.php');
    exit;
}

$errors = [];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    // CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf_token)) {
        throw new Exception('Token invalide');
    }

    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email invalide";
    }

    if (!empty($errors)) {
        throw new Exception('Données invalides');
    }

    // Rate limiting
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() >= 3) {
        throw new Exception('Trop de tentatives. Attendez 15 minutes.');
    }

    // Supprimer les anciens tokens
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$email]);

    // Générer token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$email, $token, $expires]);

    // Lien de réinitialisation
    $reset_link = "http://localhost/projet_final/projet-final-yggdrasil/reset-password.php?token=" . urlencode($token);

    $subject = "Réinitialisation de mot de passe - Yggdrasil";
    $message = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link (valide 15 min)";
    $headers = "From: noreply@yggdrasil.bzh";

    if (mail($email, $subject, $message, $headers)) {
        $_SESSION['reset_success'] = "Si cet email existe, vous recevrez un lien de réinitialisation.";
    } else {
        $_SESSION['reset_errors'] = ["Erreur d'envoi. Veuillez réessayer."];
    }

} catch (Exception $e) {
    $_SESSION['reset_errors'] = [htmlspecialchars($e->getMessage())];
}

unset($_SESSION['csrf_token']);
header('Location: forgot-password.php');
exit;
?>