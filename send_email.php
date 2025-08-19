<?php
function send_reset_email($to, $firstname, $reset_link) {
    $subject = "Réinitialisation de votre mot de passe - Yggdrasil";
    $message = "
    Bonjour $firstname,
    
    Vous avez demandé à réinitialiser votre mot de passe.
    Cliquez sur le lien ci-dessous :
    
    $reset_link
    
    Ce lien expire dans 1 heure.
    
    L'équipe Yggdrasil
    ";

    $headers = "From: no-reply@yggdrasil.bzh\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($to, $subject, $message, $headers);
}