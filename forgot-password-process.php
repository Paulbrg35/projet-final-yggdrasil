<?php
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Accès refusé.");
}

$errors = [];
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    // Validation CSRF
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        throw new Exception('Token de sécurité invalide');
    }

    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $errors[] = "L'adresse email est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format d'email invalide.";
    }

    if (!empty($errors)) {
        throw new Exception('Données invalides');
    }

    // Rate limiting
    $stmt = getDatabase()->prepare("SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() >= 3) {
        throw new Exception('Trop de tentatives. Attendez 15 minutes.');
    }

    // Nettoyer les tokens expirés
    try {
        $stmt = getDatabase()->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Erreur nettoyage tokens : " . $e->getMessage());
    }

    // Vérifier si l'utilisateur existe
    $stmt = getDatabase()->prepare("SELECT id, firstname, email FROM users WHERE email = ? AND active = 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $success_message = "Si cet email est enregistré, vous recevrez un lien de réinitialisation.";

    // Enregistrer la tentative
    $user_id = $user ? $user['id'] : null;
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);

    $stmt = getDatabase()->prepare("INSERT INTO password_resets (user_id, email, token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $email, $token, $expires_at, $ip, $user_agent]);

    if (!$user) {
        usleep(rand(500000, 1500000));
        $_SESSION['reset_success'] = $success_message;
    } else {
        $pdo = getDatabase();
        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND user_id IS NOT NULL");
            $stmt->execute([$email]);

            $new_token = bin2hex(random_bytes(32));
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user['id'], $email, $new_token, $expires_at, $ip, $user_agent]);

            $pdo->commit();

            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'];
            $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
            $reset_link = "$protocol://$host$path/reset-password.php?token=" . urlencode($new_token);

            $subject = "Réinitialisation de mot de passe - Yggdrasil";
            $message = "Cliquez sur ce lien pour réinitialiser votre mot de passe : $reset_link (valide 1 heure)";
            $headers = "From: noreply@yggdrasil.bzh";

            if (mail($email, $subject, $message, $headers)) {
                $_SESSION['reset_success'] = $success_message;
                error_log("Email envoyé à $email");
            } else {
                $_SESSION['reset_success'] = $success_message;
            }

        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollback();
            throw $e;
        }
    }

} catch (Exception $e) {
    $error_msg = $e->getMessage();
    error_log("Erreur : $error_msg - IP: $ip");
    $errors[] = "Une erreur est survenue. Veuillez réessayer.";
}

if (!empty($errors)) {
    $_SESSION['reset_errors'] = $errors;
}

unset($_SESSION['csrf_token']);
header('Location: forgot-password.php');
exit;
?>