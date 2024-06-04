<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idProduit", type: "integer")]
    private ?int $idProduit = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_produit = null;

    #[ORM\Column(length: 50)]
    private ?string $description_produit = null;

    #[ORM\Column(length: 50)]
    private ?string $prix_produit = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie_produit = null;

    #[ORM\Column(length: 50)]
    private ?string $code_barre_produit = null;

    #[ORM\Column(type: "string", length: 255)]
    private ?string $image_produit = null;

    #[ORM\Column(length: 50)]
    private ?string $quantity_produit = null;

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }
    public function setIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getNomProduit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNomProduit(string $nom_produit): static
    {
        $this->nom_produit = $nom_produit;

        return $this;
    }

    public function getDescriptionProduit(): ?string
    {
        return $this->description_produit;
    }

    public function setDescriptionProduit(string $description_produit): static
    {
        $this->description_produit = $description_produit;

        return $this;
    }

    public function getPrixProduit(): ?string
    {
        return $this->prix_produit;
    }

    public function setPrixProduit(string $prix_produit): static
    {
        $this->prix_produit = $prix_produit;

        return $this;
    }

    public function getCategorieProduit(): ?string
    {
        return $this->categorie_produit;
    }

    public function setCategorieProduit(string $categorie_produit): static
    {
        $this->categorie_produit = $categorie_produit;

        return $this;
    }

    public function getCodeBarreProduit(): ?string
    {
        return $this->code_barre_produit;
    }

    public function setCodeBarreProduit(string $code_barre_produit): static
    {
        $this->code_barre_produit = $code_barre_produit;

        return $this;
    }

    public function getImageProduit(): ?string
    {
        return $this->image_produit;
    }

    public function setImageProduit(string $image_produit): static
    {
        $this->image_produit = $image_produit;

        return $this;
    }

    public function getQuantityProduit(): ?string
    {
        return $this->quantity_produit;
    }

    public function setQuantityProduit(string $quantity_produit): static
    {
        $this->quantity_produit = $quantity_produit;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'idProduit' => $this->getIdProduit(),
            'nom_produit' => $this->getNomProduit(),
            'description_produit' => $this->getDescriptionProduit(),
            'prix_produit' => $this->getPrixProduit(),
            'categorie_produit' => $this->getCategorieProduit(),
            // 'code_barre_produit' => $this->getCodeBarreProduit(),
            'image_produit' => $this->getImageProduit(),
            'quantity_produit' => $this->getQuantityProduit()
        ];
    }
    
}
