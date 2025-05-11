<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('back-office/admin/home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/home', name: 'admin__home')]
    public function usersHome(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        return $this->render('back-office/admin/home.html.twig');
    }

    #[Route('/admin/users/contact', name: 'admin__contact')]
    public function usersContact(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        return $this->render('back-office/admin/contact.html.twig');
    }
    


    #[Route('/admin/users', name: 'admin_users')]
public function listUsers(Request $request, UserRepository $userRepository): Response
{
    $search = $request->query->get('q');
    $roleFilter = $request->query->get('role');

    $users = $userRepository->createQueryBuilder('u');

    if ($search) {
        $users
            ->andWhere('u.nom LIKE :search OR u.prenom LIKE :search OR u.email LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    if ($roleFilter) {
        $users
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"' . $roleFilter . '"%'); // attention : les rôles sont stockés comme array (JSON)
    }

    $result = $users->getQuery()->getResult();

    return $this->render('back-office/admin/index.html.twig', [
        'users' => $result,
    ]);
}


#[Route('/admin/user/{id}/block', name: 'admin_block_user', methods: ['POST'])]
public function blockUser(Request $request, UserRepository $userRepository, EntityManagerInterface $em, int $id): Response
{
    $user = $userRepository->find($id);

    if (!$user) {
        $this->addFlash('danger', 'Utilisateur non trouvé.');
        return $this->redirectToRoute('admin_users');
    }

    if ($this->isCsrfTokenValid('block' . $user->getIdUser(), $request->request->get('_token'))) {
        $user->setRoles(['ROLE_BLOCKED']);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Utilisateur bloqué avec succès.');
    }

    return $this->redirectToRoute('admin_users');
}


} 