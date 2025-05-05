<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyCodeController extends AbstractController
{
#[Route('/verify-code', name: 'app_verify_code')]
public function verifyCode(
    Request $request,
    UserRepository $userRepository,
    EntityManagerInterface $entityManager
): Response {
    $email = $request->request->get('email');
    $enteredCode = $request->request->get('code');

    $user = $userRepository->findOneBy(['email' => $email]);

    if (!$user) {
        return new Response('User not found.', 404);
    }

    if ($user->getResetToken() !== $enteredCode) {
        return new Response('Code invalide.', 400);
    }

    if ($user->getResetTokenExpiresAt() < new \DateTime()) {
        return new Response('Code expiré.', 400);
    }

    // Ici, afficher une page/formulaire pour entrer un nouveau mot de passe

    return new Response('Code correct, vous pouvez changer votre mot de passe.');
}
}