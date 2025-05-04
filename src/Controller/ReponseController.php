<?php

namespace App\Controller;

// src/Controller/ReponseController.php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Reponse;
use App\Entity\Reclamation;
use App\Form\ReponseType;
use App\Repository\ReponseRepository;
use App\Repository\ReclamationRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;


#[Route('/reponse')]
class ReponseController extends AbstractController
{
    
    private $logger;

    // Injecter le logger via le constructeur
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    #[Route('/reponse/{id}', name: 'reponse_create')]
    public function create(Request $request, int $id, EntityManagerInterface $em, MailerInterface $mailer): Response
{
    // Récupérer la réclamation
    $reclamation = $em->getRepository(Reclamation::class)->find($id);
    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée.');
    }

    // Créer une nouvelle réponse et assigner la réclamation
    $reponse = new Reponse();
    $reponse->setReclamation($reclamation);
    $reponse->setCreatedAt(new \DateTimeImmutable());

    // Créer le formulaire
    $form = $this->createForm(ReponseType::class, $reponse);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Sauvegarder la réponse dans la base de données
        $em->persist($reponse);
        $em->flush();

        // ✉ Envoi de l'email au client après la création de la réponse
        $email = (new Email())
            ->from('from@example.org')
            ->to($reclamation->getEmail()) // Utilisation de l'email de la réclamation
            ->subject('Réponse à votre réclamation')
            ->text('Bonjour, voici la réponse à votre réclamation : ' . $reponse->getContenu());

        try {
            // Envoi de l'email
            $mailer->send($email);
            $this->addFlash('success', 'Réponse envoyée par email avec succès.');
        } catch (\Exception $e) {
            // Capture l'erreur d'envoi et logge un message
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'email : ' . $e->getMessage());
            // Log l'erreur dans les logs Symfony
            $this->logger->error('Erreur d\'envoi de l\'email: ' . $e->getMessage());
        }

        // Redirection après ajout de la réponse et envoi de l'email
        return $this->redirectToRoute('admin_reclamations');
    }

    return $this->render('reponse/create.html.twig', [
        'form' => $form->createView(),
        'reclamation' => $reclamation,
        'date' => new \DateTimeImmutable(),
    ]);
}



    #[Route('/admin/reclamation/{id}/reponses', name: 'admin_voir_reponses')]
public function voirReponses(Reclamation $reclamation): Response
{
    // Vérifier si la réclamation existe
    if (!$reclamation) {
        throw $this->createNotFoundException('Réclamation non trouvée');
    }

    // Récupérer les réponses de la réclamation
    $reponses = $reclamation->getReponses(); // Assurez-vous que vous avez la relation avec Reponse

    return $this->render('back-office/reclamation/voirReponse.html.twig', [
        'reclamation' => $reclamation,
        'reponses' => $reponses,
    ]);
}



#[Route('/modifier_reponse/{id}', name: 'modifier_reponse', methods: ['GET', 'POST'])]
public function modifierReponse(Request $request, ReponseRepository $reponseRepository, EntityManagerInterface $entityManager, int $id): Response
{
    $reponse = $reponseRepository->find($id);
    if (!$reponse) {
        throw $this->createNotFoundException('Réponse non trouvée.');
    }

    if ($request->isMethod('POST')) {
        $nouveauContenu = $request->request->get('content');
        $reponse->setContenu($nouveauContenu);

        $entityManager->persist($reponse);
        $entityManager->flush();

        return $this->redirectToRoute('admin_voir_reponses', ['id' => $reponse->getReclamation()->getId()]);
    }

    return $this->render('back-office/reclamation/voirReponse.html.twig', [
        'reponse' => $reponse
    ]);
}



#[Route('/reponse/supprimer/{id}', name: 'supprimer_reponse', methods: ['GET', 'POST'])]
public function supprimerReponse(Reponse $reponse, EntityManagerInterface $entityManager): Response
{
    if (!$reponse) {
        throw $this->createNotFoundException('Réponse non trouvée.');
    }

    $entityManager->remove($reponse);
    $entityManager->flush();

    $this->addFlash('success', 'Réponse supprimée avec succès.');

    return $this->redirectToRoute('admin_voir_reponses', ['id' => $reponse->getReclamation()->getId()]);
}

/*
#[Route('/reponses-par-email/{email}', name: 'voir_reponses_par_email')]
public function voirReponsesParEmail(string $email, ReclamationRepository $reclamationRepository): Response
{
    // Récupérer toutes les réclamations associées à cet email
    $reclamations = $reclamationRepository->findByEmail($email);

    $reponses = [];
    foreach ($reclamations as $reclamation) {
        foreach ($reclamation->getReponses() as $reponse) {
            $reponses[] = $reponse;
        }
    }

    return $this->render('front-office/reclamation/reponses_par_email.html.twig', [
        'email' => $email,
        'reponses' => $reponses,
    ]);
}*/



}

