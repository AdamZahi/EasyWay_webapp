<?php

namespace App\Entity;

use App\Enum\RoleEnum;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id_user = null;

    #[ORM\Column(type: 'string', enumType: RoleEnum::class)]
    #[Assert\NotBlank(message: 'Le rôle ne peut pas être vide')]
    private RoleEnum $role;  

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom ne peut pas être vide')]
    #[Assert\Length(
        min: 2, 
        max: 50, 
        minMessage: 'Le nom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
        message: 'Le nom ne doit contenir que des lettres, espaces et tirets'
    )]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le prénom ne peut pas être vide')]
    #[Assert\Length(
        min: 2, 
        max: 50, 
        minMessage: 'Le prénom doit contenir au moins {{ limit }} caractères',
        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères'
    )]
    #[Assert\Regex(
        pattern: '/^[a-zA-ZÀ-ÿ\s\-]+$/',
        message: 'Le prénom ne doit contenir que des lettres, espaces et tirets'
    )]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: 'L\'email ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
        message: 'L\'email doit être au format exemple@exemple.exemple'
    )]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'email ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $email = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: 'Le mot de passe ne peut pas être vide')]
    #[Assert\Regex(
        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        message: 'Le mot de passe doit contenir au moins 8 caractères avec au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial'
    )]
    private ?string $mot_de_passe = null;  

    #[ORM\Column(type: 'bigint', nullable: true)]
    #[Assert\Type(
        type: 'numeric',
        message: 'Le numéro de téléphone doit être un nombre'
    )]
    #[Assert\Regex(
        pattern: '/^[0-9]{8,}$/',
        message: 'Le numéro de téléphone doit contenir au moins 8 chiffres'
    )]
    private ?int $telephonne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation_compte = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPEG, PNG, GIF)'
    )]
    private ?string $photo_profil = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Admin $admin = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Conducteur $conducteur = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Passager $passager = null;

    #[ORM\Column]
    private bool $isVerified = false;

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getRoles(): array
{
    return match($this->role) {
        RoleEnum::ADMIN => ['ROLE_ADMIN'],
        RoleEnum::CONDUCTEUR => ['ROLE_CONDUCTEUR'],
        RoleEnum::PASSAGER => ['ROLE_PASSAGER'],
        default => ['ROLE_USER'],
    };
}


    public function getRole(): RoleEnum
    {
        return $this->role;
    }

    public function setRole(RoleEnum $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }


    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function getPassword(): ?string
{
    return $this->mot_de_passe;
}

    

public function setMotDePasse(string $password): self
{
    $this->mot_de_passe = $password;
    return $this;
}


    public function eraseCredentials(): void
    {
        // clear sensitive data if any
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephonne(): ?int
    {
        return $this->telephonne;
    }

    public function setTelephonne(?int $telephonne): static
    {
        $this->telephonne = $telephonne;

        return $this;
    }

    public function getDateCreationCompte(): ?\DateTimeInterface
    {
        return $this->date_creation_compte;
    }

    public function setDateCreationCompte(\DateTimeInterface $date_creation_compte): static
    {
        $this->date_creation_compte = $date_creation_compte;

        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photo_profil;
    }

    public function setPhotoProfil(?string $photo_profil): static
    {
        $this->photo_profil = $photo_profil;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function getConducteur(): ?Conducteur
    {
        return $this->conducteur;
    }

    public function setConducteur(?Conducteur $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getPassager(): ?Passager
    {
        return $this->passager;
    }

    public function setPassager(?Passager $passager): static
    {
        $this->passager = $passager;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
