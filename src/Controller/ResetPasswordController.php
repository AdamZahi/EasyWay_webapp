<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'reset_password')]
    public function showForm(): Response
    {
        return $this->render('security/reset_pass.html.twig');
    }

    #[Route('/reset-password/send-code', name: 'reset_password_send_code', methods: ['POST'])]
    public function sendCode(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
        SessionInterface $session
    ): Response {
        $email = $request->request->get('email');

        if (!$email) {
            $this->addFlash('danger', 'Veuillez entrer un email.');
            return $this->redirectToRoute('reset_password');
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('danger', 'Aucun utilisateur trouvé avec cet email.');
            return $this->redirectToRoute('reset_password');
        }

        // Générer un code de 6 chiffres
        $code = random_int(100000, 999999);

        // Sauvegarder dans la session
        $session->set('reset_email', $email);
        $session->set('reset_code', $code);

        // Envoyer l'e-mail
        $emailMessage = (new Email())
            ->from('no-reply@votreapp.com')
            ->to($email)
            ->subject('Votre code de réinitialisation')
            ->html("<p>Bonjour,</p><p>Voici votre code de réinitialisation : <strong>$code</strong></p><p>Il est valable pendant quelques minutes.</p>");

        $mailer->send($emailMessage);

        $this->addFlash('success', 'Code envoyé à votre adresse email.');

        return $this->redirectToRoute('verify_code');
    }

    #[Route('/reset-password/verify-code', name: 'verify_code')]
    public function verifyCodeForm(): Response
    {
        return $this->render('security/verify_code.html.twig');
    }
}
