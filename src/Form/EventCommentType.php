<?php

namespace App\Form;

use App\Entity\EventComment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, [
                'label' => 'Your Comment',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Write your feedback here...',
                    'rows' => 4
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventComment::class,
        ]);
    }
}
