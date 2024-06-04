<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles')
            ->add('status')
            ->add('password')
            ->add('nom_user')
            ->add('prenom_user')
            ->add('phone_user')
            ->add('code_postal_user')
            ->add('adresse_user')
            ->add('date_naiss_user')
            ->add('date_creation_user', null, [
                'widget' => 'single_text',
            ])
            ->add('favoriteProduct')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
