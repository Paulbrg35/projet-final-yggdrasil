<?php
$to = "destinataire@exemple.com";
$subject = "Test email local";
$message = "Ceci est un test. Si vous voyez ce message, mail() fonctionne !";
$headers = "From: votre_email@gmail.com";

if (mail($to, $subject, $message, $headers)) {
    echo "✅ Email envoyé avec succès !";
} else {
    echo "❌ Échec de l'envoi.";
}
?>