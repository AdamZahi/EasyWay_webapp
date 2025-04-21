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
    #[ORM\Column(name: "id_conducteur")]
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
    #[Assert\Regex(
        pattern: "/^[0-9]{8,15}$/",
        message: "Le numéro de téléphone doit contenir entre 8 et 15 chiffres"
    )]
    private ?string $telephonne = null;

    #[ORM\Column(length: 255, nullable: false, options: ["default" => "default_profile.png"])]
    private ?string $photo_profil = 'default_profile.png';

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de trajets effectués ne peut pas être vide")]
    #[Assert\Type(type: 'integer', message: "Le nombre de trajets doit être un nombre")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le nombre de trajets ne peut pas être négatif")]
    private ?int $nb_trajet_effectues = 0;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Le nombre de passagers transportés ne peut pas être vide")]
    #[Assert\Type(type: 'integer', message: "Le nombre de passagers doit être un nombre")]
    #[Assert\GreaterThanOrEqual(value: 0, message: "Le nombre de passagers ne peut pas être négatif")]
    private ?int $nb_passagers_transportes = 0;

    public function __construct()
    {
        $this->photo_profil = 'default_profile.png';
        $this->nb_trajet_effectues = 0;
        $this->nb_passagers_transportes = 0;
    }

    public function getIdConducteur(): ?int
    {
        return $this->id_conducteur;
    }

    public function setIdConducteur(int $id_conducteur): static
    {
        $this->id_conducteur = $id_conducteur;
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
        $this->telephonne = $user->getTelephonne();
        $this->mot_de_passe = $user->getPassword();
        $this->photo_profil = $user->getPhotoProfil() ?? 'default_profile.png';
        $this->nb_trajet_effectues = 0;
        $this->nb_passagers_transportes = 0;

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

    public function getNbPassagersTransportes(): ?int
    {
        return $this->nb_passagers_transportes;
    }

    public function setNbPassagersTransportes(int $nb_passagers_transportes): static
    {
        $this->nb_passagers_transportes = $nb_passagers_transportes;
        return $this;
    }
}
