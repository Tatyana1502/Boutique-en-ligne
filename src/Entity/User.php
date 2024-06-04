<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer")]
    private ?int $id= null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $nom_user = null;

    #[ORM\Column(length: 50)]
    private ?string $prenom_user = null;

    #[ORM\Column(length: 50)]
    private ?string $phone_user = null;

    #[ORM\Column(length: 50)]
    private ?string $code_postal_user = null;

    #[ORM\Column(length: 50)]
    private ?string $adresse_user = null;

    #[ORM\Column(length: 50)]
    private ?string $date_naiss_user = null;

    #[ORM\Column(type : "datetime")]
    private ?DateTimeInterface $date_creation_user = null;

    #[ORM\Column(nullable: true)]
    private ?array $favoriteProduct = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): static
    {
        $this->nom_user = $nom_user;

        return $this;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenom_user;
    }

    public function setPrenomUser(string $prenom_user): static
    {
        $this->prenom_user = $prenom_user;

        return $this;
    }

    public function getPhoneUser(): ?string
    {
        return $this->phone_user;
    }

    public function setPhoneUser(string $phone_user): static
    {
        $this->phone_user = $phone_user;

        return $this;
    }

    public function getCodePostalUser(): ?string
    {
        return $this->code_postal_user;
    }

    public function setCodePostalUser(string $code_postal_user): static
    {
        $this->code_postal_user = $code_postal_user;

        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresse_user;
    }

    public function setAdresseUser(string $adresse_user): static
    {
        $this->adresse_user = $adresse_user;

        return $this;
    }

    public function getDateNaissUser(): ?string
    {
        return $this->date_naiss_user;
    }

    public function setDateNaissUser(string $date_naiss_user): static
    {
        $this->date_naiss_user = $date_naiss_user;

        return $this;
    }
    #[ORM\PrePersist]   
    public function prePersist(): void
    {
        error_log('PrePersist is triggered'); // Log to verify
        $this->date_creation_user = new DateTime(); // Setting the current date when creating an object
    }
    public function getDateCreationUser(): ?DateTimeInterface
    {
        return $this->date_creation_user;
    }

    public function setDateCreationUser(?DateTimeInterface $date_creation_user): self
    {
        $this->date_creation_user = $date_creation_user;
        return $this;
    }

    public function getFavoriteProduct(): ?array
    {
         return $this->favoriteProduct;
    }

    public function setFavoriteProduct(?array $favoriteProduct): static
    {
        $this->favoriteProduct[] = $favoriteProduct;
        return $this;
    }

    public function removeFavoriteProduct(int $idProduit): void
    {
        // Recherchez l'index du produit dans le tableau des favoris
        $index = array_search($idProduit, $this->favoriteProduct, true);
        // Supprimez l'élément si l'index est trouvé
        if ($index !== false) 
        {
            unset($this->favoriteProduct[$index]);
        }
    }

    public function setFavoriteProductSuprimme(?array $favoriteProduct): static
    {
        $this->favoriteProduct = $favoriteProduct;
        return $this;
    }

}

