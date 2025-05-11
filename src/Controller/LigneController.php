<?php

namespace App\Controller;

use App\Entity\Ligne;
use App\Entity\Admin;
use App\Form\LigneType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Routing\Annotation\Route;


final class LigneController extends AbstractController
{
    #[Route('admin/ligne/dashboard', name: 'ligne_dashboard')]
public function dashboard(): Response
{
    return $this->render('back-office/ligne/choice.html.twig');
}

#[Route('/admin/ligne/add', name: 'ligne_add')]
public function add(Request $request, EntityManagerInterface $entityManager, Security $security): Response
{
    $ligne = new Ligne();
    $form = $this->createForm(LigneType::class, $ligne);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $security->getUser();

        if (!$user) {
            $this->addFlash('error', 'Aucun utilisateur connecté.');
            return $this->redirectToRoute('app_login'); 
        }

        $admin = $entityManager->getRepository(Admin::class)->findOneBy(['user' => $user]);

        if (!$admin) {
            $this->addFlash('error', 'Aucun administrateur associé à cet utilisateur.');
            return $this->redirectToRoute('ligne_dashboard');
        }
        $ligne->setAdmin($admin);
        $entityManager->persist($ligne);
        $entityManager->flush();

        $this->addFlash('success', 'Ligne ajoutée avec succès.');
        return $this->redirectToRoute('ligne_list_admin');
    }

    return $this->render('back-office/ligne/add.html.twig', [
        'form' => $form->createView(),
    ]);
}

#[Route('admin/ligne/list', name: 'ligne_list_admin')]
public function list(EntityManagerInterface $entityManager): Response
{
    $lignes = $entityManager->getRepository(Ligne::class)->findAll();

    return $this->render('back-office/ligne/list.html.twig', [
        'lignes' => $lignes
    ]);
}
//espace utilisateur
#[Route('/ligne/list', name: 'ligne_list_utilisateur')]
public function list2(EntityManagerInterface $entityManager): Response
{
    $lignes = $entityManager->getRepository(Ligne::class)->findAll();

    return $this->render('/ligne/list.html.twig', [
        'lignes' => $lignes
    ]);
}

#[Route('admin/ligne/edit/{id}', name: 'ligne_edit')]
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

    return $this->render('back-office/ligne/update.html.twig', [
        'form' => $form->createView(),
        'ligne' => $ligne
    ]);
}

#[Route('admin/ligne/delete/{id}', name: 'ligne_delete')]
public function delete(EntityManagerInterface $entityManager, int $id): Response
{
    $ligne = $entityManager->getRepository(Ligne::class)->find($id);

    if ($ligne) {
        $entityManager->remove($ligne);
        $entityManager->flush();
        $this->addFlash('success', 'Ligne deleted successfully!');
    }

    return $this->redirectToRoute('ligne_list_admin');
}

}
