<?php

namespace App\Repository;

use App\Entity\Train;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Train::class);
    }

    
    public function findByProprietaire(string $proprietaire): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.proprietaire = :proprietaire')
            ->setParameter('proprietaire', $proprietaire)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTrajetByDepartAndArrivee($depart, $arrivee)
    {
        return $this->createQueryBuilder('t')
            ->where('t.depart = :depart')
            ->andWhere('t.arret = :arrivee')
            ->setParameters([
                'depart' => $depart,
                'arrivee' => $arrivee
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
