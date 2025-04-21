<?php

namespace App\Entity;

use App\Repository\PassagerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PassagerRepository::class)]
class Passager
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_passager")]
    private ?int $id_passager = null;

    #[ORM\OneToOne(inversedBy: 'passager', cascade: ['persist', 'remove'])]
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

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le numéro de téléphone ne peut pas être vide")]
    #[Assert\Type(type: 'integer', message: "Le numéro de téléphone doit être un nombre")]
    #[Assert\Length(min: 8, max: 15, minMessage: "Le numéro de téléphone doit contenir au moins 8 chiffres", maxMessage: "Le numéro de téléphone ne peut pas dépasser 15 chiffres")]
    private ?int $telephonne = null;

    #[ORM\Column(length: 255, nullable: false, options: ["default" => "default_profile.png"])]
    private ?string $photo_profil = 'default_profile.png';

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de trajets effectués ne peut pas être vide")]
    #[Assert\Type(type: 'integer', message: "Le nombre de trajets doit être un nombre")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le nombre de trajets ne peut pas être négatif")]
    private ?int $nb_trajet_effectues = 0;

    public function __construct()
    {
        $this->photo_profil = 'default_profile.png';
        $this->nb_trajet_effectues = 0;
    }

    public function getIdPassager(): ?int
    {
        return $this->id_passager;
    }

    public function setIdPassager(int $id_passager): static
    {
        $this->id_passager = $id_passager;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        $this->nom = $user->getNom();
        $this->prenom = $user->getPrenom();
        $this->email = $user->getEmail();
        $this->telephonne = (int)$user->getTelephonne();
        $this->mot_de_passe = $user->getPassword();
        $this->photo_profil = $user->getPhotoProfil() ?? 'default_profile.png';
        $this->nb_trajet_effectues = 0;

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

    public function getTelephonne(): ?int
    {
        return $this->telephonne;
    }

    public function setTelephonne(int $telephonne): static
    {
        $this->telephonne = $telephonne;
        return $this;
    }

    public function getPhotoProfil(): ?string
    {
        return $this->photo_profil;
    }

    public function setPhotoProfil(?string $photo_profil): static
    {
        $this->photo_profil = $photo_profil ?? 'default_profile.png';
        return $this;
    }

    public function getNbTrajetEffectues(): ?int
    {
        return $this->nb_trajet_effectues;
    }

    public function setNbTrajetEffectues(int $nb_trajet_effectues): static
    {
        $this->nb_trajet_effectues = $nb_trajet_effectues;
        return $this;
    }
}
