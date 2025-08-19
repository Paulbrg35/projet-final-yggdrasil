<?php
// smtp_config.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ✅ Chemin vers les fichiers de PHPMailer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // === Paramètres SMTP (exemple avec Gmail) ===
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';           // Serveur SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yggdrasil.genealogie@gmail.com';    // Votre email Gmail
        $mail->Password   = 'Fenrir.';       // Mot de passe d'application
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // === Expéditeur et destinataire ===
        $mail->setFrom('no-reply@yggdrasil.bzh', 'Yggdrasil');
        $mail->addAddress($to);

        // === Contenu de l'email ===
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = strip_tags($body);

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Erreur envoi email : " . $mail->ErrorInfo);
        return false;
    }
}