<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


final class ReservationController extends AbstractController
{
    #[Route('/reservation/add', name: 'reservation_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        SessionInterface $session
    ): Response {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $reservation->setUserId($user);
            $entityManager->persist($reservation);
            $entityManager->flush();

            $session->set('reservation_id', $reservation->getId());

            return $this->redirectToRoute('paiement_add');
        }

        $user = $security->getUser();
        $reservations = $entityManager->getRepository(Reservation::class)->findBy(['user' => $user]);

        return $this->render('reservation/add.html.twig', [
            'form' => $form->createView(),
            'reservations' => $reservations
        ]);
    }
    
    #[Route('/reservation/list', name: 'reservation_list')]
public function list(EntityManagerInterface $entityManager): Response
{
    $user = $this->getUser();
    $reservations = $entityManager->getRepository(Reservation::class)->findBy([
        'user' => $user
    ]);

    return $this->render('/reservation/list.html.twig', [
        'reservations' => $reservations
    ]);
}


    #[Route('/reservation/edit/{id}', name: 'reservation_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);

        if (!$reservation) {
            throw $this->createNotFoundException('Aucune réservation trouvée');
        }

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('succèss', 'Réservation ajoutée avec succès!');
            return $this->redirectToRoute('reservation_list');
        }

        return $this->render('/reservation/update.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

    #[Route('/reservation/delete/{id}', name: 'reservation_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $reservation = $entityManager->getRepository(Reservation::class)->find($id);

        if ($reservation) {
            $entityManager->remove($reservation);
            $entityManager->flush();
            $this->addFlash('succèss', 'Réservation supprimée avec succès!');
        }

        return $this->redirectToRoute('reservation_list');
    }
}
