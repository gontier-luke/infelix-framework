<?php
namespace Classes;
class Mail{
    public static function sendTest(string $to, string $subject, string $message): void
    {
        $headers = 'From: contact@infelix-commentator' . "\r\n" .
            'Reply-To: contact@infelix-commentator' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
    }

    public static function send(string $to, string $subject, string $message): void
    {
        $headers = 'From: contact@infelix-commentator.com' . "\r\n" .
            'Reply-To: contact@infelix-commentator.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        mail($to, $subject, $message, $headers);
    }
}