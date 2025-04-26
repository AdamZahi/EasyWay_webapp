<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ResetPasswordController extends AbstractController
{
    private $mailer;
    private $passwordHasher;
    
    public function __construct(MailerInterface $mailer, UserPasswordHasherInterface $passwordHasher)
{
    $this->mailer = $mailer;
    $this->passwordHasher = $passwordHasher;
}


    #[Route('/request-reset', name: 'app_request_reset')]
    public function requestReset(
        Request $request,
        UserRepository $userRepository,
        MailerService $mailerService,
        SessionInterface $session
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');

            if (empty($email)) {
                $this->addFlash('error', 'L\'email est requis.');
                return $this->redirectToRoute('app_request_reset');
            }

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_request_reset');
            }

            // Générer le reset code une seule fois
            $resetCode = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

            // Stocker le code dans la session
            $session->set('reset_code', $resetCode);

            // Générer l'URL de réinitialisation
            $resetUrl = $this->generateUrl('app_reset_password', [
                'resetCode' => urlencode($resetCode),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoi de l'email avec le lien de réinitialisation
            $mailerService->sendEmail(
                $user->getEmail(),
                'Votre code de réinitialisation',
                $resetUrl // Envoie directement l'URL de réinitialisation dans le mail
            );

            $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
            return $this->redirectToRoute('app_request_reset');
        }

        return $this->render('security/reset_pass.html.twig');
    }
    #[Route('/reset-password/{resetCode}', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        UserRepository $userRepository,
        SessionInterface $session,
        EntityManagerInterface $entityManager,
        $resetCode
    ): Response {
        // Vérification du code de réinitialisation
        $storedCode = $session->get('reset_code');
        
        if ($resetCode !== $storedCode) {
            $this->addFlash('error', 'Le code de réinitialisation est invalide.');
            return $this->redirectToRoute('app_request_reset');
        }

        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            // Vérification que les mots de passe correspondent
            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password', ['resetCode' => $resetCode]);
            }

            // Récupération de l'utilisateur à réinitialiser (en supposant que l'utilisateur est retrouvé par son code de réinitialisation)
            $user = $userRepository->findOneBy(['resetCode' => $resetCode]);
            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_request_reset');
            }


            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);

            // Mise à jour du mot de passe dans la base de données
            $user->setPassword($hashedPassword);

            // Persistance dans la base de données
            $entityManager->flush();

            // Message de confirmation
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

            // Redirection vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        // Affichage du formulaire pour réinitialiser le mot de passe
        return $this->render('security/new_pass.html.twig', [
            'resetCode' => $resetCode,
        ]);
    }
}
