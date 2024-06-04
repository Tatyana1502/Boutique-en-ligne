<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idEntreprise", type: "integer")]
    private ?int $idEntreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_entreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $adresse_entreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $phone_entreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $email_entreprise = null;

    #[ORM\Column(length: 50)]
    private ?string $descripsion_entreprise = null;

    #[ORM\OneToMany(targetEntity: Facture::class, mappedBy: 'idEntreprise')]
    private Collection $factures;

    public function __construct()
    {
        $this->factures = new ArrayCollection();
    }

    public function getidEntreprise(): ?int
    {
        return $this->idEntreprise;
    }

    public function getNomEntreprise(): ?string
    {
        return $this->nom_entreprise;
    }

    public function setNomEntreprise(string $nom_entreprise): static
    {
        $this->nom_entreprise = $nom_entreprise;

        return $this;
    }

    public function getAdresseEntreprise(): ?string
    {
        return $this->adresse_entreprise;
    }

    public function setAdresseEntreprise(string $adresse_entreprise): static
    {
        $this->adresse_entreprise = $adresse_entreprise;

        return $this;
    }

    public function getPhoneEntreprise(): ?string
    {
        return $this->phone_entreprise;
    }

    public function setPhoneEntreprise(string $phone_entreprise): static
    {
        $this->phone_entreprise = $phone_entreprise;

        return $this;
    }

    public function getEmailEntreprise(): ?string
    {
        return $this->email_entreprise;
    }

    public function setEmailEntreprise(string $email_entreprise): static
    {
        $this->email_entreprise = $email_entreprise;

        return $this;
    }

    public function getDescripsionEntreprise(): ?string
    {
        return $this->descripsion_entreprise;
    }

    public function setDescripsionEntreprise(string $descripsion_entreprise): static
    {
        $this->descripsion_entreprise = $descripsion_entreprise;

        return $this;
    }

    /**
     * @return Collection<int, Facture>
     */
    public function getFactures(): Collection
    {
        return $this->factures;
    }

    public function addFacture(Facture $facture): static
    {
        if (!$this->factures->contains($facture)) {
            $this->factures->add($facture);
            $facture->setIdEntreprise($this);
        }

        return $this;
    }

    public function removeFacture(Facture $facture): static
    {
        if ($this->factures->removeElement($facture)) {
            // set the owning side to null (unless already changed)
            if ($facture->getIdEntreprise() === $this) {
                $facture->setIdEntreprise(null);
            }
        }

        return $this;
    }
}
