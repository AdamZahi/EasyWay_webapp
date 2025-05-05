<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventComment;
use App\Entity\User;
use App\Form\EventType;
use App\Form\EventCommentType;
use App\Repository\EventCommentRepository;
use Symfony\Component\Mime\Email;
use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
class EventController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(Request $request, EventRepository $eventRepository): Response
    {
        $searchQuery = $request->query->get('search');
        
        if ($searchQuery) {
            $events = $eventRepository->search($searchQuery);
        } else {
            $events = $eventRepository->findAll();
        }
        
        return $this->render('event/index.html.twig', [
            'events' => $events,
            'searchQuery' => $searchQuery,
        ]);
    }

    #[Route('/front', name: 'app_event_front', methods: ['GET'])]
    public function frontList(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findBy(
            ['status' => 'En cours'], 
            ['dateDebut' => 'ASC']    
        );

        return $this->render('event/front-events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        ReservationRepository $reservationRepository,
        MailerService $mailerService
        // ,Client $twilio
    ): Response {
        $currentUser = $this->getUser(); 
        $event = new Event();
        $event->setStatus('En cours'); 
        $event->setDateDebut(new \DateTime());
        $event->setId_createur($currentUser);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
    
            $depart = $event->getLigneAffectee()->getDepart();
            $arret = $event->getLigneAffectee()->getArret();
    
            $reservations = $reservationRepository->findReservationsByDepartAndArret($depart, $arret);
            $notified_users = [];
    
            foreach ($reservations as $reservation) {
                $user = $reservation->getUser();
                $user_email =$user->getEmail(); 
    
                if ($user_email && !in_array($user_email, $notified_users)) {
                    $notified_users[] = $user_email;
                    $user_name = $user->getPrenom(). " ".$user->getNom(); 

                    $email = (new Email())
                    ->from('mejrieya384@gmail.com')
                    ->to($user_email)
                    ->subject('Nouvel événement sur votre ligne')
                    ->html("Cher(e) $user_name,<br><br>
                    Un nouvel événement a été ajouté sur votre ligne $depart → $arret.<br>
                    Date de début : " . $event->getDateDebut()->format('d/m/Y H:i') . "<br>
                    Date de fin prevue: " . $event->getDateFin()->format('d/m/Y H:i') . "<br>
                    Merci de consulter votre compte pour plus de détails.<br><br>
                    Cordialement,<br>
                    L'équipe de Easy Way");
        
                    $this->mailer->send($email);
                    
                }
            }
    
            $this->addFlash('success', 'Événement crée et SMS envoyé !');
            return $this->redirectToRoute('app_event_index');
        }
    
        return $this->render('event/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/front/{id}', name:'app_event_front_show', methods: ['GET'])]
    public function frontShow(Event $event): Response{
        return $this->render('event/front-show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été modifié avec succès.');
            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();
            $this->addFlash('success', 'L\'événement a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/{id}/comments', name: 'event_comment')]
    public function comment(Request $request, Event $event, EventCommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        // For testing with static user ID 1
        $currentUser = $entityManager->getRepository(User::class)->find(1);
        $comment = new EventComment();
        $form = $this->createForm(EventCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setEvent($event);
            $comment->setUser($currentUser); // Set the current user
            $comment->setCreatedAt(new \DateTime());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Commentaire ajouté avec succès.');
            return $this->redirectToRoute('event_comment', ['id' => $event->getId()]);
        }

        $comments = $commentRepository->findBy(['event' => $event], ['createdAt' => 'DESC']);

        return $this->render('event/eventComment.html.twig', [
            'event' => $event,
            'comments' => $comments,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ev/statistics', name: 'app_admin_statistics')]
    public function statistiquesEvenements(EventRepository $eventRepo): Response{
    $events = $eventRepo->findAll();
    $totalEvents = count($events);
    $statusCount = [];
    $typeCount = [];
    $evolutionEvents = [];
    $ligneAffecteeCount = [];
    $timeOfDayCount = [ 'le matin' => 0, "L'après-midi" => 0, 'Le soir' => 0, "L'aube" => 0 ];

    foreach ($events as $event) {
        $hour = (int) $event->getDateDebut()->format('H');

        if ($hour >= 6 && $hour < 12) {
            $timeOfDayCount['le matin']++;
        } elseif ($hour >= 12 && $hour < 18) {
            $timeOfDayCount["L'après-midi"]++;
        } elseif ($hour >= 18 && $hour < 24) {
            $timeOfDayCount['Le soir']++;
        } else {
            $timeOfDayCount["L'aube"]++;
        }
        $status = $event->getStatus();
        $type = $event->getType();
        $date = $event->getDateDebut()->format('Y-m-d');
        $ligne = $event->getLigneAffectee() ? $event->getLigneAffectee()->getDepart() . ' - ' . $event->getLigneAffectee()->getArret() : 'Inconnue';
        if (!isset($statusCount[$status])) {
            $statusCount[$status] = 0;
        }
        $statusCount[$status]++;
        if (!isset($typeCount[$type])) {
            $typeCount[$type] = 0;
        }
        $typeCount[$type]++;
        if (!isset($evolutionEvents[$date])) {
            $evolutionEvents[$date] = 0;
        }
        $evolutionEvents[$date]++;

        if (!isset($ligneAffecteeCount[$ligne])) {
            $ligneAffecteeCount[$ligne] = 0;
        }
        $ligneAffecteeCount[$ligne]++;
    }

    ksort($evolutionEvents);

    return $this->render('event/statistics.html.twig', [
        'totalEvents' => $totalEvents,
        'statusCount' => $statusCount,
        'typeCount' => $typeCount,
        'ligneAffecteeCount' => $ligneAffecteeCount,
        'evolutionEvents' => $evolutionEvents,
        'timeOfDayLabels' => array_keys($timeOfDayCount),
        'timeOfDayData' => array_values($timeOfDayCount),
    ]);
}

}