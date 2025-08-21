<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once 'config.php';

// ✅ Débogage : vérifiez que $pdo est bien défini
if (!isset($pdo) || $pdo === null) {
    error_log("ERREUR : \$pdo n'est pas défini");
    die("Erreur interne : connexion à la base de données non disponible.");
}

// Nettoyage des tokens expirés
try {
    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
    $stmt->execute();
} catch (Exception $e) {
    error_log("Erreur nettoyage tokens : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Accès refusé.");
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

// Validation des données
if (empty($token)) {
    $_SESSION['reset_errors'] = ["Lien de réinitialisation invalide."];
    header('Location: forgot-password.php');
    exit;
}

if (empty($password) || empty($confirm)) {
    $_SESSION['reset_errors'] = ["Veuillez remplir tous les champs."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

if ($password !== $confirm) {
    $_SESSION['reset_errors'] = ["Les mots de passe ne correspondent pas."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

if (strlen($password) < 8) {
    $_SESSION['reset_errors'] = ["Le mot de passe doit contenir au moins 8 caractères."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

// Vérifier le token
try {
    $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        $_SESSION['reset_errors'] = ["Lien invalide ou expiré."];
        header('Location: forgot-password.php');
        exit;
    }

    // Mettre à jour le mot de passe
    $pdo->beginTransaction();

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashed, $reset['email']]);

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$reset['email']]);

    $pdo->commit();

    $_SESSION['login_success'] = "Mot de passe mis à jour avec succès. Vous pouvez maintenant vous connecter.";
    header('Location: login.php');
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    error_log("Erreur mise à jour mot de passe : " . $e->getMessage());
    $_SESSION['reset_errors'] = ["Erreur serveur. Veuillez réessayer."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}
?>