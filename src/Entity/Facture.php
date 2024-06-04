<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\FactureRepository;
use App\Repository\EntrepriseRepository;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Entreprise;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idFacture", type: "integer", nullable: false)]
    private ?int $idFacture = null;

    #[ORM\Column(type : "datetime")]
    private ?DateTimeInterface $date = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "idCommande", referencedColumnName: 'idCommande')] 
    private ?Commande $idCommande = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "idEntreprise", referencedColumnName: 'idEntreprise')] 
    private ?Entreprise $idEntreprise = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id", referencedColumnName: 'id')] 
    private ?User $id= null; 

    #[ORM\Column(length: 50)]
    private ?string $somme = null;


    public function getIdFacture(): ?int
    {
        return $this->idFacture;
    }

      
    #[ORM\PrePersist]   
    public function prePersist(): void
    {
        error_log('PrePersist is triggered'); // Log to verify
        $this->date = new DateTime(); // Установка текущей даты при создании объекта
    }
    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getIdCommande(): ?Commande
    {
        return $this->idCommande;
    }

    public function setIdCommande(?Commande $idCommande): static
    {
        $this->idCommande = $idCommande;

        return $this;
    }
 
    public function getIdEntreprise(): ?Entreprise
    {
        return $this->idEntreprise;
    }

    public function setIdEntreprise(?Entreprise $idEntreprise): static
    {
        $this->idEntreprise = $idEntreprise;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->id;
    }

    public function setUser(?User $id): static
    {
        $this->id = $id;

        return $this;
    }
    public function getSomme(): float
    {
        return $this->somme;
    }

    public function setSomme($somme)
    {
        $this->somme = $somme;
        return $this;
    }
}
