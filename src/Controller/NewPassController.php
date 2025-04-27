<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class NewPassController extends AbstractController
{
    #[Route('/new-password', name: 'app_reset_password')]
    public function reset(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hasher, EntityManagerInterface $em)
    {
        $email = $request->query->get('email'); // ou autre méthode pour identifier l’utilisateur
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('error', "Utilisateur introuvable.");
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('new_password');
            $confirmPassword = $request->request->get('confirm_password');

            if ($newPassword !== $confirmPassword) {
                $this->addFlash('error', "Les mots de passe ne correspondent pas.");
            } else {
                $user->setPassword($hasher->hashPassword($user, $newPassword));
                $em->flush();

                $this->addFlash('success', "Mot de passe mis à jour avec succès.");
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/new_pass.html.twig');
    }
}
