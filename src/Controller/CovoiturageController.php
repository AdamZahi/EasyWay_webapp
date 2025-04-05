<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;

use App\Entity\Posts;
use App\Form\PostsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(): Response
    {
        return $this->render('covoiturage/index.html.twig');
    }

    #[Route('/covoiturage/poster', name: 'app_covoiturage_poster')]
    public function poster(Request $request, EntityManagerInterface $entityManager): Response
    {
        $post = new Posts(); 
    
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            if (null === $post->getMessage()) {
                $post->setMessage('');
            }
            // Manual validation
            $errors = [];
            
            if ($data->getVilleDepart() === $data->getVilleArrivee()) {
                $errors[] = 'La ville de départ et la ville d\'arrivée ne peuvent pas être identiques !';
            }

            if (null === $data->getDate()) {
                $errors[] = 'La date est obligatoire !';
            }
            elseif ($data->getDate() < new \DateTime('today')) {
                $errors[] = 'La date de départ ne peut pas être dans le passé !';
            }

            if ($data->getNombreDePlaces() <= 0) {
                $errors[] = 'Le nombre de places doit être supérieur à zéro !';
            }

            if ($data->getPrix() <= 0) {
                $errors[] = 'Le prix doit être supérieur à zéro !';
            }


            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
            }  elseif ($form->isValid()) {
                try {
                    // Debug output before saving
                    dump('Post before save:', $post);
                    dump('User being assigned:', $user = $entityManager->getReference('App\Entity\User', 18));
                    
                    // Set static user ID (18) for testing
                    $post->setIdUser($user);
                    
                    // Set current date if not already set (safety fallback)
                    if (!$post->getDate()) {
                        $post->setDate(new \DateTime());
                    }
                    
                    $entityManager->persist($post);
                    $entityManager->flush();
                    
                    // Debug after saving
                    dump('Post after save, ID:', $post->getIdPost());
                    
                    $this->addFlash('success', 'Votre trajet a été publié avec succès!');
                    return $this->redirectToRoute('app_covoiturage');
                    
                } catch (\Exception $e) {
                    // Enhanced error logging
                    dump('Database error:', $e->getMessage());
                    dump('Stack trace:', $e->getTraceAsString());
                    
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement: '.$e->getMessage());
                    
                    // For development - remove in production
                    throw $e;
                }
            }
        }

        return $this->render('covoiturage/poster.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/covoiturage/rechercher', name: 'app_covoiturage_rechercher')]
    public function rechercher(): Response
    {
        return $this->render('covoiturage/rechercher.html.twig');
    }
}