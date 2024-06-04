<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idLigneCommande", type: "integer")]
    private ?int $idLigneCommande = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "idProduit", referencedColumnName: 'idProduit')] 
    private ?Produit $idProduit = null; 

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id", referencedColumnName: 'id')]     
    private ?User $id = null; 


    #[ORM\Column(length: 255)]
    private ?string $quantite_achat = null;

    public function getIdLigneCommande(): ?int
    {
        return $this->idLigneCommande;
    }

    public function setIdLigneCommande(): ?int
    {
        return $this->idLigneCommande;
    }


    public function getId(): ?User
    {
        return $this->id;
    }

    public function setId(?User $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdProduit(): ?Produit
    {
        return $this->idProduit;
    }

    public function setIdProduit(?Produit $idProduit): static
    {
        $this->idProduit = $idProduit;

        return $this;
    }


    public function getQuantiteAchat(): ?string
    {
        return $this->quantite_achat;
    }

    public function setQuantiteAchat(string $quantite_achat): static
    {
        $this->quantite_achat = $quantite_achat;

        return $this;
    }
    
    public function toArray(): array
    {
        $produit = $this->getIdProduit();
        if ($produit !== null)
        {
            return [
                'idLigneCommande' => $this->getIdLigneCommande(),
                'idProduit' => [
                    'nom_produit' => $produit->getNomProduit(),
                    'prix_produit' => $produit->getPrixProduit(),
                ],
                'quantite_achat' => $this->getQuantiteAchat(),
                'total' => $this->getSousTotal(),
            ];
        } else {
            // Gérer le cas où l'objet produit est nul
            return [];
        }
    }

    public function getTotal(): float
    {
        $total_par_linge = 0.0;
        foreach ($this->ligneCommande as $ligne) 
        {
            $total_par_linge += $ligne->getSousTotal();
        }
        return $total_par_linge;
    }

//    @return float
    
   public function getSousTotal(): float
   {
       $produit = $this->getIdProduit();
       return $this->quantite_achat * $produit->getPrixProduit();
   }

   public function setTotal(): self
    {
    $this->total_par_linge = $this->getSousTotal();
    return $this;
    }
    
}
