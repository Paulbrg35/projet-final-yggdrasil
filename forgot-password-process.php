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

/**
 * Fonction pour générer un token CSRF sécurisé
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

$errors = [];
$ip = getRealIP();

try {
    // Vérifier que la méthode est POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        throw new Exception('Méthode non autorisée');
    }

    // Protection CSRF renforcée
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validateCSRFToken($csrf_token)) {
        error_log("Tentative CSRF détectée depuis $ip - Token invalide ou manquant");
        http_response_code(403);
        throw new Exception('Token de sécurité invalide');
    }

    // Vérification du referer comme protection supplémentaire
    if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) === false) {
        error_log("Tentative CSRF détectée depuis $ip - Referer invalide");
        http_response_code(403);
        throw new Exception('Accès refusé');
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
        http_response_code(429);
        throw new Exception('Trop de tentatives depuis cette adresse IP. Veuillez attendre une heure.');
    }

    // Récupérer et valider l'email
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
        // Délai pour éviter l'énumération
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

    // Enregistrer la tentative même si l'utilisateur n'existe pas (pour le rate limiting)
    $user_id = $user ? $user['id'] : null;
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent)
            VALUES (?, ?, ?, ?, NOW(), ?, ?)
        ");
        
        // Token factice si l'utilisateur n'existe pas
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $user_agent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        
        $stmt->execute([$user_id, $email, $token, $expires_at, $ip, $user_agent]);
    } catch (Exception $e) {
        error_log("Erreur enregistrement tentative : " . $e->getMessage());
    }

    // Si l'utilisateur n'existe pas, simuler un délai et rediriger
    if (!$user) {
        usleep(rand(500000, 1500000)); // Délai aléatoire pour éviter l'énumération
        $_SESSION['reset_success'] = $success_message;
        header('Location: forgot-password.php');
        exit;
    }

    // Commencer une transaction pour l'utilisateur existant
    $pdo->beginTransaction();

    try {
        // Supprimer les anciens tokens pour cet email
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ? AND user_id IS NOT NULL");
        $stmt->execute([$email]);

        // Générer un nouveau token sécurisé
        $new_token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Insérer le nouveau token
        $stmt = $pdo->prepare("
            INSERT INTO password_resets (user_id, email, token, expires_at, created_at, ip_address, user_agent)
            VALUES (?, ?, ?, ?, NOW(), ?, ?)
        ");
        $stmt->execute([$user['id'], $email, $new_token, $expires_at, $ip, $user_agent]);

        $pdo->commit();

        // Créer le lien de réinitialisation
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/');
        $reset_link = "$protocol://$host$path/reset-password.php?token=" . urlencode($new_token);

        // Préparer l'email
        $subject = "Réinitialisation de votre mot de passe - Yggdrasil";
        $user_name = htmlspecialchars($user['nom'] ?? 'Membre', ENT_QUOTES, 'UTF-8');

        // Corps de l'email avec template sécurisé
        $message = "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Réinitialisation de mot de passe</title>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background-color: #f4f4f4; 
                }
                .email-container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 10px; 
                    overflow: hidden; 
                    box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
                }
                .header { 
                    background: #2E5D42; 
                    color: white; 
                    padding: 30px 20px; 
                    text-align: center; 
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 2rem; 
                    font-weight: 700; 
                }
                .content { 
                    padding: 30px; 
                }
                .button { 
                    display: inline-block; 
                    background: #D4AF37; 
                    color: #2E5D42; 
                    padding: 15px 30px; 
                    text-decoration: none; 
                    border-radius: 8px; 
                    font-weight: bold; 
                    margin: 20px 0; 
                    text-align: center;
                    transition: all 0.3s ease;
                }
                .button:hover { 
                    background: #B8941F; 
                }
                .warning-box { 
                    background: #fff3cd; 
                    border: 1px solid #ffeaa7; 
                    border-radius: 5px; 
                    padding: 15px; 
                    margin: 20px 0; 
                    color: #856404; 
                }
                .footer { 
                    background: #f8f9fa; 
                    padding: 20px; 
                    text-align: center; 
                    font-size: 0.9em; 
                    color: #666; 
                    border-top: 1px solid #dee2e6; 
                }
                .security-info {
                    font-size: 0.85em;
                    color: #666;
                    margin-top: 20px;
                    padding: 15px;
                    background: #f8f9fa;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>
                    <h1>🌳 Yggdrasil</h1>
                </div>
                <div class='content'>
                    <h2>Réinitialisation de mot de passe</h2>
                    <p>Bonjour <strong>$user_name</strong>,</p>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Yggdrasil.</p>
                    
                    <div class='warning-box'>
                        <strong>⚠️ Important :</strong> Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email et contacter notre support.
                    </div>
                    
                    <p>Pour continuer, cliquez sur le bouton ci-dessous :</p>
                    <p style='text-align: center;'>
                        <a href='$reset_link' class='button'>Réinitialiser mon mot de passe</a>
                    </p>
                    <p><strong>⏰ Ce lien expirera dans 1 heure pour votre sécurité.</strong></p>
                    
                    <div class='security-info'>
                        <strong>Informations de sécurité :</strong><br>
                        • Demande effectuée le : " . date('d/m/Y à H:i') . "<br>
                        • Adresse IP : " . substr($ip, 0, -2) . "xx (masquée)<br>
                        • Si ce n'est pas vous, changez immédiatement votre mot de passe
                    </div>
                </div>
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement par le système Yggdrasil.<br>
                    Pour toute question, contactez : <a href='mailto:support@yggdrasil.bzh'>support@yggdrasil.bzh</a></p>
                </div>
            </div>
        </body>
        </html>";

        // En-têtes sécurisés pour l'email
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: Yggdrasil <noreply@yggdrasil.bzh>',
            'Reply-To: support@yggdrasil.bzh',
            'X-Mailer: Yggdrasil-System',
            'X-Priority: 3',
            'Message-ID: <' . uniqid() . '@yggdrasil.bzh>'
        ];

        // Envoyer l'email
        $mail_sent = mail($email, $subject, $message, implode("\r\n", $headers));

        if ($mail_sent) {
            $_SESSION['reset_success'] = $success_message;
            error_log("Lien de réinitialisation envoyé avec succès à $email depuis $ip");
        } else {
            error_log("Échec envoi email à $email depuis $ip");
            throw new Exception("Erreur lors de l'envoi de l'email");
        }

    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) {
            $pdo->rollback();
        }
        throw $e;
    }

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("Erreur DB dans forgot-password-process.php : " . $e->getMessage() . " - IP: $ip");
    $errors[] = "Erreur temporaire du système. Veuillez réessayer dans quelques instants.";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    $error_msg = $e->getMessage();
    error_log("Erreur dans forgot-password-process.php : $error_msg - IP: $ip - Email: " . ($email ?? 'non défini'));
    
    // Messages d'erreur spécifiques
    switch ($error_msg) {
        case 'Données invalides':
            // Les erreurs de validation sont déjà dans $errors
            break;
        case 'Token de sécurité invalide':
        case 'Accès refusé':
        case 'Méthode non autorisée':
            $errors[] = "Accès non autorisé. Veuillez réessayer depuis la page officielle.";
            break;
        case (strpos($error_msg, 'Trop de tentatives') === 0):
            $errors[] = $error_msg;
            break;
        default:
            $errors[] = "Une erreur temporaire est survenue. Veuillez réessayer.";
    }
}

// Gérer les erreurs ou rediriger en cas de succès
if (!empty($errors)) {
    $_SESSION['reset_errors'] = $errors;
} else {
    // Si pas d'erreurs et pas déjà redirigé, alors succès
    if (!isset($_SESSION['reset_success'])) {
        $_SESSION['reset_success'] = "Si cette adresse email est enregistrée et active, vous recevrez un lien de réinitialisation dans quelques instants.";
    }
}

// Régénérer le token CSRF pour la prochaine utilisation
unset($_SESSION['csrf_token']);

// Redirection sécurisée
header('Location: forgot-password.php');
exit;
?>