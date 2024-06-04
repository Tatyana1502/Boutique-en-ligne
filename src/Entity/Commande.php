<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\PanierRepository;
use App\Repository\LigneCommandeRepository;
use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Panier;
use App\Entity\LigneCommande;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "idCommande", type: "integer")]
    private ?int $idCommande = null;

    #[ORM\Column(length: 255)]
    private ?string $statut_commande = null;

    #[ORM\Column(nullable: true)]
    private ?array $toutLigneCommande = null;

    #[ORM\Column(type : "datetime")]
    private ?DateTimeInterface $data_creation_commande = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, name: "id", referencedColumnName: 'id')]
    private ?User $user = null;    

    public function getIdCommande(): ?int
    {
        return $this->idCommande;
    }

    public function getStatutCommande(): ?string
    {
        return $this->statut_commande;
    }

    public function setStatutCommande(string $statut_commande): static
    {
        $this->statut_commande = $statut_commande;
        return $this;
    }

    public function getToutLigneCommande(): ?array
    {
        return $this->toutLigneCommande ?? [];
    }

    public function setToutLigneCommande(?array $toutLigneCommande): static
    {
        $this->toutLigneCommande[] = $toutLigneCommande;
        return $this;
    }

    public function setToutLigneCommandeSuprimme(?array $toutLigneCommande): static
    {
        $this->toutLigneCommande = $toutLigneCommande;
        return $this;
    }
 
        /**
     * Check if the command is active and was created more than 1 days ago
     */
    public function isOldActiveCommand(): bool
    {
        $currentDate = new \DateTime();
        $oneDaysAgo = $currentDate->modify('-5 minutes');
        return ($this->getStatutCommande() === 'active' && $this->getDateCreationCommande() <= $oneDaysAgo);
    }  

    #[ORM\PrePersist]   
    public function prePersist(): void
    {
        error_log('PrePersist is triggered'); // Log to verify
        $this->data_creation_commande = new DateTime(); // Установка текущей даты при создании объекта
    }

    public function getDataCreationCommande(): ?DateTimeInterface
    {
        return $this->data_creation_commande;
    }

    public function setDataCreationCommande(?DateTimeInterface $data_creation_commande): self
    {
        $this->data_creation_commande = $data_creation_commande;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
}