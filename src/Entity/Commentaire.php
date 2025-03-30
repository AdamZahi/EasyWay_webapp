<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creat = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?posts $id_post = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $nom = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $id_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getDateCreat(): ?\DateTimeInterface
    {
        return $this->date_creat;
    }

    public function setDateCreat(\DateTimeInterface $date_creat): static
    {
        $this->date_creat = $date_creat;

        return $this;
    }

    public function getIdPost(): ?posts
    {
        return $this->id_post;
    }

    public function setIdPost(?posts $id_post): static
    {
        $this->id_post = $id_post;

        return $this;
    }

    public function getNom(): ?user
    {
        return $this->nom;
    }

    public function setNom(?user $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIdUser(): ?user
    {
        return $this->id_user;
    }

    public function setIdUser(?user $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }
}
