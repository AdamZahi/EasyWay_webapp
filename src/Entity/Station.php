<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;


#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?string $localisation = null;

    #[ORM\ManyToOne(inversedBy: 'stations')]
    #[ORM\JoinColumn(name: "id_ligne", referencedColumnName: "id")]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?Ligne $ligne = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Ce champ est obligatoire")]
    private ?int $id_admin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;
        return $this;
    }

    public function getIdLigne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setIdLigne(?Ligne $ligne): static
    {
        $this->ligne = $ligne;
        return $this;
    }

    public function getIdAdmin(): ?int
    {
        return $this->id_admin;
    }

    public function setIdAdmin(int $id_admin): static
    {
        $this->id_admin = $id_admin;
        return $this;
    }
    public function getid_ligne(): ?Ligne
    {
        return $this->ligne;
    }

    public function setid_ligne(?Ligne $ligne): static
    {
        $this->ligne = $ligne;
        return $this;
    }

    public function getid_admin(): ?int
    {
        return $this->id_admin;
    }

    public function setid_admin(int $id_admin): static
    {
        $this->id_admin = $id_admin;
        return $this;
    }
}
