<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(string $to, string $subject, string $resetUrl): void
    {
        $content = "
            <html>
                <body>
                    <p>Bonjour,</p>
                    <p>Vous avez demandé une réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
                    <p><a href=\"$resetUrl\">Réinitialiser mon mot de passe</a></p>
                    <p>Si vous n'avez pas demandé cette réinitialisation, ignorez cet email.</p>
                </body>
            </html>
        ";
    
        $email = (new Email())
            ->from('mejrieya384@gmail.com')
            ->to($to)
            ->subject($subject)
            ->html($content);
        
        $this->mailer->send($email);
    }
}
