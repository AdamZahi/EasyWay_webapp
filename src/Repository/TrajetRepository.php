<?php

namespace App\Repository;

use App\Entity\Trajet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TrajetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ligne::class);
    }
   
public function findTrajetByDepartAndArrivee($depart, $arret)
{
    return $this->createQueryBuilder('t')
        ->where('LOWER(t.depart) = LOWER(:depart)')
        ->andWhere('LOWER(t.arret) = LOWER(:arret)')
        ->setParameter('depart', $depart)
        ->setParameter('arret', $arret)
        ->getQuery()
        ->getOneOrNullResult();
}

}