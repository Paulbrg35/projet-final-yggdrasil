<?php
session_start();
define('YGGDRASIL_CONFIG', true);
require_once '../config.php'; // Pour les logs ou la base si besoin

// Initialiser les messages
$error = '';
$success = '';

// Fonction pour nettoyer les données
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Accès refusé.");
}

// === Validation CSRF (si vous l'avez implémenté) ===
// Si vous n'avez pas de token CSRF, vous pouvez commenter cette section
/*
$csrf_token = $_POST['csrf_token'] ?? '';
if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf_token)) {
    $error = "Requête non sécurisée. Veuillez réessayer.";
}
unset($_SESSION['csrf_token']);
*/

// === Validation des champs ===
$name = sanitize_input($_POST['name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$subject = sanitize_input($_POST['subject'] ?? '');
$message = sanitize_input($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    $error = "Tous les champs sont obligatoires.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Format d'email invalide.";
} elseif (strlen($message) < 10) {
    $error = "Le message doit contenir au moins 10 caractères.";
}

// En cas d'erreur
if (!empty($error)) {
    $_SESSION['contact_error'] = $error;
    header('Location: contact.phtml');
    exit;
}

// === Destinataire et sujet de l'email ===
$to = 'yggdrasil.genealogie@gmail.com'; // Votre email de réception
$subject_email = "Contact Yggdrasil : $subject";
$message_body = "
    <h2>Nouveau message de contact</h2>
    <p><strong>Nom :</strong> {$name}</p>
    <p><strong>Email :</strong> {$email}</p>
    <p><strong>Sujet :</strong> {$subject}</p>
    <p><strong>Message :</strong></p>
    <p>" . nl2br($message) . "</p>
    <p><em>IP : {$_SERVER['REMOTE_ADDR']}</em></p>
";

// En-têtes de l'email
$headers = "From: {$email}\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// === Envoi de l'email ===
if (mail($to, $subject_email, $message_body, $headers)) {
    $_SESSION['contact_success'] = "Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.";
} else {
    error_log("Échec de l'envoi de l'email de contact de : $email");
    $_SESSION['contact_error'] = "Une erreur est survenue lors de l'envoi du message. Veuillez réessayer.";
}

// Redirection
header('Location: contact.phtml');
exit;
?>