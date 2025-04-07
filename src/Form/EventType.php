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
                'choices' => array_combine(
                    array_map(fn($case) => $case->value, TypeEvent::cases()),
                    array_map(fn($case) => $case->value, TypeEvent::cases())
                ),
                'choice_value' => function ($choice) {
                    return $choice instanceof TypeEvent ? $choice->value : $choice;
                },
                'label' => 'Type',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn($case) => $case->value, EventStatus::cases()),
                    array_map(fn($case) => $case->value, EventStatus::cases())
                ),
                'choice_value' => function ($choice) {
                    return $choice instanceof EventStatus ? $choice->value : $choice;
                },
                'label' => 'Status',
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date Debut',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date Fin',
                'widget' => 'single_text',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
} 