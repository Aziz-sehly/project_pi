<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType as DecimalType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Product Name *',
                'attr' => [
                    'placeholder' => 'Enter the product name',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('category', TextType::class, [
                'label' => 'Category *',
                'attr' => [
                    'placeholder' => 'Enter the category',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'placeholder' => 'Enter the description',
                    'class' => 'form-control'
                ],
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Creation Date *',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'Enter the date',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
            ->add('stock', DecimalType::class, [
                'label' => 'Stock Capacity *',
                'attr' => [
                    'placeholder' => 'Enter stock capacity',
                    'class' => 'form-control',
                    'min' => 0,
                    'pattern' => '/^\d{0,9}(\.\d{1,2})?$/'  // pattern to allow decimal numbers with max 2 decimal places
                ],
                'required' => true
            ])
            ->add('price', NumberType::class, [
                'label' => 'Price (€) *',
                'attr' => [
                    'placeholder' => 'Enter the price',
                    'class' => 'form-control',
                    'min' => 0,
                    'pattern' => '/^\d{0,9}(\.\d{1,2})?$/' // pattern to allow decimal numbers with max 2 decimal places
                ],
                'required' => true
            ])
            ->add('region', TextType::class, [
                'label' => 'Region *',
                'attr' => [
                    'placeholder' => 'Enter the region',
                    'class' => 'form-control'
                ],
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}