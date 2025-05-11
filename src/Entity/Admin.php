<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_admin = null;

    #[ORM\OneToOne(inversedBy: 'admin', cascade: ['persist', 'remove'])]
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

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: "1024k",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, GIF)"
    )]
    private ?string $photo_profil = null;

    /**
     * @var Collection<int, Ligne>
     */
    #[ORM\OneToMany(targetEntity: Ligne::class, mappedBy: 'admin')]
    private Collection $lignes;

    
    /**
     * @var Collection<int, Station>
     */
    #[ORM\OneToMany(targetEntity: Station::class, mappedBy: 'admin')]
    private Collection $stations;

    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->stations = new ArrayCollection();
    }



    public function getIdAdmin(): ?int
    {
        return $this->id_admin;
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
        $this->photo_profil = $photo_profil;
        return $this;
    }
    /**
     * @return Collection<int, Ligne>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    public function addLigne(Ligne $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setAdmin($this);
        }

        return $this;
    }

    public function removeLigne(Ligne $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            // set the owning side to null (unless already changed)
            if ($ligne->getAdmin() === $this) {
                $ligne->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): static
    {
        if (!$this->stations->contains($station)) {
            $this->stations->add($station);
            $station->setAdmin($this);
        }

        return $this;
    }

    public function removeStation(Station $station): static
    {
        if ($this->stations->removeElement($station)) {
            if ($station->getAdmin() === $this) {
                $station->setAdmin(null);
            }
        }

        return $this;
    }

}
