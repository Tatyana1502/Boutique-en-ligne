<?php

namespace App\Form\Produit;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieProduitType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('categorie_produit', ChoiceType::class, [
            'label' => 'categorie_produit',
            'choices' => $this->getCategorie_Produit(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }

    private function getCategorie_Produit()
    {
        
        $query = $this->entityManager->createQuery('SELECT produit FROM App\Entity\Produit produit GROUP BY produit.categorie_produit');
        $produit = $query->getResult();


        $titres = [];
        foreach ($produit as $produit) 
        {        
            $titres[$produit->getCategorieProduit()] = $produit->getCategorieProduit();
        }
        return $titres;
    }
}
