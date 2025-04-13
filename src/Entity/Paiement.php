<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?string $pay_id = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?float $montant = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $res_id = null;

    #[ORM\Column]
    #[Assert\NotBlank (message: "Ce champ est obligatoire ")]
    private ?int $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getPay_Id(): ?string
    {
        return $this->pay_id;
    }

    public function setPay_Id(string $pay_id): static
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

    public function getResId(): ?int
    {
        return $this->res_id;
    }

    public function setResId(int $res_id): static
    {
        $this->res_id = $res_id;

        return $this;
    }

    public function getRes_Id(): ?int
    {
        return $this->res_id;
    }

    public function setRes_Id(int $res_id): static
    {
        $this->res_id = $res_id;

        return $this;
    }
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
    public function getUser_Id(): ?int
    {
        return $this->user_id;
    }

    public function setUser_Id(int $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
}
