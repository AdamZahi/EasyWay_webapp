<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/passager')]
class PassagerController extends AbstractController
{
    #[Route('/dashboard', name: 'passager_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('passager/dashboard.html.twig', [
            'controller_name' => 'PassagerController',
        ]);
    }
} 