<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Payment
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $paymentId;

    #[ORM\Column(type: "string", length: 255)]
    private string $transactionId;

    #[ORM\Column(type: "float")]
    private float $amount;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    public function getpaymentId()
    {
        return $this-> paymentId;
    }

    public function setpaymentId($value)
    {
        $this-> paymentId = $value;
    }

    public function getTransactionId()
    {
        return $this->transactionId;
    }

    public function setTransactionId($value)
    {
        $this->transactionId = $value;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($value)
    {
        $this->amount = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }
}
