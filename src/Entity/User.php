<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Role\RoleInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_user;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    #[ORM\Column(type: "string", length: 255)]
    private string $prenom;

    #[ORM\Column(type: "string", length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $mot_de_passe;

    #[ORM\Column(type: "integer")]
    private int $telephonne;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation_compte;

    #[ORM\Column(type: "text")]
    private string $photo_profil;

    #[ORM\Column(type: "array")]
    private array $roles = [];

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Admin::class)]
    private Collection $admins;

    #[ORM\OneToMany(mappedBy: "id_user", targetEntity: Conducteur::class)]
    private Collection $conducteurs;

    #[ORM\OneToMany(mappedBy: "user_id", targetEntity: Reservation::class)]
    private Collection $reservations;

    #[ORM\OneToMany(mappedBy: "user_id", targetEntity: Paiement::class)]
    private Collection $paiements;

    // Getters and Setters for all fields
    public function getId_user(): int
    {
        return $this->id_user;
    }

    public function setId_user(int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getMot_de_passe(): string
    {
        return $this->mot_de_passe;
    }

    public function setMot_de_passe(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;
        return $this;
    }

    public function getTelephonne(): int
    {
        return $this->telephonne;
    }

    public function setTelephonne(int $telephonne): self
    {
        $this->telephonne = $telephonne;
        return $this;
    }

    public function getDate_creation_compte(): \DateTimeInterface
    {
        return $this->date_creation_compte;
    }

    public function setDate_creation_compte(\DateTimeInterface $date_creation_compte): self
    {
        $this->date_creation_compte = $date_creation_compte;
        return $this;
    }

    public function getPhoto_profil(): string
    {
        return $this->photo_profil;
    }

    public function setPhoto_profil(string $photo_profil): self
    {
        $this->photo_profil = $photo_profil;
        return $this;
    }

    // Methods for roles
    public function getRoles(): array
    {
        // Ensuring the user has at least a ROLE_USER, even if no role is assigned
        return array_unique(array_merge($this->roles, ['ROLE_USER']));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    // Password methods
    public function getPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function setPassword(string $password): self
    {
        $this->mot_de_passe = $password;
        return $this;
    }

    public function hashPassword(UserPasswordHasherInterface $passwordHasher): void
    {
        // Hash the password when it's set
        $this->mot_de_passe = $passwordHasher->hashPassword($this, $this->mot_de_passe);
    }

    // Other UserInterface methods
    public function getUsername(): string
    {
        return $this->email; // Assuming email is used as username
    }

    public function eraseCredentials(): void
    {
        // Nothing to clear since the password is hashed
    }

    // Getters and setters for relationships
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
            if ($admin->getId_user() === $this) {
                $admin->setId_user(null);
            }
        }

        return $this;
    }

    public function getConducteurs(): Collection
    {
        return $this->conducteurs;
    }

    public function addConducteur(Conducteur $conducteur): self
    {
        if (!$this->conducteurs->contains($conducteur)) {
            $this->conducteurs[] = $conducteur;
            $conducteur->setId_user($this);
        }

        return $this;
    }

    public function removeConducteur(Conducteur $conducteur): self
    {
        if ($this->conducteurs->removeElement($conducteur)) {
            if ($conducteur->getId_user() === $this) {
                $conducteur->setId_user(null);
            }
        }

        return $this;
    }

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
            if ($reservation->getUser_id() === $this) {
                $reservation->setUser_id(null);
            }
        }

        return $this;
    }

    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): self
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements[] = $paiement;
            $paiement->setUser_id($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): self
    {
        if ($this->paiements->removeElement($paiement)) {
            if ($paiement->getUser_id() === $this) {
                $paiement->setUser_id(null);
            }
        }

        return $this;
    }
    public function getUserIdentifier(): string
    {
        return $this->email; // Assuming email is the identifier
    }
    

}
