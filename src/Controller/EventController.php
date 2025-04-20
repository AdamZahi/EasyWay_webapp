<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventComment;
use App\Entity\User;
use App\Form\EventType;
use App\Form\EventFilterType;
use App\Form\EventCommentType;
use App\Repository\EventCommentRepository;
use App\Repository\LigneRepository;
use App\Repository\EventRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/event')]
class EventController extends AbstractController
{
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
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        ReservationRepository $reservationRepository
    ): Response {
        // For testing with static user ID 1
        $currentUser = $entityManager->getRepository(User::class)->find(10);
        $event = new Event();
        $event->setStatus('En cours');
        $event->setId_createur($currentUser);
        $event->setDateDebut(new \DateTime());

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            // Fetch users who made reservations in the ligneAffectee
            $reservations = $reservationRepository->findBy(['depart' => $event->getLigneAffectee()->getDepart(), 'arret' => $event->getLigneAffectee()->getArret()]);
            foreach ($reservations as $reservation) {
                $user = $reservation->getUser();
                if ($user && $user->getEmail()) {
                    // Send email to the user
                    // $email = (new Email())
                    //     ->from('no-reply@demomailtrap.co')
                    //     ->to($user->getEmail())
                    //     ->subject('Nouvel événement sur votre ligne')
                    //     ->text('Un nouvel événement a été ajouté sur votre ligne.
                    //         Départ: ' . $event->getLigneAffectee()->getDepart() . '
                    //         Arrêt: ' . $event->getLigneAffectee()->getArret() . '
                    //         Description: ' . $event->getDescription()
                    //         . 'Merci de consulter l\'application pour plus de détails.');
                    // $mailer->send($email);
                    // Envoi de l'email de confirmation
                try {
                    $this->sendEmail(
                        $mailer,
                        $user->getEmail(),
                        'Nouvel événement sur votre ligne',
                        'Un nouvel événement a été ajouté sur votre ligne.
                                Départ: ' . $event->getLigneAffectee()->getDepart() . '
                                Arrêt: ' . $event->getLigneAffectee()->getArret() . '
                                Description: ' . $event->getDescription()
                            . 'Merci de consulter l\'application pour plus de détails.'
                    );
                    $this->addFlash('success', 'Email envoyé avec succès à ' . $user->getEmail());
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error : ' . $e->getMessage());
                }
                }
            }

            $this->addFlash('success', 'L\'événement a été crée avec succès et les utilisateurs concernés ont été notifiés.');
            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
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

    foreach ($events as $event) {
        $status = $event->getStatus();
        $type = $event->getType();
        $date = $event->getDateDebut()->format('Y-m-d');
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
    }

    ksort($evolutionEvents);

    return $this->render('event/statistics.html.twig', [
        'totalEvents' => $totalEvents,
        'statusCount' => $statusCount,
        'typeCount' => $typeCount,
        'evolutionEvents' => $evolutionEvents
    ]);
}

    public function sendEmail(MailerInterface $mailer, $toEmail, $subject, $body)
    {
        $email = (new Email())
            ->from('zahi.adem10@gmail.com')
            ->to($toEmail)
            ->subject($subject)
            ->text($body);

        $mailer->send($email);
    }
}