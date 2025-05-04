<?php
// src/Service/StripePaymentService.php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripePaymentService
{
    private string $stripeSecretKey;
    private UrlGeneratorInterface $router;

    public function __construct(
        string $stripeSecretKey,
        UrlGeneratorInterface $router
    ) {
        $this->stripeSecretKey = $stripeSecretKey;
        $this->router = $router;
        Stripe::setApiKey($this->stripeSecretKey);
    }

    public function createPaymentIntent(float $amount, string $currency = 'eur'): PaymentIntent
    {
        try {
            return PaymentIntent::create([
                'amount' => (int) ($amount * 100),
                'currency' => $currency,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            
        } catch (ApiErrorException $e) {
            throw new \Exception('Payment creation failed: ' . $e->getMessage());
        }
    }

    public function confirmPayment(string $paymentIntentId, string $paymentMethodId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
            return $paymentIntent->confirm([
                'payment_method' => $paymentMethodId,
                'return_url' => $this->router->generate(
                    'app_payment_success',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]);
        } catch (ApiErrorException $e) {
            throw new \Exception('Payment confirmation failed: ' . $e->getMessage());
        }
    }
}