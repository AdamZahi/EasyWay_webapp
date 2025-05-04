<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le champ départ ne peut pas être vide.')]
    #[Assert\Length(
        min: 5,
        minMessage: 'Le départ doit contenir au moins {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^(?!unnamed road, ).*/i',
        message: 'Le départ ne peut pas commencer par "Unnamed Road, ".'
    )]
    private ?string $depart = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le champ arrêt ne peut pas être vide.')]
    #[Assert\Length(
        min: 5,
        minMessage: 'L\'arrêt doit contenir au moins {{ limit }} caractères.'
    )]
    #[Assert\Regex(
        pattern: '/^(?!unnamed road, ).*/i',
        message: 'L\'arrêt ne peut pas commencer par "Unnamed Road, ".'
    )]
    private ?string $arret = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Veuillez choisir un type de véhicule.')]
    private ?string $vehicule = null;
    
    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 3,
        notInRangeMessage: 'Le nombre de places doit être entre {{ min }} et {{ max }}.'  // Using notInRangeMessage
    )]
    private ?int $nb = null;

    /**
     * @var Collection<int, Paiement>
     */
    #[ORM\OneToMany(targetEntity: Paiement::class, mappedBy: 'res_id')]
    private Collection $paiements;

    public function __construct()
    {
        $this->paiements = new ArrayCollection();
    }

    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user;
    }

    public function setUserId(?User $user): static
    {
        $this->user = $user;

        return $this;
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

    public function getVehicule(): ?string
    {
        return $this->vehicule;
    }

    public function setVehicule(string $vehicule): static
    {
        $this->vehicule = $vehicule;

        return $this;
    }

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(int $nb): static
    {
        $this->nb = $nb;

        return $this;
    }

    /**
     * @return Collection<int, Paiement>
     */
    public function getPaiements(): Collection
    {
        return $this->paiements;
    }

    public function addPaiement(Paiement $paiement): static
    {
        if (!$this->paiements->contains($paiement)) {
            $this->paiements->add($paiement);
            $paiement->setResId($this);
        }

        return $this;
    }

    public function removePaiement(Paiement $paiement): static
    {
        if ($this->paiements->removeElement($paiement)) {
            // set the owning side to null (unless already changed)
            if ($paiement->getResId() === $this) {
                $paiement->setResId(null);
            }
        }

        return $this;
    }
    
}
