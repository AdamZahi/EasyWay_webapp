<?php

namespace App\Entity;

use App\Enum\EventStatus;
use App\Enum\TypeEvent;
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
    #[ORM\Column(name: 'id_evenement')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le type d\'événement est obligatoire')]
    #[Assert\Choice(
        choices: ['ACCIDENT', 'PANNES', 'INCIDENT', 'TRAVAUX'],
        message: 'Le type d\'événement doit être ACCIDENT, PANNES, INCIDENT ou TRAVAUX'
    )]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The status is required')]
    #[Assert\Choice(
        choices: ['EN_COURS', 'ANNULE', 'RESOLU'],
        message: 'The statut should be EN_COURS, ANNULE ou RESOLU'
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
    #[Assert\Type("\DateTimeInterface")]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date de fin est obligatoire')]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\Expression(
        "this.getDateFin() > this.getDateDebut()",
        message: "La date de fin doit être postérieure à la date de début"
    )]
    private ?\DateTimeInterface $dateFin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?TypeEvent
    {
        return $this->type ? TypeEvent::from($this->type) : null;
    }

    public function setType(TypeEvent|string $type): static
    {
        $this->type = $type instanceof TypeEvent ? $type->value : $type;
        return $this;
    }

    public function getStatus(): ?EventStatus
    {
        return $this->status ? EventStatus::from($this->status) : null;
    }

    public function setStatus(EventStatus|string $status): static
    {
        $this->status = $status instanceof EventStatus ? $status->value : $status;
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

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }
} 