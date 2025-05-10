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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\PaiementRepository;
use Symfony\Component\HttpFoundation\JsonResponse;


class PaiementController extends AbstractController
{
        private string $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
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

        $user = $security->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('User not authenticated.');
        }

        $vehicle = strtolower($reservation->getVehicule());
        $nbSeats = $reservation->getNb();

        $pricePerSeat = match ($vehicle) {
            'bus' => 3.5,
            'metro' => 3.0,
            'train' => 2.5,
            default => 3.0,
        };

        $montant = $pricePerSeat * $nbSeats;

        Stripe::setApiKey($this->stripeSecretKey);

        $paymentIntent = PaymentIntent::create([
            'amount' => intval($montant * 100),
            'currency' => 'usd',
            'payment_method_types' => ['card'],
        ]);

        return $this->render('paiement/add.html.twig', [
            'clientSecret' => $paymentIntent->client_secret,
            'montant' => $montant,
            //'stripe_public_key' => $this->getParameter('stripe.public_key'), 
        ]);
    }

    #[Route('/paiement/confirm', name: 'paiement_confirm', methods: ['POST'])]
    public function confirm(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Get the authenticated user
        $user = $security->getUser();

        if (!$user) {
            // Returning a plain response for user authentication failure
            return new Response('User not authenticated.', Response::HTTP_UNAUTHORIZED);
        }

        // Decode the incoming JSON payload
        $data = json_decode($request->getContent(), true);
        $paymentId = $data['payment_id'] ?? null;
        $amount = $data['montant'] ?? null;

        // If any required data is missing, return a bad request error
        if (!$paymentId || !$amount) {
            return new Response('Payment data missing.', Response::HTTP_BAD_REQUEST);
        }

        // Set the Stripe API key
        Stripe::setApiKey($this->stripeSecretKey);

        try {
            // Retrieve the payment intent using the payment ID
            $paymentIntent = PaymentIntent::retrieve($paymentId);

            if ($paymentIntent->status === 'succeeded') {
                // Create a new Paiement entity and save it
                $paiement = new Paiement();
                $paiement->setUser($user);
                $paiement->setAmount($amount);
                $paiement->setPaymentIntentId($paymentId);
                $paiement->setStatus('succeeded');
                $entityManager->persist($paiement);
                $entityManager->flush();

                // Return a success response
                return new Response('Payment successful and recorded.', Response::HTTP_OK);
            }

            // If payment wasn't successful, return an error
            return new Response('Payment failed.', Response::HTTP_PAYMENT_REQUIRED);
        } catch (\Exception $e) {
            // In case of error, return the exception message
            return new Response('Error processing payment: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
