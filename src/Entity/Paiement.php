<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(name: 'res_id', referencedColumnName: 'id', nullable: false)]
    private ?Reservation $res_id = null;

    #[ORM\ManyToOne(inversedBy: 'paiements')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id_user', nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $pay_id = null;

    #[ORM\Column]
    private ?float $montant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResId(): ?Reservation
    {
        return $this->res_id;
    }

    public function setResId(?Reservation $res_id): static
    {
        $this->res_id = $res_id;

        return $this;
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

    public function getPayId(): ?string
    {
        return $this->pay_id;
    }

    public function setPayId(string $pay_id): static
    {
        $this->pay_id = $pay_id;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): static
    {
        $this->montant = $montant;

        return $this;
    }
    

}
