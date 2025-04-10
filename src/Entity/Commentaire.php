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
    #[ORM\Column(name: "id_com", type: "integer")]
    private ?int $id_com = null;

    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_creat = null;
     // 🔗 Relation ManyToOne vers User
     #[ORM\ManyToOne(targetEntity: User::class)]
     #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id_user", nullable: false)]
     private ?User $user = null;
 
     // 🔗 Relation ManyToOne vers Posts
     #[ORM\ManyToOne(targetEntity: Posts::class)]
     #[ORM\JoinColumn(name: "id_post", referencedColumnName: "id_post", nullable: false)]
     private ?Posts $post = null;



     public function getIdCom(): ?int
    {
        return $this->id_com;
    }

    public function setIdCom(int $id_com): static
    {
        $this->id_com = $id_com;

        return $this;
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
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): static
    {
        $this->post = $post;
        return $this;
    }

 
}
