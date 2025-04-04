<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
#[ORM\Table(name: 'posts')]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_post', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'ville_depart', length: 255)]
    #[Assert\NotBlank(message: "La ville de départ est obligatoire")]
    private ?string $villeDepart = null;

    #[ORM\Column(name: 'ville_arrivee', length: 255)]
    #[Assert\NotBlank(message: "La ville d'arrivée est obligatoire")]
    #[Assert\NotEqualTo(
        propertyPath: "villeDepart",
        message: "La ville d'arrivée doit être différente de la ville de départ"
    )]
    private ?string $villeArrivee = null;

    #[ORM\Column(name: 'date', type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        "today",
        message: "La date doit être aujourd'hui ou dans le futur"
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(name: 'message', type: 'text')]
    #[Assert\NotBlank(message: "Veuillez ajouter des détails sur votre trajet")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit faire au moins {{ limit }} caractères"
    )]
    private ?string $message = null;

    #[ORM\Column(name: 'nombreDePlaces', type: 'integer')]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\Positive(message: "Le nombre de places doit être supérieur à zéro")]
    private ?int $nombreDePlaces = null;

    #[ORM\Column(name: 'prix', type: 'float')]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\PositiveOrZero(message: "Le prix ne peut pas être négatif")]
    private ?float $prix = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id')]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'post')]
    private Collection $commentaires;

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVilleDepart(): ?string
    {
        return $this->villeDepart;
    }

    public function setVilleDepart(string $villeDepart): static
    {
        $this->villeDepart = $villeDepart;
        return $this;
    }

    public function getVilleArrivee(): ?string
    {
        return $this->villeArrivee;
    }

    public function setVilleArrivee(string $villeArrivee): static
    {
        $this->villeArrivee = $villeArrivee;
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

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    // public function addCommentaire(Commentaire $commentaire): static
    // {
    //     if (!$this->commentaires->contains($commentaire)) {
    //         $this->commentaires->add($commentaire);
    //         $commentaire->setPost($this);
    //     }
    //     return $this;
    // }

    // public function removeCommentaire(Commentaire $commentaire): static
    // {
    //     if ($this->commentaires->removeElement($commentaire)) {
    //         if ($commentaire->getPost() === $this) {
    //             $commentaire->setPost(null);
    //         }
    //     }
    //     return $this;
    // }
}