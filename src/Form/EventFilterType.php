<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date début',
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date fin',
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Retard' => 'Retard',
                    'Greve' => 'Greve',
                    'Incident' => 'Incident',
                ],
                'placeholder' => 'Choisir un type',
                'required' => false,
                'label' => 'Type',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'En cours',
                    'Résolu' => 'Résolu',
                    'Annulé' => 'Annulé',
                ],
                'placeholder' => 'Choisir un statut',
                'required' => false,
                'label' => 'Statut',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
