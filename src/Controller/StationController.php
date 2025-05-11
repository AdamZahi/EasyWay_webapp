<?php

namespace App\Controller;

use App\Entity\Station;
use App\Entity\Admin;
use App\Entity\Ligne;
use App\Form\StationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class StationController extends AbstractController
{
    #[Route('/admin/station/add', name: 'station_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adminId = $request->query->get('id_admin');
        $ligneId = $request->query->get('id_ligne');

        // Since the Admin uses "id_admin" as the primary key, we fetch it with find($adminId)
        /** @var Admin|null $admin */
        $admin = $entityManager->getRepository(Admin::class)->find($adminId);
        /** @var Ligne|null $ligne */
        $ligne = $entityManager->getRepository(Ligne::class)->find($ligneId);

        if (!$admin || !$ligne) {
            throw $this->createNotFoundException("Admin ou Ligne introuvable.");
        }

        $station = new Station();
        $station->setAdmin($admin);
        $station->setLigne($ligne);

        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($station);
            $entityManager->flush();

            $this->addFlash('success', 'Station ajoutée avec succès!');
            return $this->redirectToRoute('station_list_admin');
        }

        return $this->render('back-office/station/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/station/list', name: 'station_list_admin')]
    public function listAdmin(EntityManagerInterface $entityManager): Response
    {
        $stations = $entityManager->getRepository(Station::class)->findAll();

        return $this->render('back-office/station/list.html.twig', [
            'stations' => $stations
        ]);
    }

    #[Route('/station/list', name: 'station_list')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $stations = $entityManager->getRepository(Station::class)->findAll();

        return $this->render('station/list.html.twig', [
            'stations' => $stations
        ]);
    }

    #[Route('/admin/station/edit/{id}', name: 'station_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $station = $entityManager->getRepository(Station::class)->find($id);

        if (!$station) {
            throw $this->createNotFoundException('Station non trouvée.');
        }

        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Station mise à jour avec succès!');
            return $this->redirectToRoute('station_list_admin');
        }

        return $this->render('/back-office/station/update.html.twig', [
            'form' => $form->createView(),
            'station' => $station
        ]);
    }

    #[Route('/admin/station/delete/{id}', name: 'station_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $station = $entityManager->getRepository(Station::class)->find($id);

        if ($station) {
            $entityManager->remove($station);
            $entityManager->flush();
            $this->addFlash('success', 'Station supprimée avec succès!');
        }

        return $this->redirectToRoute('station_list_admin');
    }
}
