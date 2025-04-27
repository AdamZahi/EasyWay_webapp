<?php

namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private string $sid;
    private string $token;
    private string $from;
    private Client $client;

    public function __construct(string $sid, string $token, string $from)
    {
        $this->sid = $sid;
        $this->token = $token;
        $this->from = $from;
        $this->client = new Client($this->sid, $this->token); // ✅ créer le client Twilio ici
    }

    public function sendSms(string $to, string $message): void
    {
        // Vérifie si le numéro commence par +, sinon ajoute +216
        if (strpos($to, '+') !== 0) {
            $to = '+216' . $to;
        }
    
        $this->client->messages->create(
            $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
    
}
