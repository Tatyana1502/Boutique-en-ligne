<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\User;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data_facture')
            ->add('numero_facture')
            ->add('id', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom_user',
            ])
            ->add('idProduit', EntityType::class, [
                'class' => Produit ::class,
                'choice_label' => 'nom_produit',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
