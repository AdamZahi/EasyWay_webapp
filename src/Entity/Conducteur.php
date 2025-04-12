<?php

namespace App\Entity;

use App\Repository\ConducteurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConducteurRepository::class)]
class Conducteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_conducteur = null;

    #[ORM\OneToOne(inversedBy: 'conducteur', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le nom doit contenir au moins 2 caractères", maxMessage: "Le nom ne peut pas dépasser 50 caractères")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ\s\-]+$/", message: "Le nom ne doit contenir que des lettres, des espaces et des tirets")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom ne peut pas être vide")]
    #[Assert\Length(min: 2, max: 50, minMessage: "Le prénom doit contenir au moins 2 caractères", maxMessage: "Le prénom ne peut pas dépasser 50 caractères")]
    #[Assert\Regex(pattern: "/^[a-zA-ZÀ-ÿ\s\-]+$/", message: "Le prénom ne doit contenir que des lettres, des espaces et des tirets")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide")]
    #[Assert\Email(message: "L'email n'est pas valide")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide")]
    #[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins 8 caractères")]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide")]
    #[Assert\Regex(pattern: "/^[0-9]{8,15}$/", message: "Le numéro de téléphone doit contenir entre 8 et 15 chiffres")]
    private ?string $telephonne = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La photo de profil est obligatoire")]
    #[Assert\File(
        maxSize: "1024k",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)"
    )]
    private ?string $photo_profil = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le numéro de permis est obligatoire")]
    #[Assert\Length(min: 8, max: 20, minMessage: "Le numéro de permis doit contenir au moins 8 caractères", maxMessage: "Le numéro de permis ne peut pas dépasser 20 caractères")]
    private ?string $numero_permis = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'expérience est obligatoire")]
    #[Assert\Length(min: 1, max: 50, minMessage: "L'expérience doit être spécifiée", maxMessage: "L'expérience ne peut pas dépasser 50 caractères")]
    private ?string $experience = null;

    public function getIdConducteur(): ?int
    {
        return $this->id_conducteur;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
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

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): static
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getTelephonne(): ?string
    {
        return $this->telephonne;
    }

    public function setTelephonne(string $telephonne): static
    {
        $this->telephonne = $telephonne;
        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photo_profil;
    }

    public function setPhotoProfil(string $photo_profil): static
    {
        $this->photo_profil = $photo_profil;
        return $this;
    }

    public function getNumeroPermis(): ?string
    {
        return $this->numero_permis;
    }

    public function setNumeroPermis(string $numero_permis): static
    {
        $this->numero_permis = $numero_permis;
        return $this;
    }

    public function getExperience(): ?string
    {
        return $this->experience;
    }

    public function setExperience(string $experience): static
    {
        $this->experience = $experience;
        return $this;
    }
}
