<?php

namespace App\Controller;


use App\Entity\Station;
use App\Form\StationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StationController extends AbstractController
{
    #[Route('/station/add', name: 'station_add')]
public function add(Request $request, EntityManagerInterface $entityManager): Response
{
    $station = new Station();
    $form = $this->createForm(StationType::class, $station);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($station);
        $entityManager->flush();

        return $this->redirectToRoute('station_list');
    }

    return $this->render('/station/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/station/list', name: 'station_list')]
public function list(EntityManagerInterface $entityManager): Response
{
    $stations = $entityManager->getRepository(Station::class)->findAll();

    return $this->render('/station/list.html.twig', [
        'stations' => $stations
    ]);
}

#[Route('/station/edit/{id}', name: 'station_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $station = $entityManager->getRepository(Station::class)->find($id);

    if (!$station) {
        throw $this->createNotFoundException('Station not found');
    }

    $form = $this->createForm(StationType::class, $station);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Station updated successfully!');
        return $this->redirectToRoute('station_list');
    }

    return $this->render('/station/update.html.twig', [
        'form' => $form->createView(),
        'station' => $station
    ]);
}

#[Route('/station/delete/{id}', name: 'station_delete')]
public function delete(EntityManagerInterface $entityManager, int $id): Response
{
    $station = $entityManager->getRepository(Station::class)->find($id);

    if ($station) {
        $entityManager->remove($station);
        $entityManager->flush();
        $this->addFlash('success', 'Station deleted successfully!');
    }

    return $this->redirectToRoute('station_list');
}

}
