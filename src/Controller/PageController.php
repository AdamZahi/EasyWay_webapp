<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PostsRepository;

final class PageController extends AbstractController
{
    #[Route('/index', name: 'app_page')]
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
    #[Route('/dashboard4', name: 'back_office4')]
    public function backOfficeV4(PostsRepository $postsRepository): Response
    {
        // Basic stats
        $totalPosts = $postsRepository->count([]);
        $avgSeats = $postsRepository->getAverageSeats();
        $avgPrice = $postsRepository->getAveragePrice();
        $totalComments = $postsRepository->getTotalComments();
    
        $monthlyStats = $postsRepository->getMonthlyActivity();
        $monthlyLabels = json_encode(array_keys($monthlyStats)); // Maintenant ce sont des strings
        $monthlyData = json_encode(array_values($monthlyStats));
    
        // Top cities
        $topCities = $postsRepository->getTopCities(5);
        $topCitiesLabels = json_encode(array_keys($topCities));
        $topCitiesData = json_encode(array_values($topCities));
    
        // Price ranges
        $priceUnder10 = $postsRepository->countByPriceRange(0, 10);
        $price10to20 = $postsRepository->countByPriceRange(10, 20);
        $price20to50 = $postsRepository->countByPriceRange(20, 50);
        $priceOver50 = $postsRepository->countByPriceRange(50, null);
    
        // Seats distribution
        $seats1 = $postsRepository->countBySeats(1);
        $seats2 = $postsRepository->countBySeats(2);
        $seats3to4 = $postsRepository->countBySeatsRange(3, 4);
        $seats5plus = $postsRepository->countBySeatsRange(5, null);
    
        // Recent posts
        $recentPosts = $postsRepository->findBy([], ['date' => 'DESC'], 10);
    
        return $this->render('back-office/pages/back-office4.html.twig', [
            'totalPosts' => $totalPosts,
            'avgSeats' => $avgSeats,
            'avgPrice' => $avgPrice,
            'totalComments' => $totalComments,
            'monthlyLabels' => $monthlyLabels,
            'monthlyData' => $monthlyData,
            'topCitiesLabels' => $topCitiesLabels,
            'topCitiesData' => $topCitiesData,
            'priceUnder10' => $priceUnder10,
            'price10to20' => $price10to20,
            'price20to50' => $price20to50,
            'priceOver50' => $priceOver50,
            'seats1' => $seats1,
            'seats2' => $seats2,
            'seats3to4' => $seats3to4,
            'seats5plus' => $seats5plus,
            'recentPosts' => $recentPosts,
        ]);
    }
}
