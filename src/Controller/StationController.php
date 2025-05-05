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
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

final class StationController extends AbstractController
{
    #[Route('/admin/station/add', name: 'station_add')]
public function add(
    Request $request,
    EntityManagerInterface $entityManager
): Response {
    // Create a new Station entity
    $station = new Station();

    // Get ligne_id and admin_id from query parameters
    $ligneId = $request->query->get('ligne_id');
    $adminId = $request->query->get('admin_id');

    // Cast adminId to an integer
    $adminId = (int) $adminId;

    // Check if the parameters are missing
    if (!$ligneId || !$adminId) {
        $this->addFlash('error', 'Missing ligne_id or admin_id.');
        return $this->render('back-office/station/add.html.twig', [
            'form' => $this->createForm(StationType::class, $station)->createView(),
        ]);
    }

    // Fetch the Ligne and Admin entities using the IDs
    $ligne = $entityManager->getRepository(Ligne::class)->find($ligneId);
    $admin = $entityManager->getRepository(Admin::class)->find($adminId);

    // Check if either Ligne or Admin is not found
    if (!$ligne || !$admin) {
        $this->addFlash('error', 'Ligne or Admin not found.');
        return $this->render('back-office/station/add.html.twig', [
            'form' => $this->createForm(StationType::class, $station)->createView(),
        ]);
    }

    // Set the relations between the Station, Ligne, and Admin
    $station->setLigne($ligne);
    $station->setAdmin($admin);

    // Create and handle the form
    $form = $this->createForm(StationType::class, $station);
    $form->handleRequest($request);

    // Check if the form is submitted and valid
    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the new station
        $entityManager->persist($station);
        $entityManager->flush();

        // Add a success message
        $this->addFlash('success', 'Station ajoutée avec succès.');

        // Redirect to the station list page
        return $this->redirectToRoute('station_list_admin');
    }

    // If the form is not submitted or invalid, render the form again
    return $this->render('back-office/station/add.html.twig', [
        'form' => $form->createView(),
        'ligne_id' => $ligneId, // Pass ligne_id
        'admin' => $adminId, // Pass admin_id
    ]);
}




    #[Route('admin/station/list', name: 'station_list_admin')]
    public function list(EntityManagerInterface $entityManager): Response
    {
        $stations = $entityManager->getRepository(Station::class)->findAll();

        return $this->render('back-office/station/list.html.twig', [
            'stations' => $stations
        ]);
    }
    //espace utilisateur
#[Route('/station/list', name: 'station_list_utilisateur')]
public function list2(EntityManagerInterface $entityManager): Response
{
    $stations = $entityManager->getRepository(Station::class)->findAll();

    return $this->render('/station/list.html.twig', [
        'stations' => $stations
    ]);
}

    #[Route('/station/edit/{id}', name: 'station_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, int $id, Security $security): Response
    {
        $station = $entityManager->getRepository(Station::class)->find($id);

        if (!$station) {
            throw $this->createNotFoundException('Station not found');
        }

        // Ensure that the current user is associated with the station's admin
        $user = $security->getUser();
        $admin = $entityManager->getRepository(Admin::class)->findOneBy(['user' => $user]);

        if ($admin === null || $station->getAdmin() !== $admin) {
            $this->addFlash('error', 'You are not authorized to edit this station.');
            return $this->redirectToRoute('station_list_admin');
        }

        // Create the form
        $form = $this->createForm(StationType::class, $station);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Station updated successfully!');
            return $this->redirectToRoute('station_list_admin');
        }

        return $this->render('/station/update.html.twig', [
            'form' => $form->createView(),
            'station' => $station
        ]);
    }

    #[Route('admin/station/delete/{id}', name: 'station_delete')]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $station = $entityManager->getRepository(Station::class)->find($id);

        if ($station) {
            $entityManager->remove($station);
            $entityManager->flush();
            $this->addFlash('success', 'Station deleted successfully!');
        }

        return $this->redirectToRoute('station_list_admin');
    }
}
