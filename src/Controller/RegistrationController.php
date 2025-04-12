<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Passager;
use App\Entity\Conducteur;
use App\Entity\Admin;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\AppAuthenticator;
use App\Enum\RoleEnum;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private EntityManagerInterface $entityManager;
    private MailerInterface $mailer;

    // Constructor with all dependencies
    public function __construct(EmailVerifier $emailVerifier, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Set all user fields
            $user->setEmail($form->get('email')->getData());
            $user->setNom($form->get('nom')->getData());
            $user->setPrenom($form->get('prenom')->getData());
            $user->setTelephonne($form->get('telephonne')->getData());
            $user->setPhotoProfil('default_profile.png');

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Get the role from the form and set it in the roles array
            $role = $form->get('role')->getData();
            $user->setRoles([$role->value]);

            // If the user is a passenger, create a Passager entity
            if ($role === RoleEnum::PASSAGER) {
                $passager = new Passager();
                $passager->setUser($user);
                $entityManager->persist($passager);
            }
            // If the user is a driver, create a Conducteur entity
            elseif ($role === RoleEnum::CONDUCTEUR) {
                $conducteur = new Conducteur();
                $conducteur->setUser($user);
                $entityManager->persist($conducteur);
            }
            // If the user is an admin, create an Admin entity
            elseif ($role === RoleEnum::ADMIN) {
                $admin = new Admin();
                $admin->setUser($user);
                $admin->setNom($user->getNom());
                $admin->setPrenom($user->getPrenom());
                $admin->setEmail($user->getEmail());
                $admin->setMotDePasse($user->getPassword());
                $admin->setTelephonne((int)$user->getTelephonne());
                $entityManager->persist($admin);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // We don't need to check for authenticated user as the signature in the URL validates the user
        // $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            /** @var User $user */
            // Get user ID from the request if it exists, otherwise use the current user
            $userId = $request->query->get('id');
            if ($userId) {
                $user = $this->entityManager->getRepository(User::class)->find($userId);
                if (!$user) {
                    throw $this->createNotFoundException('User not found');
                }
            } else {
                $user = $this->getUser();
                if (!$user) {
                    throw $this->createNotFoundException('User not found');
                }
            }
            
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_login');
    }
}
