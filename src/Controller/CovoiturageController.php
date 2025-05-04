<?php

namespace App\Controller;
use App\Entity\Payment;
use App\Entity\Posts;
use App\Entity\User;
use App\Repository\PostsRepository;
use App\Service\EmailService;
use App\Service\SmsService; 
use App\Service\StripePaymentService;
use App\Form\PostsType; 
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

use BaconQrCode\Renderer\Path\PathImageInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\QrCodeService;






class CovoiturageController extends AbstractController
{
    private $smsService;
    private $entityManager;

    // Inject both services in a single constructor
    public function __construct(SmsService $smsService, EntityManagerInterface $entityManager)
    {
        $this->smsService = $smsService;
        $this->entityManager = $entityManager;
    }


    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(): Response
    {
        return $this->render('covoiturage/index.html.twig');
    }

    #[Route('/covoiturage/poster', name: 'app_covoiturage_poster')]
    public function poster(Request $request): Response
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
                        // Get the connected user
                        /** @var \App\Entity\User $user */
                        $user = $this->getUser();
    
                        if (!$user) {
                            $this->addFlash('error', 'Utilisateur non connecté.');
                            return $this->redirectToRoute('app_login');
                        }
    
                        $post->setUser($user); // Set the connected user
    
                        $this->entityManager->persist($post);
                        $this->entityManager->flush();
    
