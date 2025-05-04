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
    private ?Ligne $id_ligne = null;

    #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(name: 'id_admin', referencedColumnName: 'id_admin', nullable: false)]
    private ?Admin $admin = null;

  

    

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
        return $this->id_ligne;
    }

    public function setIdLigne(?Ligne $id_ligne): static
    {
        $this->id_ligne = $id_ligne;

        return $this;
    }

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

   
}
