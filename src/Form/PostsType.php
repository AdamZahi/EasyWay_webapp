<?php
// src/Form/PostsType.php
// src/Form/PostsType.php
namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType; // Changed from IntegerType
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType; // Correct import for integer fields


class PostsType extends AbstractType
{
    private const TUNISIAN_CITIES = [
        'Tunis', 'Sousse', 'Sfax', 'Monastir', 'Nabeul', 'Bizerte',
        'Gabès', 'Gafsa', 'Kairouan', 'Mahdia', 'Djerba', 'Tozeur',
        'Zarzis', 'Ben Arous', 'Ariana', 'Manouba', 'Beja', 'Jendouba',
        'Kef', 'Siliana', 'Kasserine', 'Sidi Bouzid', 'Médenine', 'Tataouine'
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ville_depart', ChoiceType::class, [
                'label' => 'Ville de départ',
                'required' => false,
                'choices' => array_combine(self::TUNISIAN_CITIES, self::TUNISIAN_CITIES),
                'attr' => ['class' => 'form-control']
            ])
            ->add('ville_arrivee', ChoiceType::class, [
                'label' => 'Ville d\'arrivée',
                'choices' => array_combine(self::TUNISIAN_CITIES, self::TUNISIAN_CITIES),
                'attr' => ['class' => 'form-control']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date du trajet',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'class' => 'form-control',
                    'min' => (new \DateTime())->format('Y-m-d') // Prevent past dates
                ],
                'empty_data' => null, // Explicitly handle empty data
                'required' => false
            ])
          
           // src/Form/PostsType.php
->add('message', TextareaType::class, [
    'label' => 'Détails du trajet',
    
    'empty_data' => '', // Ensure empty submits convert to empty string
    'attr' => [
        'class' => 'form-control',
        'rows' => 5,
        'placeholder' => "🕒 Heure : \n📍 Point de départ : "
    ]
])
->add('nombreDePlaces', IntegerType::class, [
    'label' => 'Nombre de places',
    'attr' => ['min' => 1, 'class' => 'form-control'],
    'empty_data' => 1,
])
            
->add('prix', NumberType::class, [
    'label' => 'Prix (DT)',
    'html5' => true,
    'attr' => [
        'min' => 0.1,
        'step' => 'any',
        'class' => 'form-control'
    ],
    'empty_data' => 0.0,  // Default value if empty
    'invalid_message' => 'Veuillez entrer un nombre valide'
])
            ->add('submit', SubmitType::class, [
                'label' => 'AJOUTER',
                'attr' => ['class' => 'btn btn-primary']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
}