<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'evenement')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column (type: 'integer')]
    private ?int $id_evenement = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type d\'événement est obligatoire')]
    #[Assert\Choice(
        choices: ['Greve', 'Retard', 'Incident'],
        message: 'Le type d\'événement doit être Greve, Retard ou Incident'
    )]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire')]
    #[Assert\Choice(
        choices: ['En cours', 'Annulé', 'Résolu'],
        message: 'Le statut doit être En cours, Résolu ou Annulé'
    )]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'La description doit faire au moins {{ limit }} caractères',
        maxMessage: 'La description ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de début est obligatoire')]
    #[Assert\Type("\DateTimeInterface", message: 'La date de début doit être une date valide')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire')]
    #[Assert\Type("\DateTimeInterface", message: 'La date de fin doit être une date valide')]
    #[Assert\Expression(
        "this.getDateFin() > this.getDateDebut()",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private ?\DateTimeInterface $dateFin = null;
    
    #[ORM\ManyToOne(targetEntity: Ligne::class)]
    #[Assert\NotBlank(message: 'La ligne affectee est obligatoire!')]
    #[ORM\JoinColumn(name: 'ligne_affectee', referencedColumnName: 'id')]
    private Ligne $ligneAffectee;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'id_createur', referencedColumnName: 'id_user')]
    private User $id_createur;
    
    #[ORM\OneToMany(mappedBy: 'event', targetEntity: EventComment::class, cascade: ['remove'])]
    private Collection $comments;
    
    public function __construct()
    {
        $this->comments = new ArrayCollection(); // Initialize the comments collection
    }

    public function getId(): ?int
    {
        return $this->id_evenement;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getLigneAffectee(): ?Ligne
    {
        return $this->ligneAffectee;
    }

    public function setLigneAffectee(?Ligne $ligneAffectee): static
    {
        $this->ligneAffectee = $ligneAffectee;
        return $this;
    }

    public function getId_createur(): ?User{
        return $this->id_createur;
    }

    public function setId_createur(User $user):static{
        $this->id_createur = $user;
        return $this;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(EventComment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setEvent($this); // Assuming EventComment has a setEvent method
        }

        return $this;
    }

    public function removeComment(EventComment $comment): static
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // Set the owning side to null (unless already changed)
            if ($comment->getEvent() === $this) {
                $comment->setEvent(null); // Assuming EventComment has a setEvent method
            }
        }

        return $this;
    }
}