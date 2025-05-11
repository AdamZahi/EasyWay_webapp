<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Range;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart')
            ->add('arret')
            ->add('vehicule', ChoiceType::class, [
                'choices' => [
                    '🚌 Bus' => 'bus',
                    '🚇 Metro' => 'metro',
                    '🚆 Train' => 'train',
                ],
                'expanded' => true, // this will render as radio buttons
                'multiple' => false, // only one can be selected
                'label' => 'Type de véhicule',
                'attr' => ['class' => 'vehicule-options'],
            ])
            ->add('nb')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
