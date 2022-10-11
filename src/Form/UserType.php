<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\JsonType;
use Doctrine\DBAL\Types\ArrayType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => "Nom d'utilisateur",
                'required' => false,
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse email',
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent correspondre.',
                'required' => false,
                'first_options'  => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Tapez le mot de passe à nouveau',
                ],
            ])
            ->add('roleSelection', ChoiceType::class, [
                    'choices'  => [
                        'Role utilisateur' => 'ROLE_USER',
                        'Role administrateur' => 'ROLE_ADMIN',
                    ],
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['base','createUser']
        ]);
    }
}
