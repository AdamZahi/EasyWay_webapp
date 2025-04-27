<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        MailerService $mailerService
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

            // Créer un lien simple vers la page de reset (avec l'ID utilisateur)
            $resetUrl = $this->generateUrl('app_reset_password', [
                'id' => $user->getIdUser(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Envoyer l'email avec le lien
            $mailerService->sendEmail(
                $user->getEmail(),
                'Réinitialisation de votre mot de passe',
                $resetUrl
            );

            $this->addFlash('success', 'Un lien de réinitialisation a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_pass.html.twig');
    }

    #[Route('/reset-password/{id}', name: 'app_reset_password')]
    public function resetPassword(
        int $id,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $userRepository->find($id);

        if (!$user) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_request_reset');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if (empty($newPassword) || empty($confirmPassword)) {
                $this->addFlash('error', 'Veuillez remplir tous les champs.');
                return $this->redirectToRoute('app_reset_password', ['id' => $id]);
            }

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_reset_password', ['id' => $id]);
            }

            // Hasher le nouveau mot de passe
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);

            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été modifié avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/new_pass.html.twig', [
            'id' => $id,
        ]);
    }
}
