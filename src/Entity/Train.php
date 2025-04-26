<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Vehicule;

#[ORM\Entity]
class Train
{

    #[ORM\Id]
        #[ORM\ManyToOne(targetEntity: Vehicule::class, inversedBy: "trains")]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Vehicule $vehicule;

    #[ORM\Column(type: "float")]
    private float $longueurReseau;

    #[ORM\Column(type: "integer")]
    private int $nombreLignes;

    #[ORM\Column(type: "integer")]
    private int $nombreWagons;

    #[ORM\Column(type: "float")]
    private float $vitesseMaximale;

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

    public function getNombreWagons()
    {
        return $this->nombreWagons;
    }

    public function setNombreWagons($value)
    {
        $this->nombreWagons = $value;
    }

    public function getVitesseMaximale()
    {
        return $this->vitesseMaximale;
    }

    public function setVitesseMaximale($value)
    {
        $this->vitesseMaximale = $value;
    }

    public function getProprietaire()
    {
        return $this->proprietaire;
    }

    public function setProprietaire($value)
    {
        $this->proprietaire = $value;
    }
    public function getVehicule(): Vehicule
    {
        return $this->vehicule;
    }

    public function setVehicule(Vehicule $vehicule): self
    {
        $this->vehicule = $vehicule;
        return $this;
    }

}
