<?php
$to = 'votre.email@gmail.com';
$subject = 'Test Sendmail - Yggdrasil';
$message = 'Si vous recevez cet email, sendmail fonctionne !';
$headers = 'From: noreply@yggdrasil.bzh';

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Email envoyé avec succès ! Vérifiez vos spams.";
} else {
    echo "❌ Échec de l'envoi de l'email.";
}
?>