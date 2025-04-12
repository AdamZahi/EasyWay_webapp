<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

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
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $nb = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $user_id = null;

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

    public function getUser_Id(): ?int
    {
        return $this->user_id;
    }

    public function setUser_Id(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
    public function getUserId(): ?int
{
    return $this->user_id;
}

public function setUserId(int $user_id): static
{
    $this->user_id = $user_id;
    return $this;
}
}
