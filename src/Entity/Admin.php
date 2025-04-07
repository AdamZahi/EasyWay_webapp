<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Station;

#[ORM\Entity]
class Admin
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_admin;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "admins")]
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

    #[ORM\Column(type: "string", length: 255)]
    private string $telephonne;

    #[ORM\Column(type: "text")]
    private string $photo_profil;

    public function getId_admin()
    {
        return $this->id_admin;
    }

    public function setId_admin($value)
    {
        $this->id_admin = $value;
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

    #[ORM\OneToMany(mappedBy: "id_createur", targetEntity: Evenement::class)]
    private Collection $evenements;

        public function getEvenements(): Collection
        {
            return $this->evenements;
        }
    
        public function addEvenement(Evenement $evenement): self
        {
            if (!$this->evenements->contains($evenement)) {
                $this->evenements[] = $evenement;
                $evenement->setId_createur($this);
            }
    
            return $this;
        }
    
        public function removeEvenement(Evenement $evenement): self
        {
            if ($this->evenements->removeElement($evenement)) {
                // set the owning side to null (unless already changed)
                if ($evenement->getId_createur() === $this) {
                    $evenement->setId_createur(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_admin", targetEntity: Station::class)]
    private Collection $stations;

        public function getStations(): Collection
        {
            return $this->stations;
        }
    
        public function addStation(Station $station): self
        {
            if (!$this->stations->contains($station)) {
                $this->stations[] = $station;
                $station->setId_admin($this);
            }
    
            return $this;
        }
    
        public function removeStation(Station $station): self
        {
            if ($this->stations->removeElement($station)) {
                // set the owning side to null (unless already changed)
                if ($station->getId_admin() === $this) {
                    $station->setId_admin(null);
                }
            }
    
            return $this;
        }
}
