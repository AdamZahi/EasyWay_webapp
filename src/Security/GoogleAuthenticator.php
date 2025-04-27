<?php

namespace App\Security;

use App\Entity\User;
use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;

class GoogleAuthenticator extends OAuth2Authenticator implements AuthenticatorInterface
{
    private ClientRegistry $clientRegistry;
    private RouterInterface $router;
    private UserRepository $userRepository;

    public function __construct(
        ClientRegistry $clientRegistry,
        RouterInterface $router,
        UserRepository $userRepository
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): SelfValidatingPassport
{
    $client = $this->clientRegistry->getClient('google');
    $accessToken = $client->getAccessToken();

    return new SelfValidatingPassport(
        new UserBadge($accessToken->getToken(), function () use ($client, $accessToken) {
            /** @var GoogleUser $googleUser */
            $googleUser = $client->fetchUserFromToken($accessToken);

            $email = $googleUser->getEmail();
            $name = $googleUser->getName();

            // Split the name into first and last name
            $nameParts = explode(' ', $name);
            $prenom = $nameParts[0];
            $nom = isset($nameParts[1]) ? $nameParts[1] : '';  // Assuming last name is the second part

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                // Create a new user if not found
                $user = new User();
                $user->setEmail($email);
                $user->setNom($nom);  // Set last name
                $user->setPrenom($prenom);  // Set first name

                // Assign roles based on your business logic (you could prompt the user to choose a role or assign a default)
                // In this example, we assume ROLE_PASSAGER as the default
                $user->setRoles([RoleEnum::PASSAGER->value]);

                // Set a dummy password (this is required because your schema doesn't allow null passwords)
                $user->setPassword('google_oauth_password'); // Use a default password here

                // Set a dummy phone number (because Google doesn't provide this info)
                $user->setTelephonne('dummy_phone_number'); // Use a default phone number or leave empty if allowed

                // Persist the new user
                $em = $this->userRepository->getEntityManager();
                $em->persist($user);
                $em->flush();
            }

            return $user;
        })
    );
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
{
    // Récupérer l'utilisateur authentifié
    $user = $token->getUser();
    
    // Vérifier le rôle de l'utilisateur
    if (in_array(RoleEnum::CONDUCTEUR->value, $user->getRoles())) {
        // Si l'utilisateur est un conducteur, le rediriger vers le dashboard du conducteur
        return new RedirectResponse($this->router->generate('conducteur_dashboard'));
    } elseif (in_array(RoleEnum::PASSAGER->value, $user->getRoles())) {
        // Si l'utilisateur est un passager, le rediriger vers le dashboard du passager
        return new RedirectResponse($this->router->generate('passager_dashboard'));
    }

    // Par défaut, rediriger vers la page d'accueil si aucun rôle spécifique n'est trouvé
    return new RedirectResponse($this->router->generate('app_home'));
}


    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        // Redirect in case of authentication failure
        return new RedirectResponse($this->router->generate('app_login'));
    }
}
