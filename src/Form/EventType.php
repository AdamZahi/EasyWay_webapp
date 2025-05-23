<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Ligne;
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
                    'Retard' => 'Retard',
                    'Greve' => 'Greve',
                    'Incident' => 'Incident',
                ],
                'placeholder' => 'Sélectionnez un type',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Résolu' => 'Résolu',
                    'Annulé' => 'Annulé',
                ],
                'placeholder' => 'Sélectionnez un statut',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                    'placeholder' => 'Décrivez l\'événement en détail',
                ],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('dateFin', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('ligneAffectee', EntityType::class, [
                'class' => Ligne::class,
                'choice_label' => function (Ligne $ligne) {
                    return $ligne->getDepart() . ' - ' . $ligne->getArret();
                },
                'placeholder' => 'Sélectionnez une ligne affectée',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'validation_groups' => ['Default'],
        ]);
    }
} 