<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: App\Repository\BusRepository::class)]


class Bus 

{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: "bus")]
    #[ORM\JoinColumn(name: "id", referencedColumnName: "id", onDelete: "CASCADE")]
    private Vehicule $vehicule; // Liens avec l'entité Vehicule via id

    #[ORM\Column(type: "integer")]
    private int $nombrePortes;

    #[ORM\Column(type: "string")]
    private string $typeService;

    #[ORM\Column(type: "integer")]
    private int $nombreDePlaces;

    #[ORM\Column(type: "string", length: 100)]
    private string $compagnie;

    #[ORM\Column(type: "boolean")]
    private bool $climatisation;

    public function getNombrePortes(): int
    {
        return $this->nombrePortes;
    }

    public function setNombrePortes(int $value): void
    {
        $this->nombrePortes = $value;
    }

    public function getTypeService(): string
    {
        return $this->typeService;
    }

    public function setTypeService(string $value): void
    {
        $this->typeService = $value;
    }

    public function getNombreDePlaces(): int
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces(int $value): void
    {
        $this->nombreDePlaces = $value;
    }

    public function getCompagnie(): string
    {
        return $this->compagnie;
    }

    public function setCompagnie(string $value): void
    {
        $this->compagnie = $value;
    }

    public function getClimatisation(): bool
    {
        return $this->climatisation;
    }

    public function setClimatisation(bool $value): void
    {
        $this->climatisation = $value;
    }
    public function getVehicule(): Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(Vehicule $vehicule): void
    {
        $this->vehicule = $vehicule;
    }
}
