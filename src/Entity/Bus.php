<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Vehicule;

#[ORM\Entity]
class Bus
{

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: "buss")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Vehicule $id;

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

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getNombrePortes()
    {
        return $this->nombrePortes;
    }

    public function setNombrePortes($value)
    {
        $this->nombrePortes = $value;
    }

    public function getTypeService()
    {
        return $this->typeService;
    }

    public function setTypeService($value)
    {
        $this->typeService = $value;
    }

    public function getNombreDePlaces()
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces($value)
    {
        $this->nombreDePlaces = $value;
    }

    public function getCompagnie()
    {
        return $this->compagnie;
    }

    public function setCompagnie($value)
    {
        $this->compagnie = $value;
    }

    public function getClimatisation()
    {
        return $this->climatisation;
    }

    public function setClimatisation($value)
    {
        $this->climatisation = $value;
    }
}
