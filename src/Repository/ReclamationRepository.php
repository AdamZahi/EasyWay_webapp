<?php

namespace App\Repository;

use App\Entity\Reclamation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReclamationRepository extends ServiceEntityRepository
{
    // Le constructeur qui permet d'utiliser le service ManagerRegistry de Doctrine
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reclamation::class);
    }

    // Exemple de méthode pour récupérer toutes les réclamations
    public function findAllReclamations()
    {
        return $this->findAll();
    }

    // Exemple de méthode pour trouver une réclamation par son ID
    public function findReclamationById($id)
    {
        return $this->find($id);
    }

    // Exemple d'une requête personnalisée : trouver les réclamations par état
    public function findReclamationsByStatus($status)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.statut = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    
    public function findReclamationsByEmail(string $email)
{
    return $this->createQueryBuilder('r')
        ->andWhere('r.email = :email')
        ->setParameter('email', $email)
        ->getQuery()
        ->getResult();
}

public function findReclamationsWithReponses(): array
{
    return $this->createQueryBuilder('r')
        ->select('r.id')
        ->innerJoin('r.reponses', 'rep')
        ->groupBy('r.id')
        ->getQuery()
        ->getResult();
}


}
