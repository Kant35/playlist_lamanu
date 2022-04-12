<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles', ChoiceType::class, [
            //     'choices' => [
            //         'Utilisateur' => 'ROLE_USER',
            //         'Administrateur' => 'ROLE_ADMIN',
            //         'Premium' => 'ROLE_PREMIUM',
            //         'Super Administrateur' => 'ROLE_SUPER_ADMIN'
            //     ],
            //     'multiple' => true,
            //     'expanded' => true
            // ])
            // ->add('password', PasswordType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mdp et la confirmation doivent être identique',
                'first_options' => ['label' => 'Mot de Passe'],
                'second_options' => ['label' => 'Confirmez votre Mot de Passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'indiquer votre MDP'
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit contenir au minimum {{ limit }} caractères'
                    ])
                ]
            ])
            ->add('prenom')
            ->add('nom')
            // ->add('created_at')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
