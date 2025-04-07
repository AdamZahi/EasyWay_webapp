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
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Manual validation
            $errors = [];
            
            if ($post->getVilleDepart() === $post->getVilleArrivee()) {
                $errors[] = 'La ville de départ et la ville d\'arrivée ne peuvent pas être identiques !';
            }
    
            if (null === $post->getDate()) {
                $errors[] = 'La date est obligatoire !';
            }
            elseif ($post->getDate() < new \DateTime('today')) {
                $errors[] = 'La date de départ ne peut pas être dans le passé !';
            }
    
            if ($post->getNombreDePlaces() <= 0) {
                $errors[] = 'Le nombre de places doit être supérieur à zéro !';
            }
    
            if ($post->getPrix() <= 0) {
                $errors[] = 'Le prix doit être supérieur à zéro !';
            }
    
            if (count($errors) > 0) {
                foreach ($errors as $error) {
                    $this->addFlash('error', $error);
                }
                return $this->redirectToRoute('app_covoiturage_poster');
            }
    
            try {
                $user = $entityManager->getReference('App\Entity\User', 18);
                $post->setIdUser($user);
                
                // Verify all required fields are set
                if (!$post->getDate()) {
                    $post->setDate(new \DateTime());
                }
                
                // Debug before persist
                dump('Post object before persist:', $post);
                
                $entityManager->persist($post);
                
                // Debug UnitOfWork to see what will be inserted
                dump('UnitOfWork insertions:', $entityManager->getUnitOfWork()->getScheduledEntityInsertions());
                
                $entityManager->flush();
                
                // Debug after flush
                dump('Post ID after flush:', $post->getIdPost());
                dump('Database connection:', $entityManager->getConnection());
                
                $this->addFlash('success', 'Votre trajet a été publié avec succès!');
                return $this->redirectToRoute('app_covoiturage');
                
            } catch (\Exception $e) {
                dump('Exception:', $e);
                $this->addFlash('error', 'Erreur: ' . $e->getMessage());
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