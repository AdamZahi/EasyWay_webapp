<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    private $clientRegistry;

    public function __construct(ClientRegistry $clientRegistry)
    {
        $this->clientRegistry = $clientRegistry;
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    // Route to initiate Google login
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectGoogleAction(): RedirectResponse
    {
        // 'google' is the name of the client registered in your services.yaml or config.
        $client = $this->clientRegistry->getClient('google');
        
        // Redirect to Google for OAuth2 login
        return $client->redirect([], []); // Providing default arguments (you can specify scopes here if needed)
    }

    // Route to handle the Google login callback
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectGoogleCheckAction(): void
    {
        // This action will be intercepted by the OAuth2Authenticator
        // and handled by the GoogleAuthenticator.
    }
}