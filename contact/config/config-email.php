<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

// Chemin vers PHPMailer (ajustez si besoin)
require_once 'vendor/autoload.php';

function sendEmail($to, $toName, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // === Configuration SMTP (Gmail) ===
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yggdrasil.genealogie@gmail.com'; // Votre email
        $mail->Password   = 'votre_mot_de_passe_d_application'; // Mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // === Détails de l'email ===
        $mail->setFrom('noreply@yggdrasil.bzh', 'Yggdrasil');
        $mail->addReplyTo('support@yggdrasil.bzh', 'Support Yggdrasil');
        $mail->addAddress($to, $toName);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body); // Version texte

        // === Envoi ===
        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
?>