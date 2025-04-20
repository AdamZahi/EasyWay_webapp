<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventComment;
use App\Entity\User;
use App\Form\EventType;
use App\Form\EventCommentType;
use App\Repository\EventCommentRepository;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // For testing with static user ID 1
        $currentUser = $entityManager->getRepository(User::class)->find(10);
        $event = new Event();
        $event->setStatus('En cours');
        $event->setId_createur($currentUser) ;
        $event->setDateDebut(new \DateTime());
        
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'L\'événement a été créé avec succès.');
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
} 