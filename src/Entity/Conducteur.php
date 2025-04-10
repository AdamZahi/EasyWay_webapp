<?php

namespace App\Entity;

use App\Repository\ConducteurRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConducteurRepository::class)]
class Conducteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_conducteur = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'conducteur')]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user", nullable: false)]  // Use id_user
    private ?User $user = null;
    
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\Column(length: 255)]
    private ?string $telephonne = null;

    #[ORM\Column(length: 255)]
    private ?string $photo_profil = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_permis = null;

    #[ORM\Column(length: 255)]
    private ?string $experience = null;

    public function getIdConducteur(): ?int
    {
        return $this->id_conducteur;
    }

    public function setIdConducteur(int $id_conducteur): static
    {
        $this->id_conducteur = $id_conducteur;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }
}
