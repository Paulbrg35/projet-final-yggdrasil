<?php
session_start();
require_once 'config.php'; // Assurez-vous que config.php définit YGGDRASIL_CONFIG

// Désactiver l'affichage des erreurs en production
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] !== 'development') {
    ini_set('display_errors', 0);
    error_reporting(0);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

$errors = [];

try {
    // Vérifier que la méthode est POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Accès refusé');
    }

    // Protection CSRF basique
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
        error_log("Tentative CSRF détectée depuis " . ($_SERVER['REMOTE_ADDR'] ?? 'inconnu'));
    }

    // Récupérer l'email
    $email = trim($_POST['email'] ?? '');

    // Validation de l'email
    if (empty($email)) {
        $errors[] = "L'adresse email est requise.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Le format de l'adresse email est invalide.";
    } elseif (strlen($email) > 254) {
        $errors[] = "L'adresse email est trop longue.";
    }

    if (!empty($errors)) {
        throw new Exception('Données invalides');
    }

    // Nettoyer les tokens expirés
    try {
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
    } catch (Exception $e) {
        error_log("Erreur nettoyage tokens : " . $e->getMessage());
    }

    // Limiter les tentatives (1 demande toutes les 15 minutes max)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM password_resets 
        WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
    ");
    $stmt->execute([$email]);
    $attempts = $stmt->fetchColumn();

    if ($attempts >= 3) {
        error_log("Trop de tentatives pour $email depuis $ip");
        $errors[] = "Trop de tentatives. Veuillez attendre 15 minutes avant de réessayer.";
        throw new Exception('Rate limit exceeded');
    }

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT id, nom FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Message de succès (toujours le même, pour éviter l'énumération)
    $success_message = "Si cette adresse email est enregistrée, vous recevrez un lien de réinitialisation dans quelques instants.";

    if (!$user) {
        // Délai pour éviter l'énumération d'emails
        usleep(rand(100000, 500000));
        $_SESSION['reset_success'] = $success_message;
        header('Location: forgot-password.php');
        exit;
    }

    // Générer un token sécurisé
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Commencer une transaction
    $pdo->beginTransaction();

    try {
        // Supprimer les anciens tokens pour cet email
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);

        // Insérer le nouveau token
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, email, token, expires_at, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user['id'], $email, $token, $expires_at]);

        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }

    // Créer le lien de réinitialisation
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    $reset_link = "$protocol://$host$path/reset-password.php?token=" . urlencode($token);

    // Sujet de l'email
    $subject = "Réinitialisation de votre mot de passe - Yggdrasil";

    // Corps de l'email (HTML)
    $message = "
    <html>
    <head>
        <title>Réinitialisation de mot de passe</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px; }
            .header { background: #2E5D42; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { padding: 20px; }
            .button { display: inline-block; background: #D4AF37; color: #2E5D42; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; margin: 20px 0; }
            .footer { margin-top: 30px; font-size: 0.9em; color: #666; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Yggdrasil</h1>
            </div>
            <div class='content'>
                <h2>Réinitialisation de mot de passe</h2>
                <p>Bonjour <strong>" . htmlspecialchars($user['nom'] ?? 'Membre') . "</strong>,</p>
                <p>Vous avez demandé la réinitialisation de votre mot de passe. Cliquez sur le bouton ci-dessous pour continuer :</p>
                <p style='text-align: center;'>
                    <a href='$reset_link' class='button'>Réinitialiser mon mot de passe</a>
                </p>
                <p><strong>Ce lien expirera dans 1 heure.</strong></p>
                <p>Si vous n'avez pas fait cette demande, veuillez ignorer cet email.</p>
            </div>
            <div class='footer'>
                <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // En-têtes de l'email
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: Yggdrasil <noreply@yggdrasil.bzh>',
        'Reply-To: support@yggdrasil.bzh',
        'X-Mailer: PHP/' . phpversion()
    ];

    // Envoyer l'email
    $mail_sent = mail($email, $subject, $message, implode("\r\n", $headers));

    if ($mail_sent) {
        $_SESSION['reset_success'] = $success_message;
        error_log("Lien de réinitialisation envoyé à $email");
    } else {
        throw new Exception("Échec de l'envoi de l'email");
    }

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("Erreur DB dans forgot-password-process.php : " . $e->getMessage());
    $errors[] = "Erreur de base de données. Veuillez réessayer.";
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("Erreur dans forgot-password-process.php : " . $e->getMessage());
    if (!in_array($e->getMessage(), ['Données invalides', 'Rate limit exceeded'])) {
        $errors[] = "Une erreur est survenue. Veuillez réessayer.";
    }
}

// Gérer les erreurs
if (!empty($errors)) {
    $_SESSION['reset_errors'] = $errors;
}

// Redirection
header('Location: forgot-password.php');
exit;
?>