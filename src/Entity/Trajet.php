<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Trajet
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string")]
    private string $duree;

    #[ORM\Column(type: "integer")]
    private int $distance;

    #[ORM\Column(type: "string")]
    private string $heure_depart;

    #[ORM\Column(type: "string")]
    private string $heure_arrive;

    #[ORM\Column(type: "string", length: 150)]
    private string $depart;

    #[ORM\Column(type: "string", length: 150)]
    private string $arret;

    #[ORM\Column(type: "string", length: 150)]
    private string $etat;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDuree()
    {
        return $this->duree;
    }

    public function setDuree($value)
    {
        $this->duree = $value;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    public function setDistance($value)
    {
        $this->distance = $value;
    }

    public function getHeure_depart()
    {
        return $this->heure_depart;
    }

    public function setHeure_depart($value)
    {
        $this->heure_depart = $value;
    }

    public function getHeure_arrive()
    {
        return $this->heure_arrive;
    }

    public function setHeure_arrive($value)
    {
        $this->heure_arrive = $value;
    }

    public function getDepart()
    {
        return $this->depart;
    }

    public function setDepart($value)
    {
        $this->depart = $value;
    }

    public function getArret()
    {
        return $this->arret;
    }

    public function setArret($value)
    {
        $this->arret = $value;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function setEtat($value)
    {
        $this->etat = $value;
    }
}
