<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\RoleEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\EqualTo;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne peut pas être vide']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Le nom doit contenir au moins 2 caractères', 'maxMessage' => 'Le nom ne peut pas dépasser 50 caractères']),
                    new Regex(['pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/', 'message' => 'Le nom ne doit contenir que des lettres, des espaces et des tirets']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre prénom'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne peut pas être vide']),
                    new Length(['min' => 2, 'max' => 50, 'minMessage' => 'Le prénom doit contenir au moins 2 caractères', 'maxMessage' => 'Le prénom ne peut pas dépasser 50 caractères']),
                    new Regex(['pattern' => '/^[a-zA-ZÀ-ÿ\s\-]+$/', 'message' => 'Le prénom ne doit contenir que des lettres, des espaces et des tirets']),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse mail',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'exemple@exemple.com'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'L\'email ne peut pas être vide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre mot de passe',
                    'autocomplete' => 'new-password'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le mot de passe ne peut pas être vide']),
                    new Length(['min' => 8, 'minMessage' => 'Le mot de passe doit contenir au moins 8 caractères']),
                    new Regex(['pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', 'message' => 'Le mot de passe doit contenir au moins 8 caractères avec au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial']),
                ],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'label' => 'Confirmer le mot de passe',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Confirmer votre mot de passe'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez confirmer le mot de passe']),
                    new EqualTo(['propertyPath' => 'parent.all[plainPassword].data', 'message' => 'Les mots de passe ne correspondent pas']),  // Add the EqualTo constraint
                ],
            ])
            ->add('telephonne', TextType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'XX XXX XXX'
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne peut pas être vide']),
                    new Regex(['pattern' => '/^[0-9]{8,15}$/', 'message' => 'Le numéro de téléphone doit contenir entre 8 et 15 chiffres']),
                ],
            ])
            ->add('photo_profil', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPEG, PNG, GIF)',
                    ])
                ],
            ])
            ->add('role', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Administrateur' => RoleEnum::ADMIN,
                    'Passager' => RoleEnum::PASSAGER,
                    'Conducteur' => RoleEnum::CONDUCTEUR,
                ],
                'expanded' => false,
                'multiple' => false,
                'label' => 'Je suis un',
                'attr' => ['class' => 'form-control'],
                'placeholder' => 'Choisissez votre rôle'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'registration_form',
        ]);
    }
}
