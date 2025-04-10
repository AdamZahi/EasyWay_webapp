<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Posts;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


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
            if ($form->isValid()) {
                $errors = [];
            
                if ($post->getVilleDepart() === $post->getVilleArrivee()) {
                    $errors[] = 'La ville de départ et la ville d\'arrivée ne peuvent pas être identiques !';
                }
            
                if (null === $post->getDate()) {
                    $errors[] = 'La date est obligatoire !';
                } elseif ($post->getDate() < new \DateTime('today')) {
                    $errors[] = 'La date de départ ne peut pas être dans le passé !';
                }
            
                if ($post->getNombreDePlaces() <= 0) {
                    $errors[] = 'Le nombre de places doit être supérieur à zéro !';
                }
            
              
    if ($post->getPrix() === null || $post->getPrix() <= 0) {
        $errors[] = 'Le prix doit être supérieur à zéro !';
    }
    
            
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            $this->addFlash('error', $error);
        }
                } else {
                    try {
                        // Replace this with your actual user retrieval logic
                        // For example, if you're using security:
                        // $user = $this->getUser();
                        $user = $entityManager->getReference('App\Entity\User', 1); // or 18
                        $post->setUser($user); // Changed from setIdUser to setUser
                
                        $entityManager->persist($post);
                        $entityManager->flush();
                
                          
                $this->addFlash('success', 'Votre trajet a été publié avec succès!');
                // Instead of redirecting, render the same page
                return $this->render('covoiturage/poster.html.twig', [
                    'form' => $form->createView(),   ]);
                    } catch (\Exception $e) {
                        $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
                    }
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
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
    #[Route('/covoiturage/mes-offres', name: 'app_covoiturage_mes_offres')]
    public function mesOffres(EntityManagerInterface $entityManager): Response
    {
        // Get the user with ID 1
        $user = $entityManager->getRepository(User::class)->find(1);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Get all posts created by this user
        $posts = $entityManager->getRepository(Posts::class)->findBy(['user' => $user]);

        // Render the posts in a Twig template
        return $this->render('covoiturage/mes_offres.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/covoiturage/modifier/{id_post}', name: 'app_covoiturage_modifier')]
    public function modifier(int $id_post, EntityManagerInterface $entityManager, Request $request): Response
    {
        // Get the post by ID using getIdPost() instead of id()
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
    
        if (!$post || $post->getUser()->getIdUser() !== 1) {
            throw $this->createNotFoundException('Post not found or unauthorized');
        }
    
        // Render the post for editing
        return $this->render('covoiturage/modifier_post.html.twig', [
            'post' => $post,
        ]);
    }
    
    #[Route('/covoiturage/supprimer/{id_post}', name: 'app_covoiturage_supprimer')]
    public function supprimer(int $id_post, EntityManagerInterface $entityManager): Response
    {
        // Get the post by ID using getIdPost() instead of id()
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
    
        if (!$post || $post->getUser()->getIdUser() !== 1) {
            throw $this->createNotFoundException('Post not found or unauthorized');
        }
    
        // Delete the post
        $entityManager->remove($post);
        $entityManager->flush();
    
        // Redirect back to the list of posts
        return $this->redirectToRoute('app_covoiturage_mes_offres');
    }
    
}