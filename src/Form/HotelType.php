<?php

namespace App\Form;

use App\Entity\Hotel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class HotelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('region', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Région'
            ])
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom de l\'hôtel'
            ])
            ->add('adresse', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Adresse'
            ])
            ->add('prix', NumberType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control', 'rows' => 4],
                'label' => 'Description',
                'required' => false
            ])
            ->add('contact_email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Email de contact'
            ])
            ->add('Images', FileType::class, [
                'label' => 'Hotel Images (JPG/PNG)',
                'mapped' => false,  // This ensures the file is not directly mapped to the entity property
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid JPG or PNG image',
                    ])
                ],
            ])
            
            ->add('phone', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'pattern' => '^\+?[0-9\s\-\(\)]{6,20}$',
                    'title' => 'Format: +33123456789 ou 0123456789'
                ],
                'label' => 'Téléphone'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hotel::class,
            'attr' => ['novalidate' => 'novalidate']
        ]);
    }
    
}