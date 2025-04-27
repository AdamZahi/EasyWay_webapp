<?php
    namespace App\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use App\Form\ReclamationType;
    use App\Entity\Reclamation;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\Extension\Core\Type\TextareaType;
    use Symfony\Component\Form\Extension\Core\Type\SubmitType;
    use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
    use Symfony\Component\Security\Csrf\CsrfToken;
    use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
    use App\Repository\ReclamationRepository;
    use App\Entity\Reponse;
    use App\Form\ReponseType;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use App\Repository\ReponseRepository;
    use Symfony\Bundle\SecurityBundle\Security;
    use App\Service\BrevoMailer;

    use App\Entity\User;  




    final class ReclamationController extends AbstractController
    {
        
        private EntityManagerInterface $entityManager;

        // Inject the EntityManagerInterface into the controller
        public function __construct(EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
        }

        #[Route('/reclamation', name: 'app_reclamation')]
public function index(Request $request, Security $security, BrevoMailer $brevoMailer, ReclamationRepository $reclamationRepository): Response
{
    // Create a new Reclamation instance
    $reclamation = new Reclamation();
    
    // Create the form
    $form = $this->createForm(ReclamationType::class, $reclamation);

    // Handle the form submission
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $user = $security->getUser();
        if (!$user) {
            throw new \Exception('Aucun utilisateur connecté.');
        }
    
        // Associer l'utilisateur à la réclamation
        $reclamation->setUser($user); 
        // Persist the data to the database
        $this->entityManager->persist($reclamation);
        $this->entityManager->flush();
        
        // Prepare the email content
        $contenuEmail = "
            <p><strong>Sujet :</strong> {$reclamation->getSujet()}</p>
            <p><strong>Description :</strong> {$reclamation->getDescription()}</p>
            <p><strong>Catégorie :</strong> {$reclamation->getCategoryId()->getType()}</p>
            <p><strong>Date de création :</strong> {$reclamation->getDateCreation()->format('d/m/Y')}</p>
        ";

        // Send the email
        $brevoMailer->sendEmail(
            $reclamation->getEmail(),
            'Confirmation de votre réclamation',
            $contenuEmail
        );
        
        // Redirect after successful submission to avoid resubmitting on refresh
        return $this->redirectToRoute('app_reclamation');
    }

    // Get the connected user
    $user = $security->getUser();
    if (!$user) {
        throw new \Exception('Aucun utilisateur connecté.');
    }

    // Fetch the reclamations of the connected user
    $reclamations = $reclamationRepository->createQueryBuilder('r')
        ->leftJoin('r.reponses', 'rep')
        ->where('r.user = :user')
        ->setParameter('user', $user)
        ->getQuery()
        ->getResult();

    // Prepare the responses for display
    $reponses = [];
    foreach ($reclamations as $reclamation) {
        foreach ($reclamation->getReponses() as $reponse) {
            $reponses[] = $reponse;
        }
    }

    // Render the form and responses in the template
    return $this->render('front-office/reclamation/index.html.twig', [
        'form' => $form->createView(),
        'reclamations' => $reclamations,
        'reponses' => $reponses,
    ]);
}


      /*  public function sendEmail(MailerInterface $mailer, $toEmail, $subject, $body)
        {
            $email = (new Email())
                ->from('sarrabennejma746@gmail.com')
                ->to($toEmail)
                ->subject($subject)
                ->text($body);
        
            $mailer->send($email);
        }*/
        

       
        #[Route('/add-reclamation', name: 'app_add_reclamation')]
        public function addReclamation(Request $request, MailerInterface $mailer, Security $security): Response
        {
            // Création de l'objet réclamation
            $reclamation = new Reclamation();
            
            // Création du formulaire lié à la réclamation
            $form = $this->createForm(ReclamationType::class, $reclamation);
            $form->handleRequest($request);
        
            // Vérifier si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                
                // Récupérer l'utilisateur connecté
                $user = $security->getUser();
                if (!$user) {
                    throw new \Exception('Aucun utilisateur connecté.');
                }
        
                // Associer l'utilisateur à la réclamation
                $reclamation->setUser($user);  // Liens avec l'utilisateur connecté
                $reclamation->setDateCreation(new \DateTime());  // Ajouter la date de création
        
                // Persist et flush de l'entité Reclamation
                $this->entityManager->persist($reclamation);
                $this->entityManager->flush();
        
                // Rediriger vers la page des réclamations
                return $this->redirectToRoute('app_reclamation');
            }
        
            // Rendu du formulaire
            return $this->render('front-office/reclamation/index.html.twig', [
                'form' => $form->createView(),
                
            ]);
        }
        
        
        
        

