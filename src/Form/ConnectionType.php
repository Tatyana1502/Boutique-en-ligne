<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ConnectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class,
            ['label'=>'E-mail',
            'attr' => [
                'placeholder' => 'Saisir votre e-mail.',
                'class' => 'form-control', 
            ],
            ])

            ->add('password', PasswordType::class,
            ['label'=>'Mot de passe',
            'attr' => [
                'placeholder' => 'Saisir votre mot de passe.',
                'class' => 'form-control', 
            ],
            ])
            ->add('nom_user', null, [
                'attr' => [
                    'class' => 'form-control', // Add your CSS class here
                ],
            ])
            ->add('prenom_user', null, [
                'attr' => [
                    'class' => 'form-control', 
                ],
            ])
            ->add('phone_user', null, [
                'attr' => [
                    'class' => 'form-control', 
                ],
            ])
            ->add('code_postal_user', null, [
                'attr' => [
                    'class' => 'form-control', 
                ],
            ])
            ->add('adresse_user', null, [
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('date_naiss_user', null, [
                'attr' => [
                    'class' => 'form-control', 
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary', 
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
