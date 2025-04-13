<?php
namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Reponse;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]  // Ensures auto-increment behavior
    #[ORM\Column(type: "integer")]
    private int $id;


    #[ORM\OneToMany(mappedBy: "reclamation", targetEntity: Reponse::class, cascade: ["remove"])]
    private Collection $reponses;



    
    #[ORM\Column(type: "string", length: 250)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Veuillez entrer un email valide.")]
    private string $email;


    #[ORM\Column(type: "string", length: 250)]
    #[Assert\NotBlank(message: "Le sujet est obligatoire.")]
    #[Assert\Length(
    min: 3,
    max: 250,
    minMessage: "Le sujet doit contenir au moins {{ limit }} caractères.",
    maxMessage: "Le sujet ne doit pas dépasser {{ limit }} caractères."
    )]
    private string $sujet;


    #[ORM\Column(type: "string", length: 1000)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
    min: 10,
    minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    private string $description;


    #[ORM\Column(type: "string", length: 250)]
    #[Assert\NotBlank(message: "Le statut est requis.")]
    private string $statut;


    #[ORM\Column(type: "datetime")]
    #[Assert\NotNull(message: "La date de création est requise.")]
    #[Assert\LessThanOrEqual("today", message: "La date de création ne peut pas être dans le futur.")]
    private \DateTimeInterface $dateCreation;


    #[ORM\ManyToOne(inversedBy: 'reclamations')]
    #[Assert\NotNull(message: "Veuillez choisir une catégorie.")]
    private ?Categorie $Category_Id = null;


   
    // 🔹 GETTERS & SETTERS


    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }




    public function getId(): int
    {
        return $this->id;
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


    public function getSujet(): string
    {
        return $this->sujet;
    }


    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;
        return $this;
    }


    public function getDescription(): string
    {
        return $this->description;
    }


    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }


    public function getStatut(): string
    {
        return $this->statut;
    }


    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }


    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }


    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }


    public function getCategoryId(): ?Categorie
    {
        return $this->Category_Id;
    }


    public function setCategoryId(?Categorie $Category_Id): static
    {
        $this->Category_Id = $Category_Id;


        return $this;
    }


    public function getReponses(): Collection
{
    return $this->reponses;
}

public function addReponse(Reponse $reponse): self
{
    if (!$this->reponses->contains($reponse)) {
        $this->reponses[] = $reponse;
        $reponse->setReclamation($this);
    }
    return $this;
}

public function removeReponse(Reponse $reponse): self
{
    if ($this->reponses->removeElement($reponse)) {
        if ($reponse->getReclamation() === $this) {
            $reponse->setReclamation(null);
        }
    }
    return $this;
}




}
