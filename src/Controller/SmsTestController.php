<?php
// src/Controller/SmsTestController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Service\SmsService;

class SmsTestController extends AbstractController
{
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function testSms(): Response
    {
        // Replace this with an actual phone number (in international format, e.g. +216...)
        $phoneNumber = '+21656107783';  // Your actual phone number here
        $message = ' bitch !';

        try {
            $this->smsService->sendSms($phoneNumber, $message);
            return new Response('SMS sent!');
        } catch (\Exception $e) {
            // Log or display the error if something goes wrong
            return new Response('Error: ' . $e->getMessage());
        }
    }
}
