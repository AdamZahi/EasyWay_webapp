<?php
// src/Controller/TestEmailController.php
// src/Controller/TestEmailController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class TestEmailController extends AbstractController
{
    #[Route('/test-email')]
    public function testEmail(MailerInterface $mailer): JsonResponse
    {
        $email = (new Email())
            ->from('tayssirbennejma@gmail.com')
            ->to('destinataire@example.com')  // Remplace par ton propre email
            ->subject('Test Email Symfony')
            ->text('Ceci est un test de l\'envoi d\'email dans Symfony.');

        try {
            $mailer->send($email);
            return new JsonResponse(['message' => 'Email envoyé avec succès !']);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage()], 500);
        }
    }
}
