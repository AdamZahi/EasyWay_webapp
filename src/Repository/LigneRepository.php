<?php

namespace App\Repository;

use App\Entity\Ligne;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ligne>
 */
class LigneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ligne::class);
    }

    public function findTrajetByDepartAndArrivee(string $depart, string $arret): ?Ligne
    {
        return $this->createQueryBuilder('l')
            ->where('l.depart = :depart')
            ->andWhere('l.arret = :arret')
            ->setParameter('depart', $depart)
            ->setParameter('arret', $arret)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
