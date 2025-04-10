<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_user", type: "integer")]
    private ?int $id_user = null;
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $mot_de_passe = null;

    #[ORM\Column(nullable: true)]
    private ?int $telephonne = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation_compte = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo_profil = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;
  

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): static
    {
        $this->id_user = $id_user;

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

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
#[ORM\OneToMany(targetEntity: Posts::class, mappedBy: 'user')]
private Collection $posts;

public function __construct()
{
    $this->posts = new ArrayCollection();
}

// Add getPosts() and addPost()/removePost() methods

public function getPosts(): Collection
{
    return $this->posts;
}

public function addPost(Posts $post): static
{
    if (!$this->posts->contains($post)) {
        $this->posts[] = $post;
        $post->setUser($this);
    }

    return $this;
}

public function removePost(Posts $post): static
{
    if ($this->posts->removeElement($post)) {
        if ($post->getUser() === $this) {
            $post->setUser(null);
        }
    }

    return $this;
}
}
