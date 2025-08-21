<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';
/** @var PDO $pdo */
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

// === FONCTIONS UTILITAIRES (doivent être déclarées en haut) ===

/**
 * Fonction pour obtenir l'IP réelle du client
 */
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

/**
 * Fonction pour générer un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Fonction pour valider le token CSRF
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// === TRAITEMENT DU FORMULAIRE (si POST) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    $ip = getRealIP(); // ✅ Maintenant autorisé

    try {
        // Validation CSRF
        $csrf_token = $_POST['csrf_token'] ?? '';
        if (!validateCSRFToken($csrf_token)) {
            error_log("Tentative CSRF détectée depuis $ip");
            throw new Exception('Token de sécurité invalide');
        }

        // Rate limiting global par IP (max 5 tentatives par heure)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM password_resets 
            WHERE ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$ip]);
        $ip_attempts = $stmt->fetchColumn();

        if ($ip_attempts >= 5) {
            error_log("Rate limit IP dépassé pour $ip ($ip_attempts tentatives)");
            throw new Exception('Trop de tentatives depuis cette adresse IP. Veuillez attendre une heure.');
        }

        // Validation email
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

        // Rate limiting par email (max 3 tentatives par 15 minutes)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM password_resets 
            WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->execute([$email]);
        $email_attempts = $stmt->fetchColumn();

        if ($email_attempts >= 3) {
            error_log("Rate limit email dépassé pour $email depuis $ip");
            usleep(rand(500000, 1000000));
            throw new Exception('Trop de tentatives pour cette adresse email. Veuillez attendre 15 minutes.');
        }

        // Nettoyer les tokens expirés
        try {
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
            $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur nettoyage tokens : " . $e->getMessage());
        }

        // Vérifier si l'utilisateur existe
        $stmt = $pdo->prepare("SELECT id, nom, email FROM users WHERE email = ? AND active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Message de succès standardisé pour éviter l'énumération
        $success_message = "Si cette adresse email est enregistrée et active, vous recevrez un lien de réinitialisation dans quelques instants.";

        // Enregistrer la tentative même si l'utilisateur n'existe pas
        $user_id = $user ? $user['id'] : null;
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent)
                VALUES (?, ?, ?, ?, NOW(), ?, ?)
            ");
            
            $token = bin2hex(random_bytes(32));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
            
            $stmt->execute([$user_id, $email, $token, $expires_at, $ip, $user_agent]);
        } catch (Exception $e) {
            error_log("Erreur enregistrement tentative : " . $e->getMessage());
        }

        // Si l'utilisateur n'existe pas
        if (!$user) {
            usleep(rand(500000, 1500000));
            $_SESSION['reset_success'] = $success_message;
        } else {
            // Commencer une transaction
            $pdo->beginTransaction();

            try {
                // Supprimer les anciens tokens
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND user_id IS NOT NULL");
                $stmt->execute([$email]);

                // Générer un nouveau token
                $new_token = bin2hex(random_bytes(32));
                $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

                // Insérer le nouveau token
                $stmt = $pdo->prepare("
                    INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent)
                    VALUES (?, ?, ?, ?, NOW(), ?, ?)
                ");
                $stmt->execute([$user['id'], $email, $new_token, $expires_at, $ip, $user_agent]);

                $pdo->commit();

                // Créer le lien
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
                $reset_link = "$protocol://$host$path/reset-password.php?token=" . urlencode($new_token);

                // Préparer l'email
                $subject = "Réinitialisation de votre mot de passe - Yggdrasil";
                $user_name = htmlspecialchars($user['nom'] ?? 'Membre', ENT_QUOTES, 'UTF-8');

                $message = "
                <!DOCTYPE html>
                <html lang='fr'>
                <head>
                    <meta charset='UTF-8'>
                    <title>Réinitialisation</title>
                    <style>
                        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; margin: 0; padding: 20px; }
                        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
                        .header { background: #2E5D42; color: white; padding: 20px; text-align: center; }
                        .button { display: inline-block; background: #D4AF37; color: #2E5D42; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
                        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 0.9em; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'><h1>Yggdrasil</h1></div>
                        <h2>Réinitialisation de mot de passe</h2>
                        <p>Bonjour <strong>$user_name</strong>,</p>
                        <p>Cliquez sur le bouton ci-dessous pour réinitialiser votre mot de passe :</p>
                        <p style='text-align: center;'>
                            <a href='$reset_link' class='button'>Réinitialiser</a>
                        </p>
                        <p><strong>⏰ Ce lien expire dans 1 heure.</strong></p>
                    </div>
                    <div class='footer'>
                        <p>Cet email a été envoyé automatiquement.</p>
                    </div>
                </body>
                </html>";

                $headers = [
                    'MIME-Version: 1.0',
                    'Content-Type: text/html; charset=UTF-8',
                    'From: Yggdrasil <noreply@yggdrasil.bzh>',
                    'Reply-To: support@yggdrasil.bzh'
                ];

                $mail_sent = mail($email, $subject, $message, implode("\r\n", $headers));

                if ($mail_sent) {
                    $_SESSION['reset_success'] = $success_message;
                    error_log("Email envoyé à $email depuis $ip");
                } else {
                    error_log("Échec envoi email à $email depuis $ip");
                    $_SESSION['reset_success'] = $success_message;
                }

            } catch (Exception $e) {
                if (isset($pdo) && $pdo->inTransaction()) $pdo->rollback();
                throw $e;
            }
        }

    } catch (PDOException $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollback();
        error_log("Erreur DB : " . $e->getMessage() . " - IP: $ip");
        $errors[] = "Erreur temporaire. Veuillez réessayer.";
        
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollback();
        
        $error_msg = $e->getMessage();
        error_log("Erreur : $error_msg - IP: $ip - Email: " . ($email ?? 'non défini'));
        
        switch ($error_msg) {
            case 'Données invalides':
                break;
            case 'Token de sécurité invalide':
                $errors[] = "Session expirée. Veuillez recharger la page.";
                break;
            case (strpos($error_msg, 'Trop de tentatives') === 0):
                $errors[] = $error_msg;
                break;
            default:
                $errors[] = "Une erreur est survenue. Veuillez réessayer.";
        }
    }

    // Stocker les erreurs
    if (!empty($errors)) {
        $_SESSION['reset_errors'] = $errors;
    }

    // Régénérer le token CSRF
    unset($_SESSION['csrf_token']);
}

// Générer un nouveau token CSRF pour le formulaire
$csrf_token = generateCSRFToken();

// Redirection vers la page d'origine
header('Location: forgot-password.php');
exit;
?>