////
        
    /*    #[Route('/api/reclamation', name: 'api_add_reclamation', methods: ['POST'])]
        public function addReclamationJson(Request $request, EntityManagerInterface $entityManager): JsonResponse
        {
            $data = json_decode($request->getContent(), true);
        
            if (!$data || !isset($data['email'], $data['sujet'], $data['description'])) {
                return new JsonResponse(['error' => 'Données invalides'], 400);
            }
        
            $reclamation = new Reclamation();
            $reclamation->setEmail($data['email']);
            $reclamation->setSujet($data['sujet']);
            $reclamation->setDescription($data['description']);
            $reclamation->setDateCreation(new \DateTime());
            
            // Assigner une valeur par défaut à 'statut'
            $reclamation->setStatut('en_attente'); // Valeur par défaut
        
            $entityManager->persist($reclamation);
            $entityManager->flush();
        
            return new JsonResponse(['message' => 'Réclamation ajoutée avec succès'], 201);
        }*/

        /*{
  "email": "test@example.com",
  "sujet": "Problème",
  "description": "Description du problème"
}
*/


#[Route('/list', name: 'admin_reclamations', methods: ['GET'])]
public function listReclamations(
    Request $request,
    ReclamationRepository $reclamationRepository,
    ReponseRepository $reponseRepository
): Response {
    $selectedStatut = $request->query->get('statut');
    $query = $request->query->get('q');

    $qb = $reclamationRepository->createQueryBuilder('r');

    if ($query) {
        $qb->where('r.email LIKE :query OR r.sujet LIKE :query OR r.description LIKE :query OR r.statut LIKE :query')
           ->setParameter('query', '%' . $query . '%');
    }

    if ($selectedStatut) {
        $qb->andWhere('r.statut = :statut')
           ->setParameter('statut', $selectedStatut);
    }
    $qb->orderBy('r.dateCreation', 'DESC');
    $reclamations = $qb->getQuery()->getResult();


    $reclamationsWithReponses = $reponseRepository->findBy([], ['reclamation' => 'ASC']);
    $reclamationsWithReponsesIds = array_map(fn($r) => $r->getReclamation()->getId(), $reclamationsWithReponses);

    return $this->render('back-office/reclamation/listreclamation.html.twig', [
        'reclamations' => $reclamations,
        'reclamationsWithReponsesIds' => $reclamationsWithReponsesIds,
        'selected_statut' => $selectedStatut,
    ]);
}





    #[Route('/api/reclamations', name: 'api_list_reclamations', methods: ['GET'])]
public function listReclamationsApi(): JsonResponse
{
    $reclamations = $this->entityManager->getRepository(Reclamation::class)->findAll();
    
    $data = array_map(function ($reclamation) {
        return [
            'id' => $reclamation->getId(),
            'email' => $reclamation->getEmail(),
            'sujet' => $reclamation->getSujet(),
            'description' => $reclamation->getDescription(),
            'dateCreation' => $reclamation->getDateCreation()->format('Y-m-d H:i:s'),
        ];
    }, $reclamations);

    return new JsonResponse($data, Response::HTTP_OK);
}

