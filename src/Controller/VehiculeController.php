<?php

namespace App\Controller;

use App\Repository\BusRepository;
use App\Repository\MetroRepository;
use App\Repository\TrainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    #[Route('/admin/vehicules', name: 'admin_vehicules')]
public function vehicules(
    BusRepository $busRepository,
    TrainRepository $trainRepository,
    MetroRepository $metroRepository
): Response {
    return $this->render('vehicules/vehicules.html.twig', [
        'buses' => $busRepository->findAll(),
        'trains' => $trainRepository->findAll(),
        'metros' => $metroRepository->findAll(),
    ]);
}
}
