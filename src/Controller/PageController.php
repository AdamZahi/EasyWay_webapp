<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/front', name: 'app_page')]
    public function index(): Response
    {
        return $this->render('front-office/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/admin', name: 'admin_page')]
    public function adminIndex(): Response
    {
        return $this->render('back-office/index.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
    #[Route('/dashboard', name: 'back_office')]
    public function backOfficeV1(): Response
    {
        return $this->render('back-office/pages/back-office.html.twig');
    }

    #[Route('/dashboard2', name: 'back_office2')]
    public function backOfficeV2(): Response
    {
        return $this->render('back-office/pages/back-office2.html.twig');
    }

    #[Route('/dashboard3', name: 'back_office3')]
    public function backOfficeV3(): Response
    {
        return $this->render('back-office/pages/back-office3.html.twig');
    }

}