/*
        #[Route('/edit/{id}', name: 'reclamation_edit')]
        public function edit(Reclamation $reclamation, Request $request, EntityManagerInterface $entityManager): Response
        {
            // Sauvegarder les champs à ne pas modifier
            $emailOriginal = $reclamation->getEmail();
            $sujetOriginal = $reclamation->getSujet();
            $descriptionOriginal = $reclamation->getDescription();

            $form = $this->createFormBuilder($reclamation)
                ->add('sujet', TextType::class, [
                    'label' => 'Sujet',
                    'disabled' => true,
                    'attr' => ['class' => 'form-control']
                ])
                ->add('email', TextType::class, [
                    'label' => 'Email',
                    'disabled' => true,
                    'attr' => ['class' => 'form-control']
                ])
                ->add('description', TextareaType::class, [
                    'label' => 'Description',
                    'disabled' => true,
                    'attr' => ['class' => 'form-control']
                ])
                ->add('statut', ChoiceType::class, [
                    'label' => 'Statut',
                    'choices' => [
                        'En attend' => 'en_attente',
                        'En cours' => 'en_cours',
                        'Terminer' => 'Terminer',
                    ],
                    'attr' => ['class' => 'form-control']
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Restaurer les valeurs originales
                $reclamation->setEmail($emailOriginal);
                $reclamation->setSujet($sujetOriginal);
                $reclamation->setDescription($descriptionOriginal);

                $entityManager->flush();
                $this->addFlash('success', 'Réclamation modifiée avec succès !');
                return $this->redirectToRoute('admin_reclamations');
            }

            return $this->render('back-office/reclamation/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }

       */ 

/*
        #[Route('/delete/{id}', name:'reclamation_delete', methods:["POST"])]
        
        public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): Response
        {
            $submittedToken = $request->request->get('_token');

            if ($csrfTokenManager->isTokenValid(new CsrfToken('delete' . $reclamation->getId(), $submittedToken))) {
                $entityManager->remove($reclamation);
                $entityManager->flush();

                $this->addFlash('success', 'Réclamation supprimée avec succès !');
            } else {
                $this->addFlash('danger', 'Token CSRF invalide.');
            }

            return $this->redirectToRoute('admin_reclamations');
        }


        #[Route('/api/reclamation/{id}', name: 'api_delete_reclamation', methods: ['DELETE'])]
public function deleteReclamationJson($id, EntityManagerInterface $entityManager): JsonResponse
{
    // Recherche de la réclamation par son ID
    $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

    if (!$reclamation) {
        // Si la réclamation n'existe pas, renvoyer une erreur 404
        return new JsonResponse(['error' => 'Réclamation non trouvée'], 404);
    }

    // Supprimer la réclamation
    $entityManager->remove($reclamation);
    $entityManager->flush();

    // Réponse JSON de succès
    return new JsonResponse(['message' => 'Réclamation supprimée avec succès'], 200);
}

delete + http://127.0.0.1:8000/api/reclamation/16 */


        
#[Route('/admin/reclamation/{id}/repondre', name:'admin_repondre_reclamation')]
public function repondre(int $id, Request $request, EntityManagerInterface $em, ReclamationRepository $reclamationRepository, MailerInterface $mailer): Response
{
    $reclamation = $reclamationRepository->find($id);

    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    $reponse = new Reponse();
    $form = $this->createForm(ReponseType::class, $reponse);
    $form->handleRequest($request);

    // 🎯 Gérer le statut manuellement depuis le request
    $statut = $request->request->get('statut');

    if ($form->isSubmitted() && $form->isValid()) {
        $reponse->setReclamation($reclamation);
        $reponse->setCreatedAt(new \DateTime());

        // ✅ Appliquer le statut à la réclamation
        if (in_array($statut, ['en_cours', 'Terminer'])) {
            $reclamation->setStatut($statut);
        }

        $em->persist($reponse);
        $em->persist($reclamation);
        $em->flush();

        // Envoi email
        $email = (new Email())
            ->from('tayssirbennejma@gmail.com')
            ->to($reclamation->getEmail())
            ->subject('Réponse à votre réclamation')
            ->text('Votre réclamation a été traitée. Merci pour votre patience.');

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Réponse envoyée par email avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
        }

        $this->addFlash('success', 'Réponse envoyée avec succès.');
        return $this->redirectToRoute('admin_reclamations');
    }

    return $this->render('back-office/reclamation/repondre.html.twig', [
        'reclamation' => $reclamation,
        'form' => $form->createView(),
    ]);
}

        




