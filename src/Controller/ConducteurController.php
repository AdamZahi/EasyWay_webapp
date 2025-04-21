<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/conducteur')]
class ConducteurController extends AbstractController
{
    #[Route('/dashboard', name: 'conducteur_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('conducteur/dashboard.html.twig', [
            'controller_name' => 'ConducteurController',
        ]);
    }
} 