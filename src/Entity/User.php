<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Paiement;

#[ORM\Entity]
class User
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_user;

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

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation_compte;

    #[ORM\Column(type: "text")]
    private string $photo_profil;

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
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

    public function getDate_creation_compte()
    {
        return $this->date_creation_compte;
    }

    public function setDate_creation_compte($value)
    {
        $this->date_creation_compte = $value;
    }

    public function getPhoto_profil()
    {
        return $this->photo_profil;
    }

    public function setPhoto_profil($value)
    {
        $this->photo_profil = $value;
    }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Admin::class)]
    private Collection $admins;

        public function getAdmins(): Collection
        {
            return $this->admins;
        }
    
        public function addAdmin(Admin $admin): self
        {
            if (!$this->admins->contains($admin)) {
                $this->admins[] = $admin;
                $admin->setId_user($this);
            }
    
            return $this;
        }
    
        public function removeAdmin(Admin $admin): self
        {
            if ($this->admins->removeElement($admin)) {
                // set the owning side to null (unless already changed)
                if ($admin->getId_user() === $this) {
                    $admin->setId_user(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Conducteur::class)]
    private Collection $conducteurs;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Posts::class)]
    private Collection $postss;

    #[ORM\OneToMany(mappedBy: "user_id", targetEntity: Reservation::class)]
    private Collection $reservations;

        public function getReservations(): Collection
        {
            return $this->reservations;
        }
    
        public function addReservation(Reservation $reservation): self
        {
            if (!$this->reservations->contains($reservation)) {
                $this->reservations[] = $reservation;
                $reservation->setUser_id($this);
            }
    
            return $this;
        }
    
        public function removeReservation(Reservation $reservation): self
        {
            if ($this->reservations->removeElement($reservation)) {
                // set the owning side to null (unless already changed)
                if ($reservation->getUser_id() === $this) {
                    $reservation->setUser_id(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    #[ORM\OneToMany(mappedBy: "user_id", targetEntity: Paiement::class)]
    private Collection $paiements;
}
