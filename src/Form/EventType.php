<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Ligne;
use App\Enum\EventStatus;
use App\Enum\TypeEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Retard' => 'RETARD',
                    'Greve' => 'GREVE',
                    'Incident' => 'INCIDENT',
                ],
                'placeholder' => 'Sélectionnez un type',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'EN_COUR',
                    'Résolu' => 'RESOLU',
                    'Annulé' => 'ANNULE',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez l\'événement en détail',
                ],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'validation_groups' => ['Default'],
        ]);
    }
} 