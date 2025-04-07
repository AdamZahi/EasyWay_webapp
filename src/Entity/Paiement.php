<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

#[ORM\Entity]
class Paiement
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

        #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: "paiements")]
    #[ORM\JoinColumn(name: 'res_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Reservation $res_id;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "paiements")]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $user_id;

    #[ORM\Column(type: "string", length: 50)]
    private string $pay_id;

    #[ORM\Column(type: "float")]
    private float $montant;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getRes_id()
    {
        return $this->res_id;
    }

    public function setRes_id($value)
    {
        $this->res_id = $value;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($value)
    {
        $this->user_id = $value;
    }

    public function getPay_id()
    {
        return $this->pay_id;
    }

    public function setPay_id($value)
    {
        $this->pay_id = $value;
    }

    public function getMontant()
    {
        return $this->montant;
    }

    public function setMontant($value)
    {
        $this->montant = $value;
    }
}
