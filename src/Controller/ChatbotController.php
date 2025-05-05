<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ChatGptService;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot')]
    public function index(): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render('front-office/chatbot/index.html.twig');
    }

    #[Route('/chatbot/message', name: 'chatbot_message', methods: ['POST'])]
    public function message(Request $request, ChatGptService $chatGptService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';
    
        $botReply = $chatGptService->ask($userMessage);
    
        return new JsonResponse(['reply' => $botReply]);
    }
}
