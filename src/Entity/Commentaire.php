<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\User;

#[ORM\Entity]
class Commentaire
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "id_post", referencedColumnName: "id_post")]
    private ?Posts $post = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id_user")]
    private ?User $user = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $contenu;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $date_creat;

    #[ORM\Column(type: "string", length: 255)]
    private string $nom;

    public function getId_com()
    {
        return $this->id_com;
    }

    public function setId_com($value)
    {
        $this->id_com = $value;
    }

    public function getId_post()
    {
        return $this->id_post;
    }

    public function setId_post($value)
    {
        $this->id_post = $value;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($value)
    {
        $this->user_id = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getDate_creat()
    {
        return $this->date_creat;
    }

    public function setDate_creat($value)
    {
        $this->date_creat = $value;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($value)
    {
        $this->nom = $value;
    }
}
