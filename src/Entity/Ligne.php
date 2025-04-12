<?php

namespace App\Entity;

use App\Repository\LigneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: LigneRepository::class)]
class Ligne
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire ")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire ")]
    private ?string $depart = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire ")]
    private ?string $arret = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire ")]
    private ?string $type = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire ")]
    private ?int $admin_id = null;

    /**
     * @var Collection<int, Station>
     */
    #[ORM\OneToMany(targetEntity: Station::class, mappedBy: 'id_ligne')]
    private Collection $stations;

    public function __construct()
    {
        $this->stations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDepart(): ?string
    {
        return $this->depart;
    }

    public function setDepart(string $depart): static
    {
        $this->depart = $depart;

        return $this;
    }

    public function getArret(): ?string
    {
        return $this->arret;
    }

    public function setArret(string $arret): static
    {
        $this->arret = $arret;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->admin_id;
    }

    public function setAdminId(int $admin_id): static
    {
        $this->admin_id = $admin_id;

        return $this;
    }
    public function getAdmin_Id(): ?int
    {
        return $this->admin_id;
    }

    public function setAdmin_Id(int $admin_id): static
    {
        $this->admin_id = $admin_id;

        return $this;
    }

    /**
     * @return Collection<int, Station>
     */
    public function getStations(): Collection
    {
        return $this->stations;
    }

    public function addStation(Station $station): static
    {
        if (!$this->stations->contains($station)) {
            $this->stations->add($station);
            $station->setIdLigne($this);
        }

        return $this;
    }

    public function removeStation(Station $station): static
    {
        if ($this->stations->removeElement($station)) {
            // set the owning side to null (unless already changed)
            if ($station->getIdLigne() === $this) {
                $station->setIdLigne(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        // Return a string representation of the Ligne
        // Example: Use the name if it exists, otherwise fall back to ID
        return $this->nom ?? 'Ligne #'.$this->id;
    }
}
