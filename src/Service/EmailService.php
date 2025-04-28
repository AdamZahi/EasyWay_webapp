<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;

class EmailService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private string $senderEmail;

    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $logger,
        string $senderEmail
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->senderEmail = $senderEmail;
    }

    public function sendPaymentConfirmation(
        string $recipientEmail,
        float $amount,
        int $places,
        string $departure,
        string $arrival
    ): bool {
        try {
            $email = (new Email())
                ->from($this->senderEmail)  // Use the sender email provided in constructor
                ->to($recipientEmail)
                ->subject('Confirmation de paiement')
                ->html("
                    <h2>Confirmation de paiement</h2>
                    <p>Votre paiement de {$amount} DT a été reçu avec succès.</p>
                    <p>Nombre de places réservées : {$places}</p>
                    <p>Ville de départ : {$departure}</p>
                    <p>Ville d'arrivée : {$arrival}</p>
                ");

    
            $this->mailer->send($email, envelope: null);

            $this->logger->info('Email sent successfully.');
            return true;

        } catch (\Exception $e) {
            $this->logger->error('Email sending failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
