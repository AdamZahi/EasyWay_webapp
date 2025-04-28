<?php

namespace App\Entity;

use App\Repository\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "payment_id", type: 'integer')] // Spécifiez le nom de colonne
    private ?int $paymentId = null;

    #[ORM\Column(length: 255)]
    private ?string $transactionId = null;

    #[ORM\Column(type: 'float')]
    private ?float $amount = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    public function getPaymentId(): ?int
    {
        return $this->paymentId;
    }

    // Gardez setPaymentId() si nécessaire pour votre logique métier
    public function setPaymentId(int $paymentId): self
    {
        $this->paymentId = $paymentId;
        return $this;
    }


    // Removed setPaymentId() as ID should be auto-generated

    // Changed return type and parameter type to string
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }

    // Changed parameter type to string
    public function setTransactionId(string $transactionId): static
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
}