<?php
// Activer le mode debug en développement
// ini_set('display_errors', 1); error_reporting(E_ALL);

// Démarrer la session
session_start();

// Empêcher l'accès direct
if (!defined('YGGDRASIL_CONFIG')) {
    http_response_code(403);
    die('Accès interdit');
}

// Inclure la configuration
require_once '../config.php'; // Ajustez le chemin si nécessaire

// Initialiser les variables
$errors = [];
$success = '';
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// Fonction pour obtenir l'IP réelle
function getRealIP() {
    $headers = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// === TRAITEMENT DU FORMULAIRE (si POST) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDatabase();
        $real_ip = getRealIP();

        // === 1. Validation CSRF ===
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
            throw new Exception('Token de sécurité invalide');
        }
        unset($_SESSION['csrf_token']);

        // === 2. Rate Limiting par IP (max 5 tentatives/heure) ===
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM password_resets WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$real_ip]);
        $ip_attempts = $stmt->fetchColumn();
        if ($ip_attempts >= 5) {
            error_log("Rate limit IP dépassé pour $real_ip ($ip_attempts tentatives)");
            throw new Exception('Trop de tentatives depuis cette adresse IP. Veuillez attendre une heure.');
        }

        // === 3. Validation de l'email ===
        $email = trim($_POST['email'] ?? '');
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

        // === 4. Rate Limiting par email (max 3 tentatives/15 minutes) ===
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM password_resets WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->execute([$email]);
        $email_attempts = $stmt->fetchColumn();
        if ($email_attempts >= 3) {
            error_log("Rate limit email dépassé pour $email depuis $real_ip");
            usleep(rand(500000, 1000000)); // Délai pour éviter l'énumération
            throw new Exception('Trop de tentatives pour cette adresse email. Veuillez attendre 15 minutes.');
        }

        // === 5. Nettoyer les tokens expirés ===
        try {
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur nettoyage tokens : " . $e->getMessage());
        }

        // === 6. Vérifier si l'utilisateur existe ===
        $stmt = $pdo->prepare("SELECT id, firstname, email FROM users WHERE email = ? AND active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Message de succès standardisé (pour éviter l'énumération)
        $success_message = "Si cette adresse email est enregistrée et active, vous recevrez un lien de réinitialisation dans quelques instants.";

        // Enregistrer la tentative même si l'utilisateur n'existe pas (pour le rate limiting)
        $user_id = $user ? $user['id'] : null;
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        try {
            $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
            $stmt->execute([$user_id, $email, $token, $expires_at, $real_ip, $user_agent]);
        } catch (Exception $e) {
            error_log("Erreur enregistrement tentative : " . $e->getMessage());
        }

        // Si l'utilisateur n'existe pas, simuler un délai et afficher le message
        if (!$user) {
            usleep(rand(500000, 1500000)); // Délai aléatoire
            $_SESSION['reset_success'] = $success_message;
        } else {
            // === 7. Pour utilisateur existant : générer un vrai lien ===
            $pdo->beginTransaction();
            try {
                // Supprimer les anciens tokens
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND user_id IS NOT NULL");
                $stmt->execute([$email]);

                // Générer un nouveau token
                $new_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Insérer le nouveau token
                $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
                $stmt->execute([$user['id'], $email, $new_token, $expires_at, $real_ip, $user_agent]);

                $pdo->commit();

                // === 8. Créer le lien de réinitialisation ===
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                $reset_link = "$protocol://$host$path/reset-password.php?token=" . urlencode($new_token);

                // === 9. Préparer et envoyer l'email via PHPMailer ===
                require_once '../vendor/autoload.php'; // Chemin vers PHPMailer
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'yggdrasil.genealogie@gmail.com';
                    $mail->Password   = 'votre_mot_de_passe_d_application'; // Mot de passe d'application
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('noreply@yggdrasil.bzh', 'Yggdrasil');
                    $mail->addReplyTo('support@yggdrasil.bzh', 'Support Yggdrasil');
                    $mail->addAddress($email, $user['firstname']);
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Réinitialisation de votre mot de passe - Yggdrasil';

                    $user_name = htmlspecialchars($user['firstname'], ENT_QUOTES, 'UTF-8');
                    $mail->Body = "
                        <!DOCTYPE html>
                        <html lang='fr'>
                        <head>
                            <meta charset='UTF-8'>
                            <title>Réinitialisation de mot de passe</title>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 20px; }
                                .container { max-width: 600px; margin: auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
                                .header { background: #2E5D42; color: white; padding: 30px 20px; text-align: center; }
                                .header h1 { margin: 0; font-size: 2rem; }
                                .content { padding: 30px; }
                                .content h2 { color: #2E5D42; }
                                .button { display: inline-block; background: #D4AF37; color: #2E5D42; padding: 12px 30px; margin: 20px 0; text-decoration: none; border-radius: 5px; font-weight: bold; }
                                .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 0.9rem; color: #666; }
                                .security-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
                            </style>
                        </head>
                        <body>
                            <div class='container'>
                                <div class='header'>
                                    <h1>🌳 Yggdrasil</h1>
                                </div>
                                <div class='content'>
                                    <h2>Réinitialisation de mot de passe</h2>
                                    <p>Bonjour <strong>$user_name</strong>,</p>
                                    <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Yggdrasil.</p>
                                    <div class='warning-box'>
                                        <strong>⚠️ Important :</strong> Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email.
                                    </div>
                                    <p>Pour continuer, cliquez sur le bouton ci-dessous :</p>
                                    <p style='text-align: center;'><a href='$reset_link' class='button'>Réinitialiser mon mot de passe</a></p>
                                    <p><strong>⏰ Ce lien expirera dans 1 heure pour votre sécurité.</strong></p>
                                    <div class='security-info'>
                                        <strong>Informations de sécurité :</strong><br>
                                        • Demande effectuée le : " . date('d/m/Y à H:i') . "<br>
                                        • Adresse IP : " . substr($real_ip, 0, -2) . "xx (masquée)<br>
                                        • Si ce n'est pas vous, changez immédiatement votre mot de passe
                                    </div>
                                </div>
                                <div class='footer'>
                                    <p>Cet email a été envoyé automatiquement par le système Yggdrasil.<br>
                                    © 2025 Yggdrasil. Tous droits réservés.</p>
                                </div>
                            </div>
                        </body>
                        </html>
                    ";

                    $mail->send();
                    $_SESSION['reset_success'] = $success_message;
                    error_log("Lien de réinitialisation envoyé avec succès à $email depuis $real_ip");

                } catch (Exception $e) {
                    error_log("PHPMailer Error: {$mail->ErrorInfo}");
                    $_SESSION['reset_success'] = $success_message; // Ne pas révéler l'échec
                }

            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollback();
                throw $e;
            }
        }

    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollback();
        error_log("Erreur DB dans forgot-password.php : " . $e->getMessage() . " - IP: $ip");
        $errors[] = "Erreur temporaire du système. Veuillez réessayer dans quelques instants.";
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollback();
        $error_msg = $e->getMessage();
        error_log("Erreur dans forgot-password.php : $error_msg - IP: $ip - Email: " . ($email ?? 'non défini'));

        switch ($error_msg) {
            case 'Token de sécurité invalide':
                $errors[] = "Session expirée. Veuillez recharger la page et réessayer.";
                break;
            case (strpos($error_msg, 'Trop de tentatives') === 0):
                $errors[] = $error_msg;
                break;
            default:
                $errors[] = "Une erreur temporaire est survenue. Veuillez réessayer.";
        }
    }

    // Stocker les erreurs en session
    if (!empty($errors)) {
        $_SESSION['reset_errors'] = $errors;
    }

    // Rediriger
    header('Location: ../forgot-password.php');
    exit;
}

// Méthode non autorisée
http_response_code(405);
die("Accès refusé.");
?>