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

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if (empty($token)) {
    $_SESSION['reset_errors'] = ["Lien invalide."];
    header('Location: forgot-password.php');
    exit;
}

if (empty($password) || $password !== $confirm || strlen($password) < 8) {
    $_SESSION['reset_errors'] = ["Mot de passe invalide."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}

$stmt = getDatabase()->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch();

if (!$reset) {
    $_SESSION['reset_errors'] = ["Lien invalide ou expiré."];
    header('Location: forgot-password.php');
    exit;
}

try {
    $pdo = getDatabase();
    $pdo->beginTransaction();

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->execute([$hashed, $reset['email']]);

    $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
    $stmt->execute([$reset['email']]);

    $pdo->commit();

    $_SESSION['login_success'] = "Mot de passe mis à jour. Vous pouvez vous connecter.";
    header('Location: login.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollback();
    error_log("Erreur mise à jour mot de passe : " . $e->getMessage());
    $_SESSION['reset_errors'] = ["Erreur serveur."];
    header('Location: reset-password.php?token=' . urlencode($token));
    exit;
}
?>