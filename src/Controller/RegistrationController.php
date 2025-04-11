<?php

namespace App\Controller;

use App\Entity\User;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface as HasherUserPasswordHasherInterface;

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
    public function register(Request $request, HasherUserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // Check for existing email
            $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            if ($existingUser) {
                $form->get('email')->addError(new FormError('Il existe déjà un compte avec cette adresse email'));
            }
            
            if ($form->isValid()) {
                $plainPassword = $form->get('plainPassword')->getData();
                $confirmPassword = $form->get('confirmPassword')->getData();
            
                // Debugging
                dump($plainPassword, $confirmPassword);
            
                if ($plainPassword !== $confirmPassword) {
                    $form->get('confirmPassword')->addError(new FormError('La confirmation du mot de passe ne correspond pas.'));
                    return $this->render('security/register.html.twig', [
                        'registrationForm' => $form->createView(),
                    ]);
                }

                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setMotDePasse($hashedPassword);
                $user->setDateCreationCompte(new \DateTime());

                // Gestion de la photo de profil
                $photoFile = $form->get('photo_profil')->getData();
                if ($photoFile) {
                    $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // Sécurisation du nom de fichier
                    $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();

                    // Déplacement du fichier dans le répertoire des photos de profil
                    try {
                        $photoFile->move(
                            $this->getParameter('profile_pictures_directory'),
                            $newFilename
                        );

                        // Mise à jour du chemin de la photo dans l'entité User
                        $user->setPhotoProfil($newFilename);
                    } catch (\Exception $e) {
                        $form->get('photo_profil')->addError(new FormError('Une erreur est survenue lors du téléchargement de votre photo.'));
                        return $this->render('security/register.html.twig', [
                            'registrationForm' => $form->createView(),
                        ]);
                    }
                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // Send verification email
                $email = (new TemplatedEmail())
                    ->from(new Address('mejrieya384@gmail.com', 'EasyWay'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm Your Email')
                    ->htmlTemplate('security/registration_email.html.twig')
                    ->context([
                        'user' => $user,
                    ]);

                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user, $email);

                // Flash success message
                $this->addFlash('success', 'Inscription réussie! Veuillez vérifier votre email pour confirmer votre compte.');

                return $this->redirectToRoute('app_login');
            }
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
