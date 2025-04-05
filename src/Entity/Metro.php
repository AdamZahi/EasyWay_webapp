<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Vehicule;

#[ORM\Entity]
class Metro
{

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: "metros")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Vehicule $id;

    #[ORM\Column(type: "float")]
    private float $longueurReseau;

    #[ORM\Column(type: "integer")]
    private int $nombreLignes;

    #[ORM\Column(type: "integer")]
    private int $nombreRames;

    #[ORM\Column(type: "string", length: 255)]
    private string $proprietaire;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getLongueurReseau()
    {
        return $this->longueurReseau;
    }

    public function setLongueurReseau($value)
    {
        $this->longueurReseau = $value;
    }

    public function getNombreLignes()
    {
        return $this->nombreLignes;
    }

    public function setNombreLignes($value)
    {
        $this->nombreLignes = $value;
    }

    public function getNombreRames()
    {
        return $this->nombreRames;
    }

    public function setNombreRames($value)
    {
        $this->nombreRames = $value;
    }

    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    public function setProprietaire($value)
    {
        $this->proprietaire = $value;
    }
}
