<?php

namespace App\Controller;

use App\Entity\Paiement;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class PaiementController extends AbstractController
{
    private string $stripeSecretKey;

    public function __construct(ParameterBagInterface $params)
    {
        $this->stripeSecretKey = $params->get('stripe.secret_key');
    }

    #[Route('/paiement/confirm', name: 'paiement_confirm', methods: ['POST'])]
    public function confirm(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        SessionInterface $session
    ): Response {
        // Vérification de l'utilisateur authentifié
        $user = $security->getUser();
        if (!$user) {
            return new JsonResponse(['message' => 'Utilisateur non authentifié.'], Response::HTTP_UNAUTHORIZED);
        }

        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);
        $paymentId = $data['payment_id'] ?? null;
        $amount = $data['montant'] ?? null;

        if (null === $paymentId || null === $amount) {
            return new JsonResponse(['message' => 'Données de paiement manquantes.'], Response::HTTP_BAD_REQUEST);
        }

        // Configuration de la clé API Stripe
        Stripe::setApiKey($this->stripeSecretKey);

        try {
            // Récupération du PaymentIntent depuis Stripe
            $paymentIntent = PaymentIntent::retrieve($paymentId);

            if ($paymentIntent->status === 'succeeded') {
                // Récupération de la réservation depuis la session
                $reservationId = $session->get('reservation_id');
                if (!$reservationId) {
                    return new JsonResponse(['message' => 'ID de réservation introuvable dans la session.'], Response::HTTP_BAD_REQUEST);
                }

                $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);
                if (!$reservation) {
                    return new JsonResponse(['message' => 'Réservation introuvable.'], Response::HTTP_NOT_FOUND);
                }

                // Enregistrement du paiement dans la base de données
                $paiement = new Paiement();
                $paiement->setUserId($user);
                $paiement->setResId($reservation);
                $paiement->setMontant($amount);
                $paiement->setPayId($paymentId);

                $entityManager->persist($paiement);
                $entityManager->flush();

                return new JsonResponse([
                    'message' => 'Paiement réussi et enregistré.',
                    'payment_id' => $paymentId
                ], Response::HTTP_OK);
            }

            return new JsonResponse(['message' => 'Le paiement a échoué.'], Response::HTTP_PAYMENT_REQUIRED);
        } catch (ApiErrorException $e) {
            // Gestion des erreurs API Stripe
            return new JsonResponse(['message' => 'Erreur Stripe : ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/paiement/add', name: 'paiement_add')]
    public function add(
        Request $request,
        EntityManagerInterface $entityManager,
        Security $security,
        SessionInterface $session
    ): Response {
        $reservationId = $session->get('reservation_id');
        $reservation = $entityManager->getRepository(Reservation::class)->find($reservationId);
        
        if (!$reservation) {
            throw $this->createNotFoundException('Reservation not found.');
        }

        // Check if the user is authenticated
        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        // Calculate the total amount based on the reservation
        $vehicle = strtolower($reservation->getVehicule());
        $nbSeats = $reservation->getNb();
        $pricePerSeat = match ($vehicle) {
            'bus' => 3.5,
            'metro' => 3.0,
            'train' => 2.5,
            default => 3.0,
        };

        $montant = $pricePerSeat * $nbSeats;

        // Set up Stripe API key
        Stripe::setApiKey($this->stripeSecretKey);

        // Create a Stripe payment intent
        $paymentIntent = PaymentIntent::create([
            'amount' => intval($montant * 100), // Amount in cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        // Render the payment form with the client secret
        return $this->render('paiement/add.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'montant' => $montant,
            'stripe_public_key' => $this->getParameter('stripe.public_key'),
        ]);
    }

    #[Route('/paiement/list', name: 'paiement_list')]
    public function list(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $sort = $request->query->get('sort', 'vehicule'); // Default sorting is by 'vehicule'

        $qb = $entityManager->getRepository(Paiement::class)->createQueryBuilder('p')
            ->innerJoin('p.res_id', 'r')  // Join with the Reservation entity using 'res_id'
            ->where('p.user = :user')
            ->setParameter('user', $user);

        // Sorting logic
        if ($sort == 'montant') {
            // Sorting by 'montant'
            $qb->orderBy('p.montant', 'ASC');
        } else {
            // Default sorting by 'vehicule' from the Reservation entity
            $qb->orderBy('r.vehicule', 'ASC');
        }

        $paiements = $qb->getQuery()->getResult();

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
