<?php

namespace App\Repository;

use App\Entity\Metro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class MetroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Metro::class);
    }

    
    public function findByLigne(string $ligne): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.ligne = :ligne')
            ->setParameter('ligne', $ligne)
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
