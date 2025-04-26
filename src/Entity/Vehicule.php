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

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getImmatriculation(): string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $value): void
    {
        $this->immatriculation = $value;
    }

    public function getCapacite(): int
    {
        return $this->capacite;
    }

    public function setCapacite(int $value): void
    {
        $this->capacite = $value;
    }

    public function getEtat(): string
    {
        return $this->etat;
    }

    public function setEtat(string $value): void
    {
        $this->etat = $value;
    }

    public function getTypeVehicule(): string
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule(string $value): void
    {
        $this->typeVehicule = $value;
    }

    public function getId_conducteur(): int
    {
        return $this->id_conducteur;
    }

    public function setId_conducteur(int $value): void
    {
        $this->id_conducteur = $value;
    }

    public function getIdLigne(): int
    {
        return $this->idLigne;
    }

    public function setIdLigne(int $value): void
    {
        $this->idLigne = $value;
    }
    
}
