<?php
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
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id_post', type: 'integer')]
    private ?int $idPost = null;

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
    private int $nombre_de_places;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    private float $prix = 0.0;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "posts")]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user")]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: "post", targetEntity: Commentaire::class)]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getIdPost(): ?int
    {
        return $this->idPost;
    }

    public function setIdPost(int $idPost): self
    {
        $this->idPost = $idPost;
        return $this;
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

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getNombreDePlaces(): int
    {
        return $this->nombre_de_places;
    }

    public function setNombreDePlaces(int $nombre_de_places): self
    {
        $this->nombre_de_places = $nombre_de_places;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
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
            if ($commentaire->getPost() === $this) {
                $commentaire->setPost(null);
            }
        }
        return $this;
    }
}