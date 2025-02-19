<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product', ChoiceType::class, [
                'choices' => $this->getProductChoices(),
                'choice_label' => function ($choice) {
                    return $choice->getName();
                },
                'choice_value' => 'id',
                'label' => 'Product',
                'placeholder' => 'Select a product',
            ])
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantity',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Place Order',
            ]);
    }

    private function getProductChoices()
    {
        return $this->productRepository->findAll();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
