<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use SymfonyCasts\Bundle\VerifyEmail\EmailVerifierInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $profilePhotosDirectory;
    private $emailVerifier;
    private $entityManager;

    public function __construct(
        string $profilePhotosDirectory,
        EmailVerifier $emailVerifier,
        EntityManagerInterface $entityManager
    ) {
        $this->profilePhotosDirectory = $profilePhotosDirectory;
        $this->emailVerifier = $emailVerifier;
        $this->entityManager = $entityManager;
    }
    

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SluggerInterface $slugger): Response
    {
        // Create a new User object
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // If the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle photo upload if available
            /** @var UploadedFile $photoFile */
            $photoFile = $form->get('photo_profil')->getData();

            if ($photoFile) {
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $photoFile->guessExtension();

                try {
                    // Move the file to the directory where photos are stored
                    $photoFile->move(
                        $this->profilePhotosDirectory, // Directory passed from services.yaml
                        $newFilename
                    );

                    // Set the filename in the User entity to store in the database
                    $user->setPhotoProfil($newFilename);

                } catch (FileException $e) {
                    // Handle file upload failure
                    $this->addFlash('error', 'File upload failed.');
                }
            }

            // Hash the password
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            // Persist the user to the database
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Redirect after successful registration
            return $this->redirectToRoute('app_home');
        }

        // Render the registration form
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Deny access unless the user is fully authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            // Handle email verification
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            // If an error occurs during email verification
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // Success message if email was verified
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_home');
    }
}
