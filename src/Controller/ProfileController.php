<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfileController extends AbstractController
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManager = $entityManager;
    }

    #[Route('/profile', name: 'edit_profile')]
    public function editProfile(Request $request, SluggerInterface $slugger): Response
    {
        // Récupère l'utilisateur connecté
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $photoFile = $form->get('photoProfil')->getData();

            if ($photoFile) {
                dd($photoFile); // ← Ajoute cette ligne pour tester
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    $photoFile->move(
                        $this->getParameter('profile_picture_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo.');
                }

                $user->setPhotoProfil($newFilename);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Profil mis à jour avec succès !');

            return $this->redirectToRoute('edit_profile');
        }

        return $this->render('passager/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user, // pour afficher la photo par exemple
        ]);
    }
}
