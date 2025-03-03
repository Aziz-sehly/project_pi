<?php

namespace App\Form;

use App\Entity\Hotel;
use App\Entity\Rating;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('score', ChoiceType::class, [
                'choices'  => [
                    '1 star'  => 1,
                    '2 stars' => 2,
                    '3 stars' => 3,
                    '4 stars' => 4,
                    '5 stars' => 5,
                ],
                'expanded' => true, // Displays as radio buttons
                'label'    => 'Rating',
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
                'label'    => 'Your Comment',
                'attr'     => ['placeholder' => 'Write your review here...'],
            ])
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label'  => 'Date',
            ])
            ->add('hotel', EntityType::class, [
                'class'        => Hotel::class,
                'choice_label' => 'nom', // Show hotel name instead of ID
                'label'        => 'Hotel',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
