<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Commentaire;

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

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Posts::class)]
    private Collection $posts;

    #[ORM\OneToMany(mappedBy: "user", targetEntity: Commentaire::class)]
    private Collection $commentaires;


    public function getPosts(): Collection
    {
        return $this->posts;
    }
    
    public function addPost(Posts $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setId_user($this);
        }
    
        return $this;
    }
    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
    }
    
    public function removePost(Posts $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getId_user() === $this) {
                $post->setId_user(null);
            }
        }
    
        return $this;
    }
  

}
