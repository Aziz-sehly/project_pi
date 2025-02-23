<?php
namespace App\Form;

use App\Entity\Booking;
use App\Entity\Hotel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;


class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('utilisateur', TextType::class, [
                'required' => true,
                'attr' => ['maxlength' => 255],
            ])
            ->add('id_hotel', EntityType::class, [
                'class' => Hotel::class,
                'choice_label' => 'nom', // Adjust according to your Hotel entity
                'required' => true,
                // Disable the field if a hotel is preselected
                'disabled' => $options['disable_hotel'],
            ])
            ->add('check_in', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'data' => new \DateTime('now'),  
            ])
            ->add('check_out', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'attr' => ['class' => 'form-control'],
                'data' => new \DateTime('now'),  // You can modify this to some default check-out date
            ])
            ->add('montant_total', NumberType::class, [
                'required' => true,
            ])
            ->add('number_of_guest', NumberType::class, [
                'required' => true,
            ])
            ->add('payement_method', TextType::class, [
                'required' => true,
                'attr' => ['maxlength' => 255],
            ])
            ->add('special_request', TextType::class, [
                'required' => true,
                'attr' => ['maxlength' => 255],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
{
    $resolver->setDefaults([
        'data_class' => Booking::class,
        'disable_hotel' => false, // Définit une valeur par défaut
    ]);
}
}
