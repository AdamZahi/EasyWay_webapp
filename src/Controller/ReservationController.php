<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ReservationController extends AbstractController
{
    #[Route('/reservation/add', name: 'reservation_add')]
public function add(Request $request, EntityManagerInterface $entityManager): Response
{
    $reservation = new Reservation();
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->redirectToRoute('reservation_list');
    }

    return $this->render('reservation/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/reservation/list', name: 'reservation_list')]
public function list(EntityManagerInterface $entityManager): Response
{
    $reservations = $entityManager->getRepository(Reservation::class)->findAll();

    return $this->render('/reservation/list.html.twig', [
        'reservations' => $reservations
    ]);
}

#[Route('/reservation/edit/{id}', name: 'reservation_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $reservation = $entityManager->getRepository(Reservation::class)->find($id);

    if (!$reservation) {
        throw $this->createNotFoundException('Reservation not found');
    }

    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Reservation updated successfully!');
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
        $this->addFlash('success', 'Reservation deleted successfully!');
    }

    return $this->redirectToRoute('reservation_list');
}

}