#[Route('/admin/reclamation/search', name: 'admin_search_reclamation', methods: ['GET'])]
public function search(Request $request, ReclamationRepository $reclamationRepository, ReponseRepository $reponseRepository): Response
{
    $query = $request->query->get('q'); // Récupérer la recherche
    
    if ($query) {
        $reclamations = $reclamationRepository->createQueryBuilder('r')
            ->where('r.email LIKE :query')
            ->orWhere('r.sujet LIKE :query')
            ->orWhere('r.description LIKE :query')
            ->orWhere('r.statut LIKE :query')
            ->orWhere('r.dateCreation LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    } else {
        $reclamations = $reclamationRepository->findAll();
    }

    // Récupérer les réclamations ayant des réponses
    $reclamationsWithReponses = $reponseRepository->findBy([], ['reclamation' => 'ASC']);
    
    // Récupérer les ID des réclamations ayant des réponses
    $reclamationsWithReponsesIds = array_map(function($reponse) {
        return $reponse->getReclamation()->getId();
    }, $reclamationsWithReponses);

    // Passer la variable à Twig
    return $this->render('back-office/reclamation/listreclamation.html.twig', [
        'reclamations' => $reclamations,
        'reclamationsWithReponsesIds' => $reclamationsWithReponsesIds, // Passer les IDs des réclamations avec réponses
    ]);
}

        
        #[Route('/statistiques', name: 'admin_statistiques_reclamation')]
        public function statistiquesReclamations(ReclamationRepository $reclamationRepository): Response
        {
            $reclamations = $reclamationRepository->findAll();
        
            // Compter le nombre total de réclamations
            $totalReclamations = count($reclamations);
        
            // Compter par statut
            $statutCount = [
                'en_attente' => 0,
                'en_cours' => 0,
                'Terminer' => 0
            ];
            foreach ($reclamations as $reclamation) {
                $statut = $reclamation->getStatut();
                if (isset($statutCount[$statut])) {
                    $statutCount[$statut]++;
                }
            }
        
            // Compter par catégorie
            $categoriesCount = [];
            foreach ($reclamations as $reclamation) {
                $categorie = $reclamation->getCategoryId() ? $reclamation->getCategoryId()->getType() : 'Non spécifiée';
                if (!isset($categoriesCount[$categorie])) {
                    $categoriesCount[$categorie] = 0;
                }
                $categoriesCount[$categorie]++;
            }
        
            // Évolution des réclamations par jour
            $evolutionReclamations = [];
            foreach ($reclamations as $reclamation) {
                $jour = $reclamation->getDateCreation()->format('Y-m-d'); // format correct pour jour
                if (!isset($evolutionReclamations[$jour])) {
                    $evolutionReclamations[$jour] = 0;
                }
                $evolutionReclamations[$jour]++;
            }
        
            // Tri des jours pour afficher du plus ancien au plus récent
            ksort($evolutionReclamations);
        
            // Dump pour vérifier
            dump($evolutionReclamations); 
        
            return $this->render('back-office/reclamation/statistiques.html.twig', [
                'totalReclamations' => $totalReclamations,
                'statutCount' => $statutCount,
                'categoriesCount' => $categoriesCount,
                'evolutionReclamations' => $evolutionReclamations, // Passer l'évolution par jour au template
            ]);
        }
           
    }

    