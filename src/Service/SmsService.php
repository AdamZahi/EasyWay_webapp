<?php 
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsService
{
    private $apiUrl;
    private $apiKey;
    private $sender;
    private $client;

    public function __construct(string $apiUrl, string $apiKey, string $sender, HttpClientInterface $client)
    {
        // Ensure the URL is correctly prefixed with https://
        $this->apiUrl = rtrim($apiUrl, '/') . '/'; // Ensures that there is no trailing slash
        $this->apiKey = $apiKey;
        $this->sender = $sender;
        $this->client = $client;
    }

    public function sendSms(string $to, string $message): void
    {
        // Check if the URL includes the scheme (https://), and prepend if missing
        if (strpos($this->apiUrl, 'http') !== 0) {
            $this->apiUrl = 'https://' . $this->apiUrl;
        }

        $this->client->request('POST', $this->apiUrl . 'sms/2/text/advanced', [
            'headers' => [
                'Authorization' => 'App ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'messages' => [
                    [
                        'from' => $this->sender,
                        'destinations' => [
                            ['to' => $to]
                        ],
                        'text' => $message,
                    ],
                ],
            ],
        ]);
    }
}
