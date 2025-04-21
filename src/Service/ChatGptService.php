<?php

// src/Service/ChatGptService.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatGptService
{
    private string $apiKey;
    private Client $client;

    public function __construct(string $openAiApiKey)
    {
        $this->apiKey = $openAiApiKey;
        $this->client = new Client([
            'base_uri' => 'https://openrouter.ai/api/v1/',
        ]);
        
    }

    public function ask(string $message): ?string
    {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'https://ton-site.com', // obligatoire
                ],
                'json' => [
                    'model' => 'openai/gpt-3.5-turbo', // tu peux aussi mettre "mistralai/mistral-7b-instruct"
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant utile.'],
                        ['role' => 'user', 'content' => $message],
                    ],
                ],
            ]);
            

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? null;

        } catch (GuzzleException $e) {
            // 🔴 Log ou affiche l’erreur (temporairement)
            return 'Erreur GPT : ' . $e->getMessage();
        }
    }
}
