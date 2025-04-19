<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class VerifyCodeController extends AbstractController
{
    #[Route('/reset-password/verify-code', name: 'verify_code')]
    public function verifyForm(): Response
    {
        return $this->render('security/verify_code.html.twig');
    }

    #[Route('/reset-password/check-code', name: 'check_code', methods: ['POST'])]
    public function checkCode(Request $request, SessionInterface $session): Response
    {
        $inputCode = $request->request->get('code');
        $storedCode = $session->get('reset_code');

        if ($inputCode == $storedCode) {
            $this->addFlash('success', 'Code vérifié avec succès !');

            return $this->redirectToRoute('reset_password_new');
        }

        $this->addFlash('danger', 'Code incorrect, veuillez réessayer.');
        return $this->redirectToRoute('verify_code');
    }
}
