<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $immatriculation;

    #[ORM\Column(type: "integer")]
    private int $capacite;

    #[ORM\Column(type: "string", length: 255)]
    private string $etat;

    #[ORM\Column(name: "type_vehicule", type: "string", length: 255)]
    private string $typeVehicule;

    #[ORM\Column(type: "integer")]
    private int $id_conducteur;

    #[ORM\Column(type: "integer")]
    private int $idLigne;

    // Getters & Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getImmatriculation(): string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): void
    {
        $this->immatriculation = $immatriculation;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): void
    {
        $this->capacite = $capacite;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): void
    {
        $this->etat = $etat;
    }

    public function getTypeVehicule(): string
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(string $typeVehicule): void
    {
        $this->typeVehicule = $typeVehicule;
    }

    public function getIdConducteur(): int
    {
        return $this->id_conducteur;
    }

    public function setIdConducteur(int $id_conducteur): void
    {
        $this->id_conducteur = $id_conducteur;
    }

    public function getIdLigne(): int
    {
        return $this->idLigne;
    }

    public function setIdLigne(int $idLigne): void
    {
        $this->idLigne = $idLigne;
    }
}