                        if ($post->getDate()->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {
                            $phoneNumber = '+216' . $user->getTelephonne();
                            $message = sprintf(
                                "Bonjour %s, votre covoiturage de %s à %s est prévu aujourd'hui.",
                                $user->getNom(),
                                $post->getVilleDepart(),
                                $post->getVilleArrivee()
                            );
                            $this->smsService->sendSms($phoneNumber, $message);
    
                            $this->addFlash('success', 'Votre trajet a été publié et la notification SMS a été envoyée !');
                        } else {
                            $this->addFlash('success', 'Votre trajet a été publié. Il sera notifié par SMS lorsqu\'il sera prévu pour aujourd\'hui.');
                        }
    
                        return $this->render('covoiturage/poster.html.twig', [
                            'form' => $form->createView(),
                        ]);
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
    
    #[Route('/covoiturage/show/{id}', name: 'app_covoiturage_show')]
    public function show(Posts $post): Response
    {
        // Render the details of the post (departure city, arrival city, etc.)
        return $this->render('covoiturage/show.html.twig', [
            'post' => $post,
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
        BadWordFilter $badWordFilter,
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
    
        // Get the connected user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Utilisateur non connecté.');
            return $this->redirectToRoute('app_login');
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
    
    #[Route('/covoiturage/reserver/{id_post}', name: 'app_covoiturage_reserver', methods: ['GET'])]
    public function reserver(Posts $post): Response
    {
        return $this->render('covoiturage/reserver.html.twig', [
            'post' => $post,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }
    #[Route('/payment/{id_post}', name: 'app_process_payment', methods: ['POST'])]
public function processPayment(
    Posts $post,
    Request $request,
    StripePaymentService $stripePayment,
    EmailService $emailService,
    EntityManagerInterface $entityManager,
    SessionInterface $session,
    QrCodeService $qrCodeService
): Response {
    $email = $request->request->get('email');
    $paymentMethodId = $request->request->get('payment_method_id');
    $places = (int) $request->request->get('places', 1);
    
    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->addFlash('error', 'Invalid email address.');
        return $this->render('covoiturage/reserver.html.twig', [
            'post' => $post,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    if ($places > $post->getNombreDePlaces()) {
        $this->addFlash('error', 'Not enough available places.');
        return $this->render('covoiturage/reserver.html.twig', [
            'post' => $post,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }

    $amount = $post->getPrix() * $places;

    try {
        // Create payment intent
        $paymentIntent = $stripePayment->createPaymentIntent($amount);
        
        // Confirm payment
        $confirmedIntent = $stripePayment->confirmPayment($paymentIntent->id, $paymentMethodId);
        
        // Check payment status
        if ($confirmedIntent->status !== 'succeeded') {
            throw new \Exception('Payment did not complete successfully.');
        }

        // Save payment to database
        $payment = new Payment();
        $payment->setTransactionId($paymentIntent->id);
        $payment->setAmount($amount);
        $payment->setEmail($email);
        $entityManager->persist($payment);
        
        // Update available places
        $post->setNombreDePlaces($post->getNombreDePlaces() - $places);
        $entityManager->flush();

        $data = <<<EOT
Reservation confirmée
Email : $email
Départ : {$post->getVilleDepart()}
Arrivée : {$post->getVilleArrivee()}
Nombre de places : $places
Montant payé : $amount TND
EOT;

        $qrPath = $qrCodeService->generateQrCode($data);
        $session->set('qr_path', $qrPath);

        // Send confirmation email
        $emailSent = $emailService->sendPaymentConfirmation(
            $email,
            $amount,
            $places,
            $post->getVilleDepart(),
            $post->getVilleArrivee()
        );

        if ($emailSent) {
            $this->addFlash('success', 'Payment successful and confirmation email sent!');
        } else {
            $this->addFlash('warning', 'Payment successful but email not sent.');
        }

        // Render the page again with a success message
        return $this->redirectToRoute('covoiturage_show_qr', ['id_post' => $post->getIdPost()]);
        ;
        
    } catch (\Exception $e) {
        $this->addFlash('error', 'Payment error: ' . $e->getMessage());
        return $this->render('covoiturage/reserver.html.twig', [
            'post' => $post,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
        ]);
    }
}
#[Route('/covoiturage/{id_post}/qr', name: 'covoiturage_show_qr')]
public function showQr(int $id_post, SessionInterface $session, EntityManagerInterface $entityManager): Response
{
    $qrPath = $session->get('qr_path');

    if (!$qrPath) {
        $this->addFlash('error', 'Aucun QR Code trouvé.');
        return $this->redirectToRoute('app_homepage');
    }

    $post = $entityManager->getRepository(Posts::class)->find($id_post);

    return $this->render('covoiturage/qr.html.twig', [
        'qrPath' => $qrPath,
        'post' => $post,
    ]);
}


    
    #[Route('/payment/success', name: 'app_payment_success')]
    public function paymentSuccess(): Response
    {

        return $this->render('payment/reserver.html.twig');
    }

    #[Route('/covoiturage/mes-offres', name: 'app_covoiturage_mes_offres')]
    public function mesOffres(EntityManagerInterface $entityManager): Response
    {
        // Get the currently authenticated user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
    
        // Check if the user is authenticated
        if (!$user) {
            throw $this->createNotFoundException('User not found or not authenticated');
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
        // Retrieve the currently authenticated user
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
    
        // Find the post by ID
        $post = $entityManager->getRepository(Posts::class)->find($id_post);
        
        // Check if the post exists
        if (!$post) {
            throw $this->createNotFoundException('Post non trouvé');
        }
    
        // Debugging log: check if the post's user ID matches the logged-in user
        $userId = $user->getIdUser();
        $postOwnerId = $post->getUser()->getIdUser();
        dump($userId, $postOwnerId);  // You can remove this once you're sure
    
        // Check if the logged-in user is the owner of the post
        if ($post->getUser()->getIdUser() !== $user->getIdUser()) {
            throw $this->createAccessDeniedException('Accès non autorisé');
        }
    
        // Create the form to modify the post
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
            ->add('prix', MoneyType::class, ['currency' => 'TND'])
            ->getForm();
    
        $form->handleRequest($request);
    
        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Custom validation logic (if needed)
            $this->validateBusinessRules($post, $form);
            
            // If no errors, persist the changes and flush to the database
            if (!$form->getErrors(true)->count()) {
                $entityManager->flush();
                $this->addFlash('success', 'Le trajet a été modifié avec succès!');
                return $this->redirectToRoute('app_covoiturage_mes_offres');
            }
        }
    
        // Render the form in the template
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
    // Get the currently authenticated user
    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    // Check if the user is authenticated
    if (!$user) {
        $this->addFlash('error', 'User not authenticated');
        return $this->redirectToRoute('app_login'); // Redirect to login page if the user is not authenticated
    }

    // Find the post by ID
    $post = $entityManager->getRepository(Posts::class)->find($id_post);

    if (!$post) {
        $this->addFlash('error', 'Post not found');
        return $this->redirectToRoute('app_covoiturage_mes_offres');
    }

    // Check if the logged-in user is the owner of the post
    if ($post->getUser()->getIdUser() !== $user->getIdUser()) {
        $this->addFlash('error', 'Unauthorized to delete this post');
        return $this->redirectToRoute('app_covoiturage_mes_offres');
    }

    // Remove the post
    $entityManager->remove($post);
    $entityManager->flush();

    // Flash success message
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
    // Find the comment by ID
    $commentaire = $entityManager->getRepository(Commentaire::class)->find($id);
    
    if (!$commentaire) {
        throw $this->createNotFoundException('Comment not found');
    }

    // Restrict editing to the currently logged-in user
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('You can only edit your own comments');
    }

    // Create the form for editing the comment
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

        // Save the changes to the database
        $entityManager->flush();
        $this->addFlash('success', 'Commentaire modifié avec succès!');
        return $this->redirectToRoute('app_covoiturage_rechercher');
    }

    // Render the form view
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

    // Restrict deletion to the currently logged-in user
    if ($commentaire->getUser() !== $this->getUser()) {
        throw $this->createAccessDeniedException('You can only delete your own comments');
    }

    // Check CSRF token validity
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

#[Route('/stripe/webhook', name: 'stripe_webhook')]
public function handleWebhook(Request $request, EntityManagerInterface $em): Response
{
    $payload = $request->getContent();
    $sig_header = $request->headers->get('stripe-signature');
    $endpoint_secret = $this->getParameter('stripe_webhook_secret');

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, $endpoint_secret
        );
    } catch (\Exception $e) {
        return new Response('Invalid signature', 400);
    }

    switch ($event->type) {
        case 'payment_intent.succeeded':
            $paymentIntent = $event->data->object;
            // Handle successful payment
            break;
        // Add other event types as needed
    }

    return new Response('Success', 200);
}
}