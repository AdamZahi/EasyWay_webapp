<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener
{
    public function onSecurityLogout(LogoutEvent $event)
    {
        // Personnalisation de la déconnexion (par exemple, redirection ou message flash)
        // Redirection vers la page d'accueil après déconnexion
        $response = $event->getResponse();
        $response->headers->set('Location', '/');
    }
}
