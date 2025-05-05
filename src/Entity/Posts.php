<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_post", type: "integer")]
    private ?int $id_post = null;


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
    private string $message = '';

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $nombreDePlaces;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Type(type: "float", message: "Le prix doit être un nombre")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private ?float $prix = null;  // Changed from float to ?float to allow null

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;
    
    public function getIdPost(): ?int
    {
        return $this->id_post;
    }

    public function setIdPost(int $id_post): static
    {
        $this->id_post = $id_post;

        return $this;
    }
    public function getVilleDepart(): ?string
    {
        return $this->ville_depart;
    }

    public function setVilleDepart(string $ville_depart): static
    {
        $this->ville_depart = $ville_depart;

        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->ville_arrivee;
    }

    public function setVilleArrivee(string $ville_arrivee): static
    {
        $this->ville_arrivee = $ville_arrivee;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getNombreDePlaces(): ?int
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces(int $nombreDePlaces): static
    {
        $this->nombreDePlaces = $nombreDePlaces;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }
    
    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'post')]
    private Collection $commentaires;
    
    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }
    
    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }
    
    public function addCommentaire(Commentaire $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires[] = $commentaire;
            $commentaire->setPost($this);
        }
    
        return $this;
    }
    
    public function removeCommentaire(Commentaire $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPost() === $this) {
                $commentaire->setPost(null);
            }
        }
    
        return $this;
    }

}
