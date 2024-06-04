<?php

namespace App\Form\LigneCommande;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use App\Entity\User;

use App\Form\Produit\ProduitLigneCommandeType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class LigneCommandeType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('id', HiddenType::class,[
            'mapped' => false,   
       ])
        ->add('idProduit', HiddenType::class,[
            'mapped' => false,
        ])          
        ->add('quantite_achat', null, [
            'required' => false, 
            'empty_data' => '1',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LigneCommande::class,
        ]);
    }

    private function getProduit()
    {
        
        $query = $this->entityManager->createQuery('SELECT produit FROM App\Entity\Produit produit GROUP BY produit.idProduit');
        $produit = $query->getResult();
        $titres = [];
        foreach ($produit as $produit)
        {
            $titres[$produit->getIdProduit()] = $produit->getIdProduit();
        }
        return $titres;
    }
}
