<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Station;

#[ORM\Entity]
class Ligne
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 150)]
    private string $depart;

    #[ORM\Column(type: "string", length: 150)]
    private string $arret;

    #[ORM\Column(type: "string", length: 150)]
    private string $type;

    #[ORM\Column(type: "integer")]
    private int $admin_id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
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

    public function getType()
    {
        return $this->type;
    }

    public function setType($value)
    {
        $this->type = $value;
    }

    public function getAdmin_id()
    {
        return $this->admin_id;
    }

    public function setAdmin_id($value)
    {
        $this->admin_id = $value;
    }

    #[ORM\OneToMany(mappedBy: "ligne_affectee", targetEntity: Evenement::class)]
    private Collection $evenements;

        public function getEvenements(): Collection
        {
            return $this->evenements;
        }
    
        public function addEvenement(Evenement $evenement): self
        {
            if (!$this->evenements->contains($evenement)) {
                $this->evenements[] = $evenement;
                $evenement->setLigne_affectee($this);
            }
    
            return $this;
        }
    
        public function removeEvenement(Evenement $evenement): self
        {
            if ($this->evenements->removeElement($evenement)) {
                // set the owning side to null (unless already changed)
                if ($evenement->getLigne_affectee() === $this) {
                    $evenement->setLigne_affectee(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id_ligne", targetEntity: Station::class)]
    private Collection $stations;

        public function getStations(): Collection
        {
            return $this->stations;
        }
    
        public function addStation(Station $station): self
        {
            if (!$this->stations->contains($station)) {
                $this->stations[] = $station;
                $station->setId_ligne($this);
            }
    
            return $this;
        }
    
        public function removeStation(Station $station): self
        {
            if ($this->stations->removeElement($station)) {
                // set the owning side to null (unless already changed)
                if ($station->getId_ligne() === $this) {
                    $station->setId_ligne(null);
                }
            }
    
            return $this;
        }
}
