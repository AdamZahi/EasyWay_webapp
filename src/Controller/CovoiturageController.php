<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;
use App\Entity\User;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Entity\Commentaire;
use App\Service\BadWordFilter; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;  


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
    public function rechercher(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('q');
        
        $queryBuilder = $entityManager->getRepository(Posts::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.commentaires', 'c')
            ->addSelect('c')
            ->leftJoin('c.user', 'u')
            ->addSelect('u');
    
        if ($searchTerm) {
            $queryBuilder->where('p.ville_arrivee LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }
        
        $posts = $queryBuilder->getQuery()->getResult();
    
        return $this->render('covoiturage/rechercher.html.twig', [
            'posts' => $posts,
            'searchTerm' => $searchTerm
        ]);
    }
    
    #[Route('/commentaire/new/{post_id}', name: 'app_commentaire_new', methods: ['POST'])]
    public function newComment(
        Request $request, 
        EntityManagerInterface $entityManager, 
        BadWordFilter $badWordFilter, // Inject the service
        int $post_id
    ): Response {
        // Get the post
        $post = $entityManager->getRepository(Posts::class)->find($post_id);
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
    
        // Get the comment content
        $commentText = $request->request->get('contenu');
    
        // Check for bad words
        if ($badWordFilter->containsBadWords($commentText)) {
            $this->addFlash('error', '⚠️ Votre commentaire contient des mots interdits.');
            return $this->redirectToRoute('app_covoiturage_rechercher');
        }
    
        // For testing with static user ID 1
        $user = $entityManager->getRepository(User::class)->find(1);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        // Create and save the comment
        $commentaire = new Commentaire();
        $commentaire->setContenu($commentText);
        $commentaire->setDateCreat(new \DateTime());
        $commentaire->setUser($user);
        $commentaire->setPost($post);
    
        $entityManager->persist($commentaire);
        $entityManager->flush();
    
        $this->addFlash('success', 'Commentaire ajouté avec succès!');
        return $this->redirectToRoute('app_covoiturage_rechercher');
    }
    #[Route('/covoiturage/reserver/{id_post}', name: 'app_covoiturage_reserver')]
    public function reserver(int $id_post, EntityManagerInterface $entityManager): Response
    {
        // Get the post from database
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
        
        if (!$post) {
            throw $this->createNotFoundException('Post not found');
        }
        
        return $this->render('covoiturage/reserver.html.twig', [
            'post' => $post,
        ]);
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
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
    
        if (!$post || $post->getUser()->getIdUser() !== 1) {
            throw $this->createNotFoundException('Post non trouvé ou accès non autorisé');
        }
    
        // Création du formulaire manuellement
        $form = $this->createFormBuilder($post)
            ->add('message', TextareaType::class)
            ->add('villeDepart', ChoiceType::class, [
                'choices' => $this->getTunisianCities(),
                'placeholder' => 'Choisissez une ville de départ',
                'label' => 'Ville de départ',
            ])
            ->add('villeArrivee', ChoiceType::class, [
                'choices' => $this->getTunisianCities(),
                'placeholder' => 'Choisissez une ville d\'arrivée',
                'label' => 'Ville d\'arrivée',
            ])
            ->add('date', DateType::class, ['widget' => 'single_text'])
            ->add('nombreDePlaces', IntegerType::class)
            ->add('prix', MoneyType::class, ['currency' => 'EUR'])
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Contrôle de saisie personnalisé
                $this->validateBusinessRules($post, $form);
    
                if (!$form->getErrors(true)->count()) {
                    $entityManager->flush();
                    $this->addFlash('success', 'Le trajet a été modifié avec succès!');
                    return $this->redirectToRoute('app_covoiturage_mes_offres');
                }
            }
        }
    
        return $this->render('covoiturage/modifier_post.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function validateBusinessRules(Posts $post, FormInterface $form): void
    {
        if ($post->getVilleDepart() === $post->getVilleArrivee()) {
            $form->addError(new FormError("La ville de départ et la ville d'arrivée ne peuvent pas être identiques !"));
        }

        if (null === $post->getDate()) {
            $form->addError(new FormError("La date est obligatoire !"));
        } elseif ($post->getDate() < new \DateTime('today')) {
            $form->addError(new FormError("La date de départ ne peut pas être dans le passé !"));
        }

        if ($post->getNombreDePlaces() <= 0) {
            $form->addError(new FormError("Le nombre de places doit être supérieur à zéro !"));
        }

        if ($post->getPrix() === null || $post->getPrix() <= 0) {
            $form->addError(new FormError("Le prix doit être supérieur à zéro !"));
        }
    }

    
    
    #[Route('/covoiturage/supprimer/{id_post}', name: 'app_covoiturage_supprimer')]
    public function supprimer(int $id_post, EntityManagerInterface $entityManager): Response
    {
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
        
        if (!$post) {
            $this->addFlash('error', 'Post not found');
            return $this->redirectToRoute('app_covoiturage_mes_offres');
        }
        
        if ($post->getUser()->getIdUser() !== 1) {
            $this->addFlash('error', 'Unauthorized to delete this post');
            return $this->redirectToRoute('app_covoiturage_mes_offres');
        }
    
        $entityManager->remove($post);
        $entityManager->flush();
        
        $this->addFlash('success', 'Post deleted successfully');
        return $this->redirectToRoute('app_covoiturage_mes_offres');
    }
    
    #[Route('/commentaire/edit/{id}', name: 'app_commentaire_edit', methods: ['GET', 'POST'])]
    public function editComment(
        Request $request, 
        EntityManagerInterface $entityManager, 
        BadWordFilter $badWordFilter, // Inject the service
        int $id
    ): Response {
        $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
        
        if (!$commentaire) {
            throw $this->createNotFoundException('Comment not found');
        }
    
        // Restrict editing to user ID 1 (or use $this->getUser() in a real app)
        if ($commentaire->getUser()->getIdUser() !== 1) {
            throw $this->createAccessDeniedException('You can only edit your own comments');
        }
    
        $form = $this->createFormBuilder($commentaire)
            ->add('contenu', TextareaType::class)
            ->getForm();
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Check for bad words
            if ($badWordFilter->containsBadWords($commentaire->getContenu())) {
                $this->addFlash('error', '⚠️ Le commentaire contient des mots interdits.');
                return $this->redirectToRoute('app_covoiturage_rechercher');
            }
    
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire modifié avec succès!');
            return $this->redirectToRoute('app_covoiturage_rechercher');
        }
    
        return $this->render('covoiturage/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
#[Route('/commentaire/delete/{id}', name: 'app_commentaire_delete', methods: ['POST'])]
public function deleteComment(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
    
    if (!$commentaire) {
        throw $this->createNotFoundException('Comment not found');
    }

    // For testing with static user ID 1
    if ($commentaire->getUser()->getIdUser() !== 1) {
        throw $this->createAccessDeniedException('You can only delete your own comments');
    }

    if ($this->isCsrfTokenValid('delete'.$commentaire->getIdCom(), $request->request->get('_token'))) {
        $entityManager->remove($commentaire);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_covoiturage_rechercher');
}

private function getTunisianCities(): array
{
    $cities = [
        'Tunis' => 'Tunis',
        'Sfax' => 'Sfax',
        'Sousse' => 'Sousse',
        'Gabès' => 'Gabès',
        'Bizerte' => 'Bizerte',
        'Nabeul' => 'Nabeul',
        'Kairouan' => 'Kairouan',
        'Monastir' => 'Monastir',
        'Mahdia' => 'Mahdia',
        'Tozeur' => 'Tozeur',
        'Médenine' => 'Médenine',
        'Gafsa' => 'Gafsa',
        'Tataouine' => 'Tataouine',
        'Kasserine' => 'Kasserine',
        'Le Kef' => 'Le Kef',
        'Siliana' => 'Siliana',
        'Beja' => 'Beja',
        'Zaghouan' => 'Zaghouan',
        'Ben Arous' => 'Ben Arous',
        'Ariana' => 'Ariana',
        'Manouba' => 'Manouba',
        'Jendouba' => 'Jendouba',
        'Kebili' => 'Kebili'
    ];

    return $cities;
}

    
}