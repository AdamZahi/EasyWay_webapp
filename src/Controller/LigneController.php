<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Form\LigneType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


final class LigneController extends AbstractController
{
    #[Route('/dashboard/ligne/add', name: 'ligne_add')]
public function add(Request $request, EntityManagerInterface $entityManager): Response
{
    $ligne = new Ligne();
    $form = $this->createForm(LigneType::class, $ligne);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($ligne);
        $entityManager->flush();

        return $this->redirectToRoute('ligne_list');
    }

    return $this->render('/ligne/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/ligne/list', name: 'ligne_list')]
public function list(EntityManagerInterface $entityManager): Response
{
    $lignes = $entityManager->getRepository(Ligne::class)->findAll();

    return $this->render('/ligne/list.html.twig', [
        'lignes' => $lignes
    ]);
}
#[Route('dashboard/ligne/list', name: 'ligne_list_admin')]
public function list_admin(EntityManagerInterface $entityManager): Response
{
    $lignes = $entityManager->getRepository(Ligne::class)->findAll();

    return $this->render('/ligne/list_ligne.html.twig', [
        'lignes' => $lignes
    ]);
}

#[Route('/ligne/edit/{id}', name: 'ligne_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $ligne = $entityManager->getRepository(Ligne::class)->find($id);

    if (!$ligne) {
        throw $this->createNotFoundException('Ligne not found');
    }

    $form = $this->createForm(LigneType::class, $ligne);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Ligne updated successfully!');
        return $this->redirectToRoute('ligne_list');
    }

    return $this->render('/ligne/update.html.twig', [
        'form' => $form->createView(),
        'ligne' => $ligne
    ]);
}

#[Route('/ligne/delete/{id}', name: 'ligne_delete')]
public function delete(EntityManagerInterface $entityManager, int $id): Response
{
    $ligne = $entityManager->getRepository(Ligne::class)->find($id);

    if ($ligne) {
        $entityManager->remove($ligne);
        $entityManager->flush();
        $this->addFlash('success', 'Ligne deleted successfully!');
    }

    return $this->redirectToRoute('ligne_list');
}

}
