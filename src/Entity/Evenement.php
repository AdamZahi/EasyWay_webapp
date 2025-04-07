<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Admin;

#[ORM\Entity]
class Evenement
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_evenement;

        #[ORM\ManyToOne(targetEntity: Ligne::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'ligne_affectee', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Ligne $ligne_affectee;

        #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: "evenements")]
    #[ORM\JoinColumn(name: 'id_createur', referencedColumnName: 'id_admin', onDelete: 'CASCADE')]
    private Admin $id_createur;

    #[ORM\Column(type: "string")]
    private string $type;

    #[ORM\Column(type: "text")]
    private string $description;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_creation;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_debut;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $date_fin;

    #[ORM\Column(type: "string")]
    private string $statut;

    public function getId_evenement()
    {
        return $this->id_evenement;
    }

    public function setId_evenement($value)
    {
        $this->id_evenement = $value;
    }

    public function getLigne_affectee()
    {
        return $this->ligne_affectee;
    }

    public function setLigne_affectee($value)
    {
        $this->ligne_affectee = $value;
    }

    public function getId_createur()
    {
        return $this->id_createur;
    }

    public function setId_createur($value)
    {
        $this->id_createur = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getDate_creation()
    {
        return $this->date_creation;
    }

    public function setDate_creation($value)
    {
        $this->date_creation = $value;
    }

    public function getDate_debut()
    {
        return $this->date_debut;
    }

    public function setDate_debut($value)
    {
        $this->date_debut = $value;
    }

    public function getDate_fin()
    {
        return $this->date_fin;
    }

    public function setDate_fin($value)
    {
        $this->date_fin = $value;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($value)
    {
        $this->statut = $value;
    }
}
