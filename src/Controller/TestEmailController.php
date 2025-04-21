<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

// src/Controller/TestEmailController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

// src/Controller/TestEmailController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function test(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('mejrieya384@gmail.com')
            ->to('my5982002@gmail.com')
            ->subject('Test depuis Symfony 📧')
            ->text('Ceci est un test.')
            ->html('<p>Ceci est un test depuis Symfony avec Mailtrap 🎯</p>');

        $mailer->send($email);

        return new Response('✅ Email envoyé ! Vérifie ta boîte Mailtrap');
    }
}

