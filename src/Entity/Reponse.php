<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Doctrine\Common\Collections\Collection;
use App\Entity\Reclamation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use DateTimeImmutable;


#[ORM\Entity]
class Reponse
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    #[ORM\GeneratedValue(strategy: "AUTO")] 
    private int $id;

    #[ORM\Column(type: "text")]
    private string $contenu;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTimeInterface $createdAt = null;

   
     
    #[ORM\ManyToOne(targetEntity: Reclamation::class, inversedBy: "reponses")]
    #[ORM\JoinColumn(name: "reclamation_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Reclamation $reclamation = null;
    
    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($value)
    {
        $this->contenu = $value;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getReclamation(): ?Reclamation
{
    return $this->reclamation;
}

public function setReclamation(?Reclamation $reclamation): self
{
    $this->reclamation = $reclamation;
    return $this;
}

    
        
}
