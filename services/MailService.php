<?php

namespace Services;

use Exceptions\MailException;
class MailService{
    public static function sendMail(string $to, string $subject, string $message, string $headers = ''): bool {
        // Utilisation de la fonction mail() de PHP pour envoyer l'e-mail
        if(!mail($to, $subject, $message, $headers)) {
            // Gérer l'erreur d'envoi d'e-mail ici si nécessaire
            throw new MailException("Failed to send email to $to");
        }
        return true;
    }
}