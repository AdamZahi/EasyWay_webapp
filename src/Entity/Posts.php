<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Posts
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_post;

    #[ORM\Column(type: "integer")]
    private int $id_user;

    #[ORM\Column(type: "string", length: 255)]
    private string $ville_depart;

    #[ORM\Column(type: "string", length: 255)]
    private string $ville_arrivee;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 255)]
    private string $message;

    #[ORM\Column(type: "integer")]
    private int $nombreDePlaces;

    #[ORM\Column(type: "float")]
    private float $prix;

    public function getId_post()
    {
        return $this->id_post;
    }

    public function setId_post($value)
    {
        $this->id_post = $value;
    }

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getVille_depart()
    {
        return $this->ville_depart;
    }

    public function setVille_depart($value)
    {
        $this->ville_depart = $value;
    }

    public function getVille_arrivee()
    {
        return $this->ville_arrivee;
    }

    public function setVille_arrivee($value)
    {
        $this->ville_arrivee = $value;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($value)
    {
        $this->date = $value;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($value)
    {
        $this->message = $value;
    }

    public function getNombreDePlaces()
    {
        return $this->nombreDePlaces;
    }

    public function setNombreDePlaces($value)
    {
        $this->nombreDePlaces = $value;
    }

    public function getPrix()
    {
        return $this->prix;
    }

    public function setPrix($value)
    {
        $this->prix = $value;
    }
}
