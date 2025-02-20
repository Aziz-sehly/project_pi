<?php

namespace App\Form;

use App\Entity\Attraction;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;  // Add this for file upload
use Symfony\Component\Validator\Constraints\File;  // Add this for file validation

class AttractionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Museum' => 'museum',
                    'Park' => 'park',
                    'Monument' => 'monument',
                    'Nature' => 'nature',
                    'Other' => 'other',
                ],
                'placeholder' => 'Select a type',
            ])
            ->add('description')
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'nom',
            ])
            ->add('image', FileType::class, [  // Add this to handle image upload
                'label' => 'Image (JPEG/PNG file)', 
                'mapped' => false, // The image field is not mapped to the entity directly
                'required' => false,  // Make it optional
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',  // Optional: Limit file size to 1MB
                        'mimeTypes' => ['image/jpeg', 'image/png'],  // Accept only JPEG/PNG images
                        'mimeTypesMessage' => 'Please upload a valid JPEG or PNG image',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Attraction::class,
        ]);
    }
}
