<?php

namespace App\Form\Produit;

use App\Entity\Produit;
use App\Entity\LigneCommande;
use Symfony\Component\Form\AbstractType;
use App\Form\LigneCommande\LigneCommandeType;

use App\Form\Produit\ProduitLigneCommandeType;
use App\Form\Produit\ProduitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\DataTransformerInterface;

class ProduitLigneCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_produit', EntityType::class,
            [
               'class' => Produit::class,
               'choice_label' => 'nom_produit',
            ])
            ->add('prix_produit', EntityType::class,
            [
               'class' => Produit::class,
               'choice_label' => 'prix_produit',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitLigneCommandeType::class,       
        ]);
    }

}
