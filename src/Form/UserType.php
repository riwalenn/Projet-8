<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label'     => "Nom d'utilisateur",
                'attr'      => [
                    'aria-describedby' => 'basic-addon3',
                    'placeholder' => 'Nom d\'utilisateur...',
                    'class'     => 'form-control'
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type'              => PasswordType::class,
                'invalid_message'   => 'Les deux mots de passe doivent correspondre.',
                'required'          => true,
                'first_options'     => ['label' => 'Mot de passe',
                'attr' => [
                    'aria-describedby' => 'basic-addon3',
                    'placeholder' => 'Mot de passe...',
                    'class' => 'form-control'
                ]],
                'second_options'    => [
                    'label' => 'Tapez le mot de passe à nouveau',
                    'attr'  => [
                        'aria-describedby' => 'basic-addon3',
                        'placeholder' => 'Mot de passe...',
                        'class' => 'form-control'
                    ]]
            ])
            ->add('email', EmailType::class, [
                'label'     => 'Adresse email',
                'attr'      => [
                    'aria-describedby' => 'basic-addon3',
                    'placeholder' => 'Email de l\'utilisateur...',
                    'class'     => 'form-control'
                ]])
            ->add('roles', ChoiceType::class, [
                'mapped'    => false,
                'label'     => 'Rôle',
                'attr'      => [
                    'aria-describedby' => 'basic-addon3',
                    'class'     => 'form-control'
                ],
                'choices'   => [
                    'Utilisateur'    => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
