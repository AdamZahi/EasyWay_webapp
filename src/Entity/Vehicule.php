<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Train;

#[ORM\Entity]
class Vehicule
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $immatriculation;

    #[ORM\Column(type: "integer")]
    private int $capacite;

    #[ORM\Column(type: "string", length: 255)]
    private string $etat;

    #[ORM\Column(type: "string", length: 255)]
    private string $typeVehicule;

    #[ORM\Column(type: "integer")]
    private int $id_conducteur;

    #[ORM\Column(type: "integer")]
    private int $idTrajet;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getImmatriculation()
    {
        return $this->immatriculation;
    }

    public function setImmatriculation($value)
    {
        $this->immatriculation = $value;
    }

    public function getCapacite()
    {
        return $this->capacite;
    }

    public function setCapacite($value)
    {
        $this->capacite = $value;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function setEtat($value)
    {
        $this->etat = $value;
    }

    public function getTypeVehicule()
    {
        return $this->typeVehicule;
    }

    public function setTypeVehicule($value)
    {
        $this->typeVehicule = $value;
    }

    public function getId_conducteur()
    {
        return $this->id_conducteur;
    }

    public function setId_conducteur($value)
    {
        $this->id_conducteur = $value;
    }

    public function getIdTrajet()
    {
        return $this->idTrajet;
    }

    public function setIdTrajet($value)
    {
        $this->idTrajet = $value;
    }

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Bus::class)]
    private Collection $buss;

        public function getBuss(): Collection
        {
            return $this->buss;
        }
    
        public function addBus(Bus $bus): self
        {
            if (!$this->buss->contains($bus)) {
                $this->buss[] = $bus;
                $bus->setId($this);
            }
    
            return $this;
        }
    
        public function removeBus(Bus $bus): self
        {
            if ($this->buss->removeElement($bus)) {
                // set the owning side to null (unless already changed)
                if ($bus->getId() === $this) {
                    $bus->setId(null);
                }
            }
    
            return $this;
        }

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Metro::class)]
    private Collection $metros;

    #[ORM\OneToMany(mappedBy: "id", targetEntity: Train::class)]
    private Collection $trains;
}
