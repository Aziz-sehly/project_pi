<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('region', TextType::class, [
                'label' => 'Région *',
                'attr' => [
                    'placeholder' => 'Entrez la région',
                    'class' => 'form-control'
                ],
                'help' => 'Champ obligatoire',
                'required' => true
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'événement *',
                'attr' => [
                    'placeholder' => 'Entrez le nom',
                    'class' => 'form-control'
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Entrez la description',
                    'class' => 'form-control'
                ],
                    
                
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure *',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'Entrez date',
                    'class' => 'form-control datetimepicker']
            ])
            ->add('capacite', NumberType::class, [
                'label' => 'Capacité *',
                'attr' => [
                    'placeholder' => 'Entrez la capacité',
                    'class' => 'form-control',
                    'min' => 1
                ]
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix (€) *',
                'attr' => [
                    'placeholder' => 'Entrez le prix',
                    'class' => 'form-control',
                    'step' => '0.01',
                    'min' => 0
                ],
                'scale' => 2
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'attr' => ['novalidate' => 'novalidate'] // Disable HTML5 validation
        ]);
    }
}