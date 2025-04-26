<?php

namespace App\Repository;

use App\Entity\Bus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class BusRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bus::class);
    }

    
    public function findByCompagnie(string $compagnie): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.compagnie = :compagnie')
            ->setParameter('compagnie', $compagnie)
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
