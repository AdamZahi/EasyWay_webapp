<?php
// src/Entity/Posts.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Commentaire;

#[ORM\Entity(repositoryClass: "App\Repository\PostsRepository")]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id_post = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "postss")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private ?User $id_user = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La ville de départ est obligatoire")]
    private string $ville_depart = '';

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "La ville d'arrivée est obligatoire")]
    private string $ville_arrivee = '';

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou dans le futur")]
    private ?\DateTimeInterface $date = null;

#[ORM\Column(type: "string", length: 255)]
#[Assert\NotBlank(message: "Veuillez fournir des détails sur le trajet")]
#[Assert\Length(min: 10, minMessage: "Les détails doivent contenir au moins {{ limit }} caractères")]
private string $message = ''; // Initialize with empty string

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\Positive(message: "Le nombre de places doit être positif")]
    private int $nombreDePlaces = 1;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private float $prix = 0.0;

    #[ORM\OneToMany(mappedBy: "id_post", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->date = new \DateTime();
        $this->nombreDePlaces = 1;
    }

    public function getId_post()
    {
        return $this->id_post;
    }

    public function setId_post($value)
    {
        $this->id_post = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getVilleDepart(): string 
    {
        return $this->ville_depart;
    }
    
    public function setVilleDepart(string $ville_depart): self
    {
        $this->ville_depart = $ville_depart;
        return $this;
    }
    public function getVilleArrivee(): string
    {
        return $this->ville_arrivee;
    }
    
    public function setVilleArrivee(string $ville_arrivee): self
    {
        $this->ville_arrivee = $ville_arrivee;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;
    }

    public function getNombreDePlaces()
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces($value)
    {
        $this->nombreDePlaces = $value;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($value)
    {
        $this->prix = $value;
    }

    // #[ORM\OneToMany(mappedBy: "id_post", targetEntity: Commentaire::class)]
    // private Collection $commentaires;

    //     public function getCommentaires(): Collection
    //     {
    //         return $this->commentaires;
    //     }
    
    //     public function addCommentaire(Commentaire $commentaire): self
    //     {
    //         if (!$this->commentaires->contains($commentaire)) {
    //             $this->commentaires[] = $commentaire;
    //             $commentaire->setId_post($this);
    //         }
    
    //         return $this;
    //     }
    
    //     public function removeCommentaire(Commentaire $commentaire): self
    //     {
    //         if ($this->commentaires->removeElement($commentaire)) {
    //             // set the owning side to null (unless already changed)
    //             if ($commentaire->getId_post() === $this) {
    //                 $commentaire->setId_post(null);
    //             }
    //         }
    
    //         return $this;
    //     }
        public function getIdUser(): ?User
{
    return $this->id_user;
}

public function setIdUser(?User $user): self
{
    $this->id_user = $user;
    return $this;
}
}
