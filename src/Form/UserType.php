<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('nom')
            ->add('prenom')
            ->add('tel')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Organisateur' => 'ROLE_ORGANISATEUR',
                    'Artisan' => 'ROLE_ARTISAN',
                ],
                'expanded' => false,  // Use a dropdown instead of checkboxes
                'multiple' => true,   // Allow selecting multiple roles if needed
                'data' => $options['data']->getRoles(), // Ensure default roles are set correctly
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
