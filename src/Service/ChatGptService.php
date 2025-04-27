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
        // 🔒 Limitation au contexte de l'application
        $keywords = ['réclamation', 'catégorie', 'statut', 'email', 'formulaire', 'soumettre', 'ajouter', 'passer', 'envoyer', 'application', 'site web', 'EasyWay', 'evenement', 'trajet', 'covoiturage', 'reservation', 'paiement', 'probleme','transport','bus','chauffeur'];
        $messageLower = strtolower($message);
        $isRelevant = false;
        foreach ($keywords as $keyword) {
            if (str_contains($messageLower, $keyword)) {
                $isRelevant = true;
                break;
            }
        }

        if (!$isRelevant) {
            return "❗ Je suis un assistant dédié uniquement à l'application EasyWay. Veuillez poser une question en rapport avec les réclamations, catégories, ou fonctionnalités liées.";
        }

        // ✅ Appel GPT autorisé
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => 'https://ton-site.com', // obligatoire
                ],
                'json' => [
                    'model' => 'openai/gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => "
Tu es l'assistant officiel de l'application EasyWay.
Tu dois répondre aux questions suivantes, de façon claire, concise et professionnelle :

1.  Comment soumettre une réclamation ?  
➡️ Réponse :'Pour soumettre une réclamation, connectez-vous à votre compte EasyWay, allez dans la section Réclamations, puis Remplissez le formulaire et cliquez sur Envoyer.'

2.  Comment connetre si ma reclamation a été envoyer ?  
➡️ Réponse :'Après l'envoi de votre réclamation sur EasyWay, un e-mail de confirmation vous est automatiquement envoyé.'

3.  Comment réserver un trajet ?  
➡️ Réponse : 'Sur EasyWay, vous pouvez réserver un trajet en sélectionnant votre destination et la date souhaitée, puis en validant votre réservation.'

4.  Comment effectuer un paiement ?  
➡️ Réponse : 'Les paiements EasyWay se font directement dans votre espace personnel via carte bancaire ou portefeuille EasyPay.'

5.  Informations sur les chauffeurs et les bus  
➡️ Réponse : 'Vous pouvez consulter les profils des chauffeurs et les informations sur les bus dans la rubrique Transport de votre application EasyWay.'

6.  Peut-on réserver pour plusieurs personnes ?
➡️ Réponse : 'Oui, vous pouvez ajouter plusieurs passagers lors de la réservation d'un trajet sur EasyWay.'

7. Comment annuler une réservation ?
➡️ Réponse : 'Pour annuler une réservation, allez dans votre espace Mes Trajets, sélectionnez le trajet concerné et cliquez sur Annuler.'

8.Que faire en cas de problème avec un chauffeur ?
➡️ Réponse : 'En cas de problème avec un chauffeur, vous pouvez soumettre une réclamation via la section Réclamations de votre application EasyWay.'

IMPORTANT : 
- Si la question ne correspond pas à un de ces thèmes, réponds : ' Désolé, je peux uniquement vous aider concernant les fonctionnalités de l'application EasyWay ❗'
- Ne donne jamais d'autres informations.
"]
,

                        ['role' => 'user', 'content' => $message],
                    ],
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['choices'][0]['message']['content'] ?? null;

        } catch (GuzzleException $e) {
            return 'Erreur GPT : ' . $e->getMessage();
        }
    }
}
