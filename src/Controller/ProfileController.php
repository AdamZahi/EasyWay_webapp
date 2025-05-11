<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour éditer votre profil.');
        }

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le fichier
            $file = $form->get('file')->getData();

            if ($file) {
                $filename = uniqid().'.'.$file->guessExtension();

                try {
                    // Déplacer le fichier vers un répertoire de stockage
                    $file->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads',
                        $filename
                    );

                    // Mettre à jour la photo de profil
                    $user->setPhotoProfil($filename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du fichier.');
                }
            }

            $entityManager->persist($user); // utile si nouvel utilisateur (sinon facultatif ici)
            $entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('passager/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}



