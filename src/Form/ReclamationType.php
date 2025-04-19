<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // ✅ Correction ici
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;



class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('sujet', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('Category_Id', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'type',
                'label' => 'Category',
                'placeholder' => 'Choose a category',
                'attr' => ['class' => 'form-control']
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'En attente' => 'en_attente',
                ],
                'attr' => ['class' => 'form-control']
            ])
            // ReclamationType.php
           /* ->add('dateCreation', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'label' => 'Date de création',
                'mapped' => false, // <- SUPER IMPORTANT pour ne pas qu’il écrase la valeur de l’entité
                'required' => false,
                'attr' => [
                    'readonly' => true,
                    'class' => 'form-control'
                ],
            ])
            */
            
            
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
