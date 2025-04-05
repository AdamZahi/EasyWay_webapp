<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Admin;

#[ORM\Entity]
class Station
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Ligne::class, inversedBy: "stations")]
    #[ORM\JoinColumn(name: 'id_ligne', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Ligne $id_ligne;

        #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: "stations")]
    #[ORM\JoinColumn(name: 'id_admin', referencedColumnName: 'id_admin', onDelete: 'CASCADE')]
    private Admin $id_admin;

    #[ORM\Column(type: "string", length: 100)]
    private string $nom;

    #[ORM\Column(type: "string", length: 200)]
    private string $localisation;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId_ligne()
    {
        return $this->id_ligne;
    }

    public function setId_ligne($value)
    {
        $this->id_ligne = $value;
    }

    public function getId_admin()
    {
        return $this->id_admin;
    }

    public function setId_admin($value)
    {
        $this->id_admin = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }

    public function getLocalisation()
    {
        return $this->localisation;
    }

    public function setLocalisation($value)
    {
        $this->localisation = $value;
    }
}
