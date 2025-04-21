<?php

namespace App\Controller;


use App\Entity\Paiement;
use App\Form\PaiementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class PaiementController extends AbstractController
{
    #[Route('/paiement/add', name: 'paiement_add')]
public function add(Request $request, EntityManagerInterface $entityManager): Response
{
    $paiement = new Paiement();
    $form = $this->createForm(PaiementType::class, $paiement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($paiement);
        $entityManager->flush();

        return $this->redirectToRoute('paiement_list');
    }

    return $this->render('/paiement/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('/paiement/list', name: 'paiement_list')]
public function list(EntityManagerInterface $entityManager): Response
{
    $paiements = $entityManager->getRepository(Paiement::class)->findAll();

    return $this->render('/paiement/list.html.twig', [
        'paiements' => $paiements
    ]);
}



#[Route('/paiement/delete/{id}', name: 'paiement_delete')]
public function delete(EntityManagerInterface $entityManager, int $id): Response
{
    $paiement = $entityManager->getRepository(Paiement::class)->find($id);

    if ($paiement) {
        $entityManager->remove($paiement);
        $entityManager->flush();
        $this->addFlash('success', 'Paiement deleted successfully!');
    }

    return $this->redirectToRoute('paiement_list');
}

}
