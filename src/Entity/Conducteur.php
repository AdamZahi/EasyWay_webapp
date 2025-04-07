<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

#[ORM\Entity]
class Conducteur
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_conducteur;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "conducteurs")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id_user")]
    private ?User $user = null;
    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string", length: 255)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $mot_de_passe;

    #[ORM\Column(type: "integer")]
    private int $telephonne;

    #[ORM\Column(type: "text")]
    private string $photo_profil;

    #[ORM\Column(type: "string", length: 255)]
    private string $numero_permis;

    #[ORM\Column(type: "string", length: 255)]
    private string $experience;

    public function getId_conducteur()
    {
        return $this->id_conducteur;
    }

    public function setId_conducteur($value)
    {
        $this->id_conducteur = $value;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getPrenom()
    {
        return $this->prenom;
    }

    public function setPrenom($value)
    {
        $this->prenom = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getMot_de_passe()
    {
        return $this->mot_de_passe;
    }

    public function setMot_de_passe($value)
    {
        $this->mot_de_passe = $value;
    }

    public function getTelephonne()
    {
        return $this->telephonne;
    }

    public function setTelephonne($value)
    {
        $this->telephonne = $value;
    }

    public function getPhoto_profil()
    {
        return $this->photo_profil;
    }

    public function setPhoto_profil($value)
    {
        $this->photo_profil = $value;
    }

    public function getNumero_permis()
    {
        return $this->numero_permis;
    }

    public function setNumero_permis($value)
    {
        $this->numero_permis = $value;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setExperience($value)
    {
        $this->experience = $value;
    }
}
