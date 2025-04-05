<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use App\Entity\Paiement;

#[ORM\Entity]
class Reservation
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $user_id;

    #[ORM\Column(type: "string", length: 200)]
    private string $depart;

    #[ORM\Column(type: "string", length: 200)]
    private string $arret;

    #[ORM\Column(type: "string", length: 20)]
    private string $vehicule;

    #[ORM\Column(type: "integer")]
    private int $nb;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($value)
    {
        $this->user_id = $value;
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

    public function getVehicule()
    {
        return $this->vehicule;
    }

    public function setVehicule($value)
    {
        $this->vehicule = $value;
    }

    public function getNb()
    {
        return $this->nb;
    }

    public function setNb($value)
    {
        $this->nb = $value;
    }

    #[ORM\OneToMany(mappedBy: "res_id", targetEntity: Paiement::class)]
    private Collection $paiements;

        public function getPaiements(): Collection
        {
            return $this->paiements;
        }
    
        public function addPaiement(Paiement $paiement): self
        {
            if (!$this->paiements->contains($paiement)) {
                $this->paiements[] = $paiement;
                $paiement->setRes_id($this);
            }
    
            return $this;
        }
    
        public function removePaiement(Paiement $paiement): self
        {
            if ($this->paiements->removeElement($paiement)) {
                // set the owning side to null (unless already changed)
                if ($paiement->getRes_id() === $this) {
                    $paiement->setRes_id(null);
                }
            }
    
            return $this;
        }
}
