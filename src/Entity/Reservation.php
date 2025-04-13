<?php

namespace App\Entity;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?string $depart = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?string $arret = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?string $vehicule = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]#[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "Le nombre de places doit être entre {{ min }} et {{ max }}."
    )]
    private ?int $nb = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id_user", nullable: false)]
     private ?User $user = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;

        return $this;
    }

    public function getArret(): ?string
    {
        return $this->arret;
    }

    public function setArret(string $arret): static
    {
        $this->arret = $arret;

        return $this;
    }

    public function getVehicule(): ?string
    {
        return $this->vehicule;
    }

    public function setVehicule(string $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(int $nb): static
    {
        $this->nb = $nb;

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

    
}
