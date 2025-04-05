<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Posts;

#[ORM\Entity]
class Commentaire
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id_com;

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: 'id_user', referencedColumnName: 'id_user', onDelete: 'CASCADE')]
    private User $id_user;

        #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: 'id_post', referencedColumnName: 'id_post', onDelete: 'CASCADE')]
    private Posts $id_post;

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

    public function getId_user()
    {
        return $this->id_user;
    }

    public function setId_user($value)
    {
        $this->id_user = $value;
    }

    public function getId_post()
    {
        return $this->id_post;
    }

    public function setId_post($value)
    {
        $this->id_post = $value;
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
